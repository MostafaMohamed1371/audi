# AUDI Database Schema

SQL exports for the Laravel backend (`backend/database/migrations/`).

## Files

| File | Engine | Purpose |
|------|--------|---------|
| `audi_mysql_schema.sql` | MySQL 8 | Full DDL for production/staging |
| `audi_sqlite_schema.sql` | SQLite | Dev dump from `php artisan schema:dump` |
| `generate-mysql-schema.php` | — | Regenerates MySQL file from SQLite dump |

## Option A — Laravel migrations (recommended)

```bash
cd backend
cp .env.example .env   # set DB_* for MySQL (port 3307 if needed)
php artisan migrate
php artisan db:seed
```

Default admin after seed: `admin@araburban.org` / `password`

## Option B — Import MySQL SQL file

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS audi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p audi < backend/database/schema/audi_mysql_schema.sql
cd backend && php artisan db:seed
```

> The SQL file creates tables only. Run seeders to load content from `messages/ar` and `messages/en`.

## Regenerate MySQL schema

After changing migrations:

```bash
cd backend
php artisan migrate:fresh --force
php artisan schema:dump --path=database/schema/audi_sqlite_schema.sql
cd ..
php backend/database/schema/generate-mysql-schema.php
```

## Tables (content)

- **Home:** `home_hero_slides`, `home_stats`
- **Member cities:** `countries`, `member_cities`, `member_city_stats`
- **About:** `about_content`, `leadership_messages`, `advisory_board_members`, `team_sections`, `team_members`, `partner_categories`, `partners`
- **Strategy:** `strategy_pages`, `strategy_pillars`, `strategy_diagram_items`, `focus_areas`
- **Programs:** `programs`, `program_sections`, `training_courses`, `experts`, `directory_*`
- **Content:** `resources`, `media_articles` (categories: news, newsletter, city_meetings, secretary_speaks)
- **Forms:** `contact_submissions`, `membership_applications`, `portal_contributions`, `newsletter_subscriptions`, `job_applications`
- **Footer features:** `faqs`, `legal_pages` (terms/privacy), `job_openings`
- **Global:** `site_settings`, `social_links`, `uploads`

Plus Laravel framework tables: `users`, `personal_access_tokens`, `cache`, `jobs`, etc.
