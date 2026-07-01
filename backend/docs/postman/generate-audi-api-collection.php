<?php
/**
 * Generates AUDI-API.postman_collection.json from endpoint definitions.
 * Run: php backend/docs/postman/generate-audi-api-collection.php
 *
 * Admin folders mirror Public /api/v1 groups.
 * Admin bodies use *Ar/*En pairs; Public returns locale-resolved single fields via Accept-Language.
 */

declare(strict_types=1);

$baseDir = dirname(__DIR__, 2);
$output = $baseDir . '/docs/postman/AUDI-API.postman_collection.json';
$adminDocsOutput = $baseDir . '/docs/postman/ADMIN-API.md';
$publicDocsOutput = $baseDir . '/docs/postman/PUBLIC-API.md';
$apiReadmeOutput = $baseDir . '/docs/postman/API.md';
$apiErrorsOutput = $baseDir . '/docs/postman/API-ERRORS.md';
$homepageGuideOutput = $baseDir . '/docs/postman/HOMEPAGE-ADMIN-GUIDE.md';
$programsGuideOutput = $baseDir . '/docs/postman/PROGRAMS-ADMIN-GUIDE.md';

require __DIR__ . '/postman-arabic-helpers.php';
require __DIR__ . '/postman-admin-docs.php';
require __DIR__ . '/postman-api-errors-docs.php';
require __DIR__ . '/postman-home-examples.php';
require __DIR__ . '/postman-homepage-guide-docs.php';
require __DIR__ . '/postman-program-examples.php';
require __DIR__ . '/postman-programs-guide-docs.php';

function req(string $name, string $method, string $path, array $opts = []): array
{
    $headers = $opts['headers'] ?? [];
    $query = $opts['query'] ?? [];
    $body = $opts['body'] ?? null;
    $auth = $opts['auth'] ?? false;
    $description = $opts['description'] ?? '';

    if ($auth) {
        $headers[] = ['key' => 'Authorization', 'value' => 'Bearer {{adminToken}}'];
    }

    $hasLocale = false;
    foreach ($headers as $existing) {
        if (strtolower((string) $existing['key']) === 'accept-language') {
            $hasLocale = true;
            break;
        }
    }

    if (! ($opts['noLocale'] ?? false) && ! $hasLocale) {
        array_unshift($headers, ['key' => 'Accept-Language', 'value' => '{{locale}}', 'description' => 'اللغة: ar | en']);
    }

    if ($query) {
        $query = postmanApplyArabicQueryDescriptions($query);
    }

    $headerBlock = array_map(fn ($h) => [
        'key' => $h['key'],
        'value' => $h['value'],
        'description' => $h['description'] ?? '',
    ], $headers);

    $url = [
        'raw' => '{{baseUrl}}' . $path,
        'host' => ['{{baseUrl}}'],
        'path' => array_values(array_filter(explode('/', ltrim($path, '/')))),
    ];

    if ($query) {
        $url['query'] = array_map(fn ($q) => [
            'key' => $q['key'],
            'value' => $q['value'],
            'description' => $q['description'] ?? '',
            'disabled' => $q['disabled'] ?? false,
        ], $query);
    }

    $request = [
        'method' => $method,
        'header' => $headerBlock,
        'url' => $url,
    ];

    if (isset($opts['formdata'])) {
        $formdata = [];
        foreach ($opts['formdata'] as $field) {
            $entry = [
                'key' => $field['key'],
                'type' => $field['type'] ?? 'text',
                'description' => $field['description'] ?? postmanArabicFieldLabel((string) $field['key']),
            ];
            if (($field['type'] ?? 'text') === 'file') {
                $entry['src'] = $field['src'] ?? [];
            } else {
                $entry['value'] = (string) ($field['value'] ?? '');
            }
            $formdata[] = $entry;
        }
        $request['body'] = [
            'mode' => 'formdata',
            'formdata' => $formdata,
        ];
    } elseif ($body !== null) {
        $request['header'][] = ['key' => 'Content-Type', 'value' => 'application/json'];
        $request['body'] = [
            'mode' => 'raw',
            'raw' => is_string($body) ? $body : json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ];
    }

    if (str_starts_with($path, '/api/admin')) {
        $purpose = postmanRequestPurpose($method, $name, 'admin');
        $description = "**الغرض | Purpose:** {$purpose}\n\n".$description;
    } elseif (str_starts_with($path, '/api/v1')) {
        $purpose = postmanRequestPurpose($method, $name, 'public');
        $description = "**الغرض | Purpose:** {$purpose}\n\n".$description;
    }

    if (! empty($description)) {
        $description = postmanAppendBodyDocs($description, $body, ! ($opts['skipBodyDocs'] ?? false));
    } elseif ($body !== null && ! ($opts['skipBodyDocs'] ?? false)) {
        $description = postmanAppendBodyDocs('', $body);
    }

    $request['description'] = $description;

    $item = ['name' => $name, 'request' => $request, 'response' => $opts['response'] ?? []];

    if (! empty($opts['tests'])) {
        $exec = $opts['tests'];
        if (is_string($exec)) {
            $exec = array_values(array_filter(array_map('trim', explode("\n", $exec))));
        }
        $item['event'] = [[
            'listen' => 'test',
            'script' => [
                'type' => 'text/javascript',
                'exec' => $exec,
            ],
        ]];
    }

    return $item;
}

/** Postman test script: assert image fields are full paths (not bare filenames). */
function imagePathTests(string $jsonPath, string $field = 'image'): array
{
    return [
        "pm.test('Status is 2xx', () => pm.expect(pm.response.code).to.be.oneOf([200, 201]));",
        'const body = pm.response.json();',
        "pm.test('Image fields use full paths', () => {",
        "  const items = {$jsonPath};",
        "  const list = Array.isArray(items) ? items : (items ? [items] : []);",
        "  list.forEach((row) => {",
        "    const val = row?.{$field};",
        "    if (!val) return;",
        "    pm.expect(val, `bare filename: \${val}`).to.match(/^(\/|https?:\/\/)/);",
        "  });",
        '});',
    ];
}

function folder(string $name, array $items, string $description = ''): array
{
    return [
        'name' => $name,
        'description' => $description,
        'item' => $items,
    ];
}

/** Prefix description with public endpoint mapping. */
function publicMatch(string $publicPath, string $note = ''): string
{
    $base = "**Public match:** `GET {$publicPath}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.\n\n";
    $base .= "**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).\n\n";

    return $note !== '' ? $base . $note : $base;
}

/**
 * Standard admin CRUD with full create/update bodies.
 *
 * @param  array<string, mixed>  $createBody
 * @param  array<string, mixed>  $opts  updateBody, withReorder, publicPath, description
 */
function adminCrud(string $resource, string $label, array $createBody, array $opts = []): array
{
    $base = "/api/admin/{$resource}";
    $updateBody = $opts['updateBody'] ?? $createBody;
    $withReorder = $opts['withReorder'] ?? true;
    $publicPath = $opts['publicPath'] ?? null;
    $extraDesc = $opts['description'] ?? '';
    $labelAr = $opts['labelAr'] ?? $label;

    $descPrefix = $publicPath ? publicMatch($publicPath, $extraDesc) : ($extraDesc !== '' ? $extraDesc : '');

    $items = [
        req("عرض القائمة — List {$label}", 'GET', $base . '?page=1&limit=20', [
            'auth' => true,
            'query' => [
                ['key' => 'page', 'value' => '1'],
                ['key' => 'limit', 'value' => '20'],
                ['key' => 'search', 'value' => '', 'disabled' => true],
            ],
            'description' => $descPrefix . 'قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.',
        ]),
        req("إنشاء — Create {$labelAr}", 'POST', $base, [
            'auth' => true,
            'body' => $createBody,
            'description' => $descPrefix . '**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.',
        ]),
        req("عرض — Show {$labelAr}", 'GET', $base . '/{{id}}', [
            'auth' => true,
            'description' => $descPrefix,
        ]),
        req("تحديث — Update {$labelAr}", 'PUT', $base . '/{{id}}', [
            'auth' => true,
            'body' => $updateBody,
            'description' => $descPrefix . '**جسم التحديث كامل** — نفس حقول الإنشاء.',
        ]),
        req("حذف — Delete {$labelAr}", 'DELETE', $base . '/{{id}}', [
            'auth' => true,
            'description' => $descPrefix,
        ]),
    ];

    if ($withReorder) {
        $items[] = req("إعادة الترتيب — Reorder {$labelAr}", 'POST', $base . '/reorder', [
            'auth' => true,
            'body' => ['items' => [['id' => 1, 'sortOrder' => 0], ['id' => 2, 'sortOrder' => 1]]],
            'description' => 'إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.',
        ]);
    }

    return $items;
}

$localeHeader = ['key' => 'Accept-Language', 'value' => '{{locale}}', 'description' => 'اللغة: ar | en'];

$collection = [
    'info' => [
        '_postman_id' => 'audi-api-full-v1',
        'name' => 'AUDI — Full API (المعهد العربي لإنماء المدن)',
        'description' => <<<'MD'
# AUDI API — Public + Admin

## Variables
| Variable | Example | Purpose |
|----------|---------|---------|
| `baseUrl` | `http://localhost:8000` | API base URL |
| `locale` | `ar` or `en` | Language for **all** endpoints (Public + Admin) |
| `adminToken` | Bearer token | Admin auth after login |
| `id`, `slug`, `category` | — | Path/query placeholders |

## Localization model
- **Public `/api/v1/*`**: returns **one locale** per request — e.g. `{ "title": "..." }` (not `titleAr`/`titleEn`).
- **Admin `/api/admin/*`**: stores **both languages** — e.g. `{ "titleAr": "...", "titleEn": "..." }`.
- Switch language via collection variable `locale` or header `Accept-Language: ar|en` (also `?locale=ar`).

## Admin ↔ Public mapping
| Public endpoint | Admin resources |
|-----------------|-----------------|
| `GET /api/v1/home` | hero-slides, home-stats, about-content (home_*), media, resources, settings |
| `GET /api/v1/settings` | settings, social-links |
| `GET /api/v1/about/*` | about-content, leadership, advisory-board, team-*, partners |
| `GET /api/v1/strategy/*` | strategy, strategy-pillars, strategy-diagram, focus-areas, about-content |
| `GET /api/v1/programs/*` | programs, program-sections, training-courses, experts, directory/* |
| `GET /api/v1/resources` | resources |
| `GET /api/v1/media/*` | media |
| `GET /api/v1/careers` | job-openings |
| `GET /api/v1/faqs` | faqs |
| `GET /api/v1/legal/{slug}` | legal |
| `GET /api/v1/contact` | settings (contact group) |
| Form POSTs | contact-submissions, membership-applications, portal-contributions, job-applications, newsletter-subscriptions |

## Image URL convention (Admin ↔ Public ↔ Frontend)
- **Admin** stores `imageUrl` / `logoUrl` as **full paths** — root-relative (`/emp/1.png`, `/blog/2.png`) or absolute after upload (`http://localhost:8000/storage/uploads/…`).
- **Public** returns the same path in locale-neutral fields (`image`, `imageUrl`, `listImage`, …) — **never** bare filenames.
- **Upload flow:** `POST /api/admin/uploads` → use returned **absolute** `data.url` in admin `imageUrl` fields.
- **Static asset directories:** `/emp/` (team, advisory), `/client/` (partners), `/blog/` (media), `/our-sources/` (resources), `/slider/` (hero).

Canonical examples in this collection use these paths. Public requests include tests that verify image fields start with `/`.

> Member cities GeoJSON: see `AUDI-Member-Cities.postman_collection.json`

---

# API Markdown Documentation | توثيق Markdown

| File | Content |
|------|---------|
| **`docs/postman/HOMEPAGE-ADMIN-GUIDE.md`** | Homepage build guide — 30 admin steps for /ar |
| **`docs/postman/API.md`** | Index — links to Public + Admin docs |
| **`docs/postman/PUBLIC-API.md`** | Public `/api/v1` — purpose, parameters (Arabic) |
| **`docs/postman/ADMIN-API.md`** | Admin `/api/admin` — purpose, parameters (Arabic) |

Regenerate all docs: `php docs/postman/generate-audi-api-collection.php`

---

# Public API | الواجهة العامة

Open folder **Public — /api/v1**. Each request **Description** includes purpose and parameter tables.
Set `Accept-Language: {{locale}}` (`ar` or `en`).

---

# Admin API Documentation | توثيق واجهة الإدارة

Full admin reference (purpose, description, parameters in Arabic): **`docs/postman/ADMIN-API.md`**

Open folder **لوحة التحكم — Admin /api/admin** in this collection. Each request **Description** tab includes:
- **الغرض | Purpose** — what the endpoint does
- **المعاملات (Body Parameters)** — table with Arabic field descriptions
- **Public match** — which public endpoint consumes the data

### Quick start (Admin)
1. Run **تسجيل الدخول — Login** → copy `token` to `adminToken`
2. All other admin requests use `Authorization: Bearer {{adminToken}}`
3. Create content with full bilingual bodies (`*Ar` / `*En`)
4. Verify on public API with `Accept-Language: ar` or `en`
MD,
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        ['key' => 'baseUrl', 'value' => 'http://localhost:8000'],
        ['key' => 'locale', 'value' => 'ar'],
        ['key' => 'adminToken', 'value' => 'YOUR_ADMIN_TOKEN'],
        ['key' => 'id', 'value' => '1'],
        ['key' => 'slug', 'value' => 'urban-resilience'],
        ['key' => 'category', 'value' => 'news'],
    ],
    'item' => [],
];

// =============================================================================
// PUBLIC — /api/v1
// =============================================================================

$publicHome = folder('Home — الرئيسية', [
    req('Get Home (Aggregate)', 'GET', '/api/v1/home', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/home', 'Aggregates slider, stats, programs, mediaCenter, knowledgeCenter, membershipContact.'),
        'tests' => array_merge(
            imagePathTests('(pm.response.json().slider || [])', 'imageUrl'),
            [
                "pm.test('Media center images are full paths', () => {",
                "  const mc = pm.response.json().mediaCenter || {};",
                "  [...(mc.featured || []), ...(mc.items || [])].forEach((row) => {",
                "    if (row.image) pm.expect(row.image).to.match(/^(\/|https?:\/\/)/);",
                "  });",
                '});',
                "pm.test('Knowledge center images are full paths', () => {",
                "  (pm.response.json().knowledgeCenter?.items || []).forEach((row) => {",
                "    if (row.image) pm.expect(row.image).to.match(/^(\/|https?:\/\/)/);",
                "  });",
                '});',
            ],
        ),
    ]),
    req('Get Member Cities Map', 'GET', '/api/v1/home/member-cities', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/home/member-cities', 'Returns stats + GeoJSON. Admin: member-cities/stats + cities.'),
    ]),
    req('Get Countries GeoJSON', 'GET', '/api/v1/home/member-cities/countries.geojson', [
        'description' => 'GeoJSON — not locale-dependent geometry.',
    ]),
    req('Get Cities GeoJSON', 'GET', '/api/v1/home/member-cities/cities.geojson', [
        'description' => 'GeoJSON — city names resolved by Accept-Language.',
    ]),
]);

$publicSettings = folder('Settings — الإعدادات', [
    req('Get Site Settings', 'GET', '/api/v1/settings', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/settings', 'Returns siteName, copyright, socialLinks, contact. Admin: settings + social-links.'),
    ]),
]);

$publicAbout = folder('About — من نحن', [
    req('Get Institute', 'GET', '/api/v1/about/institute', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/institute', 'Admin: about-content sectionKey=institute + home-stats.')]),
    req('Get Vision & Mission', 'GET', '/api/v1/about/vision-mission', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/vision-mission', 'Admin: about-content keys vision_mission, goals, values.')]),
    req('Get Leadership (President)', 'GET', '/api/v1/about/leadership/president', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/leadership/president', 'Admin: leadership type=president.')]),
    req('Get Leadership (Director)', 'GET', '/api/v1/about/leadership/director', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/leadership/director', 'Admin: leadership type=director.')]),
    req('Get Advisory Board', 'GET', '/api/v1/about/advisory-board', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/advisory-board', 'Admin: about-content advisory_board + advisory-board members. Public `members[].image` = admin `imageUrl`.'),
        'tests' => imagePathTests('(pm.response.json().members || [])'),
    ]),
    req('Get Team', 'GET', '/api/v1/about/team', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/team', 'Admin: team-sections + team-members. Public nested `members[].image` = admin `imageUrl`.'),
        'tests' => [
            "pm.test('Status is 2xx', () => pm.expect(pm.response.code).to.be.oneOf([200, 201]));",
            "pm.test('Team member images are full paths', () => {",
            "  (pm.response.json().sections || []).flatMap(s => s.members || []).forEach((m) => {",
            "    if (m.image) pm.expect(m.image).to.match(/^(\/|https?:\/\/)/);",
            "  });",
            '});',
        ],
    ]),
    req('Get Structure', 'GET', '/api/v1/about/structure', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/structure', 'Admin: about-content sectionKey=structure.')]),
    req('Get Partners', 'GET', '/api/v1/about/partners', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/partners', 'Admin: partners_hero + partner-categories + partners. Public `image` = admin `logoUrl`.'),
        'tests' => [
            "pm.test('Status is 2xx', () => pm.expect(pm.response.code).to.be.oneOf([200, 201]));",
            "pm.test('Partner logos are full paths', () => {",
            "  const json = pm.response.json();",
            "  [...(json.featured || []), ...(json.categories || []).flatMap(c => c.logos || [])].forEach((row) => {",
            "    if (row.image) pm.expect(row.image).to.match(/^(\/|https?:\/\/)/);",
            "  });",
            '});',
        ],
    ]),
]);

$publicStrategy = folder('Strategy — الاستراتيجية', [
    req('Get Strategy 2025/2026', 'GET', '/api/v1/strategy/strategy-2025', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/strategy-2025', 'Admin: strategy + strategy-pillars + strategy-diagram.')]),
    req('List Focus Areas', 'GET', '/api/v1/strategy/focus-areas', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/focus-areas', 'Admin: focus-areas + about-content focus_areas_pages.')]),
    req('Get Focus Area Detail', 'GET', '/api/v1/strategy/focus-areas/{{slug}}', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/focus-areas/{slug}', 'Admin: focus-areas by slug.')]),
]);

$publicPrograms = folder('Programs — البرامج', [
    req('Get Urban Policies Program', 'GET', '/api/v1/programs/urban-policies', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/urban-policies', 'Admin: programs + program-sections + directory/*.')]),
    req('Get Training Program', 'GET', '/api/v1/programs/training', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/training', 'Admin: programs + program-sections + training-courses + experts.')]),
    req('Get Partnerships Program', 'GET', '/api/v1/programs/partnerships', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/partnerships', 'Admin: programs + program-sections.')]),
    req('Get Development Portal Directory', 'GET', '/api/v1/programs/urban-policies/directory', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'tab', 'value' => 'cities', 'description' => 'cities|projects|organizations|publications'],
            ['key' => 'page', 'value' => '1'],
            ['key' => 'limit', 'value' => '20'],
            ['key' => 'search', 'value' => '', 'disabled' => true],
        ],
        'description' => publicMatch('/api/v1/programs/urban-policies/directory', 'Admin: directory/cities|projects|organizations|publications.'),
    ]),
]);

$publicResources = folder('Resources — المصادر', [
    req('List Resources', 'GET', '/api/v1/resources', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'type', 'value' => '', 'disabled' => true],
            ['key' => 'focusArea', 'value' => '', 'disabled' => true],
            ['key' => 'year', 'value' => '', 'disabled' => true],
            ['key' => 'search', 'value' => '', 'disabled' => true],
            ['key' => 'page', 'value' => '1'],
        ],
        'description' => publicMatch('/api/v1/resources', 'Admin: resources CRUD. Public `image` = admin `imageUrl`.'),
        'tests' => imagePathTests('(pm.response.json().data || [])'),
    ]),
]);

$publicMedia = folder('Media — المركز الإعلامي', [
    req('List News', 'GET', '/api/v1/media/news', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'year', 'value' => '2025', 'disabled' => true],
            ['key' => 'month', 'value' => '1', 'disabled' => true],
            ['key' => 'search', 'value' => '', 'disabled' => true],
            ['key' => 'page', 'value' => '1'],
        ],
        'description' => publicMatch('/api/v1/media/news', 'Admin: media category=news. Public `image` = admin `imageUrl`.'),
        'tests' => imagePathTests('(pm.response.json().data || [])'),
    ]),
    req('List Newsletter', 'GET', '/api/v1/media/newsletter', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/media/newsletter', 'Admin: media category=newsletter.')]),
    req('List City Meetings', 'GET', '/api/v1/media/city-meetings', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/media/city-meetings', 'Admin: media category=city_meetings.')]),
    req('List Secretary Speaks', 'GET', '/api/v1/media/secretary-speaks', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/media/secretary-speaks', 'Admin: media category=secretary_speaks.'),
    ]),
    req('Get Media Article Detail', 'GET', '/api/v1/media/{{category}}/{{slug}}', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/media/{category}/{slug}', 'Public category uses hyphens; admin uses underscores. Response includes slugAr/slugEn for language switch.'),
    ]),
]);

$publicCareers = folder('Careers — اعمل معنا', [
    req('List Job Openings', 'GET', '/api/v1/careers', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/careers', 'Admin: job-openings.')]),
    req('Get Job Opening', 'GET', '/api/v1/careers/{{id}}', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/careers/{id}', 'Admin: job-openings/{id}.')]),
    req('Submit Job Application', 'POST', '/api/v1/careers/apply', [
        'headers' => [$localeHeader],
        'body' => [
            'jobOpeningId' => 1,
            'fullName' => 'سارة عبدالله',
            'email' => 'sara@example.com',
            'phone' => '+966501234567',
            'coverLetter' => 'لدي خبرة في التخطيط الحضري والعمل مع البلديات العربية.',
            'cvUrl' => 'https://example.com/cv.pdf',
        ],
        'description' => 'Public form submission. Admin review: job-applications.',
    ]),
]);

$publicFaq = folder('FAQ — الأسئلة الشائعة', [
    req('List FAQs', 'GET', '/api/v1/faqs', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'category', 'value' => '', 'disabled' => true, 'description' => 'membership | programs | general'],
        ],
        'description' => publicMatch('/api/v1/faqs', 'Admin: faqs CRUD.'),
    ]),
]);

$publicLegal = folder('Legal — الشروط والخصوصية', [
    req('Get Terms', 'GET', '/api/v1/legal/terms', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/legal/terms', 'Admin: legal slug=terms.')]),
    req('Get Privacy Policy', 'GET', '/api/v1/legal/privacy', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/legal/privacy', 'Admin: legal slug=privacy.')]),
]);

$publicForms = folder('Forms — النماذج', [
    req('Get Contact Info', 'GET', '/api/v1/contact', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/contact', 'Admin: settings group=contact (contact.title, contact.address, …).'),
    ]),
    req('Submit Contact Form', 'POST', '/api/v1/contact', [
        'headers' => [$localeHeader],
        'body' => [
            'name' => 'أحمد محمد',
            'phone' => '+966501234567',
            'email' => 'ahmed@example.com',
            'message' => 'استفسار عن برامج المعهد التدريبية والعضوية.',
        ],
        'description' => 'Admin review: contact-submissions.',
    ]),
    req('Submit Membership Application', 'POST', '/api/v1/membership', [
        'headers' => [$localeHeader],
        'body' => [
            'organizationName' => 'أمانة منطقة الرياض',
            'contactName' => 'محمد العلي',
            'email' => 'contact@example.gov.sa',
            'phone' => '+966114802555',
            'countryCode' => 'SA',
            'city' => 'الرياض',
            'message' => 'نرغب في الانضمام كعضو في المعهد العربي لإنماء المدن.',
        ],
        'description' => 'Admin review: membership-applications.',
    ]),
    req('Submit Portal Contribution', 'POST', '/api/v1/programs/urban-policies/contribute', [
        'headers' => [$localeHeader],
        'body' => [
            'type' => 'publications',
            'email' => 'researcher@example.com',
            'payload' => [
                'title' => 'دراسة حول التخطيط الحضري المستدام',
                'description' => 'بحث يتناول أفضل الممارسات في التخطيط الحضري للمدن العربية.',
                'author' => 'د. خالد أحمد',
                'year' => 2025,
            ],
        ],
        'description' => 'Admin review: portal-contributions. type: publications | cities | organizations.',
    ]),
    req('Subscribe Newsletter', 'POST', '/api/v1/newsletter/subscribe', [
        'headers' => [$localeHeader],
        'body' => ['email' => 'subscriber@example.com', 'locale' => 'ar'],
        'description' => 'Admin list: newsletter-subscriptions. Returns 201 (new) or 200 (existing).',
    ]),
]);

$public = folder('Public — /api/v1', [
    $publicHome,
    $publicSettings,
    $publicAbout,
    $publicStrategy,
    $publicPrograms,
    $publicResources,
    $publicMedia,
    $publicCareers,
    $publicFaq,
    $publicLegal,
    $publicForms,
], 'Public website API — locale via `Accept-Language: {{locale}}`. Returns single-language fields. Full MD: `docs/postman/PUBLIC-API.md`.');

// =============================================================================
// ADMIN — /api/admin (mirrors Public structure)
// =============================================================================

$adminAuth = postmanAdminFolder('المصادقة', 'Auth', [
    req('تسجيل الدخول — Login', 'POST', '/api/admin/auth/login', [
        'body' => ['email' => 'admin@araburban.org', 'password' => 'password'],
        'description' => 'يُرجع `{ token, user }`. احفظ `token` في متغير `adminToken`.',
    ]),
    req('تسجيل الخروج — Logout', 'POST', '/api/admin/auth/logout', ['auth' => true]),
    req('الملف الشخصي — Me', 'GET', '/api/admin/auth/me', ['auth' => true]),
]);

$adminUploads = postmanAdminFolder('رفع الملفات', 'Uploads', [
    req('عرض القائمة — List Uploads', 'GET', '/api/admin/uploads?page=1&limit=20', ['auth' => true]),
    req('رفع ملف — Upload File', 'POST', '/api/admin/uploads', [
        'auth' => true,
        'formdata' => [
            [
                'key' => 'file',
                'type' => 'file',
                'description' => 'مطلوب. الأنواع: jpg, jpeg, png, gif, webp, pdf. الحد الأقصى 10 MB.',
            ],
        ],
        'description' => <<<'MD'
**Content-Type:** `multipart/form-data` (not JSON).

| Field | Type | Rules |
|-------|------|-------|
| `file` | file | required, max 10MB, mimes: jpg,jpeg,png,gif,webp,pdf |

**Response 201:**
```json
{
  "data": {
    "id": 1,
    "url": "http://localhost:8000/storage/uploads/2026/06/uuid.png",
    "mimeType": "image/png",
    "originalName": "photo.png",
    "size": 12345,
    "disk": "public",
    "path": "uploads/2026/06/uuid.png",
    "uploadedBy": "Admin",
    "createdAt": "2026-06-25T12:00:00+00:00"
  }
}
```

In Postman: Body → form-data → key `file` → type **File** → choose a local image/PDF.
Use the returned **absolute** `data.url` in admin `imageUrl`, `logoUrl`, `fileUrl`, etc. Public endpoints return the same URL unchanged.
MD,
    ]),
    req('عرض — Show Upload', 'GET', '/api/admin/uploads/{{id}}', ['auth' => true]),
    req('حذف — Delete Upload', 'DELETE', '/api/admin/uploads/{{id}}', ['auth' => true]),
]);

// --- Settings (matches Public GET /api/v1/settings) ---
$adminSettingsGroup = postmanAdminFolder('الإعدادات', 'Settings', [
    postmanAdminFolder('إعدادات الموقع', 'Site Settings', adminCrud('settings', 'Site Setting', [
        'key' => 'site.name',
        'valueAr' => 'المعهد العربي لإنماء المدن',
        'valueEn' => 'Arab Urban Development Institute',
        'group' => 'general',
    ], [
        'withReorder' => false,
        'publicPath' => '/api/v1/settings',
        'labelAr' => 'إعداد الموقع',
        'description' => 'مفاتيح أخرى: `site.copyright`, `contact.title`, …',
        'updateBody' => [
            'key' => 'site.name',
            'valueAr' => 'المعهد العربي لإنماء المدن',
            'valueEn' => 'Arab Urban Development Institute',
            'group' => 'general',
        ],
    ])),
    postmanAdminFolder('روابط التواصل', 'Social Links', adminCrud('social-links', 'Social Link', [
        'platform' => 'linkedin',
        'url' => 'https://linkedin.com/company/audi',
        'icon' => 'linkedin',
        'isActive' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/settings', 'labelAr' => 'رابط تواصل'])),
    req('إنشاء إعداد تواصل (قديم) — Legacy Contact Setting', 'POST', '/api/admin/settings', [
        'auth' => true,
        'body' => [
            'key' => 'contact.title',
            'valueAr' => 'تواصل معنا',
            'valueEn' => 'Contact Us',
            'group' => 'contact',
        ],
        'description' => 'قديم: يُفضّل `GET/PUT /api/admin/contact-info` لجميع حقول التواصل.',
    ]),
    postmanAdminFolder('معلومات التواصل', 'Contact Info', [
        req('عرض — Get Contact Info', 'GET', '/api/admin/contact-info', [
            'auth' => true,
            'description' => publicMatch('/api/v1/contact', 'حقول ثنائية اللغة (`titleAr`, `itemsAr`, …).'),
        ]),
        req('تحديث — Update Contact Info', 'PUT', '/api/admin/contact-info', [
            'auth' => true,
            'body' => postmanHomeContactInfoBody(),
            'description' => publicMatch('/api/v1/contact', 'واجهة الإدارة المفضّلة لصفحة التواصل وتذييل الرئيسية. Same body in Home → عضوية وتواصل.'),
        ]),
    ]),
], publicMatch('/api/v1/settings'));

// --- Home (matches Public GET /api/v1/home) ---
$homeHeroSlideExamples = array_map(
    fn (array $slide, int $index) => req(
        'إنشاء شريحة '.($index + 1).' — Create Hero Slide '.($index + 1),
        'POST',
        '/api/admin/hero-slides',
        [
            'auth' => true,
            'body' => array_merge($slide, ['isActive' => true]),
            'description' => publicMatch('/api/v1/home', 'Homepage hero slider item → `slider[]`.'),
        ],
    ),
    postmanHomeHeroSlides(),
    array_keys(postmanHomeHeroSlides()),
);

$homeStatExamples = array_map(
    fn (array $stat, int $index) => req(
        'إنشاء إحصائية '.($index + 1).' — Create Home Stat '.($index + 1),
        'POST',
        '/api/admin/home-stats',
        [
            'auth' => true,
            'body' => $stat,
            'description' => publicMatch('/api/v1/home', 'Counter in «المعهد في أرقام» → `stats.items[]`. Pair with `home_stats` about-content for title/subtitle.'),
        ],
    ),
    postmanHomeStatItems(),
    array_keys(postmanHomeStatItems()),
);

$homeProgramExamples = array_map(
    fn (array $program) => req(
        'إنشاء برنامج '.$program['slug'].' — Create Program '.$program['slug'],
        'POST',
        '/api/admin/programs',
        [
            'auth' => true,
            'body' => $program,
            'description' => publicMatch('/api/v1/home', 'Homepage program card → `programs.items[]`. Full page: `GET /api/v1/programs/{slug}`.'),
        ],
    ),
    postmanHomeProgramCards(),
);

$homeKnowledgeResourceExamples = array_map(
    fn (array $resource, int $index) => req(
        'إنشاء مصدر مركز المعرفة '.($index + 1).' — Create Knowledge Center Resource '.($index + 1),
        'POST',
        '/api/admin/resources',
        [
            'auth' => true,
            'body' => $resource,
            'description' => publicMatch('/api/v1/home', 'Homepage card → `knowledgeCenter.categories[].items[]`. Set `knowledgeCategoryId` from `POST /api/admin/knowledge-categories`.'),
        ],
    ),
    postmanHomeKnowledgeCenterResources(),
    array_keys(postmanHomeKnowledgeCenterResources()),
);

$homeMediaNewsExamples = array_map(
    fn (array $article) => req(
        'إنشاء '.$article['labelAr'].' — Create News '.$article['name'],
        'POST',
        '/api/admin/media',
        [
            'auth' => true,
            'body' => $article['body'],
            'description' => publicMatch(
                '/api/v1/home',
                $article['homePlacement'].' Full news page: `GET /api/v1/media/news`. **Always** `"category": "news"`.',
            ),
        ],
    ),
    postmanHomeMediaNewsArticles(),
);

$homepageSetupRequests = array_merge(
    array_map(
        fn (array $slide, int $index) => req(
            sprintf('%02d — شريحة هيرو %d', $index + 1, $index + 1),
            'POST',
            '/api/admin/hero-slides',
            [
                'auth' => true,
                'body' => array_merge($slide, ['isActive' => true]),
                'description' => publicMatch('/api/v1/home', 'Step '.($index + 1).': Hero slider → `slider[]`.'),
            ],
        ),
        postmanHomeHeroSlides(),
        array_keys(postmanHomeHeroSlides()),
    ),
    [
        req('05 — عن المعهد — home_about_intro', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeAboutIntroBody(),
            'description' => publicMatch('/api/v1/home', 'Step 5: About block → `aboutIntro`.'),
        ]),
        req('06 — المعهد في أرقام (عنوان) — home_stats', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeStatsBody(),
            'description' => publicMatch('/api/v1/home', 'Step 6: Stats title/subtitle. Counters: steps 07–10.'),
        ]),
    ],
    array_map(
        fn (array $stat, int $index) => req(
            sprintf('%02d — إحصائية %d', 7 + $index, $index + 1),
            'POST',
            '/api/admin/home-stats',
            [
                'auth' => true,
                'body' => $stat,
                'description' => publicMatch('/api/v1/home', 'Step '.(7 + $index).': Counter → `stats.items[]`.'),
            ],
        ),
        postmanHomeStatItems(),
        array_keys(postmanHomeStatItems()),
    ),
    [
        req('11 — المدن الأعضاء (عنوان) — home_member_cities', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeMemberCitiesBody(),
            'description' => publicMatch('/api/v1/home', 'Step 11: Member cities title → `memberCities.title`.'),
        ]),
        req('12 — إحصائيات المدن — member-cities/stats', 'PUT', '/api/admin/member-cities/stats', [
            'auth' => true,
            'body' => postmanHomeMemberCityStatsBody(),
            'description' => publicMatch('/api/v1/home', 'Step 12: 12 دولة / 400 مدينة / 1240 عضو → `memberCities.stats[]`.'),
        ]),
        req('13 — مدينة على الخريطة (مثال الرياض) — member city', 'POST', '/api/admin/member-cities/cities', [
            'auth' => true,
            'body' => postmanHomeMemberCityExample(),
            'description' => publicMatch('/api/v1/home/member-cities', 'Step 13: Sample map pin. Repeat or use bulk import for all cities.'),
        ]),
        req('14 — برامجنا (عنوان) — home_programs', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeProgramsBody(),
            'description' => publicMatch('/api/v1/home', 'Step 14: Programs section title + CTA. Cards: steps 15–17.'),
        ]),
    ],
    array_map(
        fn (array $program, int $index) => req(
            sprintf('%02d — برنامج %s', 15 + $index, $program['slug']),
            'POST',
            '/api/admin/programs',
            [
                'auth' => true,
                'body' => $program,
                'description' => publicMatch('/api/v1/home', 'Step '.(15 + $index).': Program card → `programs.items[]`.'),
            ],
        ),
        postmanHomeProgramCards(),
        array_keys(postmanHomeProgramCards()),
    ),
    [
        req('18 — المركز الإعلامي (عناوين) — home_media_center', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeMediaCenterBody(),
            'description' => publicMatch('/api/v1/home', 'Step 18: Media section labels. News items: steps 19–24.'),
        ]),
    ],
    array_map(
        fn (array $article, int $index) => req(
            sprintf('%02d — خبر %s', 19 + $index, $article['name']),
            'POST',
            '/api/admin/media',
            [
                'auth' => true,
                'body' => $article['body'],
                'description' => publicMatch('/api/v1/home', 'Step '.(19 + $index).': News → `mediaCenter.featured` / `items`.'),
            ],
        ),
        array_slice(postmanHomeMediaNewsArticles(), 0, 6),
        array_keys(array_slice(postmanHomeMediaNewsArticles(), 0, 6)),
    ),
    [
        req('25 — مركز المعرفة (أزرار) — home_knowledge_center labels', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeKnowledgeCenterBody(),
            'description' => publicMatch('/api/v1/home', 'Step 25: Button labels (عرض الإصدار / تنزيل PDF). Categories: steps 26–28. Cards: steps 29–31.'),
        ]),
    ],
    array_map(
        fn (array $category, int $index) => req(
            sprintf('%02d — تصنيف %s', 26 + $index, $category['slug']),
            'POST',
            '/api/admin/knowledge-categories',
            [
                'auth' => true,
                'body' => $category,
                'description' => publicMatch('/api/v1/home', 'Step '.(26 + $index).': Category → `knowledgeCenter.categories[]`. Save response `id` (first category = 1 for steps 29–31).'),
            ],
        ),
        postmanHomeKnowledgeCategories(),
        array_keys(postmanHomeKnowledgeCategories()),
    ),
    array_map(
        fn (array $resource, int $index) => req(
            sprintf('%02d — مصدر %s', 29 + $index, $resource['slug']),
            'POST',
            '/api/admin/resources',
            [
                'auth' => true,
                'body' => $resource,
                'description' => publicMatch('/api/v1/home', 'Step '.(29 + $index).': Card linked via `knowledgeCategoryId` → `categories[].items[]`.'),
            ],
        ),
        postmanHomeKnowledgeCenterResources(),
        array_keys(postmanHomeKnowledgeCenterResources()),
    ),
    [
        req('32 — عضوية المعهد — home_membership_contact', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeMembershipContactBody(),
            'description' => publicMatch('/api/v1/home', 'Step 32: Membership block → `membershipContact.membership`.'),
        ]),
        req('33 — تواصل معنا (هاتف/عنوان) — contact-info', 'PUT', '/api/admin/contact-info', [
            'auth' => true,
            'body' => postmanHomeContactInfoBody(),
            'description' => publicMatch('/api/v1/home', 'Step 33: Phone, fax, email, address, map → `membershipContact.contact`. Verify: `GET /api/v1/home`.'),
        ]),
    ],
);

$adminHomeGroup = postmanAdminFolder('الرئيسية', 'Home', [
    postmanAdminFolder('00 — بناء الصفحة الرئيسية', '00 — Build Full Homepage', $homepageSetupRequests, <<<'MD'
Run **01 → 33** in order after Login. Bodies match https://audi-ten.vercel.app/ar.

Then verify: `GET /api/v1/home` with `Accept-Language: ar`.
MD),
    postmanAdminFolder('شرائح الهيرو', 'Hero Slides', array_merge(
        adminCrud('hero-slides', 'Hero Slide', array_merge(postmanHomeHeroSlides()[0], ['isActive' => true]), [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'شريحة الهيرو',
            'description' => 'Homepage hero slider. See also the 4 example create requests below (matches https://audi-ten.vercel.app/ar).',
        ]),
        $homeHeroSlideExamples,
    )),
    postmanAdminFolder('إحصائيات الرئيسية', 'Home Stats', array_merge(
        adminCrud('home-stats', 'Home Stat', postmanHomeStatItems()[0], [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'إحصائية',
            'description' => '«المعهد في أرقام» counters. Title/subtitle: `home_stats` in about-content.',
        ]),
        $homeStatExamples,
    )),
    postmanAdminFolder('محتوى أقسام الرئيسية', 'Home About Content', array_merge(
        adminCrud('about-content', 'About Section', postmanHomeAboutIntroBody(), [
            'withReorder' => false,
            'publicPath' => '/api/v1/home',
            'labelAr' => 'قسم محتوى',
            'description' => 'Homepage intro block → `aboutIntro`.',
        ]),
        [
            req('إنشاء قسم home_about_intro — Create home_about_intro', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeAboutIntroBody(),
                'description' => publicMatch('/api/v1/home', 'About block under hero: title, description, mission, vision → `aboutIntro`.'),
            ]),
            req('إنشاء قسم home_stats — Create home_stats', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeStatsBody(),
                'description' => publicMatch('/api/v1/home', 'Section title «المعهد في أرقام» + subtitle. Counters: `home-stats` CRUD.'),
            ]),
            req('إنشاء قسم home_member_cities — Create home_member_cities', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeMemberCitiesBody(),
                'description' => publicMatch('/api/v1/home', 'Section title «المدن الأعضاء». Stats values: `PUT /api/admin/member-cities/stats`.'),
            ]),
            req('إنشاء قسم home_programs — Create home_programs', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeProgramsBody(),
                'description' => publicMatch('/api/v1/home', 'Section title + CTA only. Cards: `POST /api/admin/programs` → `programs.items[]`.'),
            ]),
            req('إنشاء قسم home_media_center — Create home_media_center', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeMediaCenterBody(),
                'description' => publicMatch('/api/v1/home', 'Section title + subtitle + قراءة المزيد + عرض الكل. **News cards:** use folder «بطاقات المركز الإعلامي» → `POST /api/admin/media` (`category: news`).'),
            ]),
            req('إنشاء قسم home_knowledge_center — Create home_knowledge_center', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeKnowledgeCenterBody(),
                'description' => publicMatch('/api/v1/home', 'Button labels (عرض الإصدار / تنزيل PDF). **Categories:** `POST /api/admin/knowledge-categories`. **Cards:** `POST /api/admin/resources` with `knowledgeCategoryId`.'),
            ]),
            req('إنشاء قسم home_membership_contact — Create home_membership_contact', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => postmanHomeMembershipContactBody(),
                'description' => publicMatch('/api/v1/home', 'Membership block labels. Contact data: folder «عضوية وتواصل» → `PUT /api/admin/contact-info`.'),
            ]),
        ],
    )),
    postmanAdminFolder('بطاقات برامج الرئيسية', 'Home Program Cards', $homeProgramExamples),
    postmanAdminFolder('بطاقات المركز الإعلامي', 'Home Media Center News', $homeMediaNewsExamples),
    postmanAdminFolder('تصنيفات مركز المعرفة', 'Home Knowledge Categories', array_merge(
        adminCrud('knowledge-categories', 'Knowledge Category', postmanHomeKnowledgeCategories()[0], [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'تصنيف مركز المعرفة',
            'description' => 'Carousel tabs: مركز المعرفة / مدننا / منصة الاجتماعات → `knowledgeCenter.categories[]`.',
        ]),
        array_map(
            fn (array $category, int $index) => req(
                'إنشاء تصنيف '.($category['titleAr'] ?? $category['slug']).' — Create '.$category['slug'],
                'POST',
                '/api/admin/knowledge-categories',
                [
                    'auth' => true,
                    'body' => $category,
                    'description' => publicMatch('/api/v1/home', 'Category '.($index + 1).' of 3. Link resources with `knowledgeCategoryId` = response `id`.'),
                ],
            ),
            postmanHomeKnowledgeCategories(),
            array_keys(postmanHomeKnowledgeCategories()),
        ),
    )),
    postmanAdminFolder('بطاقات مركز المعرفة', 'Home Knowledge Center Cards', $homeKnowledgeResourceExamples),
    postmanAdminFolder('المدن الأعضاء — إعداد الخريطة', 'Home Member Cities Setup', [
        req('تحديث إحصائيات المدن — Update Member Cities Stats', 'PUT', '/api/admin/member-cities/stats', [
            'auth' => true,
            'body' => postmanHomeMemberCityStatsBody(),
            'description' => publicMatch('/api/v1/home', '12 دولة / 400 مدينة / 1240 عضو on homepage.'),
        ]),
        req('إنشاء مدينة (الرياض) — Create Sample City Riyadh', 'POST', '/api/admin/member-cities/cities', [
            'auth' => true,
            'body' => postmanHomeMemberCityExample(),
            'description' => publicMatch('/api/v1/home/member-cities', 'One map pin. For full map use `استيراد جماعي` in المدن الأعضاء folder.'),
        ]),
    ]),
    postmanAdminFolder('عضوية وتواصل', 'Home Membership & Contact', [
        req('إنشاء عضوية وتواصل — home_membership_contact', 'POST', '/api/admin/about-content', [
            'auth' => true,
            'body' => postmanHomeMembershipContactBody(),
            'description' => publicMatch('/api/v1/home', 'انضم الى عضوية المعهد + تواصل معنا labels.'),
        ]),
        req('تحديث بيانات التواصل — Update Contact Info', 'PUT', '/api/admin/contact-info', [
            'auth' => true,
            'body' => postmanHomeContactInfoBody(),
            'description' => publicMatch('/api/v1/home', 'الهاتف، فاكس، ايميل، كود، العنوان، الخريطة → `membershipContact.contact`.'),
        ]),
    ]),
], publicMatch('/api/v1/home', postmanHomepageSectionGuide()));

// --- About (matches Public /api/v1/about/*) ---
$adminAboutGroup = postmanAdminFolder('من نحن', 'About', [
    postmanAdminFolder('محتوى أقسام من نحن', 'About Content', array_merge(
        adminCrud('about-content', 'Institute Section', [
            'sectionKey' => 'institute',
            'titleAr' => 'المعهد العربي لإنماء المدن',
            'titleEn' => 'Arab Urban Development Institute',
            'bodyAr' => [
                'paragraphs' => [
                    'تأسس المعهد العربي لإنماء المدن بهدف تعزيز التنمية الحضرية المستدامة في العالم العربي.',
                    'يعمل المعهد على بناء قدرات البلديات والمؤسسات المحلية.',
                ],
                'headquartersTitle' => 'المقر الرئيسي',
            ],
            'bodyEn' => [
                'paragraphs' => [
                    'The Arab Urban Development Institute was established to promote sustainable urban development in the Arab world.',
                    'The institute builds the capacities of municipalities and local institutions.',
                ],
                'headquartersTitle' => 'Headquarters',
            ],
        ], ['withReorder' => false, 'publicPath' => '/api/v1/about/institute', 'labelAr' => 'قسم عن المعهد']),
        [
            req('إنشاء vision_mission — Create vision_mission', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => [
                    'sectionKey' => 'vision_mission',
                    'bodyAr' => [
                        'visionTitle' => 'رؤيتنا',
                        'visionText' => 'مدن عربية مزدهرة ومستدامة.',
                        'missionTitle' => 'رسالتنا',
                        'missionText' => 'تعزيز قدرات المدن العربية.',
                        'readMore' => 'اقرأ المزيد',
                        'visionImage' => '/vision-mission/1.png',
                        'missionImage' => '/vision-mission/2.png',
                    ],
                    'bodyEn' => [
                        'visionTitle' => 'Our Vision',
                        'visionText' => 'Thriving and sustainable Arab cities.',
                        'missionTitle' => 'Our Mission',
                        'missionText' => 'Enhancing Arab cities capacity.',
                        'readMore' => 'Read more',
                        'visionImage' => '/vision-mission/1.png',
                        'missionImage' => '/vision-mission/2.png',
                    ],
                ],
                'description' => publicMatch('/api/v1/about/vision-mission'),
            ]),
            req('إنشاء structure — Create structure', 'POST', '/api/admin/about-content', [
                'auth' => true,
                'body' => [
                    'sectionKey' => 'structure',
                    'imageUrl' => '/operational-structure.png',
                    'bodyAr' => ['imageAlt' => 'الهيكل التشغيلي للمعهد'],
                    'bodyEn' => ['imageAlt' => 'Institute operational structure'],
                ],
                'description' => publicMatch('/api/v1/about/structure'),
            ]),
        ],
    )),
    postmanAdminFolder('القيادة', 'Leadership', adminCrud('leadership', 'Leadership Message', [
        'type' => 'director',
        'honorificAr' => 'سعادة',
        'honorificEn' => 'His Excellency',
        'nameAr' => 'د. أنس المغيري',
        'nameEn' => 'Dr. Anas AlMugairi',
        'positionAr' => 'المدير العام',
        'positionEn' => 'Director General',
        'quoteAr' => 'نعمل على بناء مدن عربية أكثر مرونة واستدامة.',
        'quoteEn' => 'We work to build more resilient and sustainable Arab cities.',
        'paragraphsAr' => [
            'يسعدني أن أرحب بكم في موقع المعهد العربي لإنماء المدن.',
            'نسعى لتقديم حلول مبتكرة للتحديات الحضرية.',
        ],
        'paragraphsEn' => [
            'I am pleased to welcome you to the Arab Urban Development Institute website.',
            'We strive to provide innovative solutions to urban challenges.',
        ],
        'imageUrl' => '/emp/2.png',
        'imageAltAr' => 'صورة المدير العام',
        'imageAltEn' => 'Director General photo',
    ], ['withReorder' => false, 'publicPath' => '/api/v1/about/leadership/director', 'labelAr' => 'رسالة قيادة'])),
    postmanAdminFolder('المجلس الاستشاري', 'Advisory Board', adminCrud('advisory-board', 'Advisory Member', [
        'nameAr' => 'د. فيصل بن عبدالعزيز آل سعود',
        'nameEn' => 'Dr. Faisal bin Abdulaziz Al Saud',
        'roleAr' => 'رئيس المجلس الاستشاري',
        'roleEn' => 'Advisory Board Chair',
        'bioAr' => 'خبير في التخطيط الحضري والتنمية المستدامة بخبرة تزيد عن 30 عاماً.',
        'bioEn' => 'Expert in urban planning and sustainable development with over 30 years of experience.',
        'imageUrl' => '/emp/1.png',
        'isFeatured' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/about/advisory-board', 'labelAr' => 'عضو استشاري'])),
    postmanAdminFolder('أقسام الفريق', 'Team Sections', adminCrud('team-sections', 'Team Section', [
        'slug' => 'management',
        'titleAr' => 'الإدارة التنفيذية',
        'titleEn' => 'Executive Management',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/about/team', 'labelAr' => 'قسم فريق'])),
    postmanAdminFolder('أعضاء الفريق', 'Team Members', adminCrud('team-members', 'Team Member', [
        'teamSectionId' => 1,
        'nameAr' => 'د. أنس المغيري',
        'nameEn' => 'Dr. Anas AlMugairi',
        'roleAr' => 'المدير العام',
        'roleEn' => 'Director General',
        'bioAr' => 'يقود المعهد في تنفيذ استراتيجيته ورؤيته للتنمية الحضرية.',
        'bioEn' => 'Leads the institute in implementing its strategy and vision for urban development.',
        'imageUrl' => '/emp/3.png',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/about/team', 'labelAr' => 'عضو فريق'])),
    postmanAdminFolder('تصنيفات الشركاء', 'Partner Categories', adminCrud('partner-categories', 'Partner Category', [
        'slug' => 'international',
        'titleAr' => 'المؤسسات الدولية',
        'titleEn' => 'International Organizations',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/about/partners', 'labelAr' => 'تصنيف شريك'])),
    postmanAdminFolder('الشركاء', 'Partners', adminCrud('partners', 'Partner', [
        'partnerCategoryId' => 1,
        'nameAr' => 'برنامج الأمم المتحدة للمستوطنات البشرية (UN-Habitat)',
        'nameEn' => 'UN-Habitat',
        'logoUrl' => '/client/un-habitat.png',
        'isFeatured' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/about/partners', 'labelAr' => 'شريك'])),
], publicMatch('/api/v1/about/institute'));

// --- Strategy ---
$adminStrategyGroup = postmanAdminFolder('الاستراتيجية', 'Strategy', [
    postmanAdminFolder('صفحة الاستراتيجية', 'Strategy Page', [
        req('عرض — Get Strategy Page', 'GET', '/api/admin/strategy', [
            'auth' => true,
            'description' => publicMatch('/api/v1/strategy/strategy-2025'),
        ]),
        req('تحديث — Update Strategy Page', 'PUT', '/api/admin/strategy', [
            'auth' => true,
            'body' => [
                'introTitleAr' => 'استراتيجية المعهد 2025-2026',
                'introTitleEn' => 'Institute Strategy 2025-2026',
                'introSubtitleAr' => 'خارطة طريق للتنمية الحضرية العربية',
                'introSubtitleEn' => 'A roadmap for Arab urban development',
                'bookletTitleAr' => 'الكتيب الاستراتيجي',
                'bookletTitleEn' => 'Strategy Booklet',
                'bookletPdfUrl' => '/storage/strategy/AUDI-Strategy.pdf',
            ],
            'description' => publicMatch('/api/v1/strategy/strategy-2025', 'Full bilingual strategy page update.'),
        ]),
    ]),
    postmanAdminFolder('محاور الاستراتيجية', 'Strategy Pillars', adminCrud('strategy-pillars', 'Strategy Pillar', [
        'number' => '01',
        'textAr' => 'تعزيز قدرات المؤسسات المحلية',
        'textEn' => 'Enhancing local institutions capacity',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/strategy/strategy-2025', 'labelAr' => 'محور استراتيجي'])),
    postmanAdminFolder('مخطط الاستراتيجية', 'Strategy Diagram', adminCrud('strategy-diagram', 'Diagram Item', [
        'itemKey' => 'vision',
        'titleAr' => 'الرؤية',
        'titleEn' => 'Vision',
        'contentAr' => 'مدن عربية مزدهرة ومستدامة.',
        'contentEn' => 'Thriving and sustainable Arab cities.',
        'columnsAr' => null,
        'columnsEn' => null,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/strategy/strategy-2025', 'labelAr' => 'عنصر مخطط'])),
    postmanAdminFolder('مجالات التركيز', 'Focus Areas', adminCrud('focus-areas', 'Focus Area', [
        'slug' => 'urban-resilience',
        'number' => '02',
        'titleAr' => 'المرونة الحضرية',
        'titleEn' => 'Urban Resilience',
        'highlightAr' => 'تخضير المدن',
        'highlightEn' => 'Green Cities',
        'tagsAr' => ['تخضير المدن', 'الاستدامة'],
        'tagsEn' => ['Green Cities', 'Sustainability'],
        'descriptionAr' => 'يعزز هذا المحور قدرة المدن العربية على مواجهة التحديات المناخية والبيئية.',
        'descriptionEn' => 'This focus area enhances Arab cities capacity to address climate and environmental challenges.',
        'listImageUrl' => '/focus-areas/urban-resilience-list.png',
        'detailImageUrl' => '/focus-areas/urban-resilience-detail.png',
        'isPublished' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/strategy/focus-areas', 'labelAr' => 'مجال تركيز'])),
    req('إنشاء focus_areas_pages — Create focus_areas_pages', 'POST', '/api/admin/about-content', [
        'auth' => true,
        'body' => [
            'sectionKey' => 'focus_areas_pages',
            'bodyAr' => [
                'title' => 'محاور الاستراتيجية',
                'back' => 'العودة',
                'viewMore' => 'عرض المزيد',
                'previous' => 'السابق',
                'next' => 'التالي',
            ],
            'bodyEn' => [
                'title' => 'Strategy Focus Areas',
                'back' => 'Back',
                'viewMore' => 'View more',
                'previous' => 'Previous',
                'next' => 'Next',
            ],
        ],
        'description' => publicMatch('/api/v1/strategy/focus-areas', 'Page chrome labels for focus areas list/detail.'),
    ]),
], publicMatch('/api/v1/strategy/strategy-2025'));

// --- Programs ---
$trainingProgramBuildSteps = [
    req('01 — برنامج التدريب — training program', 'POST', '/api/admin/programs', [
        'auth' => true,
        'body' => postmanTrainingProgramBody(),
        'description' => publicMatch('/api/v1/programs/training', 'Step 1: Program category. Save response `id` (usually 2). URL: /ar/برامجنا/مركز-دعم-المدن'),
    ]),
    req('02 — تسميات الصفحة — program_training labels', 'POST', '/api/admin/about-content', [
        'auth' => true,
        'body' => postmanProgramTrainingLabelsBody(),
        'description' => publicMatch('/api/v1/programs/training', 'Step 2: back + sectionsLabel (اقسام البرنامج).'),
    ]),
];

foreach (postmanTrainingProgramSections() as $index => $section) {
    $trainingProgramBuildSteps[] = req(
        sprintf('0%d — قسم %s — %s', 3 + $index, $section['body']['tabKey'], $section['name']),
        'POST',
        '/api/admin/program-sections',
        [
            'auth' => true,
            'body' => $section['body'],
            'description' => publicMatch('/api/v1/programs/training', 'Step '.(3 + $index).': `?tab='.$section['body']['tabKey'].'` → `sections.'.$section['body']['tabKey'].'`. Requires `programId` from step 1.'),
        ],
    );
}

foreach (postmanTrainingCourses() as $index => $course) {
    $step = 7 + $index;
    $trainingProgramBuildSteps[] = req(
        sprintf('%02d — دورة تدريبية — %s', $step, $course['titleAr']),
        'POST',
        '/api/admin/training-courses',
        [
            'auth' => true,
            'body' => $course,
            'description' => publicMatch('/api/v1/programs/training', 'Step '.$step.': Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.'),
        ],
    );
}

foreach (postmanTrainingExperts() as $index => $expert) {
    $step = 13 + $index;
    $trainingProgramBuildSteps[] = req(
        sprintf('%02d — خبير — %s', $step, $expert['nameAr']),
        'POST',
        '/api/admin/experts',
        [
            'auth' => true,
            'body' => $expert,
            'description' => publicMatch('/api/v1/programs/training', 'Step '.$step.': Expert card on ?tab=experts carousel. Merged into `sections.experts.experts[]`.'),
        ],
    );
}

$adminProgramsGroup = postmanAdminFolder('البرامج', 'Programs', [
    postmanAdminFolder('00 — بناء برنامج التدريب', '00 — Build Training Program', $trainingProgramBuildSteps, <<<'MD'
Build [مركز دعم المدن / training](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن) to match live tab pages:

01 program → 02 labels → **03–06** four `program-sections` (with `imageUrl` + image fields in `bodyAr`) → **07–12** six `training-courses` → **13–15** three `experts` (each with `imageUrl`).

Verify: `GET /api/v1/programs/training`.
MD),
    postmanAdminFolder('البرامج', 'Programs CRUD', adminCrud('programs', 'Program', postmanHomeProgramCards()[0], [
        'withReorder' => false,
        'publicPath' => '/api/v1/home',
        'labelAr' => 'برنامج',
        'description' => 'يغذي أيضاً home.programs.items. See «بطاقات برامج الرئيسية» for all 3 homepage programs. التفاصيل: GET /api/v1/programs/{slug}.',
    ])),
    postmanAdminFolder('أقسام البرنامج', 'Program Sections', array_merge(
        adminCrud('program-sections', 'Program Section', postmanTrainingProgramSections()[0]['body'], [
            'publicPath' => '/api/v1/programs/training',
            'labelAr' => 'قسم برنامج',
            'description' => 'Link section to program via **`programId`**. Training tabs: trainingPrograms, consulting, executive, experts.',
        ]),
        array_map(
            fn (array $section) => req(
                'إنشاء قسم '.$section['body']['tabKey'].' — Create '.$section['name'],
                'POST',
                '/api/admin/program-sections',
                [
                    'auth' => true,
                    'body' => $section['body'],
                    'description' => publicMatch('/api/v1/programs/training', 'Example for tab `'.$section['body']['tabKey'].'`. Set `programId` from program create response.'),
                ],
            ),
            postmanTrainingProgramSections(),
        ),
    )),
    postmanAdminFolder('الدورات التدريبية', 'Training Courses', array_merge(
        adminCrud('training-courses', 'Training Course', postmanTrainingCourses()[0], [
            'publicPath' => '/api/v1/programs/training',
            'labelAr' => 'دورة تدريبية',
            'description' => 'Rows under `coursesTitle` on ?tab=trainingPrograms. See folder «00 — بناء برنامج التدريب» steps 07–12.',
        ]),
        array_map(
            fn (array $course) => req(
                'إنشاء دورة — '.$course['titleAr'],
                'POST',
                '/api/admin/training-courses',
                [
                    'auth' => true,
                    'body' => $course,
                    'description' => publicMatch('/api/v1/programs/training', 'Example course for trainingPrograms tab grid.'),
                ],
            ),
            postmanTrainingCourses(),
        ),
    )),
    postmanAdminFolder('الخبراء', 'Experts', array_merge(
        adminCrud('experts', 'Expert', postmanTrainingExperts()[0], [
            'publicPath' => '/api/v1/programs/training',
            'labelAr' => 'خبير',
            'description' => 'Carousel cards on ?tab=experts. See folder «00 — بناء برنامج التدريب» steps 13–15.',
        ]),
        array_map(
            fn (array $expert) => req(
                'إنشاء خبير — '.$expert['nameAr'],
                'POST',
                '/api/admin/experts',
                [
                    'auth' => true,
                    'body' => $expert,
                    'description' => publicMatch('/api/v1/programs/training', 'Example expert for experts tab carousel.'),
                ],
            ),
            postmanTrainingExperts(),
        ),
    )),
    postmanAdminFolder('دليل المدن', 'Directory Cities', adminCrud('directory/cities', 'Directory City', [
        'number' => '01',
        'nameAr' => 'الرياض، المملكة العربية السعودية',
        'nameEn' => 'Riyadh, Saudi Arabia',
        'descriptionAr' => 'عاصمة المملكة وواحدة من أكبر المدن في المنطقة.',
        'descriptionEn' => 'Capital of the Kingdom and one of the largest cities in the region.',
        'countryCode' => 'SA',
        'citySize' => 'large',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory', 'labelAr' => 'مدينة'])),
    postmanAdminFolder('دليل المشاريع', 'Directory Projects', adminCrud('directory/projects', 'Directory Project', [
        'number' => '01',
        'cityAr' => 'الرياض',
        'cityEn' => 'Riyadh',
        'countryAr' => 'المملكة العربية السعودية',
        'countryEn' => 'Saudi Arabia',
        'startDate' => '2019',
        'endDate' => '2023',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory', 'labelAr' => 'مشروع'])),
    postmanAdminFolder('دليل المنظمات', 'Directory Organizations', adminCrud('directory/organizations', 'Directory Organization', [
        'number' => '01',
        'nameAr' => 'برنامج الأمم المتحدة للمستوطنات البشرية',
        'nameEn' => 'UN-Habitat',
        'descriptionAr' => 'منظمة الأمم المتحدة المعنية بالمستوطنات البشرية.',
        'descriptionEn' => 'UN agency for human settlements.',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory', 'labelAr' => 'منظمة'])),
    postmanAdminFolder('دليل المنشورات', 'Directory Publications', adminCrud('directory/publications', 'Directory Publication', [
        'number' => '01',
        'nameAr' => 'تقرير التنمية الحضرية العربية 2024',
        'nameEn' => 'Arab Urban Development Report 2024',
        'descriptionAr' => 'تقرير سنوي يرصد التطورات في التنمية الحضرية.',
        'descriptionEn' => 'Annual report monitoring urban development trends.',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory', 'labelAr' => 'منشور'])),
    req('إنشاء program_training — Create program_training labels', 'POST', '/api/admin/about-content', [
        'auth' => true,
        'body' => postmanProgramTrainingLabelsBody(),
        'description' => publicMatch('/api/v1/programs/training', 'Navigation labels: back + sectionsLabel (اقسام البرنامج).'),
    ]),
], publicMatch('/api/v1/programs/training'));

// --- Resources ---
$adminResourcesGroup = postmanAdminFolder('المصادر', 'Resources', [
    postmanAdminFolder('المصادر', 'Resources CRUD', adminCrud('resources', 'Resource', postmanHomeKnowledgeCenterResources()[2], [
        'publicPath' => '/api/v1/resources',
        'labelAr' => 'مصدر',
        'description' => 'Resources page + homepage knowledge center cards per category. See «تصنيفات مركز المعرفة» + «بطاقات مركز المعرفة» under Home.',
    ])),
], publicMatch('/api/v1/resources'));

// --- Media ---
$adminMediaGroup = postmanAdminFolder('المركز الإعلامي', 'Media', [
    postmanAdminFolder('المقالات الإعلامية', 'Media Articles', adminCrud('media', 'Media Article', postmanHomeMediaNewsArticles()[0]['body'], [
        'publicPath' => '/api/v1/media/news',
        'labelAr' => 'مقال إعلامي',
        'description' => 'Homepage news: see «بطاقات المركز الإعلامي» under Home for all 6 example bodies from https://audi-ten.vercel.app/ar.',
    ])),
    req('إنشاء نشرة — Create Newsletter Article', 'POST', '/api/admin/media', [
        'auth' => true,
        'body' => [
            'category' => 'newsletter',
            'key' => 'newsletter-issue-12',
            'slugAr' => 'النشرة-الإخبارية-12',
            'slugEn' => 'newsletter-issue-12',
            'titleAr' => 'النشرة الإخبارية - العدد 12',
            'titleEn' => 'Newsletter - Issue 12',
            'descriptionAr' => null,
            'descriptionEn' => null,
            'bodyAr' => ['محتوى العدد باللغة العربية.'],
            'bodyEn' => ['Issue content in English.'],
            'publishedDate' => '2025-06-01',
            'imageUrl' => '/blog/2.png',
            'pdfUrl' => '/storage/newsletter/issue-12.pdf',
            'isPublished' => true,
            'sortOrder' => 0,
        ],
        'description' => publicMatch('/api/v1/media/newsletter', 'Admin category uses underscore: newsletter. Public URL uses hyphen: /media/newsletter.'),
    ]),
    req('إنشاء لقاء مدينة — Create City Meeting', 'POST', '/api/admin/media', [
        'auth' => true,
        'body' => [
            'category' => 'city_meetings',
            'key' => 'riyadh-city-meeting-2025',
            'slugAr' => 'لقاء-مدينة-الرياض-2025',
            'slugEn' => 'riyadh-city-meeting-2025',
            'titleAr' => 'لقاء مدينة الرياض 2025',
            'titleEn' => 'Riyadh City Meeting 2025',
            'descriptionAr' => 'لقاء سنوي يجمع ممثلي المدن العربية.',
            'descriptionEn' => 'Annual meeting gathering representatives of Arab cities.',
            'bodyAr' => ['تناول اللقاء موضوعات التخطيط الحضري والتنمية المستدامة في المدن العربية.'],
            'bodyEn' => ['The meeting covered urban planning and sustainable development in Arab cities.'],
            'publishedDate' => '2025-03-15',
            'imageUrl' => '/blog/3.png',
            'authorsAr' => ['فريق المركز الإعلامي'],
            'authorsEn' => ['Media Center Team'],
            'eventTime' => '10:00 - 14:00',
            'isPublished' => true,
            'sortOrder' => 0,
        ],
        'description' => publicMatch('/api/v1/media/city-meetings', 'Admin: city_meetings. Public: city-meetings.'),
    ]),
], publicMatch('/api/v1/media/news'));

// --- Careers ---
$adminCareersGroup = postmanAdminFolder('الوظائف', 'Careers', [
    postmanAdminFolder('الوظائف الشاغرة', 'Job Openings', adminCrud('job-openings', 'Job Opening', [
        'titleAr' => 'باحث في السياسات الحضرية',
        'titleEn' => 'Urban Policy Researcher',
        'locationAr' => 'الرياض، المملكة العربية السعودية',
        'locationEn' => 'Riyadh, Saudi Arabia',
        'employmentType' => 'full_time',
        'summaryAr' => 'نبحث عن باحث متخصص في السياسات الحضرية للانضمام لفريق البحث.',
        'summaryEn' => 'We are looking for an urban policy researcher to join our research team.',
        'descriptionAr' => [
            'إعداد دراسات وبحوث في مجال السياسات الحضرية.',
            'المشاركة في ورش العمل والمؤتمرات.',
            'التعاون مع البلديات والمؤسسات الشريكة.',
        ],
        'descriptionEn' => [
            'Prepare studies and research in urban policy.',
            'Participate in workshops and conferences.',
            'Collaborate with municipalities and partner institutions.',
        ],
        'isPublished' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/careers', 'labelAr' => 'وظيفة'])),
], publicMatch('/api/v1/careers'));

// --- FAQ ---
$adminFaqGroup = postmanAdminFolder('الأسئلة الشائعة', 'FAQ', [
    postmanAdminFolder('الأسئلة', 'FAQs', adminCrud('faqs', 'FAQ', [
        'category' => 'membership',
        'questionAr' => 'كيف يمكنني الانضمام للمعهد كعضو؟',
        'questionEn' => 'How can I join the institute as a member?',
        'answerAr' => 'يمكنكم الانضمام عبر تعبئة نموذج العضوية في صفحة التواصل، وسيتواصل معكم فريق المعهد.',
        'answerEn' => 'You can join by filling out the membership form on the contact page. Our team will get in touch.',
        'isPublished' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/faqs', 'labelAr' => 'سؤال'])),
], publicMatch('/api/v1/faqs'));

// --- Legal ---
$adminLegalGroup = postmanAdminFolder('الصفحات القانونية', 'Legal', [
    postmanAdminFolder('الشروط والخصوصية', 'Legal Pages', adminCrud('legal', 'Legal Page', [
        'slug' => 'terms',
        'titleAr' => 'الشروط والأحكام',
        'titleEn' => 'Terms and Conditions',
        'contentAr' => 'مرحباً بكم في موقع المعهد العربي لإنماء المدن. باستخدامكم لهذا الموقع، فإنكم توافقون على الشروط والأحكام التالية...',
        'contentEn' => 'Welcome to the Arab Urban Development Institute website. By using this site, you agree to the following terms and conditions...',
        'effectiveDate' => '2026-01-01',
    ], ['withReorder' => false, 'publicPath' => '/api/v1/legal/terms', 'labelAr' => 'صفحة قانونية'])),
], publicMatch('/api/v1/legal/terms'));

// --- Member Cities ---
$adminMemberCities = postmanAdminFolder('المدن الأعضاء', 'Member Cities', [
    req('عرض قائمة المدن — List Cities', 'GET', '/api/admin/member-cities/cities?page=1&limit=20', [
        'auth' => true,
        'query' => [
            ['key' => 'page', 'value' => '1'],
            ['key' => 'limit', 'value' => '20'],
            ['key' => 'countryCode', 'value' => 'SA', 'disabled' => true],
        ],
        'description' => publicMatch('/api/v1/home/member-cities'),
    ]),
    req('عرض مدينة — Show City', 'GET', '/api/admin/member-cities/cities/{{id}}', ['auth' => true]),
    req('إنشاء مدينة — Create City', 'POST', '/api/admin/member-cities/cities', [
        'auth' => true,
        'body' => [
            'countryCode' => 'SA',
            'nameAr' => 'الرياض',
            'nameEn' => 'Riyadh',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'infoAr' => 'عاصمة المملكة العربية السعودية',
            'infoEn' => 'Capital of Saudi Arabia',
            'imageUrl' => null,
            'isActive' => true,
        ],
        'description' => 'Public GeoJSON uses locale-resolved name/info.',
    ]),
    req('تحديث مدينة — Update City', 'PATCH', '/api/admin/member-cities/cities/{{id}}', [
        'auth' => true,
        'body' => [
            'nameAr' => 'الرياض',
            'nameEn' => 'Riyadh',
            'infoAr' => 'عاصمة المملكة',
            'infoEn' => 'Kingdom capital',
            'isActive' => true,
        ],
    ]),
    req('حذف مدينة — Delete City', 'DELETE', '/api/admin/member-cities/cities/{{id}}', ['auth' => true]),
    req('عرض الإحصائيات — Get Stats', 'GET', '/api/admin/member-cities/stats', [
        'auth' => true,
        'description' => publicMatch('/api/v1/home/member-cities', 'Returns bilingual label/unit per stat key.'),
    ]),
    req('تحديث الإحصائيات — Update Stats', 'PUT', '/api/admin/member-cities/stats', [
        'auth' => true,
        'body' => postmanHomeMemberCityStatsBody(),
        'description' => publicMatch('/api/v1/home/member-cities', 'Homepage «المدن الأعضاء»: 12 دولة / 400 مدينة / 1240 عضو. Stats use nested label.ar/en and unit.ar/en.'),
    ]),
    req('عرض الدول — List Countries', 'GET', '/api/admin/member-cities/countries', ['auth' => true]),
    req('استيراد جماعي — Bulk Import Cities', 'POST', '/api/admin/member-cities/cities/import', [
        'auth' => true,
        'body' => [
            'cities' => [
                [
                    'countryCode' => 'SA',
                    'nameAr' => 'الرياض',
                    'nameEn' => 'Riyadh',
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                    'infoAr' => 'عاصمة المملكة',
                    'infoEn' => 'Kingdom capital',
                    'isActive' => true,
                ],
                [
                    'countryCode' => 'AE',
                    'nameAr' => 'دبي',
                    'nameEn' => 'Dubai',
                    'latitude' => 25.2048,
                    'longitude' => 55.2708,
                    'isActive' => true,
                ],
            ],
            'upsertBy' => ['countryCode', 'nameEn'],
        ],
    ]),
    req('استيراد من ملف — Import Cities from File', 'POST', '/api/admin/member-cities/cities/import-from-file', [
        'auth' => true,
        'formdata' => [
            [
                'key' => 'file',
                'type' => 'file',
                'description' => 'GeoJSON file with member cities data.',
            ],
        ],
        'description' => '**Content-Type:** `multipart/form-data`. Upload a GeoJSON file to bulk-import cities.',
    ]),
], publicMatch('/api/v1/home/member-cities'));

// --- Form Submissions ---
$adminSubmissions = postmanAdminFolder('النماذج الواردة', 'Form Submissions', [
    req('عرض رسائل التواصل — List Contact Submissions', 'GET', '/api/admin/contact-submissions?page=1', [
        'auth' => true,
        'description' => 'From public POST /api/v1/contact.',
    ]),
    req('Show Contact Submission', 'GET', '/api/admin/contact-submissions/{{id}}', ['auth' => true]),
    req('Update Contact Status', 'PATCH', '/api/admin/contact-submissions/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'read'],
        'description' => 'status: new | read | archived',
    ]),
    req('Delete Contact Submission', 'DELETE', '/api/admin/contact-submissions/{{id}}', ['auth' => true]),
    req('List Membership Applications', 'GET', '/api/admin/membership-applications?page=1', [
        'auth' => true,
        'description' => 'From public POST /api/v1/membership.',
    ]),
    req('Show Membership Application', 'GET', '/api/admin/membership-applications/{{id}}', ['auth' => true]),
    req('Update Membership Status', 'PATCH', '/api/admin/membership-applications/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'reviewing'],
        'description' => 'status: new | reviewing | approved | rejected',
    ]),
    req('Delete Membership Application', 'DELETE', '/api/admin/membership-applications/{{id}}', ['auth' => true]),
    req('List Portal Contributions', 'GET', '/api/admin/portal-contributions?page=1', [
        'auth' => true,
        'description' => 'From public POST /api/v1/programs/urban-policies/contribute.',
    ]),
    req('Show Portal Contribution', 'GET', '/api/admin/portal-contributions/{{id}}', ['auth' => true]),
    req('Update Portal Contribution Status', 'PATCH', '/api/admin/portal-contributions/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'approved'],
        'description' => 'status: new | reviewing | approved | rejected',
    ]),
    req('Delete Portal Contribution', 'DELETE', '/api/admin/portal-contributions/{{id}}', ['auth' => true]),
    req('List Job Applications', 'GET', '/api/admin/job-applications?page=1', [
        'auth' => true,
        'query' => [
            ['key' => 'page', 'value' => '1'],
            ['key' => 'status', 'value' => '', 'disabled' => true, 'description' => 'new|reviewing|shortlisted|rejected|hired'],
        ],
        'description' => 'From public POST /api/v1/careers/apply.',
    ]),
    req('Show Job Application', 'GET', '/api/admin/job-applications/{{id}}', ['auth' => true]),
    req('Update Job Application Status', 'PATCH', '/api/admin/job-applications/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'reviewing'],
        'description' => 'status: new | reviewing | shortlisted | rejected | hired',
    ]),
    req('Delete Job Application', 'DELETE', '/api/admin/job-applications/{{id}}', ['auth' => true]),
    req('List Newsletter Subscriptions', 'GET', '/api/admin/newsletter-subscriptions?page=1', [
        'auth' => true,
        'description' => 'From public POST /api/v1/newsletter/subscribe.',
    ]),
    req('Show Newsletter Subscription', 'GET', '/api/admin/newsletter-subscriptions/{{id}}', ['auth' => true]),
    req('Delete Newsletter Subscription', 'DELETE', '/api/admin/newsletter-subscriptions/{{id}}', ['auth' => true]),
]);

$admin = postmanAdminFolder('لوحة التحكم', 'Admin /api/admin', [
    $adminAuth,
    $adminUploads,
    $adminSettingsGroup,
    $adminHomeGroup,
    $adminAboutGroup,
    $adminStrategyGroup,
    $adminProgramsGroup,
    $adminResourcesGroup,
    $adminMediaGroup,
    $adminCareersGroup,
    $adminFaqGroup,
    $adminLegalGroup,
    $adminMemberCities,
    $adminSubmissions,
], 'واجهة الإدارة — تخزين حقول *Ar/*En. راجع docs/postman/ADMIN-API.md. جميع الطلبات ترسل Accept-Language: {{locale}}.');

postmanEnhancePublicFolderDescriptions($public);
postmanEnhanceAdminFolderDescriptions($admin);

$collection['item'] = [$public, $admin];

$json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, 'JSON encode failed: ' . json_last_error_msg() . "\n");
    exit(1);
}

file_put_contents($output, $json . "\n");
echo "Written: {$output}\n";

$publicMarkdown = postmanGeneratePublicApiMarkdown($public);
file_put_contents($publicDocsOutput, $publicMarkdown);
echo "Written: {$publicDocsOutput}\n";

$adminMarkdown = postmanGenerateAdminApiMarkdown($admin);
file_put_contents($adminDocsOutput, $adminMarkdown);
echo "Written: {$adminDocsOutput}\n";

$apiReadme = postmanGenerateApiReadmeMarkdown();
file_put_contents($apiReadmeOutput, $apiReadme);
echo "Written: {$apiReadmeOutput}\n";

$apiErrorsMarkdown = postmanGenerateApiErrorsMarkdown();
file_put_contents($apiErrorsOutput, $apiErrorsMarkdown);
echo "Written: {$apiErrorsOutput}\n";

$homepageGuideMarkdown = postmanGenerateHomepageAdminGuideMarkdown();
file_put_contents($homepageGuideOutput, $homepageGuideMarkdown);
echo "Written: {$homepageGuideOutput}\n";

$programsGuideMarkdown = postmanGenerateProgramsAdminGuideMarkdown();
file_put_contents($programsGuideOutput, $programsGuideMarkdown);
echo "Written: {$programsGuideOutput}\n";
