# AUDI Laravel API — Phase 0

REST API backend for the **Arab Urban Development Institute (AUDI)** website.

Phase 0 delivers the foundation: Laravel 13, Sanctum, full database schema, Eloquent models, API route stubs, locale middleware, and seed data.

## Quick start

```bash
cd backend
cp .env.example .env   # if needed
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

API base URL: `http://localhost:8000/api`

### Default admin users (after seed)

| Email | Password | Role |
|-------|----------|------|
| admin@araburban.org | password | admin |
| editor@araburban.org | password | editor |

## What's included (Phase 0)

| Area | Status |
|------|--------|
| Laravel 13 + Sanctum | Done |
| All DB migrations (30+ tables) | Done |
| Eloquent models (33 models) | Done |
| `SetLocale` middleware (`Accept-Language` / `?locale=`) | Done |
| API route stubs (`routes/api.php`) | Done — returns placeholder JSON |
| Admin + editor users seeder | Done |
| Member city stats seeder | Done |

## Project layout

```
backend/
├── app/
│   ├── Enums/              # UserRole, MediaCategory, LeadershipType, ...
│   ├── Http/Middleware/    # SetLocale
│   └── Models/             # All Eloquent models + Concerns/
├── database/
│   ├── migrations/         # 2026_06_25_* AUDI schema
│   ├── schema/               # audi_mysql_schema.sql + generator
│   ├── seeders/
│   └── scripts/            # generate-models.php
└── routes/api.php          # Public /api/v1 + Admin /api/admin
```

## Database schema (SQL)

Full MySQL DDL: `database/schema/audi_mysql_schema.sql` — see `database/schema/README.md`.

```bash
# Recommended
php artisan migrate && php artisan db:seed

# Or import SQL then seed
mysql -u root -p audi < database/schema/audi_mysql_schema.sql
php artisan db:seed
```

Regenerate after migration changes:

```bash
php artisan migrate:fresh --force
php artisan schema:dump --path=database/schema/audi_sqlite_schema.sql
php database/schema/generate-mysql-schema.php
```

## Postman collections

Import from `docs/postman/`:

| File | Purpose |
|------|---------|
| `AUDI-API.postman_collection.json` | **Full API** — all modules (public + admin) |
| `API.md` | **Documentation index** — links to Public + Admin references |
| `PUBLIC-API.md` | **Public API reference** — purpose, descriptions, parameters (Arabic + English) |
| `ADMIN-API.md` | **Admin API reference** — purpose, descriptions, parameters (Arabic + English) |
| `AUDI-Member-Cities.postman_collection.json` | Member cities map (detailed GeoJSON spec) |
| `AUDI.postman_environment.json` | Local environment (`baseUrl`, `locale`, `adminToken`) |

Regenerate the main collection after endpoint changes:

```bash
php docs/postman/generate-audi-api-collection.php
```

Verify image-path alignment on public endpoints (requires DB migrated + seeded):

```bash
php artisan migrate
php artisan db:seed
php artisan serve
php docs/postman/smoke-test-image-endpoints.php http://127.0.0.1:8000 ar
```

The regenerated collection includes **Postman tests** on key public requests (`/home`, `/about/advisory-board`, `/about/team`, `/about/partners`, `/resources`, `/media/news`) that assert image fields use full paths (`/…` or `http…`), not bare filenames.

## Implementation phases (next steps)

| Phase | Scope | Status |
|-------|-------|--------|
| **0** | Setup, migrations, models, route stubs | **Done** |
| **1** | Contact + membership forms | **Done** — public POST + admin list/patch/delete + auth login |
| **2** | Member cities map + GeoJSON + admin CRUD | **Done** — public map/stats + admin cities/stats/countries |
| **3** | Media articles + resources CRUD | **Done** — public lists/detail + admin CRUD/reorder + JSON seeders |
| **4** | About + strategy + focus areas | **Done** — public about/strategy endpoints + admin CRUD + JSON seeder |
| **5** | Programs + development portal directory | **Done** — public programs/directory/contribute + admin CRUD + JSON seeder |
| **6** | Home aggregate endpoint + admin panel wiring | **Done** — public `/home` + hero-slides/home-stats admin CRUD + JSON seeder |

Full specification: [`docs/backend/laravel-backend.md`](../docs/backend/laravel-backend.md)

## Connecting the Next.js frontend

1. Set `NEXT_PUBLIC_API_URL=http://localhost:8000` in the frontend `.env.local`
2. Replace static `messages/*.json` reads with `fetch(`${API}/api/v1/...`)`
3. Replace contact form fake submit with `POST /api/v1/contact`
4. Replace GeoJSON fetches with `GET /api/v1/home/member-cities`

## CORS (when frontend runs on another port)

Add to `bootstrap/app.php` or publish `config/cors.php` and allow your Next.js origin (e.g. `http://localhost:3000`).

## Phase 2 — Member cities (done)

**Public**

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/v1/home/member-cities` | Stats + embedded GeoJSON payloads |
| GET | `/api/v1/home/member-cities/countries.geojson` | Arab countries polygons |
| GET | `/api/v1/home/member-cities/cities.geojson` | Member city points (locale-aware names) |

**Admin** (Sanctum)

| Method | Path | Description |
|--------|------|-------------|
| GET/PUT | `/api/admin/member-cities/stats` | Read/update homepage stats |
| GET | `/api/admin/member-cities/countries` | Country list |
| GET/POST/PATCH/DELETE | `/api/admin/member-cities/cities` | City CRUD (soft delete by default) |
| POST | `/api/admin/member-cities/cities/import` | Bulk upsert from JSON body |
| POST | `/api/admin/member-cities/cities/import-from-file` | Import from `storage/app/geojson/member-cities.geojson` |

Seed GeoJSON data:

```bash
php artisan db:seed --class=MemberCitiesGeoJsonSeeder
```

GeoJSON files are downloaded from the live site when missing and stored under `storage/app/geojson/` (gitignored).

## Phase 3 — Media + resources (done)

**Public**

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/v1/media/{category}` | Paginated list (`news`, `newsletter`, `city-meetings`) |
| GET | `/api/v1/media/{category}/{slug}` | Article detail (locale slug + `key` for language switch) |
| GET | `/api/v1/resources` | Paginated resources with filters (`type`, `focusArea`, `year`, `search`) |

**Admin** (Sanctum)

| Method | Path | Description |
|--------|------|-------------|
| GET/POST | `/api/admin/media` | List/create articles |
| GET/PUT/DELETE | `/api/admin/media/{id}` | Show/update/delete |
| POST | `/api/admin/media/reorder` | Batch `sortOrder` update |
| GET/POST | `/api/admin/resources` | List/create resources |
| GET/PUT/DELETE | `/api/admin/resources/{id}` | Show/update/delete |
| POST | `/api/admin/resources/reorder` | Batch `sortOrder` update |

Seed content from existing frontend JSON:

```bash
php artisan db:seed --class=MediaArticlesSeeder
php artisan db:seed --class=ResourcesSeeder
```

## Phase 4 — About + strategy + focus areas (done)

**Public**

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/v1/about/institute` | Institute intro + stats + tasks |
| GET | `/api/v1/about/vision-mission` | Vision, mission, goals, values |
| GET | `/api/v1/about/leadership/{type}` | `president` or `director` message |
| GET | `/api/v1/about/advisory-board` | Advisory board members |
| GET | `/api/v1/about/team` | Team sections + members |
| GET | `/api/v1/about/structure` | Operational structure image |
| GET | `/api/v1/about/partners` | Featured logos + categories |
| GET | `/api/v1/strategy/strategy-2025` | Strategy page + pillars + diagram items |
| GET | `/api/v1/strategy/focus-areas` | Focus areas list |
| GET | `/api/v1/strategy/focus-areas/{slug}` | Focus area detail + prev/next nav |

**Admin** (Sanctum): CRUD/reorder for `about-content`, `leadership`, `advisory-board`, `team-sections`, `team-members`, `partner-categories`, `partners`, `strategy`, `strategy-pillars`, `strategy-diagram`, `focus-areas`.

Seed content:

```bash
php artisan db:seed --class=AboutAndStrategySeeder
```

## Phase 5 — Programs + directory (done)

**Public**

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/v1/programs/{slug}` | Full program (`training`, `urban-policies`, `partnerships`) |
| GET | `/api/v1/programs/urban-policies/directory` | Paginated directory (`tab=cities\|projects\|organizations\|publications`) |
| POST | `/api/v1/programs/urban-policies/contribute` | Portal contribution submission |

**Admin** (Sanctum): CRUD/reorder for `programs`, `program-sections`, `training-courses`, `experts`, `directory/*`; list/patch/delete `portal-contributions`.

Seed content:

```bash
php artisan db:seed --class=ProgramsSeeder
```

## Phase 6 — Home aggregate (done)

**Public**

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/v1/home` | Full homepage payload (slider, stats, programs, media, resources, membership) |

**Admin** (Sanctum): CRUD/reorder for `hero-slides`, `home-stats`.

Seed content:

```bash
php artisan db:seed --class=HomeSeeder
```

(`AboutAndStrategySeeder` also seeds `home_stats` table rows from `messages/*/home.json`.)

## Notes

- Public routes use `Accept-Language: ar|en` (default `ar`)
- Admin routes require `Authorization: Bearer {token}` (Sanctum)
- Remaining admin stubs: none (settings, social-links, uploads implemented)
- Content seeders read from `messages/ar/*.json` and `messages/en/*.json`
