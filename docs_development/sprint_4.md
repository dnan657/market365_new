# ТЗ: Спринт 4. Монетизация, Платежи (Stripe) и B2B (Магазины)

**Цель спринта:** Реализовать коммерческую логику проекта. Починить и завершить интеграцию Stripe для приёма платежей за продвижение объявлений (ТОП, Выделение), создать функционал «Мини-сайтов» для B2B-пользователей, добавить страницу истории платежей в личном кабинете.

---

## Текущее состояние (аудит перед спринтом)

| Компонент | Статус | Примечание |
|-----------|--------|------------|
| `api/chat.php` | **Готов** | Реализован в Спринте 3; методы `get_list`, `get_messages`, `send`, `unread_count` работают |
| `api/favorite.php` | **Готов** | Реализован в Спринте 3; методы `toggle`, `get_list` работают |
| Таблицы `chat`, `chat_message`, `user_favorite` | **Готовы** | Созданы в `schema_db.sql` и через `f_db_init.php` |
| `api/pay.php` | **Сломан** | Файл из другого проекта: вызывает несуществующие `f_pay_get()`, `f_list_city()`, `f_pay_type_ru()`, работает с таблицей `pay` как с пользователями — нужен полный рефакторинг |
| `page/user/user_pays_add.php` | **Сломана** | Stripe Elements подключён, но `clientSecret` берётся с несуществующего `create_payment_intent.php`; используется константа `STRIPE_PUBLISHABLE_KEY` вместо `$GLOBALS['WEB_JSON']['api_json']['stripe_public']` |
| Таблица `pay_transaction` | **Частично** | Есть в `schema_db.sql`, но без полей `stripe_intent_id`, `service_type`, `ads_id` — нужна миграция |
| Колонки `is_top_until`, `is_vip_until`, `store_id` в `ads` | **Отсутствуют** | Нужны миграции через `f_db_init.php` |
| Таблица `store` | **Отсутствует** | Нет в `schema_db.sql`; нужна миграция |
| `api/store.php` | **Отсутствует** | Файл не создан |
| `page/store/view.php` | **Отсутствует** | Файл не создан; маршрут `/shop/{slug}` не зарегистрирован |
| `page/user/user_pays.php` | **Заглушка** | 15 строк, нет логики; нужно реализовать историю транзакций |
| `page/ads_promote.php` | **Заглушка** | 174 строки; маршрут `/ads/promote/...` зарегистрирован, логика продвижения не реализована |

---

## Блок 1. Расширение БД (через `func/f_db_init.php`)

Добавить в массив `$migrations` следующие миграции:

1. **`008_ads_top_fields`** — колонки продвижения в таблице `ads`:
   ```sql
   ALTER TABLE `ads`
     ADD COLUMN IF NOT EXISTS `is_top_until` DATETIME DEFAULT NULL,
     ADD COLUMN IF NOT EXISTS `is_vip_until` DATETIME DEFAULT NULL,
     ADD COLUMN IF NOT EXISTS `store_id` BIGINT DEFAULT NULL;
   ALTER TABLE `ads` ADD INDEX IF NOT EXISTS `is_top_until` (`is_top_until`);
   ALTER TABLE `ads` ADD INDEX IF NOT EXISTS `store_id` (`store_id`);
   ```

2. **`009_pay_transaction_stripe_fields`** — поля Stripe в существующей таблице `pay_transaction`:
   ```sql
   ALTER TABLE `pay_transaction`
     ADD COLUMN IF NOT EXISTS `ads_id` BIGINT DEFAULT NULL,
     ADD COLUMN IF NOT EXISTS `stripe_intent_id` VARCHAR(100) DEFAULT NULL,
     ADD COLUMN IF NOT EXISTS `service_type` VARCHAR(50) DEFAULT NULL COMMENT 'top / vip / subscription',
     ADD COLUMN IF NOT EXISTS `user_id` BIGINT DEFAULT NULL;
   ALTER TABLE `pay_transaction` ADD INDEX IF NOT EXISTS `stripe_intent_id` (`stripe_intent_id`);
   ALTER TABLE `pay_transaction` ADD INDEX IF NOT EXISTS `user_id` (`user_id`);
   ALTER TABLE `pay_transaction` ADD INDEX IF NOT EXISTS `ads_id` (`ads_id`);
   ```

3. **`010_store_table`** — таблица магазинов:
   ```sql
   CREATE TABLE IF NOT EXISTS `store` (
     `_id` BIGINT NOT NULL AUTO_INCREMENT,
     `_create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `user_id` BIGINT NOT NULL,
     `name` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
     `slug` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
     `description` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `logo_upload_id` BIGINT DEFAULT NULL,
     `banner_upload_id` BIGINT DEFAULT NULL,
     `phone` VARCHAR(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `address` VARCHAR(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `city_id` BIGINT DEFAULT NULL,
     PRIMARY KEY (`_id`),
     UNIQUE KEY `ux_store_slug` (`slug`),
     KEY `idx_store_user` (`user_id`),
     KEY `idx_store_city` (`city_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

> **Важно:** после добавления миграций вызвать `/api/dev/db_init` для применения изменений в БД.

---

## Блок 2. Рефакторинг `api/pay.php` и интеграция Stripe

**Проблема:** Текущий `api/pay.php` — это код из другого проекта (работает с `pay` как с таблицей пользователей, вызывает несуществующие функции). Нужен полный рефакторинг под задачи платёжной системы Market365.

### 2.1. Новая структура `api/pay.php`

Переписать файл. Новый набор методов в `$gl_api_func_json`:

```php
$gl_api_func_json = [
    "create_intent"  => "f_api_pay_create_intent",
    "webhook"        => "f_api_pay_webhook",
    "get_list"       => "f_api_pay_get_list",
];
```

### 2.2. Метод `create_intent` → `f_api_pay_create_intent`

**Назначение:** Создать Stripe `PaymentIntent`, записать транзакцию в `pay_transaction` со статусом `pending`, вернуть `client_secret` фронтенду.

**Входные параметры:** `ads_id`, `service_type` (`top` / `vip`).

**Логика:**
1. Проверить авторизацию: `f_user_get()` не `false`.
2. Получить объявление из `ads` по `ads_id`; убедиться, что `user_id` совпадает с текущим пользователем.
3. Определить сумму по `service_type`:
   - `top` → 499 (пенсы, £4.99)
   - `vip` → 999 (пенсы, £9.99)
4. Создать `PaymentIntent` через Stripe API (cURL на `https://api.stripe.com/v1/payment_intents`), используя `$GLOBALS['WEB_JSON']['api_json']['stripe_secret']`.
5. Записать в `pay_transaction` через `f_db_insert`: `user_id`, `ads_id`, `service_type`, `stripe_intent_id` (из ответа Stripe), `paid_amount`, `paid_amount_currency = 'GBP'`, `payment_status = 'pending'`.
6. Вернуть `client_secret` и `stripe_public` (из `$GLOBALS['WEB_JSON']['api_json']['stripe_public']`).

### 2.3. Метод `webhook` → `f_api_pay_webhook`

**Назначение:** Принять уведомление от Stripe о результате оплаты и активировать услугу.

**Логика:**
1. Прочитать тело запроса: `$payload = file_get_contents('php://input')`.
2. Верифицировать подпись Stripe через заголовок `Stripe-Signature` (использовать `webhook_secret` из конфига — добавить ключ `stripe_webhook_secret` в `config.php` и `config.defaults.php`).
3. Декодировать JSON события; обрабатывать тип `payment_intent.succeeded`.
4. Найти запись в `pay_transaction` по `stripe_intent_id`.
5. Обновить `pay_transaction`: `payment_status = 'success'`, `update_date = NOW()`.
6. **Активация услуги:** в зависимости от `service_type`:
   - `top` → `UPDATE ads SET is_top_until = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE _id = ?`
   - `vip` → `UPDATE ads SET is_vip_until = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE _id = ?`
7. Отправить email-уведомление пользователю через `f_email_send`: «Your ad has been promoted on Market365».
8. Вернуть HTTP 200 (Stripe требует 200 при успехе).

> **Маршрут webhook:** добавить в `route.php` отдельный маршрут `/api/pay/webhook`, который вызывает `f_api_pay_webhook` **без** проверки авторизации (Stripe шлёт запрос без сессии).

### 2.4. Метод `get_list` → `f_api_pay_get_list`

**Назначение:** История транзакций текущего пользователя для страницы `/user/pays`.

**Логика:**
1. Проверить авторизацию.
2. `SELECT * FROM pay_transaction WHERE user_id = ? ORDER BY create_date DESC LIMIT 50`.
3. Для каждой записи добавить `html_date` (через `f_datetime_beauty`), `html_amount` (форматированная сумма в £), `html_service` (читаемое название услуги).
4. Вернуть массив транзакций.

---

## Блок 3. Починка страницы оплаты (`page/user/user_pays_add.php`)

**Проблема:** Страница использует несуществующий файл `create_payment_intent.php` и неопределённую константу `STRIPE_PUBLISHABLE_KEY`.

**Задачи:**

1. Убрать вызов `create_payment_intent.php`. Вместо этого при загрузке страницы принимать GET-параметры `ads_id` и `service_type` из URL.
2. Вызвать `f_ajax('pay', 'create_intent', {ads_id, service_type})` через JS при загрузке страницы — получить `client_secret` и `stripe_public`.
3. Заменить `STRIPE_PUBLISHABLE_KEY` на `$GLOBALS['WEB_JSON']['api_json']['stripe_public']` в PHP-части.
4. Инициализировать Stripe Elements с полученным `client_secret`.
5. В `confirmParams.return_url` указать реальный URL: `https://market365.uk.com/user/pays` (через `f_page_link`).
6. После успешной оплаты (`payment_intent.succeeded`) — редирект на `/user/pays` с сообщением об успехе.

---

## Блок 4. B2B Функционал (Мини-сайты)

### 4.1. `api/store.php`

Создать файл по образцу `api/ads.php`. Набор методов:

```php
$gl_api_func_json = [
    "save"     => "f_api_store_save",
    "get"      => "f_api_store_get",
    "get_list" => "f_api_store_get_list",
];
```

**`f_api_store_save`:**
- Доступ: только пользователи с `type = 'b2b'` или `admin`.
- Принимает: `name`, `slug`, `description`, `phone`, `address`, `city_id`, `logo_upload_id`, `banner_upload_id`.
- Валидация `slug`: только латиница, цифры, дефис; длина 3–50 символов; проверка уникальности в `store`.
- Если `store` для текущего `user_id` уже существует — обновить через `f_db_update_smart`; иначе создать через `f_db_insert`.
- Вернуть `slug` для редиректа на `/shop/{slug}`.

**`f_api_store_get`:**
- Принимает `slug`.
- `SELECT * FROM store WHERE slug = ?`; добавить URL логотипа и баннера из таблицы `upload`.
- Вернуть данные магазина.

**`f_api_store_get_list`:**
- Принимает `store_id` или `slug`.
- Возвращает объявления магазина: `SELECT * FROM ads WHERE store_id = ? AND delete_on = 0 AND publication_on = 1 ORDER BY _create_date DESC`.
- Формат карточки — аналогичен `api/ads.php → get_list` (поля `html_img_src`, `title`, `html_price`, `html_city`, `html_date`, `html_link_ad`).

### 4.2. Маршрут `/shop/{slug}` в `route.php`

Добавить правило в `$arr_json_route` **перед** общим catch-all:
```php
[
    'regex'      => '^shop/([a-z0-9\-]+)$',
    'file'       => 'page/store/view.php',
    'user_check' => false,
    'ads_side'   => true,
]
```
Значение `slug` извлекать из `$uri_dir_arr[1]` внутри `page/store/view.php`.

### 4.3. Страница магазина (`page/store/view.php`)

Создать файл:
- PHP: вызвать `f_ajax` или прямой SQL для получения данных магазина по `slug`.
- Если магазин не найден — вернуть 404 (подключить `page/tools/404.php`).
- Верстка: баннер магазина, логотип, название, описание, контакты.
- Ниже — сетка объявлений магазина (через `f_ajax('store', 'get_list', {slug})` + `f_ads_item_line_make`).
- SEO: `f_page_title_set` с названием магазина; `og:title`, `og:image` — логотип.

---

## Блок 5. Фронтенд: коммерческие интерфейсы

### 5.1. Страница истории платежей (`page/user/user_pays.php`)

Маршрут `/user/pays` зарегистрирован в `route.php` с `user_check = true`.

- При загрузке вызвать `f_ajax('pay', 'get_list', {})`.
- Отрисовать таблицу транзакций: дата, услуга, сумма, статус (`pending` / `success` / `fail`).
- Добавить кнопку «Top up / Promote» — ссылка на `/user/pays/add`.

### 5.2. Страница продвижения (`page/ads_promote.php`)

Маршрут `/ads/promote/{_id_str}` зарегистрирован в `route.php`.

- PHP: получить объявление по `_id_str` из URL; проверить, что текущий пользователь — владелец.
- Отобразить карточку объявления (превью).
- Блок выбора услуги:
  - **TOP (7 дней) — £4.99** — объявление закрепляется в верхней части выдачи.
  - **VIP (30 дней) — £9.99** — визуальное выделение карточки в ленте.
- При выборе услуги — редирект на `/user/pays/add?ads_id={_id_str}&service_type={top|vip}`.

### 5.3. Визуальное выделение ТОП-объявлений в ленте

В `api/ads.php` → `f_api_get_list_ads` при формировании карточки добавить:
- Если `is_top_until > NOW()` → добавить в карточку поле `html_is_top = true` (или CSS-класс `card-top`).
- Если `is_vip_until > NOW()` → добавить `html_is_vip = true` (CSS-класс `card-vip`).
- В `f_ads_item_line_make` (JS) обрабатывать эти флаги: добавлять бейдж «TOP» или выделение рамкой.

### 5.4. Личный кабинет B2B (`page/user/user_item.php` или новая страница)

- Добавить в профиль пользователя с `type = 'b2b'` блок «My Store»:
  - Если магазин существует — показать ссылку на `/shop/{slug}` и кнопку «Edit store».
  - Если нет — кнопку «Create store».
- Форма редактирования магазина: поля `name`, `slug`, `description`, `phone`, `address`; загрузка логотипа через `f_ajax('upload', 'file', ...)` с `item_table=store`.

---

## Блок 6. Обновление документации

После реализации обновить:

- **`docs_project/api_modules.md`** — добавить секции `api/pay.php` (новые методы) и `api/store.php`.
- **`docs_project/pages_modules_architecture.md`** — добавить строки для `/shop/{slug}` → `page/store/view.php` и обновить строку `/user/pays`.
- **`docs_project/schema_db.sql`** — добавить DDL для таблицы `store` и новых колонок.

---

## Definition of Done (Критерии завершения Спринта 4)

- [ ] Миграции `008`, `009`, `010` применены через `/api/dev/db_init`; таблица `store` и новые колонки в `ads`/`pay_transaction` существуют.
- [ ] `api/pay.php` переписан: `create_intent` создаёт `PaymentIntent` в Stripe и записывает транзакцию в `pay_transaction`.
- [ ] Webhook `/api/pay/webhook` принимает событие `payment_intent.succeeded`, обновляет `pay_transaction` и активирует `is_top_until` / `is_vip_until` в `ads`.
- [ ] Страница `/user/pays/add` корректно инициализирует Stripe Elements, принимает оплату и редиректит на `/user/pays`.
- [ ] Страница `/user/pays` отображает историю транзакций пользователя.
- [ ] Страница `/ads/promote/{_id_str}` позволяет выбрать услугу продвижения и перейти к оплате.
- [ ] ТОП-объявления (`is_top_until > NOW()`) отображаются в верхней части ленты с визуальным бейджем.
- [ ] Пользователь с `type = 'b2b'` может создать/редактировать магазин через `api/store.php`.
- [ ] Страница `/shop/{slug}` отображает профиль магазина и его активные объявления.
- [ ] Документы `api_modules.md`, `pages_modules_architecture.md`, `schema_db.sql` обновлены.
