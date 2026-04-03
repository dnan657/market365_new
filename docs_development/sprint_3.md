# ТЗ: Спринт 3. Коммуникация и Удержание (Чаты и Избранное)

**Цель спринта:** Реализовать систему личных сообщений между покупателем и продавцом, а также функционал «Избранного». Эти модули превращают сайт из «витрины» в живую платформу и обеспечивают возвращаемость пользователей (retention).

---

## Текущее состояние (аудит перед спринтом)

| Компонент | Статус | Примечание |
|-----------|--------|------------|
| Таблицы `chat`, `chat_message`, `user_favorite` | **Отсутствуют** | Нет в `schema_db.sql`; нужны миграции через `f_db_init.php` |
| `api/chat.php` | **Отсутствует** | Файл не создан |
| `api/favorite.php` | **Отсутствует** | Файл не создан |
| `page/user/user_messages.php` | **Заглушка** | Маршрут `/user/messages` зарегистрирован в `route.php`, файл есть, но логики нет |
| `page/user/user_favorites.php` | **Заглушка** | Маршрут `/user/favorites` зарегистрирован, файл есть, данные не подтягиваются |
| `page/ads_item.php` | **Частично** | Карточка товара есть; кнопки «Написать» и «Избранное» — заглушки без AJAX |
| Бейдж непрочитанных в хедере | **Отсутствует** | `template/page.php` не вызывает счётчик 

---

## Блок 1. Миграции БД (`func/f_db_init.php`)

Добавить в массив миграций `f_db_init.php` три новые таблицы. Запуск — через эндпоинт `/api/dev/db_init` (подключается из `route.php` напрямую, минуя `api.php`).

**Задачи:**

1. **Таблица `chat`** — реестр диалогов:
   - `_id` (BIGINT PK AUTO_INCREMENT), `_create_date` (DATETIME DEFAULT CURRENT_TIMESTAMP).
   - `ads_id` (BIGINT) — FK к `ads._id`; товар, по которому ведётся переписка.
   - `user_buyer_id` (BIGINT) — FK к `user._id`.
   - `user_seller_id` (BIGINT) — FK к `user._id`.
   - Индексы: `ads_id`, `user_buyer_id`, `user_seller_id`.

2. **Таблица `chat_message`** — хранилище сообщений:
   - `_id` (BIGINT PK AUTO_INCREMENT), `_create_date` (DATETIME DEFAULT CURRENT_TIMESTAMP).
   - `chat_id` (BIGINT) — FK к `chat._id`.
   - `user_sender_id` (BIGINT) — кто отправил; FK к `user._id`.
   - `message_text` (TEXT NOT NULL).
   - `is_read` (TINYINT(1) NOT NULL DEFAULT 0).
   - Индексы: `chat_id`, `user_sender_id`, `is_read`.

3. **Таблица `user_favorite`** — закладки:
   - `user_id` (BIGINT NOT NULL), `ads_id` (BIGINT NOT NULL), `_create_date` (DATETIME DEFAULT CURRENT_TIMESTAMP).
   - UNIQUE KEY `(user_id, ads_id)` — один пользователь не может добавить одно объявление дважды.
   - Индексы: `user_id`, `ads_id`.

> **Соглашение:** поле ссылки на объявление называть `ads_id` (FK к `ads._id`) — в соответствии с существующей схемой (`ads_img.ads_id`, `ads_item_param_value.ads_item_id`). Не `item_id`.

---

## Блок 2. Бэкенд API

### 2.1. `api/chat.php`

Структура файла аналогична `api/ads.php`: массив `$gl_api_func_json` с ключами → функциями. Доступ только для авторизованных пользователей (`f_user_get()` возвращает не `false`).

**Методы (`query`):**

1. **`get_list`** → `f_api_chat_get_list`
   - Возвращает все диалоги текущего пользователя (где `user_buyer_id = $me` OR `user_seller_id = $me`).
   - JOIN с `ads` и `ads_img` (первое фото по `ads_id`, аналогично `api/ads.php` → `get_list`): возвращать `ads_title`, `ads_img_src`.
   - JOIN с `user` для имени собеседника.
   - Для каждого чата — последнее сообщение из `chat_message` (подзапрос или `ORDER BY _id DESC LIMIT 1`).
   - Поле `unread_count` — количество `is_read = 0`, где `user_sender_id != $me`.

2. **`get_messages`** → `f_api_chat_get_messages`
   - Принимает `chat_id`.
   - **Проверка прав:** убедиться, что `$me` является `user_buyer_id` или `user_seller_id` в этом чате — иначе вернуть ошибку.
   - Возвращает все сообщения чата, отсортированные по `_create_date ASC`.
   - Помечает все входящие сообщения как прочитанные: `UPDATE chat_message SET is_read = 1 WHERE chat_id = ? AND user_sender_id != $me AND is_read = 0`.

3. **`send`** → `f_api_chat_send`
   - Принимает `ads_id` (обязательно), `message_text`, опционально `chat_id`.
   - Если `chat_id` не передан (первое сообщение): найти `user_id` продавца из `ads` (`SELECT user_id FROM ads WHERE _id = ?`), создать запись в `chat` через `f_db_insert`.
   - Добавить запись в `chat_message` через `f_db_insert`.
   - Вызвать `f_email_send` для уведомления продавца: «New message on your listing: [ads.title]».
   - Вернуть `chat_id` и созданное сообщение.

### 2.2. `api/favorite.php`

**Методы (`query`):**

1. **`toggle`** → `f_api_favorite_toggle`
   - Принимает `ads_id`.
   - Проверить наличие записи в `user_favorite` для пары `(user_id = $me, ads_id)`.
   - Если есть — удалить (`DELETE`), вернуть `{"is_favorite": false}`.
   - Если нет — вставить через `f_db_insert`, вернуть `{"is_favorite": true}`.
   - Использовать `INSERT IGNORE` или `ON DUPLICATE KEY` как защиту от гонки.

2. **`get_list`** → `f_api_favorite_get_list`
   - Возвращает список объявлений из `user_favorite` JOIN `ads` JOIN `ads_img` для текущего пользователя.
   - Формат карточки — аналогичен `api/ads.php` → `get_list` (поля `html_img_src`, `title`, `html_price`, `html_city`, `html_date`, `html_link_ad`), чтобы переиспользовать JS-функцию `f_ads_item_line_make`.

---

## Блок 3. Фронтенд и UI-компоненты

### 3.1. Страница чатов (`page/user/user_messages.php`)

Маршрут `/user/messages` уже зарегистрирован в `route.php` с `user_check = true`.

- **Верстка:** двухколоночный layout (Bootstrap `row`): слева список диалогов, справа — окно переписки или заглушка «Select a chat».
- При загрузке страницы вызвать `f_ajax('chat', 'get_list', {})` и отрисовать список диалогов.
- При клике на диалог — вызвать `f_ajax('chat', 'get_messages', {chat_id: ...})` и отрисовать историю.
- **Polling:** `setInterval` раз в 8 секунд вызывает `get_messages` для активного `chat_id` и дописывает новые сообщения в конец ленты (сравнивать по последнему `_id`).
- Форма отправки: `<textarea>` + кнопка «Send»; вызывает `f_ajax('chat', 'send', {chat_id, message_text})`.

### 3.2. Страница избранного (`page/user/user_favorites.php`)

Маршрут `/user/favorites` уже зарегистрирован с `user_check = true`.

- При загрузке вызвать `f_ajax('favorite', 'get_list', {})`.
- Отрисовать сетку карточек через `f_ads_item_line_make` (та же JS-функция, что на главной).
- На каждой карточке — кнопка «Remove from favorites», вызывающая `f_ajax('favorite', 'toggle', {ads_id})` и удаляющая карточку из DOM.

---

## Блок 4. Интеграция в карточку товара (`page/ads_item.php`)

1. **Кнопка «Write a message»:**
   - Если пользователь не авторизован — редирект на `/login` (использовать `f_page_link('login')`).
   - Если авторизован и пользователь — владелец объявления — кнопку скрыть.
   - Если авторизован и не владелец — кнопка открывает модальное окно с `<textarea>` и отправляет `f_ajax('chat', 'send', {ads_id, message_text})`, после успеха — редирект на `/user/messages`.

2. **Виджет «Favourite» (сердечко):**
   - При рендере страницы проверить на PHP: есть ли запись в `user_favorite` для текущего пользователя и `ads_id` — выставить CSS-класс `active` на кнопку.
   - При клике — `f_ajax('favorite', 'toggle', {ads_id})`, по ответу переключать класс `active` без перезагрузки.
   - Гостям кнопка видна, но при клике — редирект на `/login`.

3. **Бейдж непрочитанных в хедере (`template/page.php`):**
   - Добавить вызов `f_ajax('chat', 'get_list', {})` при загрузке (или отдельный легкий эндпоинт `get_unread_count`).
   - Отображать красный badge рядом с иконкой «Messages» в навигации, если `unread_count > 0`.
   - Обновлять счётчик тем же `setInterval` (8 сек), что и polling в чате.

---

## Блок 5. Маршруты и документация

При добавлении новых API-модулей обновить:

- **`docs_project/api_modules.md`** — добавить секции `api/chat.php` и `api/favorite.php`.
- **`docs_project/pages_modules_architecture.md`** — в таблицу раздела 2.2 добавить строки для `user_messages.php` и `user_favorites.php`.

Новые маршруты в `route.php` не нужны — `/user/messages` и `/user/favorites` уже зарегистрированы.

---

## Definition of Done (Критерии готовности Спринта 3)

- [ ] Таблицы `chat`, `chat_message`, `user_favorite` созданы в БД через `/api/dev/db_init`.
- [ ] `api/chat.php` реализован: методы `get_list`, `get_messages`, `send` работают корректно.
- [ ] `api/favorite.php` реализован: методы `toggle`, `get_list` работают корректно.
- [ ] Покупатель может отправить первое сообщение продавцу прямо со страницы объявления.
- [ ] Продавец видит входящее сообщение на странице `/user/messages` и может ответить.
- [ ] Сообщения помечаются `is_read = 1` при открытии диалога.
- [ ] Пользователь не может читать чужие диалоги (проверка прав в `get_messages`).
- [ ] Продавец получает email-уведомление о новом сообщении через `f_email_send`.
- [ ] Товар добавляется/удаляется из избранного без перезагрузки страницы (AJAX).
- [ ] Страница `/user/favorites` отображает сохранённые объявления.
- [ ] В хедере корректно отображается счётчик непрочитанных сообщений.
- [ ] Документы `api_modules.md` и `pages_modules_architecture.md` обновлены.
