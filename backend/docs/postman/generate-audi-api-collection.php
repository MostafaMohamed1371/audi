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
require __DIR__ . '/postman-public-site-map.php';

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

/** Public request — Arabic name first for admin users. */
function publicReq(string $nameAr, string $nameEn, string $method, string $path, array $opts = []): array
{
    return req("{$nameAr} — {$nameEn}", $method, $path, $opts);
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
    $idVar = $opts['idVariable'] ?? 'id';
    $idPlaceholder = '{{'.$idVar.'}}';

    $descPrefix = $publicPath ? publicMatch($publicPath, $extraDesc) : ($extraDesc !== '' ? $extraDesc : '');

    $listQuery = $opts['listQuery'] ?? [
        ['key' => 'page', 'value' => '1'],
        ['key' => 'limit', 'value' => '20'],
        ['key' => 'search', 'value' => '', 'disabled' => true],
    ];

    $createReqOpts = [
        'auth' => true,
        'body' => $createBody,
        'description' => $descPrefix . '**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.',
    ];
    if (! empty($opts['saveIdVariable'])) {
        $createReqOpts['tests'] = postmanSaveCollectionIdTests($opts['saveIdVariable']);
    }

    $items = [
        req("عرض القائمة — List {$label}", 'GET', $base, [
            'auth' => true,
            'query' => $listQuery,
            'description' => $descPrefix . 'قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.',
        ]),
        req("إنشاء — Create {$labelAr}", 'POST', $base, $createReqOpts),
        req("عرض — Show {$labelAr}", 'GET', $base . '/'.$idPlaceholder, [
            'auth' => true,
            'description' => $descPrefix . 'استخدم `'.$idVar.'` من استجابة الإنشاء.',
        ]),
        req("تحديث — Update {$labelAr}", 'PUT', $base . '/'.$idPlaceholder, [
            'auth' => true,
            'body' => $updateBody,
            'description' => $descPrefix . '**جسم التحديث كامل** — نفس حقول الإنشاء.',
        ]),
        req("حذف — Delete {$labelAr}", 'DELETE', $base . '/'.$idPlaceholder, [
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
# واجهة برمجة المعهد — Public + Admin

> **المعهد العربي لإنماء المدن** — مرجع Postman للموقع [audi-ten.vercel.app/ar](https://audi-ten.vercel.app/ar)

## المتغيرات | Variables
| المتغير | مثال | الغرض |
|---------|------|--------|
| `baseUrl` | `http://localhost:8000` | عنوان الخادم |
| `locale` | `ar` أو `en` | اللغة لجميع الطلبات |
| `adminToken` | Bearer token | بعد تسجيل الدخول |
| `programId`, `programSectionId` | — | سلسلة FK لبناء البرامج |

## نموذج اللغات
- **Public `/api/v1/*`**: حقل واحد لكل لغة — `{ "title": "..." }` (ليس `titleAr`/`titleEn`).
- **Admin `/api/admin/*`**: تخزين ثنائي — `{ "titleAr": "...", "titleEn": "..." }`.
- غيّر `locale` أو `Accept-Language: ar|en`.

## Admin ↔ Public (ملخص)
| Public | Admin |
|--------|-------|
| `GET /api/v1/home` | hero-slides, home-stats, about-content (home_*), programs, media, resources, contact-info |
| `GET /api/v1/settings` | settings, social-links |
| `GET /api/v1/about/*` | about-content, leadership, advisory-board, team-*, partners |
| `GET /api/v1/strategy/*` | strategy, strategy-pillars, focus-areas, about-content |
| `GET /api/v1/programs/*` | programs, program-sections, program-section-details, training-courses, experts, directory/* |
| `GET /api/v1/resources` | resources, knowledge-categories |
| `GET /api/v1/media/*` | media |
| `GET /api/v1/contact` | contact-info |
| نماذج POST | contact-submissions, membership-applications, … |

## دليل سريع للمسؤول (Admin)
1. **تسجيل الدخول** → `المصادقة` → احفظ `token` في `adminToken`
2. **بناء الرئيسية:** `الرئيسية` → `00 — بناء الصفحة الرئيسية` (خطوات 01–33)
3. **بناء البرامج:** `البرامج` → `00 — أدلة البناء`
4. **التحقق:** مجلد `Public` → `الرئيسية` → `GET /api/v1/home`

## توثيق Markdown
| الملف | المحتوى |
|-------|---------|
| `HOMEPAGE-ADMIN-GUIDE.md` | دليل بناء الرئيسية |
| `PROGRAMS-ADMIN-GUIDE.md` | دليل بناء البرامج |
| `PUBLIC-API.md` / `ADMIN-API.md` | مرجع كامل |

أعد التوليد: `php docs/postman/generate-audi-api-collection.php`
MD,
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        ['key' => 'baseUrl', 'value' => 'http://localhost:8000'],
        ['key' => 'locale', 'value' => 'ar'],
        ['key' => 'adminToken', 'value' => 'YOUR_ADMIN_TOKEN'],
        ['key' => 'id', 'value' => '1'],
        ['key' => 'programId', 'value' => '1'],
        ['key' => 'programSectionId', 'value' => '1'],
        ['key' => 'programSectionDetailId', 'value' => '1'],
        ['key' => 'slug', 'value' => 'urban-resilience'],
        ['key' => 'category', 'value' => 'news'],
        ['key' => 'directoryTab', 'value' => 'cities'],
        ['key' => 'directoryNumber', 'value' => '01'],
    ],
    'item' => [],
];

// =============================================================================
// PUBLIC — /api/v1
// =============================================================================

$publicHome = folder('الرئيسية — Home', [
    publicReq('جلب الصفحة الرئيسية', 'Get Home (Aggregate)', 'GET', '/api/v1/home', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/home', postmanPublicHomeSiteMap()."\n\n".postmanPublicAuditSummary()),
        'tests' => array_merge(
            imagePathTests('(pm.response.json().slider || [])', 'imageUrl'),
            [
                "pm.test('Status is 2xx', () => pm.expect(pm.response.code).to.be.oneOf([200, 201]));",
                "pm.test('Homepage sections present', () => {",
                "  const h = pm.response.json();",
                "  pm.expect(h.slider, 'slider').to.be.an('array');",
                "  pm.expect(h.aboutIntro, 'aboutIntro').to.be.an('object');",
                "  pm.expect(h.stats?.items, 'stats.items').to.be.an('array');",
                "  pm.expect(h.programs?.items, 'programs.items').to.be.an('array');",
                "  pm.expect(h.mediaCenter, 'mediaCenter').to.be.an('object');",
                "  pm.expect(h.knowledgeCenter, 'knowledgeCenter').to.be.an('object');",
                "  pm.expect(h.membershipContact, 'membershipContact').to.be.an('object');",
                '});',
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
    publicReq('خريطة المدن الأعضاء', 'Get Member Cities Map', 'GET', '/api/v1/home/member-cities', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/home/member-cities', 'قسم «المدن الأعضاء» على الرئيسية — إحصائيات + GeoJSON. Admin: `member-cities/stats` + `member-cities/cities`.'),
    ]),
    publicReq('GeoJSON الدول', 'Get Countries GeoJSON', 'GET', '/api/v1/home/member-cities/countries.geojson', [
        'description' => 'طبقة حدود الدول للخريطة — لا تعتمد على اللغة.',
    ]),
    publicReq('GeoJSON المدن', 'Get Cities GeoJSON', 'GET', '/api/v1/home/member-cities/cities.geojson', [
        'description' => 'طبقة نقاط المدن — أسماء المدن حسب Accept-Language.',
    ]),
]);

$publicSettings = folder('الإعدادات — Settings', [
    publicReq('إعدادات الموقع والتذييل', 'Get Site Settings', 'GET', '/api/v1/settings', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/settings', 'اسم الموقع، حقوق النشر، روابط التواصل (Footer). Admin: `settings` + `social-links`.'),
    ]),
]);

$publicAbout = folder('من نحن — About', [
    publicReq('عن المعهد', 'Get Institute', 'GET', '/api/v1/about/institute', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/institute', 'Admin: `about-content` (`institute`) + `home-stats`.')]),
    publicReq('الرؤية والرسالة', 'Get Vision & Mission', 'GET', '/api/v1/about/vision-mission', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/vision-mission', 'Admin: `about-content` (`vision_mission`, `goals`, `values`).')]),
    publicReq('كلمة رئيس المعهد', 'Get Leadership (President)', 'GET', '/api/v1/about/leadership/president', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/leadership/president', 'Admin: `leadership` type=president.')]),
    publicReq('رسالة المدير العام', 'Get Leadership (Director)', 'GET', '/api/v1/about/leadership/director', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/leadership/director', 'Admin: `leadership` type=director.')]),
    publicReq('المجلس الاستشاري', 'Get Advisory Board', 'GET', '/api/v1/about/advisory-board', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/advisory-board', 'Admin: `about-content` + `advisory-board`.'),
        'tests' => imagePathTests('(pm.response.json().members || [])'),
    ]),
    publicReq('فريق العمل', 'Get Team', 'GET', '/api/v1/about/team', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/team', 'Admin: `team-sections` + `team-members`.'),
        'tests' => [
            "pm.test('Status is 2xx', () => pm.expect(pm.response.code).to.be.oneOf([200, 201]));",
            "pm.test('Team member images are full paths', () => {",
            "  (pm.response.json().sections || []).flatMap(s => s.members || []).forEach((m) => {",
            "    if (m.image) pm.expect(m.image).to.match(/^(\/|https?:\/\/)/);",
            "  });",
            '});',
        ],
    ]),
    publicReq('الهيكل التشغيلي', 'Get Structure', 'GET', '/api/v1/about/structure', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/about/structure', 'Admin: `about-content` (`structure`).')]),
    publicReq('الشركاء', 'Get Partners', 'GET', '/api/v1/about/partners', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/about/partners', 'Admin: `partner-categories` + `partners`.'),
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

$publicStrategy = folder('الاستراتيجية — Strategy', [
    publicReq('استراتيجية 2025–2026', 'Get Strategy 2025/2026', 'GET', '/api/v1/strategy/strategy-2025', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/strategy-2025', 'Admin: `strategy` + `strategy-pillars` + `strategy-diagram`.')]),
    publicReq('قائمة مجالات التركيز', 'List Focus Areas', 'GET', '/api/v1/strategy/focus-areas', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/focus-areas', 'Admin: `focus-areas` + `about-content` (`focus_areas_pages`).')]),
    publicReq('تفاصيل مجال تركيز', 'Get Focus Area Detail', 'GET', '/api/v1/strategy/focus-areas/{{slug}}', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/strategy/focus-areas/{slug}', 'Admin: `focus-areas` by slug.')]),
]);

$publicPrograms = folder('البرامج — Programs', [
    publicReq('السياسات الحضرية', 'Get Urban Policies Program', 'GET', '/api/v1/programs/urban-policies', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/urban-policies', 'Admin: `00 — بناء برنامج السياسات الحضرية` (9 خطوات).')]),
    publicReq('التدريب وتطوير القدرات', 'Get Training Program', 'GET', '/api/v1/programs/training', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/training', 'Admin: `00 — بناء برنامج التدريب` (9 خطوات).')]),
    publicReq('الشراكات', 'Get Partnerships Program', 'GET', '/api/v1/programs/partnerships', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/programs/partnerships', 'Admin: `00 — بناء برنامج الشراكات` (9 خطوات).')]),
    publicReq('دليل بوابة التنمية', 'Get Development Portal Directory', 'GET', '/api/v1/programs/urban-policies/directory', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'tab', 'value' => 'cities', 'description' => 'cities|projects|organizations|publications'],
            ['key' => 'page', 'value' => '1'],
            ['key' => 'limit', 'value' => '20'],
            ['key' => 'search', 'value' => '', 'disabled' => true],
        ],
        'description' => publicMatch('/api/v1/programs/urban-policies/directory', 'Directory list. Build: step 03 `directory.rows`. Detail page: `GET .../directory/{tab}/{number}`.')."\n\n".postmanDirectoryGuides(),
    ]),
    folder('تفاصيل المدن — City Detail Pages', array_map(
        fn (array $city) => publicReq(
            $city['labelAr'].' — '.$city['labelEn'],
            'Get City Detail — '.$city['slug'],
            'GET',
            '/api/v1/programs/urban-policies/directory/cities/'.$city['number'],
            [
                'headers' => [$localeHeader],
                'description' => publicMatch(
                    '/api/v1/programs/urban-policies/directory/cities/'.$city['number'],
                    'Live: [audi-w.vercel.app/.../المدن/'.$city['slug'].']('.'https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/'.$city['slug'].'). Admin: step 03 `directory.rows.cities[]` + `messages/data/'.$city['slug'].'-detail.{ar,en}.json`.',
                ),
            ],
        ),
        postmanDirectoryCityDetailPages(),
    )),
    folder('تفاصيل المنظمات — Organization Detail Pages', array_map(
        fn (array $org) => publicReq(
            $org['labelAr'].' — '.$org['labelEn'],
            'Get Organization Detail — '.$org['number'],
            'GET',
            '/api/v1/programs/urban-policies/directory/organizations/'.$org['number'],
            [
                'headers' => [$localeHeader],
                'description' => publicMatch(
                    '/api/v1/programs/urban-policies/directory/organizations/'.$org['number'],
                    'Live: [?directory=organizations&item='.$org['number'].']('.'https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item='.$org['number'].'). Admin: step 03 `directory.rows.organizations[]`.',
                ),
            ],
        ),
        postmanDirectoryOrganizationDetailPages(),
    )),
    folder('تفاصيل المشاريع — Project Detail Pages', array_map(
        fn (array $project) => publicReq(
            $project['labelAr'].' — '.$project['labelEn'],
            'Get Project Detail — '.$project['slug'],
            'GET',
            '/api/v1/programs/urban-policies/directory/projects/'.$project['number'],
            [
                'headers' => [$localeHeader],
                'description' => publicMatch(
                    '/api/v1/programs/urban-policies/directory/projects/'.$project['number'],
                    'Live: [/المشاريع/'.$project['slug'].']('.'https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/'.$project['slug'].'). Admin: step 03 `directory.rows.projects[]`.',
                ),
            ],
        ),
        postmanDirectoryProjectDetailPages(),
    )),
    publicReq('تفاصيل عنصر الدليل (متغير)', 'Get Directory Item Detail (variables)', 'GET', '/api/v1/programs/urban-policies/directory/{{directoryTab}}/{{directoryNumber}}', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/programs/urban-policies/directory/{tab}/{number}', 'Set `directoryTab` to `cities|organizations|projects|publications` and `directoryNumber` accordingly. See folders **تفاصيل المدن**, **تفاصيل المنظمات**, **تفاصيل المشاريع**.')."\n\n".postmanDirectoryGuides(),
    ]),
    publicReq('إضافة تعليق على عنصر الدليل', 'Post Directory Discussion', 'POST', '/api/v1/programs/urban-policies/directory/{{directoryTab}}/{{directoryNumber}}/discussions', [
        'headers' => [$localeHeader],
        'body' => [
            'authorName' => 'د. سارة العتيبي',
            'body' => 'تعليق جديد حول هذا العنصر في الدليل.',
        ],
        'description' => publicMatch('/api/v1/programs/urban-policies/directory/{tab}/{number}/discussions', 'Public comment (pending approval). Admin review: `directory/discussions`.'),
    ]),
]);

$publicResources = folder('المصادر — Resources', [
    publicReq('قائمة المصادر', 'List Resources', 'GET', '/api/v1/resources', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'type', 'value' => '', 'disabled' => true],
            ['key' => 'focusArea', 'value' => '', 'disabled' => true],
            ['key' => 'year', 'value' => '', 'disabled' => true],
            ['key' => 'search', 'value' => '', 'disabled' => true],
            ['key' => 'page', 'value' => '1'],
        ],
        'description' => publicMatch('/api/v1/resources', 'صفحة مصادرنا + بطاقات مركز المعرفة. Admin: `resources` + `knowledge-categories`.'),
        'tests' => imagePathTests('(pm.response.json().data || [])'),
    ]),
]);

$publicMedia = folder('المركز الإعلامي — Media', [
    publicReq('الأخبار', 'List News', 'GET', '/api/v1/media/news', [
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
    publicReq('نشرة مدننا', 'List Newsletter', 'GET', '/api/v1/media/newsletter', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/media/newsletter', 'Admin: `media` category=newsletter.')]),
    publicReq('لقاءات حراك المدن', 'List City Meetings', 'GET', '/api/v1/media/city-meetings', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/media/city-meetings', 'Admin: `media` category=city_meetings.')]),
    publicReq('الأمين يتحدث', 'List Secretary Speaks', 'GET', '/api/v1/media/secretary-speaks', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/media/secretary-speaks', 'Admin: `media` category=secretary_speaks.'),
    ]),
    publicReq('تفاصيل مقال', 'Get Media Article Detail', 'GET', '/api/v1/media/{{category}}/{{slug}}', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/media/{category}/{slug}', 'Public: شرطة في الرابط (news). Admin: underscore (news).'),
    ]),
]);

$publicCareers = folder('اعمل معنا — Careers', [
    publicReq('الوظائف الشاغرة', 'List Job Openings', 'GET', '/api/v1/careers', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/careers', 'Admin: `job-openings`.')]),
    publicReq('تفاصيل وظيفة', 'Get Job Opening', 'GET', '/api/v1/careers/{{id}}', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/careers/{id}', 'Admin: `job-openings/{id}`.')]),
    publicReq('تقديم على وظيفة', 'Submit Job Application', 'POST', '/api/v1/careers/apply', [
        'headers' => [$localeHeader],
        'body' => [
            'jobOpeningId' => 1,
            'fullName' => 'سارة عبدالله',
            'email' => 'sara@example.com',
            'phone' => '+966501234567',
            'coverLetter' => 'لدي خبرة في التخطيط الحضري والعمل مع البلديات العربية.',
            'cvUrl' => 'https://example.com/cv.pdf',
        ],
        'description' => 'نموذج التقديم — يُراجع في Admin → `النماذج الواردة` → `job-applications`.',
    ]),
]);

$publicFaq = folder('الأسئلة الشائعة — FAQ', [
    publicReq('قائمة الأسئلة', 'List FAQs', 'GET', '/api/v1/faqs', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'category', 'value' => '', 'disabled' => true, 'description' => 'membership | programs | general'],
        ],
        'description' => publicMatch('/api/v1/faqs', 'Admin: faqs CRUD.'),
    ]),
]);

$publicLegal = folder('الصفحات القانونية — Legal', [
    publicReq('الشروط والأحكام', 'Get Terms', 'GET', '/api/v1/legal/terms', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/legal/terms', 'Admin: `legal` slug=terms.')]),
    publicReq('سياسة الخصوصية', 'Get Privacy Policy', 'GET', '/api/v1/legal/privacy', ['headers' => [$localeHeader], 'description' => publicMatch('/api/v1/legal/privacy', 'Admin: `legal` slug=privacy.')]),
]);

$publicForms = folder('النماذج — Forms', [
    publicReq('بيانات التواصل', 'Get Contact Info', 'GET', '/api/v1/contact', [
        'headers' => [$localeHeader],
        'description' => publicMatch('/api/v1/contact', 'صفحة تواصل معنا + تذييل الرئيسية. Admin: `contact-info` (ليس settings).'),
    ]),
    publicReq('إرسال رسالة تواصل', 'Submit Contact Form', 'POST', '/api/v1/contact', [
        'headers' => [$localeHeader],
        'body' => [
            'name' => 'أحمد محمد',
            'phone' => '+966501234567',
            'email' => 'ahmed@example.com',
            'message' => 'استفسار عن برامج المعهد التدريبية والعضوية.',
        ],
        'description' => 'نموذج تواصل — Admin → `contact-submissions`.',
    ]),
    publicReq('طلب عضوية', 'Submit Membership Application', 'POST', '/api/v1/membership', [
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
        'description' => 'نموذج انضم الى عضوية المعهد — Admin → `membership-applications`.',
    ]),
    publicReq('مساهمة بوابة التنمية', 'Submit Portal Contribution', 'POST', '/api/v1/programs/urban-policies/contribute', [
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
        'description' => 'مساهمة في دليل بوابة التنمية — Admin → `portal-contributions`. type: publications | cities | organizations.',
    ]),
    publicReq('الاشتراك في النشرة', 'Subscribe Newsletter', 'POST', '/api/v1/newsletter/subscribe', [
        'headers' => [$localeHeader],
        'body' => ['email' => 'subscriber@example.com', 'locale' => 'ar'],
        'description' => 'نموذج النشرة في التذييل — Admin → `newsletter-subscriptions`.',
    ]),
]);

$public = folder('الواجهة العامة — Public /api/v1', [
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
], postmanPublicSitePagesMap()."\n\n".postmanPublicAuditSummary()."\n\nاللغة: `Accept-Language: {{locale}}` (`ar` أو `en`). التفاصيل: `docs/postman/PUBLIC-API.md`.");

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
            'description' => publicMatch('/api/v1/contact', 'واجهة الإدارة المفضّلة لصفحة التواصل وتذييل الرئيسية. Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** step 33.'),
        ]),
    ]),
], publicMatch('/api/v1/settings'));

// --- Home (matches Public GET /api/v1/home) ---
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
**Start here** — run steps **01 → 33** in order after Login. Bodies match https://audi-ten.vercel.app/ar.

Each `POST` appears **once** in this folder (no duplicate requests elsewhere).

Then verify: `GET /api/v1/home` with `Accept-Language: ar`.
MD),
    postmanAdminFolder('01 — مرجع CRUD', '01 — CRUD Reference', [
        postmanAdminFolder('شرائح الهيرو', 'Hero Slides', adminCrud('hero-slides', 'Hero Slide', array_merge(postmanHomeHeroSlides()[0], ['isActive' => true]), [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'شريحة الهيرو',
            'description' => 'Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.',
        ])),
        postmanAdminFolder('إحصائيات الرئيسية', 'Home Stats', adminCrud('home-stats', 'Home Stat', postmanHomeStatItems()[0], [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'إحصائية',
            'description' => '«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.',
        ])),
        postmanAdminFolder('محتوى أقسام الرئيسية', 'Home About Content', adminCrud('about-content', 'About Section', postmanHomeAboutIntroBody(), [
            'withReorder' => false,
            'publicPath' => '/api/v1/home',
            'labelAr' => 'قسم محتوى',
            'description' => 'Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.',
        ])),
        postmanAdminFolder('تصنيفات مركز المعرفة', 'Home Knowledge Categories', adminCrud('knowledge-categories', 'Knowledge Category', postmanHomeKnowledgeCategories()[0], [
            'publicPath' => '/api/v1/home',
            'labelAr' => 'تصنيف مركز المعرفة',
            'description' => 'Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.',
        ])),
    ], 'Generic List / Create / Show / Update / Delete. For the full homepage workflow use **00 — بناء الصفحة الرئيسية** only.'),
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
        'tests' => postmanSaveCollectionIdTests('programId'),
        'description' => publicMatch('/api/v1/programs/training', "**الخطوة 1 — فئة البرنامج**\n\nيحفظ **`{{programId}}`** — مطلوب لجميع الخطوات التالية.\n\nURL: `/ar/برامجنا/مركز-دعم-المدن`"),
    ]),
];

$trainingTabFolders = [];
foreach (postmanProgramSectionPairs('training') as $index => $section) {
    $sectionStep = 2 + ($index * 2);
    $detailsStep = 3 + ($index * 2);
    $lastStep = postmanTrainingTabStepRange($section['tabKey'], $sectionStep, $detailsStep);
    $legacy = postmanTrainingSectionLegacyFromJson($section['tabKey'], $section['sortOrder']);
    $tabLabel = postmanTrainingTabLabelAr($section['tabKey']);

    $trainingTabFolders[] = postmanAdminFolder(
        sprintf('خطوات %02d–%02d — %s', $sectionStep, $lastStep, $tabLabel),
        sprintf('Steps %02d–%02d — %s', $sectionStep, $lastStep, $section['tabKey']),
        [
            req(
                sprintf('%02d — [قسم] %s — عنوان + صورة — program-sections', $sectionStep, $tabLabel),
                'POST',
                '/api/admin/program-sections',
                [
                    'auth' => true,
                    'body' => postmanSectionCreateBody($legacy),
                    'tests' => postmanSaveCollectionIdTests('programSectionId'),
                    'description' => publicMatch('/api/v1/programs/training', postmanTrainingSectionStepDescription($section['tabKey'], $sectionStep)),
                ],
            ),
            req(
                postmanTrainingDetailsStepName($section['tabKey'], $detailsStep),
                'POST',
                '/api/admin/program-section-details',
                [
                    'auth' => true,
                    'body' => postmanSectionDetailsBodyFromLegacy($legacy),
                    'tests' => postmanSaveCollectionIdTests('programSectionDetailId'),
                    'description' => publicMatch('/api/v1/programs/training', postmanTrainingDetailsStepDescription($section['tabKey'], $detailsStep)),
                ],
            ),
        ],
        postmanTrainingTabFolderDescription($section['tabKey'], $sectionStep, $detailsStep, $lastStep),
    );
}

$trainingProgramBuildSteps = array_merge(
    $trainingProgramBuildSteps,
    $trainingTabFolders,
);

$partnershipsProgramBuildSteps = [
    req('01 — برنامج الشراكات — partnerships program', 'POST', '/api/admin/programs', [
        'auth' => true,
        'body' => postmanPartnershipsProgramBody(),
        'tests' => postmanSaveCollectionIdTests('programId'),
        'description' => publicMatch('/api/v1/programs/partnerships', 'Step 1: Program category. Saves `programId` collection variable. URL: /ar/برامجنا/الشراكات'),
    ]),
];

foreach (postmanPartnershipsProgramSectionPairs() as $index => $section) {
    $sectionStep = 2 + ($index * 2);
    $detailsStep = 3 + ($index * 2);
    $legacy = postmanSectionLegacyFromJson('partnerships', $section['tabKey'], $section['sortOrder']);

    $partnershipsProgramBuildSteps[] = req(
        sprintf('%02d — قسم %s — program-sections', $sectionStep, $section['tabKey']),
        'POST',
        '/api/admin/program-sections',
        [
            'auth' => true,
            'body' => postmanSectionCreateBody($legacy),
            'tests' => postmanSaveCollectionIdTests('programSectionId'),
            'description' => publicMatch('/api/v1/programs/partnerships', 'Step '.$sectionStep.': **One request** — tab label + image: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`.'),
        ],
    );

    $partnershipsProgramBuildSteps[] = req(
        sprintf('%02d — تفاصيل %s — program-section-details', $detailsStep, $section['tabKey']),
        'POST',
        '/api/admin/program-section-details',
        [
            'auth' => true,
            'body' => postmanSectionDetailsBodyFromLegacy($legacy),
            'tests' => postmanSaveCollectionIdTests('programSectionDetailId'),
            'description' => publicMatch('/api/v1/programs/partnerships', 'Step '.$detailsStep.': Detail page: `introAr/En` (+ optional `titleAr/En`, `imageUrl` if different from section).'),
        ],
    );
}

$urbanPoliciesTabFolders = [];
foreach (postmanProgramSectionPairs('urban-policies') as $index => $section) {
    $sectionStep = 2 + ($index * 2);
    $detailsStep = 3 + ($index * 2);
    $legacy = postmanUrbanPoliciesSectionLegacyFromJson($section['tabKey'], $section['sortOrder']);
    $tabLabel = postmanUrbanPoliciesTabLabelAr($section['tabKey']);

    $urbanPoliciesTabFolders[] = postmanAdminFolder(
        sprintf('خطوات %02d–%02d — %s', $sectionStep, $detailsStep, $tabLabel),
        sprintf('Steps %02d–%02d — %s', $sectionStep, $detailsStep, $section['tabKey']),
        [
            req(
                sprintf('%02d — [قسم] %s — عنوان + صورة — program-sections', $sectionStep, $tabLabel),
                'POST',
                '/api/admin/program-sections',
                [
                    'auth' => true,
                    'body' => postmanSectionCreateBody($legacy),
                    'tests' => postmanSaveCollectionIdTests('programSectionId'),
                    'description' => publicMatch('/api/v1/programs/urban-policies', postmanUrbanPoliciesSectionStepDescription($section['tabKey'], $sectionStep)),
                ],
            ),
            req(
                postmanUrbanPoliciesDetailsStepName($section['tabKey'], $detailsStep),
                'POST',
                '/api/admin/program-section-details',
                [
                    'auth' => true,
                    'body' => postmanSectionDetailsBodyFromLegacy($legacy),
                    'tests' => postmanSaveCollectionIdTests('programSectionDetailId'),
                    'description' => publicMatch('/api/v1/programs/urban-policies', postmanUrbanPoliciesDetailsStepDescription($section['tabKey'], $detailsStep)),
                ],
            ),
        ],
        postmanUrbanPoliciesTabFolderDescription($section['tabKey'], $sectionStep, $detailsStep),
    );
}

$urbanPoliciesProgramBuildSteps = [
    req('01 — برنامج السياسات الحضرية — urban-policies program', 'POST', '/api/admin/programs', [
        'auth' => true,
        'body' => postmanUrbanPoliciesProgramBody(),
        'tests' => postmanSaveCollectionIdTests('programId'),
        'description' => publicMatch('/api/v1/programs/urban-policies', "**الخطوة 1 — فئة البرنامج**\n\nيحفظ **`{{programId}}`**.\n\nURL: [برنامج السياسات الحضرية](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية)"),
    ]),
];

$urbanPoliciesProgramBuildSteps = array_merge($urbanPoliciesProgramBuildSteps, $urbanPoliciesTabFolders);

$adminProgramsGroup = postmanAdminFolder('البرامج', 'Programs', [
    postmanAdminFolder('00 — أدلة البناء', '00 — Build Guides', [
        postmanAdminFolder('بناء برنامج الشراكات', 'Build Partnerships Program', $partnershipsProgramBuildSteps, <<<'MD'
Build [الشراكات / partnerships](https://audi-ten.vercel.app/ar/برامجنا/الشراكات):

**Note:** `back` and `sectionsLabel` are **not** admin API — the frontend reads them from `messages/{locale}/programs.json` (i18n).

**FK chain (2 steps per tab):**
1. `POST /api/admin/programs` → saves **`{{programId}}`**
2. Per tab: **`program-sections`** (`titleAr`, `titleEn`, `imageUrl`) → **`program-section-details`** (`intro`, `body`, optional `title`/`image`)

Verify: `GET /api/v1/programs/partnerships`.
MD),
        postmanAdminFolder('بناء برنامج التدريب', 'Build Training Program', $trainingProgramBuildSteps, postmanTrainingBuildFolderGuide()."\n\n".postmanProgramsTabContentGuide()."\n\nBuild [مركز دعم المدن / training](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن). Bodies from `messages/{ar,en}/programs.json`."),
        postmanAdminFolder('بناء برنامج السياسات الحضرية', 'Build Urban Policies Program', $urbanPoliciesProgramBuildSteps, postmanUrbanPoliciesBuildFolderGuide()."\n\n".postmanUrbanPoliciesTabContentGuide()."\n\nBuild [برنامج السياسات الحضرية](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية). Bodies from `messages/{ar,en}/programs.json`."),
    ], 'Step-by-step workflows — each `POST` appears once here. Use collection variables `programId`, `programSectionId`, `programSectionDetailId`.'),
    postmanAdminFolder('01 — مرجع CRUD', '01 — CRUD Reference', [
        postmanAdminFolder('البرامج', 'Programs CRUD', adminCrud('programs', 'Program', postmanHomeProgramCards()[0], [
            'withReorder' => false,
            'publicPath' => '/api/v1/home',
            'labelAr' => 'برنامج',
            'description' => 'Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.',
        ])),
        postmanAdminFolder('أقسام البرنامج', 'Program Sections', adminCrud('program-sections', 'Program Section', postmanSectionCreateBody(postmanSectionLegacyFromJson('partnerships', 'euroArabDialogue', 0)), [
            'publicPath' => '/api/v1/programs/partnerships',
            'labelAr' => 'قسم برنامج',
            'idVariable' => 'programSectionId',
            'saveIdVariable' => 'programSectionId',
            'listQuery' => [
                ['key' => 'page', 'value' => '1'],
                ['key' => 'limit', 'value' => '20'],
                ['key' => 'programId', 'value' => '{{programId}}'],
            ],
            'description' => '**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.',
        ])),
        postmanAdminFolder('تفاصيل أقسام البرنامج', 'Program Section Details', adminCrud('program-section-details', 'Program Section Detail', postmanSectionDetailsBodyFromLegacy(postmanDevelopmentPortalSectionBody()), [
            'publicPath' => '/api/v1/programs/urban-policies',
            'labelAr' => 'تفاصيل قسم',
            'withReorder' => false,
            'idVariable' => 'programSectionDetailId',
            'saveIdVariable' => 'programSectionDetailId',
            'listQuery' => [
                ['key' => 'page', 'value' => '1'],
                ['key' => 'limit', 'value' => '20'],
                ['key' => 'programId', 'value' => '{{programId}}'],
                ['key' => 'programSectionId', 'value' => '{{programSectionId}}'],
            ],
            'description' => 'After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.',
        ])),
        postmanAdminFolder('الدورات التدريبية', 'Training Courses', adminCrud('training-courses', 'Training Course', postmanTrainingCourses()[0], [
            'publicPath' => '/api/v1/programs/training',
            'labelAr' => 'دورة تدريبية',
            'description' => 'Training grid on ?tab=trainingPrograms. Build guide: `courses[]` inside **program-section-details** step 03. Optional row edits here.',
        ])),
        postmanAdminFolder('الخبراء', 'Experts', adminCrud('experts', 'Expert', postmanTrainingExperts()[0], [
            'publicPath' => '/api/v1/programs/training',
            'labelAr' => 'خبير',
            'description' => 'Experts carousel on ?tab=experts. Build guide: `experts[]` inside **program-section-details** step 09. Optional row edits here.',
        ])),
        postmanAdminFolder('دليل المدن', 'Directory Cities', adminCrud('directory/cities', 'Directory City', [
        'number' => '01',
        'nameAr' => 'الباحة، المملكة العربية السعودية',
        'nameEn' => 'Al Baha, Saudi Arabia',
        'descriptionAr' => 'مدينة صغيرة أو متوسطة الحجم',
        'descriptionEn' => 'Small or medium-sized city',
        'countryCode' => 'SA',
        'citySize' => 'medium',
        'detailAr' => postmanDirectoryCityDetailExampleAr(),
        'detailEn' => postmanDirectoryCityDetailExampleEn(),
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory/cities/01', 'labelAr' => 'مدينة', 'description' => 'Directory cities. Build guide: `directory.rows.cities` + `detail.sections[]` in developmentPortal step 03. Live example: [Al Baha](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/al-baha).'])),
    postmanAdminFolder('دليل المشاريع', 'Directory Projects', adminCrud('directory/projects', 'Directory Project', [
        'number' => '01',
        'cityAr' => 'القاهرة',
        'cityEn' => 'Cairo',
        'countryAr' => 'مصر',
        'countryEn' => 'Egypt',
        'startDate' => '2020',
        'endDate' => '2024',
        'detailAr' => postmanDirectoryProjectDetailExampleAr(),
        'detailEn' => postmanDirectoryProjectDetailExampleEn(),
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory/projects/01', 'labelAr' => 'مشروع', 'description' => 'Directory projects. Build guide: `directory.rows.projects` in step 03. **Rich** example: [Cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/cairo). **Simple** example (02): [Riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/riyadh) — use `detailAr` from `messages/data/riyadh-project-detail.{ar,en}.json`.'])),
    postmanAdminFolder('دليل المنظمات', 'Directory Organizations', adminCrud('directory/organizations', 'Directory Organization', [
        'number' => '01',
        'nameAr' => 'PLATFORMA',
        'nameEn' => 'PLATFORMA',
        'descriptionAr' => 'منظمة دولية',
        'descriptionEn' => 'International Organization',
        'detailAr' => postmanDirectoryOrganizationDetailExampleAr(),
        'detailEn' => postmanDirectoryOrganizationDetailExampleEn(),
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory/organizations/01', 'labelAr' => 'منظمة', 'description' => 'Directory organizations. Build guide: `directory.rows.organizations` in step 03. Live list: [organizations tab](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations).'])),
    postmanAdminFolder('دليل المنشورات', 'Directory Publications', adminCrud('directory/publications', 'Directory Publication', [
        'number' => '01',
        'nameAr' => 'تقرير التنمية الحضرية العربية 2024',
        'nameEn' => 'Arab Urban Development Report 2024',
        'descriptionAr' => 'تقرير سنوي يرصد التطورات في التنمية الحضرية.',
        'descriptionEn' => 'Annual report monitoring urban development trends.',
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory', 'labelAr' => 'منشور', 'description' => 'Directory publications. Build guide: `directory.rows.publications` in step 03. Detail: `GET .../directory/publications/{number}`.'])),
    postmanAdminFolder('نقاشات الدليل', 'Directory Discussions', adminCrud('directory/discussions', 'Directory Discussion', [
        'directoryType' => 'cities',
        'directoryNumber' => '01',
        'authorNameAr' => 'د. سارة العتيبي',
        'authorNameEn' => 'Dr. Sarah Al-Otaibi',
        'bodyAr' => 'مناقشة حول التخضير الحضري في المدن الصغيرة.',
        'bodyEn' => 'Discussion on urban greening in small cities.',
        'isApproved' => true,
        'sortOrder' => 0,
    ], ['publicPath' => '/api/v1/programs/urban-policies/directory/cities/01', 'labelAr' => 'تعليق', 'description' => 'Discussion threads on directory item detail pages. Build guide: `discussions[]` inside `directory.rows.*` in step 03.'])),
    ], 'Generic List / Create / Show / Update / Delete. Full program seed: **00 — أدلة البناء** only.'),
], publicMatch('/api/v1/programs/training', postmanProgramsTabContentGuide()));

// --- Resources ---
$adminResourcesGroup = postmanAdminFolder('المصادر', 'Resources', [
    postmanAdminFolder('المصادر', 'Resources CRUD', adminCrud('resources', 'Resource', postmanHomeKnowledgeCenterResources()[2], [
        'publicPath' => '/api/v1/resources',
        'labelAr' => 'مصدر',
        'description' => 'Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.',
    ])),
], publicMatch('/api/v1/resources'));

// --- Media ---
$adminMediaGroup = postmanAdminFolder('المركز الإعلامي', 'Media', [
    postmanAdminFolder('المقالات الإعلامية', 'Media Articles', adminCrud('media', 'Media Article', postmanHomeMediaNewsArticles()[0]['body'], [
        'publicPath' => '/api/v1/media/news',
        'labelAr' => 'مقال إعلامي',
        'description' => 'News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.',
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
