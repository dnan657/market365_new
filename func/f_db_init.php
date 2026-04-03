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
	];

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
