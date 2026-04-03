# Справочник PHP-функций проекта (Market365)

Описаны функции, объявленные в коде приложения: **`func/`**, **`api/`**, локальные в **`page/`**. **Не включено:** библиотека **`class/phpmailer/`** (сторонний код).

Соглашение имён: префикс **`f_`** — «своя» функция; **`f_db_*`** — работа с БД; **`f_api_*`** — обработчики JSON API.

### Загрузка конфигурации в `route.php`

| Элемент | Назначение |
|---------|------------|
| **`config.defaults.php`** | Возвращает массив ключей API/почты с пустыми значениями; файл в репозитории. |
| **`config.php`** | Опциональный локальный массив с реальными значениями; в **`.gitignore`**. |
| **`$MARKET365_CONFIG`** | **`array_merge(defaults, config.php или [])`** в начале **`route.php`**; далее поля попадают в **`WEB_JSON['api_json']`** и **`WEB_JSON['email_json']['main']`**. |

Поток запроса и деплой **`config.php`**: **`pages_modules_architecture.md`**, раздел **1.1**.

---

## `func/f_default.php` — вывод, страница, шаблоны

| Функция | Назначение |
|---------|------------|
| **`f_echo`**, **`f_echo_html`**, **`f_echo_encode`** | Вывод текста в поток (сырой / с `htmlspecialchars` / с unicode-escape). |
| **`f_translate_echo`**, **`f_translate`** | Перевод строки через таблицу **`translate`** и текущий язык из **`page_json`**. |
| **`f_html`** | Обёртка для неэкранированного HTML (осторожно с XSS). |
| **`f_html_head_title`**, **`f_page_title_set`** | Формирование `<title>` из `title`, `title_important`, `title_glue`. |
| **`f_page_currency`** | Возвращает строку валюты сайта. |
| **`f_page_breadcump`** | Рендер хлебных крошек по массиву ссылок. |
| **`f_page_library_add`** | Подключает CDN-скрипт/стиль из **`page_library`** (swiper, masonry, simplebar). |
| **`f_html_add`** | Добавляет фрагмент в **`page_json['html_head']`** и т.п. по ключу. |
| **`f_page_link`**, **`f_page_link_echo`** | URL по имени из **`page_link`** в `route.php`. |
| **`f_template`** | `include` шаблона из **`dir_template`**. |

---

## `func/f_default.php` — числа, даты, время

| Функция | Назначение |
|---------|------------|
| **`f_number_parse`**, **`f_number_beauty`**, **`f_parse_number_str`** | Нормализация/форматирование чисел из строк. |
| **`f_number_if_min_max`** | Ограничение числа диапазоном `[min, max]`. |
| **`f_number_space`** | Разделители тысяч в числе (строка для вывода). |
| **`f_number_word`**, **`f_number_word_string`** | Склонение / фраза с числом (массив форм слова). |
| **`f_datetime_current`** | Текущая дата-время в заданном формате. |
| **`f_datetime_beauty`**, **`f_date_beauty`** | Человекочитаемое представление даты/времени. |
| **`f_valid_date`**, **`f_date_validate`**, **`f_date_check`** | Проверка корректности дат. |
| **`f_diff_date_to_time`**, **`f_date_diff_seconds`**, **`f_date_left_time`**, **`f_date_left_time_1`**, **`f_date_diff_days`**, **`f_day_left`** | Разницы и «осталось времени» между датами. |
| **`f_check_diap_number`**, **`f_check_diap_time`** | Валидация диапазонов (сообщение об ошибке строкой). |
| **`f_phone_beauty`** | Форматирование телефона для отображения. |

---

## `func/f_default.php` — GPS, HTTP, формы

| Функция | Назначение |
|---------|------------|
| **`f_gps_validate`** | Разбор строки координат, опционально подстановка дефолта. |
| **`f_gps_distanse`** | Расстояние между двумя точками (формула гаверсинуса и др. в коде). |
| **`f_post_end`**, **`f_get_end`** | Завершение запроса редиректом POST/GET (паттерн PRG). |
| **`f_auth_http`** | Проверка Basic Auth из `$_SERVER`. |
| **`f_valid_type_id`** | Нормализация списка id (строка/массив) для полей типа JSON списка. |

---

## `func/f_default.php` — пользователь, сессия, cookie

| Функция | Назначение |
|---------|------------|
| **`f_user_get`** | Текущий пользователь из сессии/куки (массив полей или `false`). |
| **`f_user_auto`**, **`f_user_check`** | Автовосстановление сессии по cookie **`uid`**, проверка «залогинен». |
| **`f_user_request_ignored`** | Флаг «игнорировать запрос» (боты и т.д.). |
| **`f_user_gender`**, **`f_user_city`** | Метки пола и города для UI. Роль пользователя хранится в БД в колонке **`user.user_type`** (и дублируется в **`user.type`** для совместимости); в сессии **`f_user_auto`** выставляет **`type`** из **`user_type`**. |
| **`f_user_check_redirect`** | Редирект, если тип пользователя не совпал с ожидаемым. |
| **`f_user_exit`** | Выход: очистка сессии и cookie. |
| **`f_user_set_cookie`**, **`f_cookie_set`**, **`f_cookie_get`**, **`f_cookie_delete`** | Работа с cookie **`uid`** и произвольными именами. |
| **`f_user_check_exist_redirect`** | Редирект, если пользователь уже авторизован (для OAuth и т.п.). |

---

## `func/f_default.php` — DID (устройство/визит), reCAPTCHA, API ответ

| Функция | Назначение |
|---------|------------|
| **`f_did_request`**, **`f_did_generate`**, **`f_did_create`**, **`f_did_auto`** | Идентификатор посетителя/устройства в таблице **`did`**, привязка к сессии. |
| **`f_google_recaptcha`**, **`f_google_recaptcha_v2`** | Верификация токена v3 / v2 у Google. |
| **`f_api_response_exit`** | `json_encode` + `exit` для API и AJAX-ответов. |

---

## `func/f_default.php` — почта, валидация, файлы, редирект

| Функция | Назначение |
|---------|------------|
| **`f_email_validate`** | Проверка email фильтром PHP. |
| **`f_iin_parse_birth`**, **`f_iin_parse_gender`** | Разбор ИИН (КЗ). |
| **`f_file_gen_link`** | Публичный URL для файла в **`public/`**. |
| **`f_gen_password`** | Случайная строка для кодов активации и т.п. |
| **`f_redirect`** | `header('Location: ...')` + `exit`. |
| **`f_byte_format`** | Размер файла в KB/MB. |

---

## `func/f_default.php` — JSON, SEO, UI-хелперы

| Функция | Назначение |
|---------|------------|
| **`f_is_json`**, **`f_json_diff`** | Проверка JSON и сравнение двух структур. |
| **`f_referer_check`** | Проверка заголовка Referer на домен. |
| **`f_shuffle_seed`** | Перемешивание массива с фиксированным seed. |
| **`f_seo_text_to_url`** | Транслит/очистка для slug в URL. |
| **`f_html_pagination`** | Разметка пагинации. |
| **`f_html_checkbox`**, **`f_html_checkbox_echo`** | Чекбокс с меткой. |
| **`mb_trim`** | `trim` для многобайтовых строк. |
| **`f_arr_random_group`** | Случайная группировка элементов массива. |
| **`f_css_text_encode`**, **`f_chars_unique`**, **`f_chars_popular`** | Утилиты для работы с текстом/CSS. |
| **`f_html_obus_chars_init`**, **`f_html_obus_chars`**, **`f_html_obus_chars_style`** | Обфускация/подсветка символов в тексте (антипарсинг). |

---

## `func/f_default.php` — Google OAuth, curl, отладка

| Функция | Назначение |
|---------|------------|
| **`f_google_auth_create_auth_url`**, **`f_google_auth_get_access_token`**, **`f_google_auth_get_user_data`** | OAuth2 Google для входа. |
| **`f_curl`** | Универсальный HTTP POST/GET с JSON. |
| **`f_test`** | Отладочный вывод (`var_dump` / print). |

---

## `func/f_default.php` — unicode

| Функция | Назначение |
|---------|------------|
| **`f_unicode_encode_1`**, **`f_unicode_encode_2`**, **`f_unicode_encode`** | Кодирование строк в `\uXXXX` для JS/JSON-подобного вывода. |

---

## `func/f_db.php` — MySQLi

| Функция | Назначение |
|---------|------------|
| **`f_db_link`** | Singleton соединения MySQLi. |
| **`f_db_query`** | Выполнение произвольного SQL (без подготовленных выражений — внимание к инъекциям). |
| **`f_db_select`**, **`f_db_select_id`**, **`f_db_select_smart`**, **`f_db_select_get`**, **`f_db_select_count`** | SELECT с разной степенью «обёртки» над массивом условий. |
| **`f_db_update`**, **`f_db_update_smart`**, **`f_db_insert`**, **`f_db_delete_id`** | Мутации данных. |
| **`f_db_sql_column`**, **`f_db_sql_table`**, **`f_db_sql_string_escape`**, **`f_db_sql_where_if`**, **`f_db_sql_value_only`**, **`f_db_sql_value`**, **`f_db_value_str_date`** | Экранирование и подстановка значений в SQL. |
| **`f_num_conv`** | Перевод числа между системами счисления (произвольная база). |
| **`f_num_encode`**, **`f_num_decode`** | Кодирование числового id в короткую строку для URL (в файле **объявлены дважды** — действует **последнее** определение). |

---

## `func/f_db_init.php` — миграции БД

| Функция | Назначение |
|---------|------------|
| **`f_db_init`** | Создаёт **`_db_migrations`**, применяет зарегистрированные миграции (**`ALTER`/`UPDATE`**, таблицы **`chat`**, **`chat_message`**, **`user_favorite`** и т.д.). Вызывается по **`GET/POST /api/dev/db_init`** из **`route.php`**. |

---

## `func/f_db_get.php` — выборки предметной области

| Функция | Назначение |
|---------|------------|
| **`f_db_get_subscription_list`**, **`f_db_get_subscription_id`**, **`f_db_get_subscription_group_name`**, **`f_db_get_subscription_user_id`** | Подписки **`pdd_subscription`**. |
| **`f_db_get_test_update_expired`**, **`f_db_get_test_list`**, **`f_db_get_test_not_expired`**, **`f_db_get_test_trouble`** | Сущности «тестов» / сроков. |
| **`f_db_get_subscription_update_expired`** | Массовое обновление просроченных подписок. |
| **`f_db_get_translate`** | Строки перевода. |
| **`f_db_get_pay`**, **`f_db_get_user_list`**, **`f_db_get_user`** | Платежи/пользователи (списки и одна запись). |
| **`f_html_date_to_last_day`** | Вспомогательная дата для UI. |
| **`f_db_get_label_ads`** | Подписи/лейблы для объявлений. |
| **`f_db_get_list_ads`**, **`f_db_get_ads`** | Списки и одна карточка объявления (тяжёлые JOIN в SQL). |
| **`f_db_ads_category_descendant_ids`** | ID категории **`ads_category`** и всех потомков (по `parent_1_id` / `parent_2_id` / `parent_3_id` / `parent_id`). |
| **`f_db_ads_img_public_url`** | Публичный URL превью из путей **`ads_img`** (`jpg_path` / `webp_path`). |
| **`f_db_table_exists`** | Проверка наличия таблицы в текущей БД (кэш на запрос). |
| **`f_db_user_unread_chat_count`** | Количество непрочитанных входящих сообщений пользователя (**`chat`** / **`chat_message`**). |
| **`f_db_user_favorite_count`** | Количество записей в **`user_favorite`** для пользователя. |
| **`f_db_get_list_upload`**, **`f_db_get_upload`** | Метаданные загрузок. |

---

## `func/f_email_send.php`

| Функция | Назначение |
|---------|------------|
| **`f_email_send`** | Высокоуровневая отправка: выбор SMTP-профиля из **`email_json`**, вызов PHPMailer. |
| **`f_phpmailer_send`** | Низкоуровневая отправка через PHPMailer по массиву параметров. |

---

## `api/*.php` — обработчики API

См. детальный разбор эндпоинтов и зависимостей: **`docs_project/api_modules.md`**.

Кратко: **`f_api_get_list_ads`**, **`f_api_ads_save`**, **`f_api_ads_delete`**, **`f_api_get_ads`**, **`f_api_user_*`**, **`f_api_upload_file`**, **`f_upload_error`**, **`f_image_compress_save`**, **`f_file_category`**, **`f_api_pay_*`**, **`f_api_subscription_*`**, **`f_api_cron_expired`**, **`f_api_chat_*`**, **`f_api_favorite_*`**.

---

## Локальные функции в `page/`

| Файл | Функции | Назначение |
|------|---------|------------|
| **`ads_create.php`** | **`f_arr_child_tree`**, **`f_arr_child_tree_options`** | Рекурсивное дерево категорий и HTML `<option>` для select. |
| **`ads_category.php`**, **`sitemap.php`** | **`f_ads_category_tree`** (в `ads_category` — две перегрузки с разной логикой URL) | Рекурсивный вывод дерева категорий. |
| **`sitemap.php`** | **`f_ads_category_tree_arr`**, **`f_ads_category_tree_draw`** | Промежуточные структуры/отрисовка для карты сайта. |
| **`login_activation.php`** | **`f_html_email_activation`** | HTML тела письма активации. |
| **`login_forgout.php`** | **`f_html_email_forgout`** | HTML письма восстановления пароля. |
| **`user_pays_add.php`**, **`user_pays_add_intent.php`** | **`create_payment_intent`** | Серверный cURL к Stripe API (не JSON API проекта). |

---

## Примечания по качеству кода

- В **`api/pay.php`** и др. по-прежнему могут вызываться вспомогательные функции (**`f_pay_get`**, **`f_pay_type_ru`**, **`f_get_pdd_category_arr`** и др.), которых **нет** в текущем `func/` — при соответствующих запросах возможны **фатальные ошибки**.
- В **`f_db.php`** дублирование **`f_num_encode`/`f_num_decode`** — оставить одну реализацию после ревью.
- Для полного понимания полей БД смотрите **`schema.sql`**.

---

## Связанные документы

- **`docs_project/api_modules.md`** — модули `/api/...`.
- **`docs_project/pages_modules_architecture.md`** — страницы и вызовы API.
- **`docs_project/custom_js_functions.md`** — клиентский JS.
