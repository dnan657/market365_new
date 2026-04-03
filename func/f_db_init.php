<?php

/**
 * Применяет зарегистрированные миграции БД (таблица _db_migrations).
 * Вызывается из route.php по GET /api/dev/db_init
 */
function f_db_init() {
	$sql_setup = "
		CREATE TABLE IF NOT EXISTS `_db_migrations` (
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`migration_name` VARCHAR(255) NOT NULL UNIQUE,
			`executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	";
	f_db_query($sql_setup);

	$migrations = [
		'001_user_add_user_type_nullable' => "
			ALTER TABLE `user`
			ADD COLUMN IF NOT EXISTS `user_type` VARCHAR(50) NULL;
		",
		'002_user_backfill_user_type' => "
			UPDATE `user` SET `user_type` = `type` WHERE `user_type` IS NULL OR TRIM(`user_type`) = '';
		",
		'003_user_user_type_not_null' => "
			ALTER TABLE `user`
			MODIFY COLUMN `user_type` VARCHAR(50) NOT NULL DEFAULT 'user';
		",
		'004_user_phone_verified_accepted_terms' => "
			ALTER TABLE `user`
			ADD COLUMN IF NOT EXISTS `phone_verified` TINYINT(1) NOT NULL DEFAULT 0,
			ADD COLUMN IF NOT EXISTS `accepted_terms` TINYINT(1) NOT NULL DEFAULT 0;
		",
		'005_chat_table' => "
			CREATE TABLE IF NOT EXISTS `chat` (
				`_id` BIGINT NOT NULL AUTO_INCREMENT,
				`_create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`ads_id` BIGINT NOT NULL,
				`user_buyer_id` BIGINT NOT NULL,
				`user_seller_id` BIGINT NOT NULL,
				PRIMARY KEY (`_id`),
				UNIQUE KEY `ux_chat_ads_buyer_seller` (`ads_id`, `user_buyer_id`, `user_seller_id`),
				KEY `idx_chat_buyer` (`user_buyer_id`),
				KEY `idx_chat_seller` (`user_seller_id`),
				KEY `idx_chat_ads` (`ads_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		",
		'006_chat_message_table' => "
			CREATE TABLE IF NOT EXISTS `chat_message` (
				`_id` BIGINT NOT NULL AUTO_INCREMENT,
				`_create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`chat_id` BIGINT NOT NULL,
				`user_sender_id` BIGINT NOT NULL,
				`message_text` TEXT NOT NULL,
				`is_read` TINYINT(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (`_id`),
				KEY `idx_cm_chat` (`chat_id`),
				KEY `idx_cm_sender` (`user_sender_id`),
				KEY `idx_cm_chat_read` (`chat_id`, `is_read`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		",
		'007_user_favorite_table' => "
			CREATE TABLE IF NOT EXISTS `user_favorite` (
				`_id` BIGINT NOT NULL AUTO_INCREMENT,
				`_create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`user_id` BIGINT NOT NULL,
				`ads_id` BIGINT NOT NULL,
				PRIMARY KEY (`_id`),
				UNIQUE KEY `ux_fav_user_ads` (`user_id`, `ads_id`),
				KEY `idx_fav_ads` (`ads_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		",
		'008_ads_promote_columns' => "
			ALTER TABLE `ads`
			ADD COLUMN IF NOT EXISTS `is_top_until` DATETIME NULL DEFAULT NULL,
			ADD COLUMN IF NOT EXISTS `is_vip_until` DATETIME NULL DEFAULT NULL,
			ADD COLUMN IF NOT EXISTS `store_id` BIGINT NULL DEFAULT NULL,
			ADD KEY `idx_ads_top_until` (`is_top_until`);
		",
		'009_pay_transaction_stripe' => "
			ALTER TABLE `pay_transaction`
			ADD COLUMN IF NOT EXISTS `user_id` BIGINT NULL DEFAULT NULL,
			ADD COLUMN IF NOT EXISTS `ads_id` BIGINT NULL DEFAULT NULL,
			ADD COLUMN IF NOT EXISTS `stripe_intent_id` VARCHAR(100) NULL DEFAULT NULL,
			ADD COLUMN IF NOT EXISTS `service_type` VARCHAR(50) NULL DEFAULT NULL,
			ADD KEY `idx_pt_stripe_intent` (`stripe_intent_id`);
		",
		'010_store_table' => "
			CREATE TABLE IF NOT EXISTS `store` (
				`_id` BIGINT NOT NULL AUTO_INCREMENT,
				`_create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`user_id` BIGINT NOT NULL,
				`name` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
				`slug` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
				`description` TEXT COLLATE utf8mb4_unicode_ci NULL,
				`logo_upload_id` BIGINT NULL DEFAULT NULL,
				`banner_upload_id` BIGINT NULL DEFAULT NULL,
				`phone` VARCHAR(30) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
				`address` VARCHAR(300) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
				`city_id` BIGINT NULL DEFAULT NULL,
				PRIMARY KEY (`_id`),
				UNIQUE KEY `ux_store_slug` (`slug`),
				KEY `idx_store_user` (`user_id`),
				KEY `idx_store_city` (`city_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		",
	];

	// Примечание: если миграция 008/009 уже частично применена вручную и падает на ADD KEY,
	// удалите проблемный ключ в БД или закомментируйте соответствующую строку в миграции.

	$applied = 0;
	foreach ($migrations as $name => $sql) {
		$exists = f_db_select("SELECT `id` FROM `_db_migrations` WHERE `migration_name` = " . f_db_sql_value($name) . " LIMIT 1");
		if (count($exists) > 0) {
			continue;
		}
		f_db_query(trim($sql));
		f_db_insert('_db_migrations', ['migration_name' => $name]);
		$applied++;
	}

	return "OK. Applied migrations: " . $applied;
}
