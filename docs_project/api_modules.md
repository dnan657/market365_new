# Модули JSON API (`/api/{method}/{query}`)

Диспетчер: **`api.php`**. Сегменты URI: `uri_dir_arr[1]` = **method** (имя файла без `.php` в `api/`), `uri_dir_arr[2]` = **query** (ключ в `$gl_api_func_json`). Ответ: JSON с **`data`** и **`metadata`**; ошибки часто в **`data.error`**.

Дополнительно: если в запросе передан **`recaptcha_response`**, перед вызовом обработчика выполняется **`f_google_recaptcha()`**; при провале — ответ с ошибкой о боте.

Подробнее про формат запроса с фронта: **`f_ajax()`** в `template/script.php` (POST, `FormData`, URL `https://{host}/api/{method}/{query}`).

---

## Общая таблица модулей

| Файл | Query → обработчик | Назначение |
|------|-------------------|------------|
| **`api/ads.php`** | `get_list` → `f_api_get_list_ads` | Сейчас возвращает **заглушечные** повторяющиеся карточки (цикл из шаблона), не реальная БД. |
| | `save` → `f_api_ads_save` | Создание/обновление объявления в таблице **`ads`**, проверки полей, GPS, права (в коде есть противоречивые проверки `type == 'user'`). |
| | `delete` → `f_api_ads_delete` | Мягкое удаление (`delete_on`), проверка владельца; в условии используется **`$is_admin`** без явного определения — риск предупреждений PHP. |
| | `get` → строка **`f_api_get_ads`** | В массиве указано имя **`f_api_get_ads`**, а реализована функция **`f_api_ads_get`** — вызов **`/api/ads/get`** с высокой вероятностью **сломан**. |
| **`api/user.php`** | `find` → `f_api_user_find` | Поиск в таблице **`user`**; доступ только **`admin`**; в ответе **`type_str`** — значение **`user_type`** (или **`type`**). |
| | `save` → `f_api_user_save` | Обновление **`user`**; для не-админа при **`save_scope=password`** — только смена пароля (проверка **`password_old`**). Админ может менять **`user_type`** (синхронизируется с **`type`**). |
| | `login_create` / `login_edit` | Создание/правка записей в **`user`** (вспомогательные учётные записи с логином **`@...`**). |
| **`api/upload.php`** | `file` → `f_api_upload_file` | Приём **`$_FILES`**, валидация по **`WEB_JSON['upload_json']`**, запись в **`upload`**, сжатие изображений (**`f_image_compress_save`**). Для **`item_table=user`** разрешена загрузка только для **`item_id`** текущего пользователя. |
| **`api/pay.php`** | `find` / `save` | Работа с сущностью **`pay`**; в **`find`** вызывается **`f_pay_get()`** — в проекте **нет определения** функции (код, вероятно, из другой ветки). Используются также **`f_pay_type_ru`**, **`f_pay_city_ru`**. |
| **`api/subscription.php`** | `edit` → `f_api_subscription_edit` | Обновление **`pdd_subscription`**: ветки для **admin** и **school** (активация, отмена, поля цен/дат). Вызывается **`f_get_pdd_category_arr()`** — в репозитории **не найдена**. |
| | `create` → `f_api_subscription_create` | Только **admin**: пакетная вставка подписок, проверка **`ssid` === session_id()**, запись в **`pdd_pay`**. |
| **`api/cron.php`** | `expired` → `f_api_cron_expired` | Вызов **`f_db_get_test_update_expired()`** и **`f_db_get_subscription_update_expired()`** — обновление просроченных сущностей. Предполагается вызов по cron (в комментарии пример URL). |

---

## Детали по файлам

### `api/ads.php`

- **Использует:** `f_db_get_ads`, `f_db_update_smart`, `f_db_insert` (закомментирован сценарий редиректа), `f_user_get`, `f_gps_validate`, `f_valid_type_id`, `f_number_if_min_max`, `f_number_parse`, `f_datetime_current`, `f_db_value_str_date`, `f_seo_text_to_url`, `f_num_encode`, `f_page_link`, `f_number_space`.
- **`get_list`:** отдаёт массив **`arr_item`** из одного шаблона (для вёрстки ленты на `ads_list`).
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

---

## Связанные документы

- Какие страницы дергают API: **`docs_project/pages_modules_architecture.md`** (раздел про API).
- Справочник PHP-функций: **`docs_project/php_functions_reference.md`**.
