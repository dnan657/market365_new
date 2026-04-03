# Модули JSON API (`/api/{method}/{query}`)

Диспетчер: **`api.php`**. Сегменты URI: `uri_dir_arr[1]` = **method** (имя файла без `.php` в `api/`), `uri_dir_arr[2]` = **query** (ключ в `$gl_api_func_json`). Ответ: JSON с **`data`** и **`metadata`**; ошибки часто в **`data.error`**.

Дополнительно: если в запросе передан **`recaptcha_response`**, перед вызовом обработчика выполняется **`f_google_recaptcha()`**; при провале — ответ с ошибкой о боте.

Подробнее про формат запроса с фронта: **`f_ajax()`** в `template/script.php` (POST, `FormData`, URL `https://{host}/api/{method}/{query}`).

Ключи reCAPTCHA и прочие параметры внешних сервисов для проверок на стороне API читаются из **`$GLOBALS['WEB_JSON']['api_json']`**, который наполняется из **`config.defaults.php`** + **`config.php`** при старте **`route.php`** (см. **`pages_modules_architecture.md`**, § **1.1**).

---

## Общая таблица модулей

| Файл | Query → обработчик | Назначение |
|------|-------------------|------------|
| **`api/ads.php`** | `get_list` → `f_api_get_list_ads` | Реальная выборка из **`ads`**: **`delete_on=0`**, **`publication_on=1`**, JOIN превью из **`ads_img`** (первое по `_id`), **`city`**, пагинация **`page_num`** / **`page_size`**, сортировка **`sort`** (`newest` / `price_asc` / `price_desc`), при наличии колонки **`is_top`** — приоритет сверху. Фильтры: **`category_id`** (дерево **`ads_category`** через **`f_db_ads_category_descendant_ids`**), **`ads_search_title`**, **`ads_search_city_id`**, динамика из **`json_url_query`** по **`ads_item_param_value`**. |
| | `save` → `f_api_ads_save` | Создание/обновление объявления в **`ads`**; для новой записи вызывается **`f_db_insert`**, в ответе **`_id_str`**, **`redirect`**. |
| | `delete` → `f_api_ads_delete` | Мягкое удаление (`delete_on`); доступ у владельца или **admin**. |
| | `get` → **`f_api_get_ads`** | Одна карточка по **`_id_str`** через **`f_db_get_ads`**. |
| **`api/user.php`** | `find` → `f_api_user_find` | Поиск в таблице **`user`**; доступ только **`admin`**; в ответе **`type_str`** — значение **`user_type`** (или **`type`**). |
| | `save` → `f_api_user_save` | Обновление **`user`**; для не-админа при **`save_scope=password`** — только смена пароля (проверка **`password_old`**). Админ может менять **`user_type`** (синхронизируется с **`type`**). |
| | `login_create` / `login_edit` | Создание/правка записей в **`user`** (вспомогательные учётные записи с логином **`@...`**). |
| **`api/upload.php`** | `file` → `f_api_upload_file` | Приём **`$_FILES`**, валидация по **`WEB_JSON['upload_json']`**, запись в **`upload`**, сжатие изображений (**`f_image_compress_save`**). Для **`item_table=user`** разрешена загрузка только для **`item_id`** текущего пользователя. |
| **`api/pay.php`** | `find` / `save` | Работа с сущностью **`pay`**; в **`find`** вызывается **`f_pay_get()`** — в проекте **нет определения** функции (код, вероятно, из другой ветки). Используются также **`f_pay_type_ru`**, **`f_pay_city_ru`**. |
| **`api/subscription.php`** | `edit` → `f_api_subscription_edit` | Обновление **`pdd_subscription`**: ветки для **admin** и **school** (активация, отмена, поля цен/дат). Вызывается **`f_get_pdd_category_arr()`** — в репозитории **не найдена**. |
| | `create` → `f_api_subscription_create` | Только **admin**: пакетная вставка подписок, проверка **`ssid` === session_id()**, запись в **`pdd_pay`**. |
| **`api/cron.php`** | `expired` → `f_api_cron_expired` | Вызов **`f_db_get_test_update_expired()`** и **`f_db_get_subscription_update_expired()`** — обновление просроченных сущностей. Предполагается вызов по cron (в комментарии пример URL). |
| **`api/chat.php`** | `get_list` → `f_api_chat_get_list` | Список диалогов текущего пользователя (**`chat`** + **`ads`** + превью **`ads_img`**, последнее сообщение, **`unread_count`**). Только авторизованный пользователь. |
| | `get_messages` → `f_api_chat_get_messages` | История **`chat_message`** по **`chat_id`**; проверка участия в чате; пометка входящих как **`is_read = 1`**. В **`data.chat`** — карточка объявления и ссылка. |
| | `send` → `f_api_chat_send` | Отправка: либо **`chat_id`**, либо первое сообщение по **`ads_id`** (создание **`chat`**). Уведомление второй стороне через **`f_email_send`** (**`email_json['main']`**). |
| | `unread_count` → `f_api_chat_unread_count` | Число непрочитанных входящих сообщений (для бейджа в шапке). |
| **`api/favorite.php`** | `toggle` → `f_api_favorite_toggle` | Добавить/удалить запись в **`user_favorite`** по паре текущий пользователь + **`ads_id`**. |
| | `get_list` → `f_api_favorite_get_list` | Список избранных объявлений в формате карточки (**`arr_item`** как у **`ads/get_list`**). |

---

## Детали по файлам

### `api/ads.php`

- **Использует:** `f_db_get_ads`, `f_db_update_smart`, `f_db_insert` (закомментирован сценарий редиректа), `f_user_get`, `f_gps_validate`, `f_valid_type_id`, `f_number_if_min_max`, `f_number_parse`, `f_datetime_current`, `f_db_value_str_date`, `f_seo_text_to_url`, `f_num_encode`, `f_page_link`, `f_number_space`.
- **`get_list`:** отдаёт **`arr_item`** для **`f_ads_item_line_make`** (`html_img_src`, `title`, `html_price`, `html_city`, `html_date`, `html_favorite_on`, `html_link_ad`), плюс **`count_total`**, **`has_more`**, **`page_num`**, **`page_size`**.
- **`save`:** длинный набор полей под смешанную модель (объявление + учебные поля вроде `lang_edu_type_arr_id`).

### `api/user.php`

- **Использует:** `f_db_select`, `f_db_get_user`, `f_db_insert`, `f_db_update_smart`, `f_num_encode` / `f_num_decode`, `f_user_get`, `f_phone_beauty`, `f_date_beauty`, `f_datetime_beauty`, `f_api_user_effective_type`, и т.д.
- Таблица **`user`**, колонка роли **`user_type`** (миграции — **`func/f_db_init.php`** / **`/api/dev/db_init`**).

### `api/upload.php`

- **Использует:** лимиты `ini_set` для больших загрузок, `f_user_get`, `f_db_sql_table`, `f_num_decode`, `f_upload_error`, `f_image_compress_save`, `f_file_category`, `f_db_insert`, правила из **`$GLOBALS['WEB_JSON']['upload_json']`**.
- Вспомогательные функции в том же файле: **`f_upload_error`**, **`f_image_compress_save`**, **`f_file_category`**.

### `api/pay.php`

- Задуман как аналог user API для сущности платежей/клиентов **`pay`**.
- Перед использованием в продакшене нужно восстановить **`f_pay_get`** и вспомогательные **`f_pay_*_ru`**, **`f_list_city`** (часть уже используется в **`save`**).

### `api/subscription.php`

- **Таблицы:** `pdd_subscription`, `pdd_pay`, `user`.
- **Права:** жёстко завязаны на `f_user_get()['type']` (`admin`, `school`).
- Зависимость **`f_get_pdd_category_arr()`** должна быть реализована или заменена.

### `api/cron.php`

- Без явной авторизации в файле: любой, кто знает URL, может дернуть **`expired`** (стоит защитить секретом или IP на уровне веб-сервера).

### `api/chat.php`

- **Таблицы:** `chat`, `chat_message`, `ads`, `ads_img`, `user`.
- **Почта:** `f_email_send` при каждой отправке сообщения — письмо адресату диалога (вторая сторона чата).
- **Миграции:** `func/f_db_init.php` (миграции `005_chat_table`, `006_chat_message_table`).

### `api/favorite.php`

- **Таблицы:** `user_favorite`, `ads`, `ads_img`, `city`.
- **Миграции:** `007_user_favorite_table` в **`f_db_init.php`**.

---

## Связанные документы

- Какие страницы дергают API: **`docs_project/pages_modules_architecture.md`** (раздел про API).
- Справочник PHP-функций: **`docs_project/php_functions_reference.md`**.
