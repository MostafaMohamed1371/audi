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

CREATE TABLE IF NOT EXISTS `migrations`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `batch` BIGINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `users`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `role` VARCHAR(255) NOT NULL default 'editor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `users_email_unique` on `users`(`email`);
CREATE TABLE IF NOT EXISTS `password_reset_tokens`(
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  primary key(`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `sessions`(
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` TEXT,
  `payload` JSON NOT NULL,
  `last_activity` BIGINT UNSIGNED NOT NULL,
  primary key(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `sessions_user_id_index` on `sessions`(`user_id`);
CREATE INDEX `sessions_last_activity_index` on `sessions`(`last_activity`);
CREATE TABLE IF NOT EXISTS `cache`(
  `key` VARCHAR(255) NOT NULL,
  `value` JSON NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  primary key(`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `cache_expiration_index` on `cache`(`expiration`);
CREATE TABLE IF NOT EXISTS `cache_locks`(
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  primary key(`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `cache_locks_expiration_index` on `cache_locks`(`expiration`);
CREATE TABLE IF NOT EXISTS `jobs`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` JSON NOT NULL,
  `attempts` BIGINT UNSIGNED NOT NULL,
  `reserved_at` BIGINT UNSIGNED,
  `available_at` BIGINT UNSIGNED NOT NULL,
  `created_at` BIGINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `jobs_queue_index` on `jobs`(`queue`);
CREATE TABLE IF NOT EXISTS `job_batches`(
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` BIGINT UNSIGNED NOT NULL,
  `pending_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` JSON,
  `cancelled_at` BIGINT UNSIGNED,
  `created_at` BIGINT UNSIGNED NOT NULL,
  `finished_at` BIGINT UNSIGNED,
  primary key(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `failed_jobs`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` VARCHAR(255) NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` JSON NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `failed_jobs_connection_queue_failed_at_index` on `failed_jobs`(
  `connection`,
  `queue`,
  `failed_at`
);
CREATE UNIQUE INDEX `failed_jobs_uuid_unique` on `failed_jobs`(`uuid`);
CREATE TABLE IF NOT EXISTS `personal_access_tokens`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `abilities` JSON,
  `last_used_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` on `personal_access_tokens`(
  `tokenable_type`,
  `tokenable_id`
);
CREATE UNIQUE INDEX `personal_access_tokens_token_unique` on `personal_access_tokens`(
  `token`
);
CREATE INDEX `personal_access_tokens_expires_at_index` on `personal_access_tokens`(
  `expires_at`
);
CREATE TABLE IF NOT EXISTS `site_settings`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(255) NOT NULL,
  `value_ar` TEXT,
  `value_en` TEXT,
  `group` VARCHAR(255) NOT NULL default 'general',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `site_settings_key_unique` on `site_settings`(`key`);
CREATE TABLE IF NOT EXISTS `social_links`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `platform` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `uploads`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `disk` VARCHAR(255) NOT NULL default 'public',
  `path` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(255),
  `size` BIGINT UNSIGNED,
  `original_name` VARCHAR(255),
  `uploaded_by` BIGINT UNSIGNED,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `home_hero_slides`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `image_url` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `home_stats`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `value` VARCHAR(255) NOT NULL,
  `label_ar` VARCHAR(255) NOT NULL,
  `label_en` VARCHAR(255) NOT NULL,
  `description_ar` VARCHAR(255) NOT NULL,
  `description_en` VARCHAR(255) NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `countries`(
  `code_a2` VARCHAR(255) NOT NULL,
  `code_a3` VARCHAR(255),
  `name_en` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255),
  `geojson` LONGTEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  primary key(`code_a2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `member_cities`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `country_code` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `latitude` DECIMAL(9,6) NOT NULL,
  `longitude` DECIMAL(9,6) NOT NULL,
  `info_ar` TEXT,
  `info_en` TEXT,
  `image_url` VARCHAR(255),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_country_code` FOREIGN KEY (`country_code`) REFERENCES `countries` (`code_a2`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `member_cities_country_code_is_active_index` on `member_cities`(
  `country_code`,
  `is_active`
);
CREATE TABLE IF NOT EXISTS `member_city_stats`(
  `key` VARCHAR(255) NOT NULL,
  `value` BIGINT UNSIGNED,
  `label_ar` VARCHAR(255) NOT NULL,
  `label_en` VARCHAR(255) NOT NULL,
  `unit_ar` VARCHAR(255) NOT NULL,
  `unit_en` VARCHAR(255) NOT NULL,
  `auto_calculate` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  primary key(`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `about_content`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `section_key` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255),
  `title_en` VARCHAR(255),
  `body_ar` JSON,
  `body_en` JSON,
  `image_url` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `about_content_section_key_unique` on `about_content`(
  `section_key`
);
CREATE TABLE IF NOT EXISTS `leadership_messages`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `position_ar` VARCHAR(255) NOT NULL,
  `position_en` VARCHAR(255) NOT NULL,
  `honorific_ar` VARCHAR(255),
  `honorific_en` VARCHAR(255),
  `quote_ar` TEXT NOT NULL,
  `quote_en` TEXT NOT NULL,
  `paragraphs_ar` JSON NOT NULL,
  `paragraphs_en` JSON NOT NULL,
  `image_url` VARCHAR(255),
  `image_alt_ar` VARCHAR(255),
  `image_alt_en` VARCHAR(255),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `leadership_messages_type_unique` on `leadership_messages`(
  `type`
);
CREATE TABLE IF NOT EXISTS `advisory_board_members`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `role_ar` VARCHAR(255) NOT NULL,
  `role_en` VARCHAR(255) NOT NULL,
  `bio_ar` TEXT,
  `bio_en` TEXT,
  `image_url` VARCHAR(255),
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `team_sections`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `team_sections_slug_unique` on `team_sections`(`slug`);
CREATE TABLE IF NOT EXISTS `team_members`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_section_id` BIGINT UNSIGNED NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `role_ar` VARCHAR(255) NOT NULL,
  `role_en` VARCHAR(255) NOT NULL,
  `bio_ar` TEXT,
  `bio_en` TEXT,
  `image_url` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_team_section_id` FOREIGN KEY (`team_section_id`) REFERENCES `team_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `partner_categories`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `partner_categories_slug_unique` on `partner_categories`(
  `slug`
);
CREATE TABLE IF NOT EXISTS `partners`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `partner_category_id` BIGINT UNSIGNED,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `logo_url` VARCHAR(255),
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_partner_category_id` FOREIGN KEY (`partner_category_id`) REFERENCES `partner_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `strategy_pages`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL default 'strategy-2025',
  `booklet_title_ar` VARCHAR(255),
  `booklet_title_en` VARCHAR(255),
  `booklet_pdf_url` VARCHAR(255),
  `intro_title_ar` TEXT,
  `intro_title_en` TEXT,
  `intro_subtitle_ar` TEXT,
  `intro_subtitle_en` TEXT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `strategy_pages_slug_unique` on `strategy_pages`(`slug`);
CREATE TABLE IF NOT EXISTS `strategy_pillars`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(255) NOT NULL,
  `text_ar` TEXT NOT NULL,
  `text_en` TEXT NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `strategy_diagram_items`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_key` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `content_ar` TEXT,
  `content_en` TEXT,
  `columns_ar` JSON NULL,
  `columns_en` JSON NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `strategy_diagram_items_item_key_unique` on `strategy_diagram_items`(
  `item_key`
);
CREATE TABLE IF NOT EXISTS `focus_areas`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `number` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `highlight_ar` VARCHAR(255) NOT NULL,
  `highlight_en` VARCHAR(255) NOT NULL,
  `tags_ar` JSON NOT NULL,
  `tags_en` JSON NOT NULL,
  `description_ar` TEXT NOT NULL,
  `description_en` TEXT NOT NULL,
  `list_image_url` VARCHAR(255),
  `detail_image_url` VARCHAR(255),
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `focus_areas_slug_unique` on `focus_areas`(`slug`);
CREATE TABLE IF NOT EXISTS `programs`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `hero_intro_ar` TEXT,
  `hero_intro_en` TEXT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `programs_slug_unique` on `programs`(`slug`);
CREATE TABLE IF NOT EXISTS `program_sections`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `program_id` BIGINT UNSIGNED NOT NULL,
  `tab_key` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `intro_ar` TEXT,
  `intro_en` TEXT,
  `body_ar` JSON,
  `body_en` JSON,
  `image_url` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_program_id` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `program_sections_program_id_tab_key_unique` on `program_sections`(
  `program_id`,
  `tab_key`
);
CREATE TABLE IF NOT EXISTS `training_courses`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `count_ar` VARCHAR(255) NOT NULL,
  `count_en` VARCHAR(255) NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `experts`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `specialty_ar` VARCHAR(255) NOT NULL,
  `specialty_en` VARCHAR(255) NOT NULL,
  `image_url` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `directory_cities`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `description_ar` TEXT,
  `description_en` TEXT,
  `country_code` VARCHAR(255),
  `city_size` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `directory_projects`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(255) NOT NULL,
  `city_ar` VARCHAR(255) NOT NULL,
  `city_en` VARCHAR(255) NOT NULL,
  `country_ar` VARCHAR(255) NOT NULL,
  `country_en` VARCHAR(255) NOT NULL,
  `start_date` VARCHAR(255),
  `end_date` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `directory_organizations`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `description_ar` TEXT,
  `description_en` TEXT,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `directory_publications`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) NOT NULL,
  `name_en` VARCHAR(255) NOT NULL,
  `description_ar` TEXT,
  `description_en` TEXT,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `resources`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `published_date` DATE NULL,
  `image_url` VARCHAR(255),
  `file_url` VARCHAR(255),
  `resource_type` VARCHAR(255),
  `focus_area_id` BIGINT UNSIGNED,
  `year` BIGINT UNSIGNED,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_focus_area_id` FOREIGN KEY (`focus_area_id`) REFERENCES `focus_areas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `resources_slug_unique` on `resources`(`slug`);
CREATE TABLE IF NOT EXISTS `media_articles`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(255) NOT NULL,
  `key` VARCHAR(255) NOT NULL,
  `slug_ar` VARCHAR(255) NOT NULL,
  `slug_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `description_ar` TEXT,
  `description_en` TEXT,
  `body_ar` JSON NOT NULL,
  `body_en` JSON NOT NULL,
  `published_date` DATE NULL,
  `image_url` VARCHAR(255),
  `pdf_url` VARCHAR(255),
  `authors_ar` JSON NULL,
  `authors_en` JSON NULL,
  `event_time` VARCHAR(255),
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `media_articles_category_slug_ar_unique` on `media_articles`(
  `category`,
  `slug_ar`
);
CREATE UNIQUE INDEX `media_articles_category_slug_en_unique` on `media_articles`(
  `category`,
  `slug_en`
);
CREATE INDEX `media_articles_category_is_published_published_date_index` on `media_articles`(
  `category`,
  `is_published`,
  `published_date`
);
CREATE UNIQUE INDEX `media_articles_key_unique` on `media_articles`(`key`);
CREATE TABLE IF NOT EXISTS `contact_submissions`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(255) NOT NULL default 'new',
  `ip_address` VARCHAR(255),
  `user_agent` TEXT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `membership_applications`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `organization_name` VARCHAR(255) NOT NULL,
  `contact_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL,
  `country_code` VARCHAR(255),
  `city` VARCHAR(255),
  `message` TEXT,
  `status` VARCHAR(255) NOT NULL default 'new',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `portal_contributions`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `payload` JSON NOT NULL,
  `status` VARCHAR(255) NOT NULL default 'new',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `locale` VARCHAR(255) NOT NULL default 'ar',
  `is_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `newsletter_subscriptions_email_unique` on `newsletter_subscriptions`(
  `email`
);
CREATE TABLE IF NOT EXISTS `faqs`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(255),
  `question_ar` VARCHAR(500) NOT NULL,
  `question_en` VARCHAR(500) NOT NULL,
  `answer_ar` TEXT NOT NULL,
  `answer_en` TEXT NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `faqs_is_published_sort_order_index` on `faqs`(
  `is_published`,
  `sort_order`
);
CREATE TABLE IF NOT EXISTS `legal_pages`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `content_ar` TEXT NOT NULL,
  `content_en` TEXT NOT NULL,
  `effective_date` DATE NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX `legal_pages_slug_unique` on `legal_pages`(`slug`);
CREATE TABLE IF NOT EXISTS `job_openings`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `location_ar` VARCHAR(255),
  `location_en` VARCHAR(255),
  `employment_type` VARCHAR(255) NOT NULL default 'full_time',
  `summary_ar` TEXT,
  `summary_en` TEXT,
  `description_ar` TEXT,
  `description_en` TEXT,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` BIGINT UNSIGNED NOT NULL default '0',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `job_openings_is_published_sort_order_index` on `job_openings`(
  `is_published`,
  `sort_order`
);
CREATE TABLE IF NOT EXISTS `job_applications`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `job_opening_id` BIGINT UNSIGNED,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255),
  `cover_letter` TEXT,
  `cv_url` VARCHAR(255),
  `status` VARCHAR(255) NOT NULL default 'new',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_job_opening_id` FOREIGN KEY (`job_opening_id`) REFERENCES `job_openings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX `job_applications_status_created_at_index` on `job_applications`(
  `status`,
  `created_at`
);
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
