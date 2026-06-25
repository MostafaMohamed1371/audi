<?php
/**
 * Converts Laravel SQLite schema dump to MySQL 8 DDL.
 * Run from repo root:
 *   php backend/database/schema/generate-mysql-schema.php
 *
 * Prerequisite (refresh SQLite dump):
 *   cd backend && php artisan migrate:fresh --force
 *   php artisan schema:dump --path=database/schema/audi_sqlite_schema.sql
 */

declare(strict_types=1);

$baseDir = dirname(__DIR__, 2);
$input = $baseDir . '/database/schema/audi_sqlite_schema.sql';
$output = $baseDir . '/database/schema/audi_mysql_schema.sql';

if (! is_file($input)) {
    fwrite(STDERR, "Missing {$input}. Run schema:dump first.\n");
    exit(1);
}

$sql = file_get_contents($input);
if ($sql === false) {
    exit(1);
}

// Strip migration seed rows from sqlite dump — we re-add them for MySQL below.
$sql = preg_replace('/^INSERT INTO migrations.*$/m', '', $sql);

$replacements = [
    '/CREATE TABLE IF NOT EXISTS "migrations"/' => 'CREATE TABLE IF NOT EXISTS `migrations`',
    '/"([^"]+)"/' => '`$1`',
    '/integer primary key autoincrement not null/i' => 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
    '/\binteger not null\b/i' => 'BIGINT UNSIGNED NOT NULL',
    '/\binteger,\s*$/m' => 'BIGINT UNSIGNED,',
    '/\binteger\)/' => 'BIGINT UNSIGNED)',
    '/\binteger not null default/i' => 'BIGINT NOT NULL DEFAULT',
    '/\binteger\)/i' => 'INT)',
    '/\bvarchar not null\b/i' => 'VARCHAR(255) NOT NULL',
    '/\bvarchar,\s*$/m' => 'VARCHAR(255),',
    '/\bvarchar\)/' => 'VARCHAR(255))',
    '/\bvarchar not null default/i' => 'VARCHAR(255) NOT NULL DEFAULT',
    '/\btext not null\b/i' => 'JSON NOT NULL',
    '/\btext,\s*$/m' => 'JSON,',
    '/\btext\)/' => 'JSON)',
    '/\btext not null default/i' => 'TEXT NOT NULL DEFAULT',
    '/\bdatetime,\s*$/m' => 'TIMESTAMP NULL,',
    '/\bdatetime\)/' => 'TIMESTAMP NULL)',
    '/\bdate,\s*$/m' => 'DATE NULL,',
    '/\bdate\)/' => 'DATE NULL)',
    '/\bnumeric not null\b/i' => 'DECIMAL(9,6) NOT NULL',
    '/\btinyint\(1\) not null default \'1\'/i' => 'TINYINT(1) NOT NULL DEFAULT 1',
    '/\btinyint\(1\) not null default \'0\'/i' => 'TINYINT(1) NOT NULL DEFAULT 0',
    '/foreign key\(`([^`]+)`\) references `([^`]+)`\(`([^`]+)`\) on delete set null/i' => 'CONSTRAINT `fk_$1` FOREIGN KEY (`$1`) REFERENCES `$2` (`$3`) ON DELETE SET NULL',
    '/foreign key\(`([^`]+)`\) references `([^`]+)`\(`([^`]+)`\) on delete cascade/i' => 'CONSTRAINT `fk_$1` FOREIGN KEY (`$1`) REFERENCES `$2` (`$3`) ON DELETE CASCADE',
    '/foreign key\(`([^`]+)`\) references `([^`]+)`\(`([^`]+)`\) on update cascade/i' => 'CONSTRAINT `fk_$1` FOREIGN KEY (`$1`) REFERENCES `$2` (`$3`) ON UPDATE CASCADE',
];

foreach ($replacements as $pattern => $replacement) {
    $sql = preg_replace($pattern, $replacement, $sql);
}

// Fix JSON columns that should stay TEXT
$textColumns = [
    'message', 'user_agent', 'info_ar', 'info_en', 'description_ar', 'description_en',
    'quote_ar', 'quote_en', 'bio_ar', 'bio_en', 'hero_intro_ar', 'hero_intro_en',
    'intro_ar', 'intro_en', 'content_ar', 'content_en', 'text_ar', 'text_en',
    'highlight_ar', 'highlight_en', 'value_ar', 'value_en', 'payload', 'exception',
    'geojson', 'intro_title_ar', 'intro_title_en', 'intro_subtitle_ar', 'intro_subtitle_en',
    'answer_ar', 'answer_en', 'summary_ar', 'summary_en', 'cover_letter',
];
foreach ($textColumns as $col) {
    $sql = preg_replace('/`' . preg_quote($col, '/') . '` JSON/', "`{$col}` TEXT", $sql);
}

// members table fields that were wrongly converted
$sql = str_replace('`organization_name` JSON NOT NULL', '`organization_name` VARCHAR(255) NOT NULL', $sql);
$sql = str_replace('`contact_name` JSON NOT NULL', '`contact_name` VARCHAR(255) NOT NULL', $sql);
$sql = str_replace('`name` JSON NOT NULL', '`name` VARCHAR(255) NOT NULL', $sql);
$sql = str_replace('`payload` TEXT NOT NULL', '`payload` JSON NOT NULL', $sql);
$sql = str_replace('`body_ar` TEXT NOT NULL', '`body_ar` JSON NOT NULL', $sql);
$sql = str_replace('`body_en` TEXT NOT NULL', '`body_en` JSON NOT NULL', $sql);
$sql = str_replace('`paragraphs_ar` TEXT NOT NULL', '`paragraphs_ar` JSON NOT NULL', $sql);
$sql = str_replace('`paragraphs_en` TEXT NOT NULL', '`paragraphs_en` JSON NOT NULL', $sql);
$sql = str_replace('`tags_ar` TEXT NOT NULL', '`tags_ar` JSON NOT NULL', $sql);
$sql = str_replace('`tags_en` TEXT NOT NULL', '`tags_en` JSON NOT NULL', $sql);
$sql = str_replace('`columns_ar` JSON,', '`columns_ar` JSON NULL,', $sql);
$sql = str_replace('`columns_en` JSON,', '`columns_en` JSON NULL,', $sql);
$sql = str_replace('`authors_ar` JSON,', '`authors_ar` JSON NULL,', $sql);
$sql = str_replace('`authors_en` JSON,', '`authors_en` JSON NULL,', $sql);
$sql = str_replace('`geojson` TEXT,', '`geojson` LONGTEXT NULL,', $sql);

// FAQ questions need more than the default VARCHAR(255)
$sql = str_replace('`question_ar` VARCHAR(255) NOT NULL', '`question_ar` VARCHAR(500) NOT NULL', $sql);
$sql = str_replace('`question_en` VARCHAR(255) NOT NULL', '`question_en` VARCHAR(500) NOT NULL', $sql);

// Add ENGINE only to CREATE TABLE (not CREATE INDEX)
$sql = preg_replace(
    '/(CREATE TABLE IF NOT EXISTS `[^`]+`[\s\S]*?\))\s*;/',
    '$1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
    $sql
);

// Laravel framework columns that must stay TEXT, not JSON
$sql = str_replace('`sessions`(`payload` JSON NOT NULL', '`sessions`(`payload` LONGTEXT NOT NULL', $sql);
$sql = str_replace('`cache`(`key` VARCHAR(255) NOT NULL, `value` JSON NOT NULL', '`cache`(`key` VARCHAR(255) NOT NULL, `value` MEDIUMTEXT NOT NULL', $sql);
$sql = str_replace('`jobs`(`payload` JSON NOT NULL', '`jobs`(`payload` LONGTEXT NOT NULL', $sql);
$sql = str_replace('`failed_jobs`(`payload` JSON NOT NULL', '`failed_jobs`(`payload` LONGTEXT NOT NULL', $sql);
$sql = str_replace('`failed_job_ids` JSON NOT NULL', '`failed_job_ids` LONGTEXT NOT NULL', $sql);
$sql = str_replace('`updated_at` datetime', '`updated_at` TIMESTAMP NULL', $sql);
$sql = str_replace('`failed_at` datetime not null default CURRENT_TIMESTAMP', '`failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', $sql);

$header = <<<'SQL'
-- AUDI Laravel Backend — MySQL 8 schema
-- Generated from Laravel migrations (see generate-mysql-schema.php)
--
-- Usage:
--   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS audi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
--   mysql -u root -p audi < backend/database/schema/audi_mysql_schema.sql
--   cd backend && php artisan db:seed
--
-- Alternative (recommended): php artisan migrate && php artisan db:seed

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


SQL;

$footer = <<<'SQL'

-- Register migrations so `php artisan migrate` skips already-imported tables
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_06_24_215043_create_personal_access_tokens_table', 1),
('2026_06_25_100000_add_role_to_users_table', 1),
('2026_06_25_100001_create_audi_global_tables', 1),
('2026_06_25_100002_create_audi_home_tables', 1),
('2026_06_25_100003_create_audi_member_cities_tables', 1),
('2026_06_25_100004_create_audi_about_tables', 1),
('2026_06_25_100005_create_audi_strategy_tables', 1),
('2026_06_25_100006_create_audi_programs_tables', 1),
('2026_06_25_100007_create_audi_content_and_forms_tables', 1),
('2026_06_25_200000_alter_countries_geojson_to_longtext', 1),
('2026_06_25_300000_create_footer_feature_tables', 1),
('2026_06_25_300001_drop_json_valid_checks_on_framework_tables', 1);

SET FOREIGN_KEY_CHECKS = 1;

SQL;

file_put_contents($output, $header . trim($sql) . $footer);
echo "Written: {$output}\n";
