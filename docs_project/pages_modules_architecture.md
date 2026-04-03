# Архитектура страниц и модулей (Market365)

Краткое описание потока запросов, маршрутов, связи страниц с **JSON API** и шаблонами. Актуальная «главная» версия этого документа; прежний **`modules_architecture.md`** можно считать черновиком — при расхождении ориентируйтесь на **этот файл**.

---

## 1. Общий поток запроса

1. **`.htaccess`** перенаправляет запросы (кроме `/public/`) в **`route.php`**.
2. **`route.php`** сначала собирает **`$MARKET365_CONFIG`**: **`array_merge`** из **`config.defaults.php`** (в репозитории) и при наличии файла **`config.php`** (локально/на сервере, в **`.gitignore`**). Затем поднимает сессию (`ssid`), заполняет **`$GLOBALS['WEB_JSON']`** (пути, **`api_json`** / **`email_json`** из конфига, `page_link`, настройки загрузок), подключает **`func/f_db.php`**, **`f_db_get.php`**, **`f_default.php`**, **`f_email_send.php`**. Для **`/api/dev/db_init`** (до разбора маршрутов) подключается **`func/f_db_init.php`** и выполняются миграции БД (текстовый ответ).
3. URI без ведущего слэша разбирается в **`uri_clean`** и **`uri_dir_arr`**.
4. Подбирается первая подходящая запись из **`$arr_json_route`** (regex → файл страницы).
   - При **`user_check === true`** и отсутствии входа — редирект на **`/`**.
   - Для маршрутов с **`user_check === false`** логика «уже залогинен» обрабатывается внутри конкретных страниц (часто редирект).
5. Если первый сегмент пути — **`api`**, подключается **`api.php`** (только JSON), обёртка **`template/page.php`** не используется.
6. Иначе контент страницы попадает в **`$WEB_PAGE_HTML`**, затем выводится через **`template/page.php`** (шапка, `nav_top`, контент, боковая реклама при `ads_side`, `footer`, **`template/script.php`**).

**Константы URL:** массив **`page_link`** в **`route.php`**.

### 1.1. Секреты и внешние сервисы (`config.defaults.php` / `config.php`)

| Файл | Роль |
|------|------|
| **`config.defaults.php`** | Обязательный шаблон в git: те же ключи, что у секретов, значения — пустые строки. Позволяет запускать приложение без локального **`config.php`** (без падения с 500). |
| **`config.php`** | Реальные ключи и пароли; **не коммитить** (запись в корневом **`.gitignore`**). Перекрывает соответствующие поля из defaults. |

Поля конфига маппятся в **`WEB_JSON['api_json']`**: Stripe (`stripe_public`, `stripe_secret`), reCAPTCHA v2/v3, Google OAuth (`google_oauth_*`). Почта **`WEB_JSON['email_json']['main']`**: `login` / `pass` из **`email_main_login`**, **`email_main_pass`**.

**Деплой:** после **`git clone` / `git pull`** на сервер нужно один раз положить **`config.php`** (скопировать с рабочей машины или собрать вручную). Иначе платежи, OAuth, reCAPTCHA и SMTP не заработают, хотя сайт отдаёт страницы.

**MySQL:** параметры подключения по-прежнему задаются в **`func/f_db.php`** внутри **`f_db_link()`**, не в **`config.php`**.

**GitHub:** секреты не держать в **`route.php`** и не коммитить **`config.php`** — иначе срабатывает push protection (secret scanning).

---

## 2. JSON API: обзор и использование на страницах

### 2.1. Как устроен вызов

- URL: **`/api/{method}/{query}`** (например `/api/ads/get_list`).
- Диспетчер: **`api.php`** → файл **`api/{method}.php`** → функция из **`$gl_api_func_json[$query]`**.
- С фронта: **`f_ajax(module, query, data_json, ...)`** в **`template/script.php`** (POST, `FormData`).

Подробно по каждому **`method`**, правам и таблицам БД: **`docs_project/api_modules.md`**.

### 2.2. Какая страница какой API пользуется

| Зона / файл страницы | Вызов API | Комментарий |
|----------------------|-----------|-------------|
| **`template/script.php`** (глобально на всех страницах с шаблоном) | **`POST /api/ads/get_list`** | Любой блок с атрибутом **`[ads_list_type]`** (сейчас в разметке **`page/ads_list.php`**): бесконечная подгрузка карточек через **`f_ads_item_list_scroll_load`**. |
| **`page/ads_create.php`** | **`POST /api/ads/save`**, **`POST /api/upload/file`** | Сохранение объявления и загрузка изображений с формы. |
| **`page/user/user_item.php`** | **`POST /api/user/save`** | Сохранение полей профиля с клиента. |
| **`page/user/user_settings.php`** | **`POST /api/user/save`** (`save_scope=password`), **`POST /api/upload/file`** | Смена пароля и загрузка аватара (`item_table=user`, `item_type=avatar`). |
| **`page/subscld_ription_list.php`** | **`POST /api/subscription/create`** | Массовое создание подписок (админ). |
| **`page/subscld_ription_list.php`** | **`$.ajax` на `/api/user?query=find`** | **Не соответствует** диспетчеру **`api.php`** (ожидается путь **`/api/user/find`** и POST-параметры). Требует правки URL или серверной части. |
| **`page/admin/translate_list.php`** | *нет проектного `/api/*`* | Сохранение: POST на **тот же URL страницы** (`type=save_all`), обновление БД через **`f_db_update_smart`**; ответ **`f_api_response_exit`**. |
| **`page/auth/login.php`** и прочие auth | *нет* | Вход и регистрация — обычный POST формы на страницу, reCAPTCHA, **`f_db_get_user`**, cookie. |
| **`page/info_item.php`** (about, terms, …) | *нет* | Только чтение таблицы **`info`**. |
| **`page/landing.php`**, **`page/ads_category.php`**, **`page/ads_item.php`**, **`page/user/*.php`** (кроме `user_item`) | *как правило нет* | Данные с сервера в PHP при рендере; **`user_pays_add`** использует **Stripe.js** и отдельный endpoint **`create_payment_intent.php`** (не модуль в **`api/`**). |

**Итог:** проектный REST-слой **`/api/...`** на страницах используется точечно; большая часть контента — **SSR в PHP** без AJAX.

---

## 3. Маршруты из `route.php` → страницы

Параметры **`user_check`**, **`ads_side`**, **`file_on`** берутся из **`$arr_json_route`**.

### Главная

| URL | Файл | Как работает |
|-----|------|----------------|
| **`''`** | `page/landing.php` | Поиск (`box_search`), категории, ссылки в каталог. **API:** только если позже добавят блоки **`ads_list_type`** на лендинг. |

### Авторизация (`page/auth/`)

| URL | Файл | Защита | Как работает |
|-----|------|--------|----------------|
| **`/login`** | `login.php` | — | Форма входа и регистрации (вкладки), reCAPTCHA v2, БД **`user`**, письма активации. **API:** нет. |
| **`/login/activation`** | `login_activation.php` | — | Активация аккаунта по ссылке из письма. **API:** нет. |
| **`/login/forgout-password`** | `login_forgout.php` | — | Восстановление пароля. **API:** нет. |
| **`/login/oauth/{service}`** | `login_oauth.php` | `f_user_check_exist_redirect` | Google OAuth → **`user`**, cookie. **API:** нет. |

### Кабинет (`page/user/`)

| URL | Файл | `user_check` | API с браузера |
|-----|------|--------------|----------------|
| **`/user`** | `user_item.php` | да | **`/api/user/save`** |
| **`/user/ads`** | `user_ads.php` | да | — |
| **`/user/pays`** | `user_pays.php` | да | — |
| **`/user/pays/add`** | `user_pays_add.php` | да | Stripe (внешний JS/API), не **`api/`** проекта |
| **`/user/favorites`** | `user_favorites.php` | да | — |
| **`/user/messages`** | `user_messages.php` | да | — |
| **`/user/notifications`** | `user_notifications.php` | да | — |
| **`/user/settings`** | `user_settings.php` | да | **`/api/user/save`**, **`/api/upload/file`** |
| **`/user/exit/...`** | `user_exit.php` | да | — |
| **`/user/set-auth`** | `user_set_auth.php` | — | Служебная установка cookie (осторожно) |

### Инфостраницы (about, rules, privacy, …)

| URL | Файл | API |
|-----|------|-----|
| **`/info`** | `info_list.php` | **Файл отсутствует** |
| **`/info/{slug}`** | `info_item.php` | нет (чтение **`info`**) |

### Объявления

| URL (шаблон) | Файл | `ads_side` | API с браузера |
|--------------|------|------------|----------------|
| **`/ads/list/...`** | `ads_list.php` | да | **`/api/ads/get_list`** (через общий **`script.php`**) |
| **`/sitemap`**, **`/ads`**, **`/ads/category/...`** | `ads_category.php` | — | — |
| **`/ads/create`** | `ads_create.php` | — | **`/api/ads/save`**, **`/api/upload/file`** |
| **`/ads/promote/...`** | `ads_promote.php` | — | в коде страницы вызовов **`f_ajax`** не найдено |
| **`/ads/...`** (карточка) | `ads_item.php` | да | — (карта: внешний Google Static Map в примере) |

### Служебное

| URL | Файл |
|-----|------|
| **`/api/dev/db_init`** | **`func/f_db_init.php`** (подключается из **`route.php`**, не через **`api.php`**) |
| **`/manifest.json`**, **`/robots.txt`**, **`/sw.js`** | `page_file/*.php` |
| **`/cookie-set`**, **`/redirect`** | `page/tools/*.php` |
| *не найдено* | `page/tools/404.php` |

### Админ / инструменты

| URL | Файл | API |
|-----|------|-----|
| **`/translate`** | `admin/translate_list.php` | Нет `/api/*`; POST на саму страницу |

---

## 4. Файлы в `page/` без маршрута

| Файл | Назначение |
|------|------------|
| **`sitemap.php`** | Карта сайта; маршрут закомментирован в пользу **`ads_category.php`**. |
| **`admin/user_list.php`** | Список пользователей (админ); маршрут закомментирован. |
| **`subscld_ription_list.php`** | Подписки; маршрута нет; использует **`/api/subscription/create`** и некорректный URL для user find. |
| **`file/file.php`** | Файлы; маршрут закомментирован. |

---

## 5. Зависимости слоёв

| Слой | Использует |
|------|------------|
| Страницы | **`f_db_*`**, **`f_db_get_*`**, **`f_user_*`**, **`f_template`**, **`f_page_*`**, **`f_translate`**, при необходимости клиентский **`f_ajax`**. |
| Шаблоны | Bootstrap, jQuery, **`template/script.php`**. |
| Почта | **`f_email_send`**, PHPMailer. |
| Схема БД | **`docs_project/schema_db.sql`**. |

---

## 6. Несоответствия маршрутов и файлов

1. **`page/info_list.php`** — нет при маршруте **`/info`**.

---

## 7. Связанные документы

| Документ | Содержание |
|----------|------------|
| **`docs_project/api_modules.md`** | Все модули **`api/*.php`**, query, таблицы, известные баги. |
| **`docs_project/php_functions_reference.md`** | Справочник PHP-функций **`func/`** и локальных в **`page/`**. |
| **`docs_project/custom_js_functions.md`** | Клиентские функции в **`template/script.php`** и др. |
| **`config.defaults.php`** (корень проекта) | Шаблон ключей API/почты для git; **`config.php`** — локальные значения, не в репозитории. |

---

## 8. Обновление документации

При добавлении страницы: маршрут в **`route.php`**, при AJAX — вызов **`/api/{method}/{query}`** и строка в разделе **2.2** этого файла.

При добавлении новых секретных ключей: объявить ключ в **`config.defaults.php`** (пустое значение), пробросить в **`route.php`** в нужный блок **`WEB_JSON`**, задать реальное значение только в локальном **`config.php`**.
