CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "role" varchar not null default 'editor'
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" varchar not null,
  "queue" varchar not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE INDEX "failed_jobs_connection_queue_failed_at_index" on "failed_jobs"(
  "connection",
  "queue",
  "failed_at"
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "site_settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value_ar" text,
  "value_en" text,
  "group" varchar not null default 'general',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "site_settings_key_unique" on "site_settings"("key");
CREATE TABLE IF NOT EXISTS "social_links"(
  "id" integer primary key autoincrement not null,
  "platform" varchar not null,
  "url" varchar not null,
  "icon" varchar,
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "uploads"(
  "id" integer primary key autoincrement not null,
  "disk" varchar not null default 'public',
  "path" varchar not null,
  "url" varchar not null,
  "mime_type" varchar,
  "size" integer,
  "original_name" varchar,
  "uploaded_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("uploaded_by") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "home_hero_slides"(
  "id" integer primary key autoincrement not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "image_url" varchar,
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "home_stats"(
  "id" integer primary key autoincrement not null,
  "value" varchar not null,
  "label_ar" varchar not null,
  "label_en" varchar not null,
  "description_ar" varchar not null,
  "description_en" varchar not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "countries"(
  "code_a2" varchar not null,
  "code_a3" varchar,
  "name_en" varchar not null,
  "name_ar" varchar,
  "geojson" text,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("code_a2")
);
CREATE TABLE IF NOT EXISTS "member_cities"(
  "id" integer primary key autoincrement not null,
  "country_code" varchar not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "latitude" numeric not null,
  "longitude" numeric not null,
  "info_ar" text,
  "info_en" text,
  "image_url" varchar,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("country_code") references "countries"("code_a2") on update cascade
);
CREATE INDEX "member_cities_country_code_is_active_index" on "member_cities"(
  "country_code",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "member_city_stats"(
  "key" varchar not null,
  "value" integer,
  "label_ar" varchar not null,
  "label_en" varchar not null,
  "unit_ar" varchar not null,
  "unit_en" varchar not null,
  "auto_calculate" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "about_content"(
  "id" integer primary key autoincrement not null,
  "section_key" varchar not null,
  "title_ar" varchar,
  "title_en" varchar,
  "body_ar" text,
  "body_en" text,
  "image_url" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "about_content_section_key_unique" on "about_content"(
  "section_key"
);
CREATE TABLE IF NOT EXISTS "leadership_messages"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "position_ar" varchar not null,
  "position_en" varchar not null,
  "honorific_ar" varchar,
  "honorific_en" varchar,
  "quote_ar" text not null,
  "quote_en" text not null,
  "paragraphs_ar" text not null,
  "paragraphs_en" text not null,
  "image_url" varchar,
  "image_alt_ar" varchar,
  "image_alt_en" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "leadership_messages_type_unique" on "leadership_messages"(
  "type"
);
CREATE TABLE IF NOT EXISTS "advisory_board_members"(
  "id" integer primary key autoincrement not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "role_ar" varchar not null,
  "role_en" varchar not null,
  "bio_ar" text,
  "bio_en" text,
  "image_url" varchar,
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "team_sections"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "team_sections_slug_unique" on "team_sections"("slug");
CREATE TABLE IF NOT EXISTS "team_members"(
  "id" integer primary key autoincrement not null,
  "team_section_id" integer not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "role_ar" varchar not null,
  "role_en" varchar not null,
  "bio_ar" text,
  "bio_en" text,
  "image_url" varchar,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("team_section_id") references "team_sections"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "partner_categories"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "partner_categories_slug_unique" on "partner_categories"(
  "slug"
);
CREATE TABLE IF NOT EXISTS "partners"(
  "id" integer primary key autoincrement not null,
  "partner_category_id" integer,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "logo_url" varchar,
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("partner_category_id") references "partner_categories"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "strategy_pages"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null default 'strategy-2025',
  "booklet_title_ar" varchar,
  "booklet_title_en" varchar,
  "booklet_pdf_url" varchar,
  "intro_title_ar" text,
  "intro_title_en" text,
  "intro_subtitle_ar" text,
  "intro_subtitle_en" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "strategy_pages_slug_unique" on "strategy_pages"("slug");
CREATE TABLE IF NOT EXISTS "strategy_pillars"(
  "id" integer primary key autoincrement not null,
  "number" varchar not null,
  "text_ar" text not null,
  "text_en" text not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "strategy_diagram_items"(
  "id" integer primary key autoincrement not null,
  "item_key" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "content_ar" text,
  "content_en" text,
  "columns_ar" text,
  "columns_en" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "strategy_diagram_items_item_key_unique" on "strategy_diagram_items"(
  "item_key"
);
CREATE TABLE IF NOT EXISTS "focus_areas"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "number" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "highlight_ar" varchar not null,
  "highlight_en" varchar not null,
  "tags_ar" text not null,
  "tags_en" text not null,
  "description_ar" text not null,
  "description_en" text not null,
  "list_image_url" varchar,
  "detail_image_url" varchar,
  "is_published" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "focus_areas_slug_unique" on "focus_areas"("slug");
CREATE TABLE IF NOT EXISTS "programs"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "hero_intro_ar" text,
  "hero_intro_en" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "programs_slug_unique" on "programs"("slug");
CREATE TABLE IF NOT EXISTS "program_sections"(
  "id" integer primary key autoincrement not null,
  "program_id" integer not null,
  "tab_key" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "intro_ar" text,
  "intro_en" text,
  "body_ar" text,
  "body_en" text,
  "image_url" varchar,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("program_id") references "programs"("id") on delete cascade
);
CREATE UNIQUE INDEX "program_sections_program_id_tab_key_unique" on "program_sections"(
  "program_id",
  "tab_key"
);
CREATE TABLE IF NOT EXISTS "training_courses"(
  "id" integer primary key autoincrement not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "count_ar" varchar not null,
  "count_en" varchar not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "experts"(
  "id" integer primary key autoincrement not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "specialty_ar" varchar not null,
  "specialty_en" varchar not null,
  "image_url" varchar,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "directory_cities"(
  "id" integer primary key autoincrement not null,
  "number" varchar not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "description_ar" text,
  "description_en" text,
  "country_code" varchar,
  "city_size" varchar,
  "detail_ar" text,
  "detail_en" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "directory_projects"(
  "id" integer primary key autoincrement not null,
  "number" varchar not null,
  "city_ar" varchar not null,
  "city_en" varchar not null,
  "country_ar" varchar not null,
  "country_en" varchar not null,
  "start_date" varchar,
  "end_date" varchar,
  "detail_ar" text,
  "detail_en" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "directory_organizations"(
  "id" integer primary key autoincrement not null,
  "number" varchar not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "description_ar" text,
  "description_en" text,
  "detail_ar" text,
  "detail_en" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "directory_publications"(
  "id" integer primary key autoincrement not null,
  "number" varchar not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "description_ar" text,
  "description_en" text,
  "detail_ar" text,
  "detail_en" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "directory_discussions"(
  "id" integer primary key autoincrement not null,
  "directory_type" varchar not null,
  "directory_number" varchar not null,
  "author_name_ar" varchar not null,
  "author_name_en" varchar not null,
  "body_ar" text not null,
  "body_en" text not null,
  "is_approved" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "directory_discussions_type_number_index" on "directory_discussions"("directory_type", "directory_number");
CREATE TABLE IF NOT EXISTS "resources"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "published_date" date,
  "image_url" varchar,
  "file_url" varchar,
  "resource_type" varchar,
  "focus_area_id" integer,
  "year" integer,
  "is_published" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("focus_area_id") references "focus_areas"("id") on delete set null
);
CREATE UNIQUE INDEX "resources_slug_unique" on "resources"("slug");
CREATE TABLE IF NOT EXISTS "media_articles"(
  "id" integer primary key autoincrement not null,
  "category" varchar not null,
  "key" varchar not null,
  "slug_ar" varchar not null,
  "slug_en" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "description_ar" text,
  "description_en" text,
  "body_ar" text not null,
  "body_en" text not null,
  "published_date" date,
  "image_url" varchar,
  "pdf_url" varchar,
  "authors_ar" text,
  "authors_en" text,
  "event_time" varchar,
  "is_published" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "media_articles_category_slug_ar_unique" on "media_articles"(
  "category",
  "slug_ar"
);
CREATE UNIQUE INDEX "media_articles_category_slug_en_unique" on "media_articles"(
  "category",
  "slug_en"
);
CREATE INDEX "media_articles_category_is_published_published_date_index" on "media_articles"(
  "category",
  "is_published",
  "published_date"
);
CREATE UNIQUE INDEX "media_articles_key_unique" on "media_articles"("key");
CREATE TABLE IF NOT EXISTS "contact_submissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "phone" varchar not null,
  "email" varchar not null,
  "message" text not null,
  "status" varchar not null default 'new',
  "ip_address" varchar,
  "user_agent" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "membership_applications"(
  "id" integer primary key autoincrement not null,
  "organization_name" varchar not null,
  "contact_name" varchar not null,
  "email" varchar not null,
  "phone" varchar not null,
  "country_code" varchar,
  "city" varchar,
  "message" text,
  "status" varchar not null default 'new',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "portal_contributions"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "email" varchar not null,
  "payload" text not null,
  "status" varchar not null default 'new',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "newsletter_subscriptions"(
  "id" integer primary key autoincrement not null,
  "email" varchar not null,
  "locale" varchar not null default 'ar',
  "is_confirmed" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "newsletter_subscriptions_email_unique" on "newsletter_subscriptions"(
  "email"
);
CREATE TABLE IF NOT EXISTS "faqs"(
  "id" integer primary key autoincrement not null,
  "category" varchar,
  "question_ar" varchar not null,
  "question_en" varchar not null,
  "answer_ar" text not null,
  "answer_en" text not null,
  "is_published" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "faqs_is_published_sort_order_index" on "faqs"(
  "is_published",
  "sort_order"
);
CREATE TABLE IF NOT EXISTS "legal_pages"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "content_ar" text not null,
  "content_en" text not null,
  "effective_date" date,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "legal_pages_slug_unique" on "legal_pages"("slug");
CREATE TABLE IF NOT EXISTS "job_openings"(
  "id" integer primary key autoincrement not null,
  "title_ar" varchar not null,
  "title_en" varchar not null,
  "location_ar" varchar,
  "location_en" varchar,
  "employment_type" varchar not null default 'full_time',
  "summary_ar" text,
  "summary_en" text,
  "description_ar" text,
  "description_en" text,
  "is_published" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "job_openings_is_published_sort_order_index" on "job_openings"(
  "is_published",
  "sort_order"
);
CREATE TABLE IF NOT EXISTS "job_applications"(
  "id" integer primary key autoincrement not null,
  "job_opening_id" integer,
  "full_name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "cover_letter" text,
  "cv_url" varchar,
  "status" varchar not null default 'new',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("job_opening_id") references "job_openings"("id") on delete set null
);
CREATE INDEX "job_applications_status_created_at_index" on "job_applications"(
  "status",
  "created_at"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_06_24_215043_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(5,'2026_06_25_100000_add_role_to_users_table',1);
INSERT INTO migrations VALUES(6,'2026_06_25_100001_create_audi_global_tables',1);
INSERT INTO migrations VALUES(7,'2026_06_25_100002_create_audi_home_tables',1);
INSERT INTO migrations VALUES(8,'2026_06_25_100003_create_audi_member_cities_tables',1);
INSERT INTO migrations VALUES(9,'2026_06_25_100004_create_audi_about_tables',1);
INSERT INTO migrations VALUES(10,'2026_06_25_100005_create_audi_strategy_tables',1);
INSERT INTO migrations VALUES(11,'2026_06_25_100006_create_audi_programs_tables',1);
INSERT INTO migrations VALUES(12,'2026_06_25_100007_create_audi_content_and_forms_tables',1);
INSERT INTO migrations VALUES(13,'2026_06_25_200000_alter_countries_geojson_to_longtext',1);
INSERT INTO migrations VALUES(14,'2026_06_25_300000_create_footer_feature_tables',1);
