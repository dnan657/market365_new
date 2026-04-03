# ТЗ: Спринт 5. Аудит проекта и устранение технического долга

**Цель спринта:** Провести полный аудит кодовой базы после четырёх спринтов разработки. Устранить накопленные баги, заглушки, дублирование кода, несоответствия UX/UI дизайн-гайдлайнам и логические ошибки. Подготовить проект к продуктовым спринтам (автомодерация, поиск по карте, мобильная навигация).

---

## Текущее состояние (аудит перед спринтом 5)

### Что реализовано и работает

| Компонент | Статус | Примечание |
|-----------|--------|------------|
| `api/pay.php` | **Готов** | `create_intent`, `get_list`, webhook-верификация через HMAC |
| `api/store.php` | **Готов** | `save`, `get`, `get_list` |
| `page/store/view.php` | **Готов** | SSR + AJAX-сетка объявлений |
| `page/user/user_pays.php` | **Готов** | История транзакций через `f_ajax` |
| `page/user/user_pays_add.php` | **Готов** | Stripe Elements, `create_intent` через `f_ajax` |
| `page/ads_promote.php` | **Готов** | Выбор TOP/VIP, редирект на оплату |
| `page/user/user_item.php` | **Готов** | Блок «Магазин» для `business`-аккаунтов |
| `api/ads.php` → `get_list` | **Готов** | `html_promo` флаг (`top`/`vip`), приоритет в сортировке |
| Миграции `008`–`010` | **Готовы** | `is_top_until`, `is_vip_until`, `store_id`, `store` |
| `route.php` | **Готов** | Маршрут `/shop/{slug}`, webhook `/api/pay/webhook` |

---

## Блок 1. Критические баги (Priority 1 — исправить немедленно)

### 1.1. `route.php` — `config.php` без `config.defaults.php`

**Проблема:** `route.php` строка 3 делает `require __DIR__ . '/config.php'` напрямую, без `array_merge` с `config.defaults.php`. Если `config.php` отсутствует на сервере — сайт падает с Fatal Error вместо деградации.

**Документация** (`pages_modules_architecture.md` §1.1) описывает паттерн `array_merge(defaults, config)`, но в коде он не реализован.

**Исправление:**
```php
// route.php, строка 3 — заменить:
$MARKET365_CONFIG = require __DIR__ . '/config.php';

// На:
$defaults = require __DIR__ . '/config.defaults.php';
$local = file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : [];
$MARKET365_CONFIG = array_merge($defaults, $local);
```

Убедиться, что `config.defaults.php` существует и содержит все ключи с пустыми значениями: `stripe_public`, `stripe_secret`, `stripe_webhook_secret`, `google_recaptcha_v3_public`, `google_recaptcha_v3_secret`, `google_recaptcha_v2_public`, `google_recaptcha_v2_secret`, `google_oauth_client_id`, `google_oauth_client_secret`, `google_oauth_redirect_url`, `email_main_login`, `email_main_pass`.

---

### 1.2. `route.php` — `user_check` при незалогиненном пользователе редиректит на `/` вместо `/login`

**Проблема:** Строка `f_redirect('/')` при `user_check === true` и отсутствии авторизации редиректит на главную, а не на страницу входа. Пользователь не понимает, почему его «выбрасывает».

**Исправление:**
```php
// Заменить:
f_redirect('/');
// На:
f_redirect(f_page_link('login'));
```

---

### 1.3. `route.php` — Cloudflare-фильтр по стране закомментирован, но код присутствует

**Проблема:** Строки 21–23 содержат мёртвый код с проверкой `HTTP_CF_IPCOUNTRY` (KZ/UZ). Для UK-маркетплейса это нерелевантно, создаёт путаницу и риск случайной активации.

**Исправление:** Удалить блок целиком (строки 21–23).

---

### 1.4. `route.php` — `ads_side` обращается к несуществующему ключу без `??`

**Проблема:** Строка 399: `$json_route['ads_side'] === true` — если ключ `ads_side` отсутствует в маршруте, PHP генерирует Notice (при `E_NOTICE` включённом).

**Исправление:**
```php
$GLOBALS['WEB_JSON']['page_ads']['side'] = ($json_route['ads_side'] ?? false) === true;
```

---

### 1.5. `route.php` — `file_on` присваивается вне цикла неверно

**Проблема:** Строка 405 (`$file_on = $json_route['file_on'] == true`) выполняется на каждой итерации, но `break` прерывает цикл до неё — значение `$file_on` всегда остаётся `false` для совпавшего маршрута. Маршруты с `file_on => true` (manifest.json, robots.txt, sw.js) работают только случайно.

**Исправление:** Переместить присвоение `$file_on` внутрь блока совпадения:
```php
if(preg_match($regex, $WEB_JSON['uri_clean'], $matches) > 0){
    $file_on = $json_route['file_on'] ?? false;
    // ... остальная логика ...
    break;
}
```

---

### 1.6. `api/ads.php` → `f_api_get_ads` — возвращает поля от другого проекта

**Проблема:** Метод `get` (строки 219–260) возвращает поля `html_price_min`, `html_lesson_dur`, `html_lesson_count_week`, `html_age`, `html_lang_edu`, `html_week_of_day` — это данные из образовательного проекта, не маркетплейса. Функция `f_db_get_ads` тоже содержит логику чужого проекта.

**Исправление:** Переписать `f_api_get_ads` для возврата полей маркетплейса: `_id_str`, `title`, `description`, `price`, `price_currency`, `city_id`, `html_city`, `html_price`, `html_date`, `gps_point`, `phone`, `user_id`, `publication_on`, `is_top_until`, `is_vip_until`.

---

### 1.7. `func/f_db.php` — дублирование `f_num_encode` / `f_num_decode`

**Проблема:** Функции `f_num_encode` и `f_num_decode` объявлены дважды в `f_db.php`. Действует последнее определение — это скрытый баг при рефакторинге.

**Исправление:** Оставить одно определение (последнее), удалить первое. Добавить комментарий о назначении функции.

---

### 1.8. `custom_js_functions.md` — `showMessage` в `user_pays_add.php` содержит баг

**Проблема:** В `setTimeout` используется `messageText.textContent` — у строки нет свойства `.textContent`. Должно быть `messageContainer.textContent = ''`.

**Файл:** `page/user/user_pays_add.php` — функция `showMessage`.

**Исправление:** Найти `setTimeout` в `showMessage` и заменить `messageText.textContent` на корректную ссылку на DOM-элемент `#payment-message`.

> **Примечание:** В текущей реализации `user_pays_add.php` этот баг уже исправлен (используется `el.textContent`). Убедиться, что `user_pays_add_intent.php` (старый файл) не используется нигде в маршрутах.

---

### 1.9. `page/user/user_pays_add_intent.php` — устаревший файл-заглушка

**Проблема:** Файл `page/user/user_pays_add_intent.php` существует, но не зарегистрирован в `route.php`. Содержит старую логику `create_payment_intent` через прямой cURL без авторизации. Может быть случайно подключён.

**Исправление:** Удалить файл `page/user/user_pays_add_intent.php`.

---

## Блок 2. Логические ошибки и несоответствия

### 2.1. `api/store.php` — `f_api_store_require_b2b` не учитывает тип `b2b`

**Проблема:** Проверка типа пользователя: `in_array($t, ['business', 'b2b', 'admin'], true)`. Однако в `user_item.php` в select для `user_type` присутствуют только значения `user`, `business`, `moderator`, `admin` — значения `b2b` нет. Тип `b2b` из документации спринта 4 не соответствует реальным значениям в форме.

**Исправление:** Привести к единому значению. Рекомендуется использовать `business` как единственный B2B-тип. Убрать `b2b` из проверки или добавить `b2b` в select формы `user_item.php`.

---

### 2.2. `api/store.php` → `get_list` — лимит 200 записей без пагинации

**Проблема:** Запрос `LIMIT 200` возвращает все объявления магазина без пагинации. При большом количестве объявлений у B2B-продавца страница `/shop/{slug}` будет медленной.

**Исправление:** Добавить параметры `page_num` / `page_size` (аналогично `api/ads.php`), вернуть `has_more` и `count_total`. На фронте `page/store/view.php` реализовать кнопку «Загрузить ещё» или infinite scroll.

---

### 2.3. `api/pay.php` → `f_pay_transaction_next_id` — race condition

**Проблема:** Функция `f_pay_transaction_next_id` вычисляет `MAX(_id) + 1` вручную. При параллельных запросах возможен дубликат `_id`. Таблица `pay_transaction` должна использовать `AUTO_INCREMENT`.

**Исправление:** Добавить миграцию `011_pay_transaction_auto_increment`:
```sql
ALTER TABLE `pay_transaction` MODIFY `_id` BIGINT NOT NULL AUTO_INCREMENT;
```
После применения миграции — убрать `f_pay_transaction_next_id()` из `f_api_pay_create_intent` и не передавать `_id` в `f_db_insert`.

---

### 2.4. `api/ads.php` — `html_promo` не обрабатывается в `f_ads_item_line_make`

**Проблема:** `f_api_get_list_ads` возвращает поле `html_promo` со значением `'top'` или `'vip'`, но функция `f_ads_item_line_make` в `template/script.php` не обрабатывает это поле — бейджи TOP/VIP не отображаются в ленте объявлений.

**Исправление:** В `template/script.php` в функции `f_ads_item_line_make` добавить обработку `html_promo`:
```javascript
if (json_item.html_promo === 'top') {
    // Добавить бейдж «TOP» в левый верхний угол фото
    var badge = $('<span class="badge bg-warning text-dark position-absolute top-0 start-0 m-1">TOP</span>');
    col.find('.ads_item_img_wrap').css('position','relative').prepend(badge);
} else if (json_item.html_promo === 'vip') {
    // Добавить рамку VIP согласно design guidelines (2px solid --color-primary)
    col.find('.ads_item_line').css('border', '2px solid var(--color-primary, #FF6B00)');
}
```

---

### 2.5. `page/store/view.php` — отсутствует SEO Open Graph разметка

**Проблема:** Страница магазина не устанавливает `og:title`, `og:image`, `og:description`. При шеринге ссылки в соцсетях превью будет пустым. Требование из `PRD_b2b.md` §3.1.

**Исправление:** После `f_page_title_set($name)` добавить:
```php
$GLOBALS['WEB_JSON']['page_json']['description'] = mb_substr(strip_tags((string)($store_row['description'] ?? '')), 0, 160);
if( $logo_url !== '' ){
    $GLOBALS['WEB_JSON']['page_json']['html_head'] .= '<meta property="og:image" content="' . htmlspecialchars($logo_url) . '">';
}
$GLOBALS['WEB_JSON']['page_json']['html_head'] .= '<meta property="og:title" content="' . htmlspecialchars($name) . '">';
```

---

### 2.6. `page/ads_promote.php` — ссылка «назад» ведёт на `admin_ads_list` которого нет

**Проблема:** Строка 71: `f_page_link('admin_ads_list')` — в `page_link` нет ключа `admin_ads_list` (есть `admin_ads_list` в `page_link` массиве? Проверить). Если ключ отсутствует, `f_page_link` вернёт пустую строку.

**Исправление:** Проверить наличие `admin_ads_list` в `page_link` в `route.php`. Если отсутствует — заменить на `f_page_link('user_ads')` для обоих случаев (admin и owner), или добавить ключ.

---

### 2.7. `page/user/user_item.php` — `$is_new` не определена

**Проблема:** Строка 253: `f_translate($is_new ? 'Create' : 'Save')` — переменная `$is_new` нигде не объявлена в файле. PHP выдаст Notice, кнопка всегда покажет `'Save'`.

**Исправление:** Добавить в начало файла: `$is_new = false;` (страница всегда редактирует текущего пользователя, не создаёт нового).

---

### 2.8. `api/pay.php` → webhook — fallback `service = 'top'` при пустом `service_type`

**Проблема:** Строка 283–286: если `service_type` пустой, webhook автоматически применяет `top`-продвижение. Это может привести к неверной активации услуги, если транзакция была создана без `service_type` (например, для будущих типов услуг).

**Исправление:** Убрать fallback. Если `service_type` пустой — не активировать ни одну услугу, только обновить статус транзакции.

---

## Блок 3. UX/UI несоответствия (по `frontend_design_guidelines.md`)

### 3.1. Статус платежа в `user_pays.php` — нет визуальных бейджей

**Проблема:** Статус транзакции (`pending`, `success`, `fail`) выводится как обычный текст в `<td>`. По дизайн-гайдлайнам (§6.7) статусы должны отображаться как цветные бейджи.

**Исправление:** В JS-рендере таблицы заменить `tr.append($('<td></td>').text(row.status))` на:
```javascript
var statusMap = {
    'success': ['bg-success', 'Active'],
    'pending': ['bg-warning text-dark', 'Pending'],
    'fail': ['bg-danger', 'Failed']
};
var s = statusMap[row.status] || ['bg-secondary', row.status];
tr.append($('<td></td>').html('<span class="badge ' + s[0] + '">' + s[1] + '</span>'));
```

---

### 3.2. `page/user/user_pays.php` — нет кнопки «Продвинуть объявление»

**Проблема:** По ТЗ спринта 4 (§5.1) на странице истории платежей должна быть кнопка «Top up / Promote» → ссылка на `/user/ads`. Кнопка ведёт на «Мои объявления», но подпись «Мои объявления» не объясняет действие.

**Исправление:** Изменить подпись кнопки на «Promote an ad» и добавить второй CTA:
```php
<a class="btn btn-primary btn-sm" href="<?php f_echo_html(f_page_link('user_ads')); ?>">
    <?php f_translate_echo('Promote an ad'); ?>
</a>
```

---

### 3.3. `page/ads_promote.php` — две кнопки Primary на одном экране

**Проблема:** На странице продвижения две кнопки `btn-primary` («Оплатить TOP» и «Оплатить VIP»). По дизайн-гайдлайнам §6.1 запрещено использовать более одной Primary-кнопки в одной секции.

**Исправление:** Одну кнопку оставить `btn-primary`, вторую сделать `btn-outline-primary`.

---

### 3.4. `page/store/view.php` — нет skeleton loader при загрузке объявлений

**Проблема:** При загрузке `f_ajax('store', 'get_list', ...)` сетка объявлений пуста. По дизайн-гайдлайнам §6.6 skeleton loaders обязательны для сеток карточек.

**Исправление:** Перед вызовом `f_ajax` отрисовать 6 skeleton-карточек в `#store_ads_grid`, убрать их после получения ответа.

---

### 3.5. `page/user/user_pays_add.php` — нет информации о том, что оплачивается

**Проблема:** Форма оплаты не показывает название объявления и тип услуги (TOP/VIP). Пользователь не видит, за что платит.

**Исправление:** В PHP-части загрузить объявление по `$ads_id` и отобразить блок:
```php
$ad_title_row = f_db_select('SELECT `title` FROM `ads` WHERE `_id` = ' . $ads_id . ' LIMIT 1');
$ad_title = $ad_title_row[0]['title'] ?? '';
$service_label = $service_type === 'top' ? 'TOP — 7 days (£4.99)' : 'VIP — 30 days (£9.99)';
```
Вывести перед формой: «You are paying for: **{$service_label}** for ad: **{$ad_title}**».

---

### 3.6. `page/store/view.php` — нет placeholder при отсутствии логотипа/баннера

**Проблема:** Если логотип или баннер не загружены, блок просто скрывается. По дизайн-гайдлайнам §8 должен быть placeholder (серый фон + иконка).

**Исправление:** Добавить placeholder-блок для логотипа:
```php
<?php if( $logo_url === '' || $logo_url === '/public/ad_default.jpg' ){ ?>
    <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="width:96px;height:96px;">
        <i class="bi bi-shop fs-2 text-muted"></i>
    </div>
<?php } ?>
```

---

### 3.7. `route.php` — timezone установлена как `Etc/GMT-0` с комментарием «по Астане»

**Проблема:** Строки 8–9: `date_default_timezone_set('Etc/GMT-0')` с комментарием `// Время по Астане`. Для UK-маркетплейса нужен `Europe/London` (учитывает BST/GMT переход).

**Исправление:**
```php
date_default_timezone_set('Europe/London');
```

---

### 3.8. `route.php` — `session_set_cookie_params` с `domain` = `$_SERVER['HTTP_HOST']`

**Проблема:** Использование `$_SERVER['HTTP_HOST']` как домена cookie может привести к проблемам на localhost (cookie не будет работать) и при поддоменах.

**Исправление:** Убрать `'domain'` из параметров или задать явно:
```php
'domain' => '',  // браузер сам определит домен
```

---

## Блок 4. Заглушки и мёртвый код

### 4.1. `page/user/user_notifications.php` — заглушка без функционала

**Статус:** Маршрут `/user/notifications` зарегистрирован, файл существует, но функционал уведомлений не реализован.

**Задача спринта 5:** Реализовать минимальную версию: список системных уведомлений из таблицы (или временно показать заглушку с сообщением «Notifications coming soon»), убрать пустую страницу.

---

### 4.2. `page/subscld_ription_list.php` — опечатка в имени файла + нет маршрута

**Проблема:** Файл называется `subscld_ription_list.php` (опечатка `subscld`). Маршрута нет. Использует некорректный URL `/api/user?query=find` вместо `/api/user/find`.

**Задача:** Переименовать файл в `subscription_list.php`, исправить URL API-вызова, добавить маршрут или пометить как `admin`-only.

---

### 4.3. `page/info_list.php` — файл отсутствует при зарегистрированном маршруте `/info`

**Проблема:** Маршрут `'regex' => 'info'` ведёт на `page/info_list.php`, которого нет. Переход на `/info` даст Fatal Error.

**Задача:** Создать минимальный `page/info_list.php` с перечнем инфостраниц (about, terms, privacy, faq) или сделать редирект на `/info/about-us`.

---

### 4.4. `api/subscription.php` — вызов несуществующей `f_get_pdd_category_arr()`

**Проблема:** `api/subscription.php` вызывает `f_get_pdd_category_arr()`, которой нет ни в `func/`, ни в `api/`. Любой запрос к `subscription/edit` вызовет Fatal Error.

**Задача:** Либо реализовать функцию, либо удалить/закомментировать вызов, либо пометить модуль как «не для текущего проекта».

---

### 4.5. `api/cron.php` — нет защиты от публичного доступа

**Проблема:** Эндпоинт `/api/cron/expired` доступен без авторизации. Любой может вызвать его и инициировать обновление просроченных сущностей.

**Задача:** Добавить проверку секретного токена из конфига:
```php
$cron_secret = $GLOBALS['WEB_JSON']['api_json']['cron_secret'] ?? '';
if( $cron_secret === '' || ($ARGS['secret'] ?? '') !== $cron_secret ){
    return ['error' => 'Forbidden', 'error_code' => 403];
}
```
Добавить `cron_secret` в `config.defaults.php` и `config.php`.

---

### 4.6. `template/script.php` — `f_form_m_value` вызывается, но не объявлена

**Проблема:** `f_form_reset` вызывает `f_form_m_value`, которой нет в репозитории. При вызове `f_form_reset` — JS-ошибка.

**Задача:** Добавить заглушку функции или убрать вызов:
```javascript
function f_form_m_value(control, val) {
    // placeholder — реализовать при необходимости
}
```

---

### 4.7. `template/script.php` — `f_back_page_link` задублирована в комментарии

**Проблема:** Функция `f_back_page_link` закомментирована, но логика «назад» реализована ниже без неё. Мёртвый код.

**Задача:** Удалить закомментированную функцию.

---

### 4.8. `page_file/sw.js.php` — Service Worker с закомментированной стратегией кэширования

**Проблема:** Большой блок закомментированного кода стратегии кэширования. SW регистрируется, но не кэширует ничего полезного.

**Задача:** Либо реализовать минимальную стратегию `cache-first` для статики, либо удалить регистрацию SW из шаблона до готовности.

---

## Блок 5. Безопасность

### 5.1. `page/user/user_set_auth.php` — служебная установка cookie без защиты

**Проблема:** Маршрут `/user/set-auth` позволяет установить cookie авторизации. Нет проверки IP, токена или Basic Auth. Потенциальный вектор атаки.

**Задача:** Добавить проверку `f_auth_http()` или секретного параметра. Если функционал не нужен — удалить маршрут и файл.

---

### 5.2. `func/f_db.php` → `f_db_query` — SQL без prepared statements

**Проблема:** `f_db_query` выполняет произвольный SQL без параметризации. В `api/ads.php` и других файлах строки экранируются через `f_db_sql_string_escape`, но это не полная защита от SQL-инъекций при неправильном использовании.

**Задача:** Добавить в документацию явное предупреждение. Для новых запросов использовать `f_db_select` с массивом условий. Провести аудит всех мест, где `f_db_query` получает данные из `$ARGS`.

---

### 5.3. `page/auth/login.php` — нет rate limiting на попытки входа

**Проблема:** PRD §5 требует защиту от брутфорса. Текущая реализация не ограничивает количество попыток входа.

**Задача:** Добавить счётчик попыток в таблицу `did` (поле `login_fail_count`, `login_fail_date`). После 5 неудачных попыток за 15 минут — блокировать на 15 минут и показывать reCAPTCHA v2.

---

## Блок 6. Несоответствия документации и кода

### 6.1. `schema_db.sql` — отсутствуют таблицы из миграций 008–010

**Проблема:** `schema_db.sql` не содержит DDL для таблицы `store` и новых колонок `ads.is_top_until`, `ads.is_vip_until`, `ads.store_id`, `pay_transaction.user_id`, `pay_transaction.ads_id`, `pay_transaction.stripe_intent_id`, `pay_transaction.service_type`.

**Задача:** Обновить `docs_project/schema_db.sql`:
- Добавить DDL таблицы `store` (из миграции `010_store_table`).
- Добавить `ALTER TABLE ads ADD COLUMN ...` для полей продвижения.
- Добавить `ALTER TABLE pay_transaction ADD COLUMN ...` для Stripe-полей.
- Добавить DDL таблиц `chat`, `chat_message`, `user_favorite` (они уже в конце файла, но без индексов из миграций).

---

### 6.2. `api_modules.md` — описание `api/pay.php` не содержит webhook-маршрут

**Проблема:** В `api_modules.md` webhook описан как `*Webhook*` в таблице, но без явного указания, что он обрабатывается в `route.php` напрямую (не через `api.php`). Разработчик может искать его в `$gl_api_func_json`.

**Задача:** Добавить в `api_modules.md` отдельную секцию «Специальные маршруты» с описанием `/api/pay/webhook` и `/api/dev/db_init`.

---

### 6.3. `pages_modules_architecture.md` — `page/user/user_item.php` вызывает `api/store/save`

**Проблема:** В таблице §2.2 для `user_item.php` указан вызов `POST /api/store/save (тип business)`, но в реальном коде `user_item.php` вызывает `f_ajax('store', 'save', ...)` — это соответствует. Однако не указан маршрут `user_item.php` → `api/user/save` для сохранения профиля.

**Задача:** Уточнить строку в таблице: добавить оба вызова `POST /api/user/save` и `POST /api/store/save (только для business)`.

---

### 6.4. `php_functions_reference.md` — упоминание `user_pays_add_intent.php` как актуального

**Проблема:** В разделе «Локальные функции в page/» упоминается `user_pays_add_intent.php` как содержащий функцию `create_payment_intent`. Файл является устаревшим и должен быть удалён (см. п. 1.9).

**Задача:** После удаления файла убрать строку из `php_functions_reference.md`.

---

## Блок 7. Производительность

### 7.1. `api/ads.php` — `SHOW COLUMNS` вызывается при каждом запросе

**Проблема:** Функция `f_api_get_list_ads` вызывает `SHOW COLUMNS FROM ads LIKE 'is_top'` и `SHOW COLUMNS FROM ads LIKE 'is_top_until'` через `static` переменные — кэш работает только в рамках одного запроса. При каждом HTTP-запросе выполняются лишние SQL-запросы.

**Задача:** Перенести проверку колонок в `f_db_init.php` или использовать `f_db_table_exists`-подобный подход с кэшем на уровне `f_db_get.php`. Либо убрать проверку, так как миграция `008` уже гарантирует наличие колонок.

---

### 7.2. `api/pay.php` → `f_pay_table_has_column` — `SHOW COLUMNS` на каждый вызов

**Проблема:** Аналогично п. 7.1 — `f_pay_table_has_column` кэширует результат через `static`, но кэш живёт только в рамках одного запроса. 4 вызова `SHOW COLUMNS` при каждом `create_intent`.

**Задача:** После применения миграции `009` убрать проверки `f_pay_table_has_column` и всегда записывать все поля напрямую.

---

### 7.3. `api/store.php` → `get_list` — дублирование SQL с `api/ads.php`

**Проблема:** Запрос в `f_api_store_get_list` дублирует структуру JOIN из `f_api_get_list_ads` (LEFT JOIN ads_img с subquery для первого изображения). При изменении логики превью нужно менять в двух местах.

**Задача:** Вынести общий SQL-фрагмент для превью в вспомогательную функцию `f_db_ads_img_join_sql()` в `func/f_db_get.php`.

---

## Блок 8. Новый функционал (минимальный, в рамках спринта 5)

### 8.1. Кнопка «Продвинуть» в `page/user/user_ads.php`

**Задача:** На странице «Мои объявления» добавить кнопку «Promote» рядом с каждым активным объявлением — ссылка на `/ads/promote/{_id_str}`. Это замыкает UX-флоу продвижения.

---

### 8.2. Отображение статуса продвижения в `page/user/user_ads.php`

**Задача:** Если объявление имеет активный `is_top_until` или `is_vip_until` — показывать бейдж «TOP active until DD/MM/YYYY» или «VIP active until DD/MM/YYYY» рядом с объявлением.

---

### 8.3. `page/info_list.php` — минимальная реализация

**Задача:** Создать файл `page/info_list.php` с перечнем ссылок на инфостраницы (About Us, Terms of Service, Privacy Policy, FAQ, Safety Tips, Payment Info). Предотвратить 404/500 при переходе на `/info`.

---

### 8.4. Добавить `stripe_webhook_secret` и `cron_secret` в `config.defaults.php`

**Задача:** Убедиться, что `config.defaults.php` содержит пустые значения для всех ключей, включая `stripe_webhook_secret` и `cron_secret`. Это предотвратит Fatal Error при деплое без `config.php`.

---

## Definition of Done (Критерии завершения Спринта 5)

- [ ] `route.php` использует `array_merge(defaults, config)` — сайт запускается без `config.php`.
- [ ] `route.php` редиректит на `/login` при `user_check`, удалён Cloudflare-фильтр KZ/UZ, исправлен `file_on`.
- [ ] `f_num_encode`/`f_num_decode` объявлены по одному разу в `f_db.php`.
- [ ] `f_api_get_ads` возвращает поля маркетплейса (убраны поля чужого проекта).
- [ ] `f_api_pay_create_intent` не использует `f_pay_transaction_next_id` (AUTO_INCREMENT).
- [ ] `f_ads_item_line_make` отображает бейджи TOP/VIP по полю `html_promo`.
- [ ] `page/store/view.php` имеет OG-разметку, skeleton loader, placeholder для логотипа.
- [ ] `page/user/user_pays.php` отображает статусы как цветные бейджи.
- [ ] `page/user/user_pays_add.php` показывает название объявления и тип услуги перед оплатой.
- [ ] `page/ads_promote.php` — только одна Primary-кнопка.
- [ ] `page/user/user_item.php` — переменная `$is_new` объявлена, тип B2B унифицирован.
- [ ] `page/user/user_pays_add_intent.php` удалён.
- [ ] `page/info_list.php` создан (минимальная реализация).
- [ ] `api/subscription.php` — вызов `f_get_pdd_category_arr()` защищён или удалён.
- [ ] `api/cron.php` защищён секретным токеном.
- [ ] `template/script.php` — добавлена заглушка `f_form_m_value`, удалён мёртвый код.
- [ ] `page/user/user_ads.php` — кнопка «Promote» и статус активного продвижения.
- [ ] `docs_project/schema_db.sql` обновлён (таблица `store`, новые колонки).
- [ ] `docs_project/api_modules.md` — добавлена секция «Специальные маршруты».
- [ ] `docs_project/php_functions_reference.md` — убрана ссылка на `user_pays_add_intent.php`.
- [ ] Timezone в `route.php` изменён на `Europe/London`.

---

## Приоритеты выполнения

| Приоритет | Задачи |
|-----------|--------|
| **P1 — Критические** | 1.1, 1.2, 1.5, 1.7, 2.3, 2.4, 4.3, 4.4 |
| **P2 — Важные** | 1.3, 1.4, 1.6, 1.9, 2.1, 2.5, 2.7, 3.7, 4.6, 5.1 |
| **P3 — Улучшения UX** | 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 8.1, 8.2 |
| **P4 — Документация** | 6.1, 6.2, 6.3, 6.4, 8.3, 8.4 |
| **P5 — Рефакторинг** | 2.2, 2.6, 2.8, 4.5, 4.7, 4.8, 5.2, 5.3, 7.1, 7.2, 7.3 |
