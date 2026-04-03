# Кастомные функции JavaScript в проекте market365

JavaScript в проекте в основном встроен в PHP-шаблоны (нет отдельных `.js` модулей приложения). Ниже перечислены **именованные функции**, объявленные в коде проекта (не библиотеки: jQuery, Bootstrap, Leaflet, Select2, Stripe и т.д.).

---

## `template/script.php` (основной «ядро» фронтенда)

| Функция | Назначение |
|--------|------------|
| **`f_parse_int(num)`** | Парсит число через `parseFloat`; при `NaN` возвращает `0`. Используется для координат GPS и проверок. |
| **`f_form_get(jq_modal_form)`** | Собирает объект полей формы: обходит элементы с атрибутом `field_name`, для чекбоксов пишет `1`/`0`. |
| **`f_form_set(form_json, set_json)`** | По описанию формы (`form_json.name`, `control_arr`) находит контролы и подставляет значения из `set_json` по именам полей. |
| **`f_form_reset(form_json)`** | Сбрасывает форму к исходным подписям, значениям и подсказкам из `control_arr`; пропускает `tag === 'html'`. Вызывает **`f_form_m_value`**. |
| **`f_ads_item_list_scroll_load(jq_ads_list)`** | Инициализирует бесконечную подгрузку объявлений для контейнера с атрибутами `ads_list_type`, `ads_list_query`, `ads_list_category_id`. Внутри объявлена **`f_ads_items_load()`** — один запрос страницы к API `ads/get_list` через `f_ajax`, добавление строк через `f_ads_item_line_make`. Для `list_type === 'line'` вешает скролл на `window` и вызывает `f_scroll_list_loader`. |
| **`f_scroll_list_loader(jq_list, f_callback, before_px)`** | Если низ окна ближе чем `before_px` (по умолчанию 400) к низу списка — вызывает `f_callback` (типично подгрузка следующей страницы). |
| **`f_ads_item_line_make(json_item)`** | Клонирует шаблон `ads_item_line`, заполняет ссылку, картинку, заголовок, цену, город, дату, класс избранного; по **`html_promo`** (`top` / `vip`) — бейдж и рамка VIP (**`item_ad_vip`**). |
| **`f_ads_filter_param_get()`** | Читает фильтры из `.list_param_filter_box_split_list_ads`: `input[filter_type]`, чекбоксы, `select` с `filter_id`; возвращает вложенный объект для API/URL. |
| **`f_gl_show_filter_box_split_list_ads()`** | Переключает CSS-класс `show_filter_box_split_list_ads` у `.page` (панель фильтров в split-layout). |
| **`f_format_number(number)`** | Форматирует число с пробелами как разделителем тысяч (regex-группировка). |
| **`f_url_remove_hash()`** | Убирает hash из URL через `history.replaceState`, оставляя path и query. |
| **`f_back_page_link()`** | *Закомментирована.* Задумывалась для ссылки «назад» по `document.referrer` в пределах того же домена. |
| **`f_copy_text(text)`** | Копирует строку в буфер: временный `textarea`, `document.execCommand('copy')`. |
| **`f_template_page()`** | При загрузке DOM: все `[template]` вырезаются из DOM, их HTML сохраняется в глобальный объект `gl_template_json` по имени шаблона. |
| **`f_template_get(name)`** | Возвращает jQuery-объект клона HTML из `gl_template_json[name]`. |
| **`f_scroll_left_to_center(query_elem)`** | Горизонтально прокручивает родителя так, чтобы элемент оказался по центру видимой области (таблицы, чипы). |
| **`f_scroll_to_auto()`** | Для `.scroll_to_top` / `.scroll_to_left` прокручивает родителя к позиции элемента (вертикально/горизонтально). Вызывается сразу при парсинге скрипта. |
| **`f_url_query_to_json(query)`** | Парсит query-string (`?a=1&b=2`) в объект; декодирует URI, `+` → пробел. |
| **`f_url_json_to_query(json)`** | Собирает query-string; значения-объекты сериализует через `JSON.stringify`; пустые строки отбрасывает. |
| **`f_form_m_value(json_item, jq_input)`** | Заглушка для `f_form_reset`: возвращает `json_item` без изменений. |
| **`f_ajax(module, query, data_json, ...callbacks)`** | POST на `https://{host}/api/{module}/{query}` через `FormData`; объекты в FormData кладутся как JSON-строки; добавляет `cur_time` ISO. Колбэки: success, error, complete. |
| **`f_confirm_modal(config_json)`** | Модальное окно подтверждения на базе шаблона `alert_modal`: `title`, `icon` (класс Bootstrap Icons без префикса `bi-`), `body`, опционально одна кнопка `btn` + всегда «Отмена». |

Дополнительно в том же файле задаётся логика карты Leaflet в модалке GPS (глобальные переменные `gl_modal_gps_*`, обработчики без отдельных именованных функций, кроме использования `f_parse_int`).

---

## `template/nav_top.php`

| Функция | Назначение |
|--------|------------|
| **`f_hide_box_user_nav_top()`** | Внутри `DOMContentLoaded`: ставит таймер ~300 ms и сворачивает Bootstrap Collapse `#box_collapse_user_nav_top` (меню пользователя при уходе курсора). |

---

## `page/admin/translate_list.php`

Объявлены внутри обработчика загрузки страницы (локальная область видимости, не глобальный API сайта):

| Функция | Назначение |
|--------|------------|
| **`f_copy_text_column_1()`** | Собирает текст из ячеек `td[column='…']` в массив, склеивает через `\n--------\n`. |
| **`f_text_set_column(text, column)`** | Делит `text` по разделителю из дефисов, подставляет части в поля `[field_row='column']` по строкам таблицы. |

---

## `page/user/user_pays_add.php`

Оплата: **`f_ajax('pay', 'create_intent', { ads_id, service_type })`**, затем **`Stripe.js`** (`elements`, `confirmPayment`). Отдельного `create_payment_intent.php` нет.

---

## `page_file/sw.js.php`

Именованных пользовательских функций нет: только обработчики **`install`** / **`activate`** у Service Worker (`skipWaiting`, `clients.claim`), константа `CACHE_NAME`. Большой закомментированный блок — старая стратегия кэширования.

---

## Замечания по целостности кода

1. **`f_form_m_value`** — заглушка-идентичность в **`template/script.php`** (возвращает `json_item` без изменений); при необходимости расширить для полей с особыми типами.
2. **`f_back_page_link`** продублирована в комментарии; актуальная логика «назад» реализована ниже по файлу без этой функции (`history.back()` по условию referrer).

---

## Связанные документы

- **`docs_project/pages_modules_architecture.md`** — маршруты страниц и таблица вызовов **`f_ajax`** / `/api/...`.
- **`docs_project/api_modules.md`**, **`docs_project/php_functions_reference.md`**.

---

## Обновление документа

При добавлении новых `<script>` с именованными `function` в страницы имеет смысл дополнять этот файл и держать список синхронным с кодом.
