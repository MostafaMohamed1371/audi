<?php

declare(strict_types=1);

/** @return array<string, array{ar: string, en: string, public?: string}> */
function postmanAdminSectionCatalog(): array
{
    return [
        'المصادقة — Auth' => [
            'ar' => 'تسجيل الدخول والخروج والتحقق من المستخدم الحالي.',
            'en' => 'Login, logout, and current user profile.',
        ],
        'رفع الملفات — Uploads' => [
            'ar' => 'رفع الصور وملفات PDF واستخدام الرابط المُرجَع في حقول imageUrl وfileUrl.',
            'en' => 'Upload images/PDFs; use returned URL in imageUrl/fileUrl fields.',
        ],
        'الإعدادات — Settings' => [
            'ar' => 'إعدادات الموقع، روابط التواصل، ومعلومات التواصل (صفحة Contact + التذييل).',
            'en' => 'Site settings, social links, and contact info block.',
            'public' => 'GET /api/v1/settings, GET /api/v1/contact',
        ],
        'الرئيسية — Home' => [
            'ar' => 'محتوى الصفحة الرئيسية: الهيرو، الإحصائيات، وأقسام about-content (home_*).',
            'en' => 'Homepage: hero slides, stats, and home_* about-content sections.',
            'public' => 'GET /api/v1/home',
        ],
        'من نحن — About' => [
            'ar' => 'صفحات عن المعهد: المحتوى، القيادة، المجلس، الفريق، الشركاء.',
            'en' => 'About pages: content, leadership, advisory board, team, partners.',
            'public' => 'GET /api/v1/about/*',
        ],
        'الاستراتيجية — Strategy' => [
            'ar' => 'استراتيجية المعهد، المحاور، المخطط، ومجالات التركيز.',
            'en' => 'Strategy page, pillars, diagram, and focus areas.',
            'public' => 'GET /api/v1/strategy/*',
        ],
        'البرامج — Programs' => [
            'ar' => 'برامج المعهد (3 برامج)، أقسامها، الدورات، الخبراء، ودليل السياسات الحضرية.',
            'en' => 'Programs, sections, training courses, experts, urban policies directory.',
            'public' => 'GET /api/v1/programs/*',
        ],
        'المصادر — Resources' => [
            'ar' => 'تقارير ودراسات ومصادر المعرفة (صفحة مصادرنا + مركز المعرفة في الرئيسية).',
            'en' => 'Reports and knowledge resources.',
            'public' => 'GET /api/v1/resources',
        ],
        'المركز الإعلامي — Media' => [
            'ar' => 'الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث.',
            'en' => 'News, newsletter, city meetings, secretary speaks articles.',
            'public' => 'GET /api/v1/media/*',
        ],
        'الوظائف — Careers' => [
            'ar' => 'الوظائف الشاغرة المعروضة في صفحة اعمل معنا.',
            'en' => 'Job openings for careers page.',
            'public' => 'GET /api/v1/careers',
        ],
        'الأسئلة الشائعة — FAQ' => [
            'ar' => 'أسئلة وأجوبة صفحة FAQ.',
            'en' => 'FAQ questions and answers.',
            'public' => 'GET /api/v1/faqs',
        ],
        'الصفحات القانونية — Legal' => [
            'ar' => 'الشروط والأحكام وسياسة الخصوصية.',
            'en' => 'Terms and privacy pages.',
            'public' => 'GET /api/v1/legal/{slug}',
        ],
        'المدن الأعضاء — Member Cities' => [
            'ar' => 'إدارة مدن الأعضاء، الإحصائيات، واستيراد GeoJSON.',
            'en' => 'Member cities CRUD, stats, GeoJSON import.',
            'public' => 'GET /api/v1/home/member-cities',
        ],
        'النماذج الواردة — Form Submissions' => [
            'ar' => 'مراجعة رسائل التواصل، طلبات العضوية، المساهمات، التقديم على الوظائف، النشرة.',
            'en' => 'Review contact, membership, contributions, job applications, newsletter.',
            'public' => 'POST /api/v1/contact, /membership, …',
        ],
    ];
}

function postmanRequestPurpose(string $method, string $name, string $apiType = 'admin'): string
{
    if ($apiType === 'public') {
        return postmanPublicRequestPurpose($method, $name);
    }
    if (str_contains($name, 'إنشاء') || str_contains($name, 'Create')) {
        return 'إنشاء سجل جديد في قاعدة البيانات.';
    }
    if (str_contains($name, 'تحديث') || str_contains($name, 'Update')) {
        return 'تحديث سجل موجود (PUT/PATCH).';
    }
    if (str_contains($name, 'حذف') || str_contains($name, 'Delete')) {
        return 'حذف سجل نهائياً.';
    }
    if (str_contains($name, 'عرض القائمة') || str_contains($name, 'List')) {
        return 'عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).';
    }
    if (str_contains($name, 'عرض —') || str_contains($name, 'Show')) {
        return 'عرض تفاصيل سجل واحد بالمعرّف {{id}}.';
    }
    if (str_contains($name, 'إعادة الترتيب') || str_contains($name, 'Reorder')) {
        return 'تغيير ترتيب العرض عبر sortOrder.';
    }
    if (str_contains($name, 'تسجيل الخروج') || str_contains($name, 'Logout')) {
        return 'إبطال توكن الجلسة الحالي (Bearer token).';
    }
    if (str_contains($name, 'الملف الشخصي') || str_contains($name, ' — Me')) {
        return 'عرض بيانات المستخدم المسجّل حالياً.';
    }
    if (str_contains($name, 'رفع') || str_contains($name, 'Upload')) {
        return 'رفع ملف (صورة أو PDF) والحصول على URL لاستخدامه في حقول imageUrl / fileUrl.';
    }
    if (str_contains($name, 'تسجيل الدخول') || str_contains($name, 'Login')) {
        return 'الحصول على Bearer token للمصادقة على باقي طلبات الإدارة.';
    }

    return match ($method) {
        'GET' => 'قراءة بيانات من الخادم.',
        'POST' => 'إرسال بيانات جديدة أو تنفيذ عملية.',
        'PUT' => 'استبدال/تحديث بيانات كاملة.',
        'PATCH' => 'تحديث جزئي للحقول.',
        'DELETE' => 'حذف مورد.',
        default => 'طلب واجهة الإدارة.',
    };
}

/** @return array<string, array{ar: string, en: string, admin?: string}> */
function postmanPublicSectionCatalog(): array
{
    return [
        'Home — الرئيسية' => [
            'ar' => 'الصفحة الرئيسية: السلايدر، الإحصائيات، البرامج، المركز الإعلامي، مركز المعرفة، وخريطة المدن الأعضاء.',
            'en' => 'Homepage aggregate, member cities map, and GeoJSON layers.',
            'admin' => 'hero-slides, home-stats, programs, about-content (home_*), media, resources',
        ],
        'Settings — الإعدادات' => [
            'ar' => 'إعدادات الموقع العامة: اسم الموقع، حقوق النشر، روابط التواصل الاجتماعي.',
            'en' => 'Site-wide settings and social links.',
            'admin' => 'settings, social-links',
        ],
        'About — من نحن' => [
            'ar' => 'صفحات عن المعهد: التعريف، الرؤية والرسالة، القيادة، المجلس الاستشاري، الفريق، الهيكل، الشركاء.',
            'en' => 'About institute pages with locale-resolved content.',
            'admin' => 'about-content, leadership, advisory-board, team-*, partners',
        ],
        'Strategy — الاستراتيجية' => [
            'ar' => 'استراتيجية المعهد ومجالات التركيز.',
            'en' => 'Strategy page and focus areas.',
            'admin' => 'strategy, strategy-pillars, focus-areas, about-content',
        ],
        'Programs — البرامج' => [
            'ar' => 'صفحات البرامج الثلاثة ودليل بوابة التنمية الحضرية.',
            'en' => 'Program pages and urban policies directory.',
            'admin' => 'programs, program-sections, training-courses, experts, directory/*',
        ],
        'Resources — المصادر' => [
            'ar' => 'قائمة التقارير والدراسات والمصادر (مع فلاتر اختيارية).',
            'en' => 'Paginated knowledge resources list.',
            'admin' => 'resources',
        ],
        'Media — المركز الإعلامي' => [
            'ar' => 'الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث، وتفاصيل المقالات.',
            'en' => 'Media center listings and article detail.',
            'admin' => 'media (category news, newsletter, city_meetings, secretary_speaks)',
        ],
        'Careers — اعمل معنا' => [
            'ar' => 'عرض الوظائف الشاغرة وتقديم طلب التوظيف.',
            'en' => 'Job listings and application form.',
            'admin' => 'job-openings, job-applications',
        ],
        'FAQ — الأسئلة الشائعة' => [
            'ar' => 'قائمة الأسئلة الشائعة (مع تصنيف اختياري).',
            'en' => 'FAQ list with optional category filter.',
            'admin' => 'faqs',
        ],
        'Legal — الشروط والخصوصية' => [
            'ar' => 'صفحات الشروط والأحكام وسياسة الخصوصية.',
            'en' => 'Terms and privacy policy pages.',
            'admin' => 'legal',
        ],
        'Forms — النماذج' => [
            'ar' => 'معلومات التواصل ونماذج الإرسال: تواصل، عضوية، مساهمة، توظيف، نشرة.',
            'en' => 'Contact info and public form submissions.',
            'admin' => 'contact-info, contact-submissions, membership-applications, portal-contributions, job-applications, newsletter-subscriptions',
        ],
    ];
}

function postmanPublicRequestPurpose(string $method, string $name): string
{
    if (str_contains($name, 'Get Home') || str_contains($name, 'Aggregate')) {
        return 'جلب محتوى الصفحة الرئيسية كاملاً (سلايدر، إحصائيات، برامج، إعلام، معرفة، عضوية).';
    }
    if (str_contains($name, 'Member Cities')) {
        return 'بيانات خريطة المدن الأعضاء: إحصائيات + GeoJSON.';
    }
    if (str_contains($name, 'GeoJSON')) {
        return 'طبقة GeoJSON للخريطة (هندسة + أسماء حسب اللغة حيث ينطبق).';
    }
    if (str_contains($name, 'Submit') || str_contains($name, 'Subscribe')) {
        return 'إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).';
    }
    if (str_contains($name, 'List')) {
        return 'قائمة paginated — حقول بلغة واحدة حسب Accept-Language.';
    }
    if (str_starts_with($name, 'Get ')) {
        return 'قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).';
    }

    return match ($method) {
        'GET' => 'قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).',
        'POST' => 'إرسال نموذج أو بيانات من زائر الموقع.',
        default => 'طلب الواجهة العامة.',
    };
}

/**
 * @return list<array{key: string, desc: string, example: string}>
 */
function postmanExtractBodyFieldsFromRequest(array $request): array
{
    $body = $request['body'] ?? null;

    if (! is_array($body)) {
        return [];
    }

    if (($body['mode'] ?? '') === 'raw' && ! empty($body['raw'])) {
        $decoded = json_decode((string) $body['raw'], true);

        return is_array($decoded) ? postmanFlattenBodyFields($decoded) : [];
    }

    if (($body['mode'] ?? '') === 'formdata' && ! empty($body['formdata'])) {
        return array_map(static fn (array $field) => [
            'field' => (string) $field['key'],
            'desc' => (string) ($field['description'] ?? postmanArabicFieldLabel((string) $field['key'])),
            'example' => ($field['type'] ?? 'text') === 'file' ? '(ملف)' : (string) ($field['value'] ?? ''),
        ], $body['formdata']);
    }

    return [];
}

/**
 * @return list<array{key: string, desc: string, value: string}>
 */
function postmanExtractQueryParams(array $request): array
{
    $query = $request['url']['query'] ?? [];

    return array_map(static fn (array $q) => [
        'key' => (string) $q['key'],
        'desc' => (string) ($q['description'] ?? postmanArabicQueryLabel((string) $q['key'])),
        'value' => (string) ($q['value'] ?? ''),
    ], $query);
}

function postmanRequestPath(array $request): string
{
    $path = $request['url']['path'] ?? [];

    if (is_array($path)) {
        return '/'.implode('/', $path);
    }

    return '/'.ltrim((string) $path, '/');
}

function postmanSectionAnchor(string $name, bool $englishFirst = false): string
{
    if (str_contains($name, ' — ')) {
        $parts = explode(' — ', $name, 2);
        $slug = $englishFirst ? trim($parts[0]) : trim($parts[1]);
    } else {
        $slug = $name;
    }

    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $slug), '-'));
}

/**
 * @param  array<string, mixed>  $folder
 */
function postmanRenderApiFolderMarkdown(array $folder, int $level, string $apiType): string
{
    $lines = [];
    $heading = str_repeat('#', min($level, 4));
    $name = (string) ($folder['name'] ?? 'Folder');
    $catalog = $apiType === 'public' ? postmanPublicSectionCatalog() : postmanAdminSectionCatalog();
    $meta = $catalog[$name] ?? null;

    $lines[] = "{$heading} {$name}";
    $lines[] = '';

    if ($meta) {
        $anchor = postmanSectionAnchor($name, $apiType === 'public');
        $lines[] = "<a id=\"{$anchor}\"></a>";
        $lines[] = '**الغرض | Purpose:** '.$meta['ar'].' / '.$meta['en'];
        if ($apiType === 'admin' && ! empty($meta['public'])) {
            $lines[] = '**الواجهة العامة | Public API:** `'.$meta['public'].'`';
        }
        if ($apiType === 'public' && ! empty($meta['admin'])) {
            $lines[] = '**لوحة التحكم | Admin resources:** `'.$meta['admin'].'`';
        }
        $lines[] = '';
    }

    if (! empty($folder['description'])) {
        $lines[] = trim((string) $folder['description']);
        $lines[] = '';
    }

    foreach ($folder['item'] ?? [] as $child) {
        if (isset($child['request'])) {
            $lines[] = postmanRenderRequestMarkdown($child, $level + 1, $apiType);
        } elseif (isset($child['item'])) {
            $lines[] = postmanRenderApiFolderMarkdown($child, $level + 1, $apiType);
        }
    }

    return implode("\n", $lines);
}

/**
 * @param  array<string, mixed>  $folder
 */
function postmanRenderAdminFolderMarkdown(array $folder, int $level = 2): string
{
    return postmanRenderApiFolderMarkdown($folder, $level, 'admin');
}

/**
 * @param  array<string, mixed>  $folder
 */
function postmanRenderPublicFolderMarkdown(array $folder, int $level = 2): string
{
    return postmanRenderApiFolderMarkdown($folder, $level, 'public');
}

/**
 * @param  array<string, mixed>  $item
 */
function postmanRenderRequestMarkdown(array $item, int $level, string $apiType = 'admin'): string
{
    $request = $item['request'];
    $method = (string) ($request['method'] ?? 'GET');
    $name = (string) ($item['name'] ?? '');
    $path = postmanRequestPath($request);
    $heading = str_repeat('#', min($level, 4));

    $lines = [];
    $lines[] = "{$heading} {$method} `{$path}`";
    $lines[] = '';
    $lines[] = '**الاسم | Name:** '.$name;
    $lines[] = '';
    $lines[] = '**الغرض | Purpose:** '.postmanRequestPurpose($method, $name, $apiType);
    $lines[] = '';

    $auth = false;
    $localeHeader = null;
    foreach ($request['header'] ?? [] as $header) {
        $key = strtolower((string) ($header['key'] ?? ''));
        if ($key === 'authorization') {
            $auth = true;
        }
        if ($key === 'accept-language') {
            $localeHeader = (string) ($header['value'] ?? '{{locale}}');
        }
    }

    if ($apiType === 'public') {
        $lines[] = '**المصادقة | Auth:** غير مطلوب (واجهة عامة)';
        $lines[] = '**اللغة | Language:** `Accept-Language: '.($localeHeader ?? '{{locale}}').'` أو `?locale=ar|en`';
    } else {
        $lines[] = '**المصادقة | Auth:** '.($auth ? 'Bearer `{{adminToken}}` (مطلوب)' : 'غير مطلوب');
    }
    $lines[] = '';

    $query = postmanExtractQueryParams($request);
    if ($query !== []) {
        $lines[] = '#### Query Parameters | معاملات الرابط';
        $lines[] = '';
        $lines[] = '| المعامل | الوصف | مثال |';
        $lines[] = '|---------|--------|------|';
        foreach ($query as $q) {
            $lines[] = "| `{$q['key']}` | {$q['desc']} | `{$q['value']}` |";
        }
        $lines[] = '';
    }

    $bodyFields = postmanExtractBodyFieldsFromRequest($request);
    if ($bodyFields !== []) {
        $lines[] = '#### Body Parameters | معاملات الجسم (JSON)';
        $lines[] = '';
        $lines[] = '| الحقل | الوصف | مثال |';
        $lines[] = '|-------|--------|------|';
        foreach ($bodyFields as $row) {
            $example = str_replace('|', '\\|', $row['example']);
            if (mb_strlen($example) > 50) {
                $example = mb_substr($example, 0, 47).'…';
            }
            $lines[] = "| `{$row['field']}` | {$row['desc']} | {$example} |";
        }
        $lines[] = '';
    }

    $desc = trim((string) ($request['description'] ?? ''));
    $desc = preg_replace('/^\*\*الغرض \| Purpose:\*\*[^\n]*\n\n?/u', '', $desc);
    $desc = preg_replace('/### المعاملات \(Body Parameters\).*$/s', '', $desc);
    $desc = trim($desc);
    if ($desc !== '') {
        $lines[] = '#### Notes | ملاحظات';
        $lines[] = '';
        $lines[] = $desc;
        $lines[] = '';
    }

    $lines[] = '---';
    $lines[] = '';

    return implode("\n", $lines);
}

/**
 * @param  array<string, mixed>  $adminFolder
 */
function postmanGenerateAdminApiMarkdown(array $adminFolder): string
{
    $intro = <<<'MD'
# توثيق واجهة الإدارة — AUDI Admin API

> **المعهد العربي لإنماء المدن** — واجهة برمجة لوحة التحكم  
> Base URL: `{{baseUrl}}/api/admin`  
> يُولَّد تلقائياً من `AUDI-API.postman_collection.json`

---

## نظرة عامة | Overview

واجهة الإدارة (`/api/admin/*`) تُستخدم لإدارة محتوى الموقع بالكامل.  
كل المحتوى ثنائي اللغة: الحقول تنتهي بـ `Ar` (عربي) و `En` (إنجليزي).

| الموضوع | التفاصيل |
|---------|----------|
| **المصادقة** | `POST /api/admin/auth/login` → احفظ `token` في `adminToken` |
| **الرأس** | `Authorization: Bearer {{adminToken}}` |
| **اللغة** | `Accept-Language: ar` أو `en` (اختياري للإدارة) |
| **الصور** | ارفع عبر `POST /api/admin/uploads` → استخدم `data.url` في `imageUrl` |
| **الواجهة العامة** | `GET /api/v1/*` يقرأ locale واحد من حقول *Ar/*En |

### متغيرات Postman

| المتغير | مثال | الغرض |
|---------|------|--------|
| `baseUrl` | `http://localhost:8000` | عنوان الخادم |
| `adminToken` | (من Login) | توكن Sanctum |
| `locale` | `ar` | لغة Accept-Language |
| `id` | `1` | معرّف السجل في المسار |

### نموذج البيانات ثنائي اللغة

```
Admin POST/PUT:  { "titleAr": "...", "titleEn": "..." }
Public GET:      { "title": "..." }  ← حسب Accept-Language
```

---

## فهرس الأقسام | Section Index

MD;

    $catalog = postmanAdminSectionCatalog();
    $indexLines = [];
    foreach ($catalog as $section => $meta) {
        $anchor = postmanSectionAnchor($section, false);
        $indexLines[] = "- [{$section}](#{$anchor}) — {$meta['ar']}";
    }

    $body = postmanRenderAdminFolderMarkdown($adminFolder, 2);

    return $intro.implode("\n", $indexLines)."\n\n---\n\n".$body;
}

/**
 * @param  array<string, mixed>  $publicFolder
 */
function postmanGeneratePublicApiMarkdown(array $publicFolder): string
{
    $intro = <<<'MD'
# توثيق الواجهة العامة — AUDI Public API

> **المعهد العربي لإنماء المدن** — واجهة برمجة الموقع العام  
> Base URL: `{{baseUrl}}/api/v1`  
> يُولَّد تلقائياً من `AUDI-API.postman_collection.json`

---

## نظرة عامة | Overview

الواجهة العامة (`/api/v1/*`) تُستخدم من الموقع (Frontend) لعرض المحتوى للزوار.  
كل طلب يُرجع **لغة واحدة** حسب `Accept-Language` أو `?locale=ar|en`.

| الموضوع | التفاصيل |
|---------|----------|
| **المصادقة** | غير مطلوبة — API عام |
| **اللغة** | `Accept-Language: ar` أو `en` (مطلوب لمعظم الطلبات) |
| **الحقول** | `{ "title": "..." }` — وليس `titleAr` / `titleEn` |
| **الصور** | مسارات كاملة: `/emp/1.png` أو `https://…` |
| **الإدارة** | المحتوى يُعدّ عبر `/api/admin/*` — راجع `ADMIN-API.md` |

### متغيرات Postman

| المتغير | مثال | الغرض |
|---------|------|--------|
| `baseUrl` | `http://localhost:8000` | عنوان الخادم |
| `locale` | `ar` | لغة Accept-Language |
| `slug`, `id`, `category` | — | معاملات المسار |

### نموذج البيانات

```
Admin يخزن:     { "titleAr": "...", "titleEn": "..." }
Public يُرجع:  { "title": "..." }  ← حسب locale
```

---

## فهرس الأقسام | Section Index

MD;

    $catalog = postmanPublicSectionCatalog();
    $indexLines = [];
    foreach ($catalog as $section => $meta) {
        $anchor = postmanSectionAnchor($section, true);
        $indexLines[] = "- [{$section}](#{$anchor}) — {$meta['ar']}";
    }

    $body = postmanRenderPublicFolderMarkdown($publicFolder, 2);

    return $intro.implode("\n", $indexLines)."\n\n---\n\n".$body;
}

function postmanGenerateApiReadmeMarkdown(): string
{
    return <<<'MD'
# توثيق واجهات AUDI API

> **المعهد العربي لإنماء المدن** — Arab Urban Development Institute  
> يُولَّد تلقائياً مع مجموعة Postman

---

## الملفات | Documentation Files

| الملف | الوصف |
|-------|--------|
| **[PUBLIC-API.md](./PUBLIC-API.md)** | الواجهة العامة `/api/v1/*` — للموقع والزوار |
| **[ADMIN-API.md](./ADMIN-API.md)** | واجهة الإدارة `/api/admin/*` — لوحة التحكم |
| **[API-ERRORS.md](./API-ERRORS.md)** | أخطاء API — صيغة 400, 401, 404, 422, 500… |
| **[AUDI-API.postman_collection.json](./AUDI-API.postman_collection.json)** | مجموعة Postman كاملة (Public + Admin) |

---

## Public vs Admin

| | Public `/api/v1` | Admin `/api/admin` |
|--|------------------|---------------------|
| **الغرض** | عرض المحتوى للموقع | إنشاء/تعديل المحتوى |
| **المصادقة** | لا | Bearer token (Sanctum) |
| **اللغة** | لغة واحدة لكل طلب | حقول `*Ar` و `*En` معاً |
| **مثال** | `{ "title": "عنوان" }` | `{ "titleAr": "…", "titleEn": "…" }` |

---

## البدء السريع | Quick Start

### Public API
```http
GET /api/v1/home
Accept-Language: ar
```

### Admin API
```http
POST /api/admin/auth/login
Content-Type: application/json

{ "email": "admin@araburban.org", "password": "password" }
```
ثم استخدم `Authorization: Bearer {token}` في باقي طلبات الإدارة.

---

## إعادة التوليد | Regenerate

```bash
php docs/postman/generate-audi-api-collection.php
```

يُحدّث: Postman collection + `PUBLIC-API.md` + `ADMIN-API.md` + `API-ERRORS.md` + `API.md`
MD;
}

/**
 * @param  array<string, mixed>  $adminFolder
 */
function postmanEnhanceAdminFolderDescriptions(array &$adminFolder): void
{
    $catalog = postmanAdminSectionCatalog();
    $name = (string) ($adminFolder['name'] ?? '');

    if (isset($catalog[$name])) {
        $meta = $catalog[$name];
        $adminFolder['description'] = "**الغرض:** {$meta['ar']}\n\n**Purpose:** {$meta['en']}"
            .(! empty($meta['public']) ? "\n\n**Public API:** `{$meta['public']}`" : '')
            ."\n\n".(string) ($adminFolder['description'] ?? '');
    }

    foreach ($adminFolder['item'] ?? [] as &$child) {
        if (isset($child['item'])) {
            postmanEnhanceAdminFolderDescriptions($child);
        }
    }
    unset($child);
}

/**
 * @param  array<string, mixed>  $publicFolder
 */
function postmanEnhancePublicFolderDescriptions(array &$publicFolder): void
{
    $catalog = postmanPublicSectionCatalog();
    $name = (string) ($publicFolder['name'] ?? '');

    if (isset($catalog[$name])) {
        $meta = $catalog[$name];
        $publicFolder['description'] = "**الغرض:** {$meta['ar']}\n\n**Purpose:** {$meta['en']}"
            .(! empty($meta['admin']) ? "\n\n**Admin resources:** `{$meta['admin']}`" : '')
            ."\n\n".(string) ($publicFolder['description'] ?? '');
    }

    foreach ($publicFolder['item'] ?? [] as &$child) {
        if (isset($child['item'])) {
            postmanEnhancePublicFolderDescriptions($child);
        }
    }
    unset($child);
}
