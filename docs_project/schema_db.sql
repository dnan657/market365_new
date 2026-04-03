-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 23 2026 г., 09:57
-- Версия сервера: 8.0.45-0ubuntu0.22.04.1
-- Версия PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `market`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ads`
--

CREATE TABLE `ads` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `project_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ads_category_id` bigint DEFAULT NULL,
  `ads_category_1_id` bigint DEFAULT NULL,
  `ads_category_2_id` bigint DEFAULT NULL,
  `ads_category_3_id` bigint DEFAULT NULL,
  `city_id` bigint DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_on` tinyint(1) NOT NULL DEFAULT '0',
  `publication_date` datetime DEFAULT NULL,
  `draft_on` tinyint(1) NOT NULL DEFAULT '1',
  `draft_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `moderate_on` tinyint(1) NOT NULL DEFAULT '0',
  `moderate_date` datetime DEFAULT NULL,
  `moderate_user_id` bigint DEFAULT NULL,
  `gps_point` point DEFAULT NULL,
  `price` float DEFAULT '0',
  `price_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_on` tinyint(1) NOT NULL DEFAULT '0',
  `delete_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
\

-- --------------------------------------------------------

--
-- Структура таблицы `ads_category`
--

CREATE TABLE `ads_category` (
  `_id` bigint NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `parent_1_id` bigint DEFAULT NULL,
  `parent_2_id` bigint DEFAULT NULL,
  `parent_3_id` bigint DEFAULT NULL,
  `parent_bro_id` bigint DEFAULT NULL,
  `parent_bro_1_id` bigint DEFAULT NULL,
  `parent_bro_2_id` bigint DEFAULT NULL,
  `hide_on` tinyint DEFAULT '0',
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_bg` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int DEFAULT '999999',
  `tmp_parent_1_domain` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmp_parent_2_domain` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmp_parent_3_domain` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `ads_img`
--

CREATE TABLE `ads_img` (
  `_id` bigint UNSIGNED NOT NULL,
  `_create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint UNSIGNED DEFAULT NULL,
  `_update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `_update_user_id` bigint UNSIGNED DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ads_id` bigint UNSIGNED DEFAULT NULL,
  `orig_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_filesize` int UNSIGNED DEFAULT NULL,
  `orig_width` smallint UNSIGNED DEFAULT NULL,
  `orig_height` smallint UNSIGNED DEFAULT NULL,
  `orig_mime` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_sha256` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_crc32` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_tiger` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` smallint UNSIGNED DEFAULT NULL,
  `height` smallint UNSIGNED DEFAULT NULL,
  `jpg_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jpg_filesize` int UNSIGNED DEFAULT NULL,
  `webp_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webp_filesize` int UNSIGNED DEFAULT NULL,
  `metadata` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ads_item_param_value`
--

CREATE TABLE `ads_item_param_value` (
  `_id` bigint NOT NULL,
  `ads_item_id` bigint DEFAULT NULL,
  `ads_param_key_id` bigint DEFAULT NULL,
  `value_str` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_int` double DEFAULT NULL,
  `ads_param_value_id` bigint DEFAULT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `_update_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_update_user_id` bigint DEFAULT NULL,
  `_delete_on` tinyint DEFAULT NULL,
  `_delete_date` datetime DEFAULT NULL,
  `_delete_user_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ads_param_category`
--

CREATE TABLE `ads_param_category` (
  `_id` bigint NOT NULL,
  `ads_category_1_domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ads_category_2_domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ads_category_3_domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ads_category_1_id` bigint DEFAULT NULL,
  `ads_category_2_id` bigint DEFAULT NULL,
  `ads_category_3_id` bigint DEFAULT NULL,
  `ads_category_id` bigint DEFAULT NULL,
  `ads_param_key_id` bigint DEFAULT NULL,
  `hide_on` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `ads_param_key`
--

CREATE TABLE `ads_param_key` (
  `_id` bigint NOT NULL,
  `domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_ru` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `important_on` tinyint(1) NOT NULL DEFAULT '0',
  `import_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_valid` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_min` float DEFAULT NULL,
  `form_max` float DEFAULT NULL,
  `ads_category_id` bigint DEFAULT NULL,
  `select_parent_id` bigint DEFAULT NULL,
  `sort` int DEFAULT '999999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ads_param_value`
--

CREATE TABLE `ads_param_value` (
  `_id` bigint NOT NULL,
  `ads_param_key_id` bigint DEFAULT NULL,
  `title_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `import_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int NOT NULL DEFAULT '999999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `city`
--

CREATE TABLE `city` (
  `_id` bigint NOT NULL,
  `title_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` bigint DEFAULT NULL,
  `country_id` bigint DEFAULT NULL,
  `gps_point` point DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `did`
--

CREATE TABLE `did` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT NULL,
  `_update_date` datetime DEFAULT NULL,
  `ip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang_orig` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash_sha256` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_date` datetime DEFAULT NULL,
  `test_json` json DEFAULT NULL,
  `activation_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_send_date` datetime DEFAULT NULL,
  `activation_expired_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `history_change`
--

CREATE TABLE `history_change` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `row_id` bigint DEFAULT NULL,
  `table` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_crc32` int DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `info`
--

CREATE TABLE `info` (
  `_id` bigint NOT NULL,
  `_craete_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `_update_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_update_user_id` bigint DEFAULT NULL,
  `_delete_date` datetime DEFAULT NULL,
  `_delete_user_id` bigint DEFAULT NULL,
  `_delete_on` tinyint(1) DEFAULT '0',
  `title_en` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_text_en` mediumtext COLLATE utf8mb4_unicode_ci,
  `body_html_en` mediumtext COLLATE utf8mb4_unicode_ci,
  `title_ru` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ru` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_text_ru` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `body_html_ru` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `uri` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_on` tinyint(1) DEFAULT '0',
  `publication_date` datetime DEFAULT NULL,
  `publication_user_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pay`
--

CREATE TABLE `pay` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `from_user_id` bigint DEFAULT NULL,
  `subscription_group_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` bigint DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` bigint DEFAULT NULL,
  `price` bigint DEFAULT NULL,
  `sum` bigint DEFAULT NULL,
  `comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pay_transaction`
--

CREATE TABLE `pay_transaction` (
  `_id` bigint NOT NULL,
  `customer_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` float(10,2) DEFAULT NULL,
  `item_price_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `paid_amount_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `project_id` bigint DEFAULT NULL,
  `unit_id` bigint DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `project`
--

CREATE TABLE `project` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `from_user_id` bigint DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `project_user`
--

CREATE TABLE `project_user` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `request`
--

CREATE TABLE `request` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `uri` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `did_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `get_json` json DEFAULT NULL,
  `post_json` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `translate`
--

CREATE TABLE `translate` (
  `_id` int NOT NULL,
  `_create_date` datetime DEFAULT NULL,
  `ready_on` tinyint(1) NOT NULL DEFAULT '0',
  `request_total` bigint NOT NULL DEFAULT '0',
  `request_last_date` datetime DEFAULT NULL,
  `ru_crc32` bigint DEFAULT NULL,
  `ru` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kz` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `type_group`
--

CREATE TABLE `type_group` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `type_item`
--

CREATE TABLE `type_item` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint DEFAULT NULL,
  `type_group_id` bigint NOT NULL,
  `is_child` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Структура таблицы `unit`
--

CREATE TABLE `unit` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `project_id` bigint DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `upload`
--

CREATE TABLE `upload` (
  `_id` bigint NOT NULL,
  `_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `_moderate_user_id` bigint DEFAULT NULL,
  `_moderate_on` tinyint(1) DEFAULT '0',
  `_moderate_date` datetime DEFAULT NULL,
  `_update_user_id` bigint DEFAULT NULL,
  `_update_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_delete_on` tinyint(1) DEFAULT '0',
  `_delete_date` datetime DEFAULT NULL,
  `_delete_user_id` bigint DEFAULT NULL,
  `_parent_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `item_table` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` bigint DEFAULT NULL,
  `upload_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `path_date` datetime DEFAULT NULL,
  `basename` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ext` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint DEFAULT '0',
  `size_format` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path_tmp` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_sha256` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_tiger` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_crc32b` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_crc32_int` bigint DEFAULT NULL,
  `img_width` int DEFAULT NULL,
  `img_height` int DEFAULT NULL,
  `img_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_width_compress` int DEFAULT NULL,
  `img_height_compress` int DEFAULT NULL,
  `img_quality_compress` int DEFAULT NULL,
  `img_px_size_compress` int DEFAULT NULL,
  `img_jpg_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_jpg_size` int UNSIGNED DEFAULT NULL,
  `img_webp_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_webp_size` int UNSIGNED DEFAULT NULL,
  `metadata_json` json DEFAULT NULL,
  `path_date_str` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path_dir` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path_file` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_thumb_width_compress` int DEFAULT NULL,
  `img_thumb_height_compress` int DEFAULT NULL,
  `img_thumb_quality_compress` int DEFAULT NULL,
  `img_thumb_px_size_compress` int DEFAULT NULL,
  `img_thumb_jpg_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_thumb_jpg_size` int UNSIGNED DEFAULT NULL,
  `img_thumb_webp_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_thumb_webp_size` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `_id` bigint NOT NULL,
  `_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `did_id` bigint DEFAULT NULL,
  `email` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ban_on` tinyint(1) DEFAULT '0',
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL COMMENT '0/NULL - неизвестно\r\n1 - мужчина\r\n2 - женщина',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday_date` date DEFAULT NULL,
  `admin_comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_type_id` bigint DEFAULT NULL,
  `password_hash_sha256` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `_create_did_id` bigint DEFAULT NULL,
  `google_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_login_first_date` datetime DEFAULT NULL,
  `visit_date` datetime DEFAULT NULL,
  `auth_date` datetime DEFAULT NULL,
  `activation_on` tinyint(1) NOT NULL DEFAULT '0',
  `activation_date` datetime DEFAULT NULL,
  `activation_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `activation_expired_date` datetime DEFAULT NULL,
  `activation_send_date` datetime DEFAULT NULL,
  `forgout_password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgout_password_hash_sha256` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgout_create_date` datetime DEFAULT NULL,
  `forgout_expired_date` datetime DEFAULT NULL,
  `forgout_send_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user_data`
--

CREATE TABLE `user_data` (
  `_id` bigint NOT NULL,
  `_create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `_create_user_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `type` int DEFAULT NULL,
  `data` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `title` (`title`),
  ADD KEY `phone` (`phone`),
  ADD KEY `address` (`address`),
  ADD KEY `description` (`description`),
  ADD KEY `publication_on` (`publication_on`),
  ADD KEY `publication_date` (`publication_date`),
  ADD KEY `price` (`price`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `category_type_id` (`ads_category_id`),
  ADD KEY `delete_on` (`delete_on`),
  ADD KEY `delete_date` (`delete_date`),
  ADD KEY `moderate_date` (`moderate_date`),
  ADD KEY `moderate_user_id` (`moderate_user_id`),
  ADD KEY `moderate_on` (`moderate_on`),
  ADD KEY `ads_category_1_id` (`ads_category_1_id`),
  ADD KEY `ads_category_2_id` (`ads_category_2_id`),
  ADD KEY `ads_category_3_id` (`ads_category_3_id`),
  ADD KEY `draft_on` (`draft_on`),
  ADD KEY `draft_date` (`draft_date`),
  ADD KEY `price_currency` (`price_currency`);

--
-- Индексы таблицы `ads_category`
--
ALTER TABLE `ads_category`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `parent_1_id` (`parent_1_id`),
  ADD KEY `parent_2_id` (`parent_2_id`),
  ADD KEY `parent_3_id` (`parent_3_id`),
  ADD KEY `parent_1_1_id` (`parent_bro_1_id`),
  ADD KEY `parent_2_1_id` (`parent_bro_2_id`),
  ADD KEY `hide_on` (`hide_on`),
  ADD KEY `level` (`level`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `sort` (`sort`),
  ADD KEY `parent_brother_id` (`parent_bro_id`),
  ADD KEY `tmp_parent_1_domain` (`tmp_parent_1_domain`),
  ADD KEY `tmp_parent_2_domain` (`tmp_parent_2_domain`),
  ADD KEY `tmp_parent_3_domain` (`tmp_parent_3_domain`);

--
-- Индексы таблицы `ads_img`
--
ALTER TABLE `ads_img`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `ads_id` (`ads_id`),
  ADD KEY `upload_date` (`upload_date`),
  ADD KEY `hash_sha256` (`hash_sha256`),
  ADD KEY `hash_crc32` (`hash_crc32`),
  ADD KEY `orig_filesize` (`orig_filesize`),
  ADD KEY `width` (`width`,`height`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_update_date` (`_update_date`),
  ADD KEY `_update_user_id` (`_update_user_id`);

--
-- Индексы таблицы `ads_item_param_value`
--
ALTER TABLE `ads_item_param_value`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `ads_item_id` (`ads_item_id`),
  ADD KEY `ads_param_key_id` (`ads_param_key_id`),
  ADD KEY `ads_param_value_id` (`ads_param_value_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `_update_date` (`_update_date`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_update_user_id` (`_update_user_id`),
  ADD KEY `_delete_date` (`_delete_date`),
  ADD KEY `_delete_on` (`_delete_on`),
  ADD KEY `_delete_user_id` (`_delete_user_id`);

--
-- Индексы таблицы `ads_param_category`
--
ALTER TABLE `ads_param_category`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `ads_category_1_domain` (`ads_category_1_domain`),
  ADD KEY `ads_category_2_domain` (`ads_category_2_domain`),
  ADD KEY `ads_category_3_domain` (`ads_category_3_domain`),
  ADD KEY `ads_category_1_id` (`ads_category_1_id`),
  ADD KEY `ads_category_2_id` (`ads_category_2_id`),
  ADD KEY `ads_category_3_id` (`ads_category_3_id`),
  ADD KEY `ads_category_id` (`ads_category_id`),
  ADD KEY `ads_param_key_id` (`ads_param_key_id`),
  ADD KEY `hide_on` (`hide_on`);

--
-- Индексы таблицы `ads_param_key`
--
ALTER TABLE `ads_param_key`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `domain` (`domain`),
  ADD KEY `ads_category_id` (`ads_category_id`),
  ADD KEY `important_on` (`important_on`),
  ADD KEY `import_id` (`import_id`),
  ADD KEY `priority` (`sort`),
  ADD KEY `select_parent_id` (`select_parent_id`);

--
-- Индексы таблицы `ads_param_value`
--
ALTER TABLE `ads_param_value`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `ads_param_key_id` (`ads_param_key_id`),
  ADD KEY `domain` (`domain`),
  ADD KEY `import_id` (`import_id`),
  ADD KEY `parent_domain` (`parent_domain`),
  ADD KEY `sort` (`sort`);

--
-- Индексы таблицы `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `title_en` (`title_en`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `domain` (`domain`);

--
-- Индексы таблицы `did`
--
ALTER TABLE `did`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `_update_date` (`_update_date`),
  ADD KEY `ip` (`ip`),
  ADD KEY `country` (`country`),
  ADD KEY `visit_date` (`visit_date`),
  ADD KEY `city` (`city`);

--
-- Индексы таблицы `history_change`
--
ALTER TABLE `history_change`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `row_id` (`row_id`),
  ADD KEY `table` (`table`),
  ADD KEY `table_crc32` (`table_crc32`),
  ADD KEY `type` (`type`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`);

--
-- Индексы таблицы `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `uri_2` (`uri`),
  ADD KEY `_craete_date` (`_craete_date`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_update_date` (`_update_date`),
  ADD KEY `_update_user_id` (`_update_user_id`),
  ADD KEY `title_ru` (`title_ru`),
  ADD KEY `description_ru` (`description_ru`),
  ADD KEY `publication_user_id` (`publication_user_id`),
  ADD KEY `_delete_user_id` (`_delete_user_id`),
  ADD KEY `_delete_date` (`_delete_date`),
  ADD KEY `uri` (`uri`),
  ADD KEY `title_en` (`title_en`),
  ADD KEY `description_en` (`description_en`);

--
-- Индексы таблицы `pay`
--
ALTER TABLE `pay`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `title` (`title`),
  ADD KEY `subscription_group_id` (`subscription_group_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Индексы таблицы `pay_transaction`
--
ALTER TABLE `pay_transaction`
  ADD PRIMARY KEY (`_id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `title` (`title`),
  ADD KEY `description` (`description`);

--
-- Индексы таблицы `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `title` (`title`),
  ADD KEY `domain` (`domain`);

--
-- Индексы таблицы `project_user`
--
ALTER TABLE `project_user`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`);

--
-- Индексы таблицы `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `did_id` (`did_id`);
ALTER TABLE `request` ADD FULLTEXT KEY `uri` (`uri`);

--
-- Индексы таблицы `translate`
--
ALTER TABLE `translate`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `ru_crc32` (`ru_crc32`),
  ADD KEY `ready_on` (`ready_on`),
  ADD KEY `request_last_date` (`request_last_date`);

--
-- Индексы таблицы `type_group`
--
ALTER TABLE `type_group`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `create_date` (`_create_date`),
  ADD KEY `title` (`title`),
  ADD KEY `name` (`name`),
  ADD KEY `category` (`category`);

--
-- Индексы таблицы `type_item`
--
ALTER TABLE `type_item`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `create_date` (`_create_date`),
  ADD KEY `title` (`title`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `type_group_id` (`type_group_id`),
  ADD KEY `is_child` (`is_child`),
  ADD KEY `value` (`value`);

--
-- Индексы таблицы `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `title` (`title`);

--
-- Индексы таблицы `upload`
--
ALTER TABLE `upload`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_moderate_user_id` (`_moderate_user_id`),
  ADD KEY `_moderate_on` (`_moderate_on`),
  ADD KEY `_moderate_date` (`_moderate_date`),
  ADD KEY `_update_user_id` (`_update_user_id`),
  ADD KEY `_update_date` (`_update_date`),
  ADD KEY `_delete_on` (`_delete_on`),
  ADD KEY `_delete_date` (`_delete_date`),
  ADD KEY `_delete_user_id` (`_delete_user_id`),
  ADD KEY `_parent_id` (`_parent_id`),
  ADD KEY `path_date` (`path_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_table` (`item_table`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `upload_date` (`upload_date`),
  ADD KEY `mime` (`mime`),
  ADD KEY `hash_sha256` (`hash_sha256`),
  ADD KEY `hash_tiger` (`hash_tiger`),
  ADD KEY `hash_crc32_int` (`hash_crc32_int`),
  ADD KEY `hash_crc32b` (`hash_crc32b`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `_create_user` (`_create_user_id`),
  ADD KEY `name` (`name`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `on_ban` (`ban_on`),
  ADD KEY `gender` (`gender`),
  ADD KEY `activation_on` (`activation_on`),
  ADD KEY `_create_did_id` (`_create_did_id`),
  ADD KEY `did_id` (`did_id`),
  ADD KEY `visit_date` (`visit_date`),
  ADD KEY `forgout_create_date` (`forgout_create_date`),
  ADD KEY `type` (`type`),
  ADD KEY `login` (`login`),
  ADD KEY `auth_date` (`auth_date`),
  ADD KEY `google_id` (`google_id`),
  ADD KEY `google_login_first_date` (`google_login_first_date`),
  ADD KEY `city_type_id` (`city_type_id`),
  ADD KEY `phone` (`phone`) USING BTREE;

--
-- Индексы таблицы `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `_create_user_id` (`_create_user_id`),
  ADD KEY `_create_date` (`_create_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `data` (`data`);
