<?php

declare(strict_types=1);

/**
 * Example admin bodies for program pages — loaded from messages/{ar,en}/programs.json
 * (same source as https://audi-ten.vercel.app/ar program pages).
 */

/**
 * @return array<string, mixed>
 */
function postmanLoadProgramsJson(string $locale): array
{
    static $cache = [];
    if (! isset($cache[$locale])) {
        $path = dirname(__DIR__, 3).'/messages/'.$locale.'/programs.json';
        $cache[$locale] = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $cache[$locale] = postmanMergeDirectoryCityDetails($cache[$locale], $locale);
    }

    return $cache[$locale];
}

/**
 * Merge full city detail pages from messages/data/{slug}-detail.{locale}.json
 *
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function postmanMergeDirectoryCityDetails(array $data, string $locale): array
{
    $rows = $data['urbanPolicies']['developmentPortal']['directory']['rows']['cities'] ?? null;
    if (! is_array($rows)) {
        return $data;
    }

    foreach ($rows as $index => $row) {
        if (! is_array($row)) {
            continue;
        }

        $slug = $row['slug'] ?? null;
        if (! is_string($slug) || $slug === '') {
            continue;
        }

        $detailPath = dirname(__DIR__, 3).'/messages/data/'.$slug.'-detail.'.$locale.'.json';
        if (! is_file($detailPath)) {
            continue;
        }

        $detail = json_decode((string) file_get_contents($detailPath), true, 512, JSON_THROW_ON_ERROR);
        $data['urbanPolicies']['developmentPortal']['directory']['rows']['cities'][$index]['detail'] = $detail;
    }

    return $data;
}

/**
 * @return array<string, mixed>
 */
function postmanDirectoryCityDetailExampleAr(): array
{
    $path = dirname(__DIR__, 3).'/messages/data/al-baha-detail.ar.json';

    return is_file($path)
        ? json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR)
        : [];
}

/**
 * @return array<string, mixed>
 */
function postmanDirectoryCityDetailExampleEn(): array
{
    $path = dirname(__DIR__, 3).'/messages/data/al-baha-detail.en.json';

    return is_file($path)
        ? json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR)
        : [];
}

/**
 * City detail pages — matches live site /المدن/{slug}.
 *
 * @return array<int, array{number: string, slug: string, labelAr: string, labelEn: string}>
 */
function postmanDirectoryCityDetailPages(): array
{
    return [
        ['number' => '01', 'slug' => 'al-baha', 'labelAr' => 'الباحة', 'labelEn' => 'Al Baha'],
        ['number' => '02', 'slug' => 'riyadh', 'labelAr' => 'الرياض', 'labelEn' => 'Riyadh'],
        ['number' => '03', 'slug' => 'jeddah', 'labelAr' => 'جدة', 'labelEn' => 'Jeddah'],
        ['number' => '04', 'slug' => 'cairo', 'labelAr' => 'القاهرة', 'labelEn' => 'Cairo'],
        ['number' => '05', 'slug' => 'amman', 'labelAr' => 'عمان', 'labelEn' => 'Amman'],
        ['number' => '06', 'slug' => 'beirut', 'labelAr' => 'بيروت', 'labelEn' => 'Beirut'],
    ];
}

function postmanDirectoryCityDetailPagesGuide(): string
{
    $layouts = [
        'al-baha' => 'rich — صور + نقاشات',
        'riyadh' => 'simple — نص فقط',
        'jeddah' => 'simple — نص فقط',
        'cairo' => 'simple — نص فقط',
        'amman' => 'simple — نص فقط',
        'beirut' => 'simple — نص فقط',
    ];

    $lines = array_map(
        fn (array $city) => sprintf(
            '| `%s` | `%s` | %s | %s | [/%s](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/%s) |',
            $city['number'],
            $city['slug'],
            $city['labelAr'],
            $layouts[$city['slug']] ?? 'simple',
            $city['slug'],
            $city['slug'],
        ),
        postmanDirectoryCityDetailPages(),
    );

    return "**صفحات المدن على الموقع | City detail pages**\n\n"
        ."`detail.layout`: **`rich`** = Al Baha (images, figures, related projects, discussions). "
        ."**`simple`** = Riyadh, Jeddah, Cairo, Amman, Beirut (geography text + CTA only — no images, no discussions on live site).\n\n"
        ."| رقم | slug | المدينة | layout | رابط الموقع |\n|-----|------|---------|--------|-------------|\n"
        .implode("\n", $lines);
}

/**
 * Organization detail pages — matches live site ?directory=organizations&item=01–04.
 *
 * @return array<int, array{number: string, labelAr: string, labelEn: string}>
 */
function postmanDirectoryOrganizationDetailPages(): array
{
    return [
        ['number' => '01', 'labelAr' => 'PLATFORMA', 'labelEn' => 'PLATFORMA'],
        ['number' => '02', 'labelAr' => 'منظمة التعاون والتنمية الاقتصادية', 'labelEn' => 'OECD'],
        ['number' => '03', 'labelAr' => 'الاتحاد الدولي للمواصلات العامة', 'labelEn' => 'UITP'],
        ['number' => '04', 'labelAr' => 'المجلس الأوروبي للبلديات والمناطق', 'labelEn' => 'CEMR'],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanDirectoryOrganizationDetailExampleAr(): array
{
    return postmanDirectoryOrganizationProfileFromRow(
        postmanLoadProgramsJson('ar')['urbanPolicies']['developmentPortal']['directory']['rows']['organizations'][0] ?? []
    );
}

/**
 * @return array<string, mixed>
 */
function postmanDirectoryOrganizationDetailExampleEn(): array
{
    return postmanDirectoryOrganizationProfileFromRow(
        postmanLoadProgramsJson('en')['urbanPolicies']['developmentPortal']['directory']['rows']['organizations'][0] ?? []
    );
}

/**
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>
 */
function postmanDirectoryOrganizationProfileFromRow(array $row): array
{
    $keys = [
        'type',
        'country',
        'countryCode',
        'address',
        'phone',
        'email',
        'website',
        'founded',
        'employees',
        'budget',
        'interventionAreas',
        'interventionFields',
        'interventionTypes',
        'socialLinks',
    ];

    return array_intersect_key($row, array_flip($keys));
}

function postmanDirectoryOrganizationDetailPagesGuide(): string
{
    $lines = array_map(
        fn (array $org) => sprintf(
            '| `%s` | %s | [?item=%s](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=%s) |',
            $org['number'],
            $org['labelAr'],
            $org['number'],
            $org['number'],
        ),
        postmanDirectoryOrganizationDetailPages(),
    );

    return "**صفحات المنظمات على الموقع | Organization detail pages**\n\n"
        ."List tab: `?tab=developmentPortal&directory=organizations`. List shows **type** + **country** (with flag). Detail (`?item=01`–`04`) shows org **name** + profile fields (`organizationFields`).\n\n"
        ."| رقم | المنظمة | رابط الموقع |\n|-----|---------|-------------|\n"
        .implode("\n", $lines);
}

function postmanDirectoryGuides(): string
{
    return postmanDirectoryCityDetailPagesGuide()."\n\n".postmanDirectoryOrganizationDetailPagesGuide();
}

function postmanProgramJsonKey(string $slug): string
{
    return match ($slug) {
        'urban-policies' => 'urbanPolicies',
        'training' => 'training',
        'partnerships' => 'partnerships',
        default => $slug,
    };
}

/**
 * @return list<string>
 */
function postmanProgramTabKeys(string $slug): array
{
    return match ($slug) {
        'training' => ['trainingPrograms', 'consulting', 'executive', 'experts'],
        'urban-policies' => ['developmentPortal', 'developmentIndex', 'innovationLab', 'practiceReports'],
        'partnerships' => ['euroArabDialogue', 'secretarySpeaks', 'urbanAwards', 'partnersGuide'],
        default => [],
    };
}

/**
 * @return array<int, array{name: string, tabKey: string, sortOrder: int}>
 */
function postmanProgramSectionPairs(string $slug): array
{
    return array_map(
        fn (string $tabKey, int $index) => ['name' => $tabKey, 'tabKey' => $tabKey, 'sortOrder' => $index],
        postmanProgramTabKeys($slug),
        array_keys(postmanProgramTabKeys($slug)),
    );
}

/**
 * Body fields stored in program_section_details (matches ProgramsSeeder — strips nested rows).
 *
 * @param  array<string, mixed>  $section
 * @return array<string, mixed>
 */
function postmanSectionBodyFromProgramsJson(array $section, string $tabKey): array
{
    if ($tabKey === 'experts') {
        return ['title' => $section['title'] ?? ''];
    }

    $body = $section;
    unset($body['title'], $body['intro'], $body['image']);

    if ($tabKey === 'trainingPrograms') {
        unset($body['courses']);
    }

    if ($tabKey === 'developmentPortal' && isset($body['directory']) && is_array($body['directory'])) {
        unset($body['directory']['rows']);
    }

    return $body;
}

/**
 * Full tab body for program-section-details — same shape as messages/{locale}/programs.json.
 *
 * @param  array<string, mixed>  $section
 * @return array<string, mixed>
 */
function postmanSectionFullBodyFromProgramsJson(array $section, string $tabKey): array
{
    if ($tabKey === 'experts') {
        return [
            'experts' => $section['experts'] ?? [],
        ];
    }

    $body = $section;
    unset($body['title'], $body['intro'], $body['image']);

    return $body;
}

/**
 * Full section row split into shell / about / details layers.
 *
 * @return array<string, mixed>
 */
function postmanSectionLegacyFromJson(string $slug, string $tabKey, int $sortOrder, bool $fullBody = false): array
{
    $jsonKey = postmanProgramJsonKey($slug);
    $arSection = postmanLoadProgramsJson('ar')[$jsonKey][$tabKey] ?? [];
    $enSection = postmanLoadProgramsJson('en')[$jsonKey][$tabKey] ?? [];
    $bodyFn = $fullBody ? 'postmanSectionFullBodyFromProgramsJson' : 'postmanSectionBodyFromProgramsJson';

    return [
        'programId' => '{{programId}}',
        'tabKey' => $tabKey,
        'sortOrder' => $sortOrder,
        'titleAr' => $arSection['title'] ?? null,
        'titleEn' => $enSection['title'] ?? null,
        'introAr' => $arSection['intro'] ?? null,
        'introEn' => $enSection['intro'] ?? null,
        'imageUrl' => $arSection['image'] ?? null,
        'bodyAr' => $bodyFn($arSection, $tabKey),
        'bodyEn' => $bodyFn($enSection, $tabKey),
    ];
}

/**
 * Training build guide — details body includes courses[] / experts[] (one POST per tab).
 *
 * @return array<string, mixed>
 */
function postmanTrainingSectionLegacyFromJson(string $tabKey, int $sortOrder): array
{
    return postmanSectionLegacyFromJson('training', $tabKey, $sortOrder, fullBody: true);
}

/**
 * @return array<string, mixed>
 */
function postmanProgramBodyFromJson(string $slug, int $sortOrder): array
{
    $jsonKey = postmanProgramJsonKey($slug);
    $pageKey = postmanProgramJsonKey($slug);
    $ar = postmanLoadProgramsJson('ar');
    $en = postmanLoadProgramsJson('en');
    $arProgram = $ar[$jsonKey] ?? [];
    $enProgram = $en[$jsonKey] ?? [];

    $cards = [
        'urban-policies' => [
            'cardDescriptionAr' => 'إعداد دراسات وتقارير سياسات حضرية تدعم صناع القرار في المدن العربية، وتعزز التخطيط الحضري المستدام.',
            'cardDescriptionEn' => 'Developing urban policy studies and reports that support decision-makers in Arab cities and promote sustainable urban planning.',
        ],
        'training' => [
            'cardDescriptionAr' => 'برامج تدريبية متخصصة لبناء قدرات العاملين في البلديات والمؤسسات الحضرية في العالم العربي.',
            'cardDescriptionEn' => 'Specialized training programs to build the capabilities of municipal staff and urban institutions across the Arab world.',
        ],
        'partnerships' => [
            'cardDescriptionAr' => 'معاً لنصنع مستقبل حضري أفضل: تعرف كيف نبني جسور التواصل بين المدن وشركاء التنمية.',
            'cardDescriptionEn' => 'Building strategic partnerships with cities and regional and international institutions to support shared urban development.',
        ],
    ];

    $card = $cards[$slug] ?? ['cardDescriptionAr' => null, 'cardDescriptionEn' => null];

    return array_merge([
        'slug' => $slug,
        'titleAr' => $ar['pages'][$pageKey] ?? $slug,
        'titleEn' => $en['pages'][$pageKey] ?? $slug,
        'heroIntroAr' => $arProgram['heroIntro'] ?? null,
        'heroIntroEn' => $enProgram['heroIntro'] ?? null,
        'sortOrder' => $sortOrder,
    ], $card);
}

/**
 * @return array<string, mixed>
 */
function postmanTrainingProgramBody(): array
{
    return postmanProgramBodyFromJson('training', 1);
}

/**
 * Full tab content for ?tab=trainingPrograms — from programs.json.
 *
 * @return array<string, mixed>
 */
function postmanTrainingProgramsSectionBody(): array
{
    return postmanSectionLegacyFromJson('training', 'trainingPrograms', 0);
}

/**
 * ?tab=consulting — from programs.json.
 *
 * @return array<string, mixed>
 */
function postmanConsultingSectionBody(): array
{
    return postmanSectionLegacyFromJson('training', 'consulting', 1);
}

/**
 * ?tab=executive — from programs.json.
 *
 * @return array<string, mixed>
 */
function postmanExecutiveSectionBody(): array
{
    return postmanSectionLegacyFromJson('training', 'executive', 2);
}

/**
 * ?tab=experts — from programs.json.
 *
 * @return array<string, mixed>
 */
function postmanExpertsSectionBody(): array
{
    return postmanSectionLegacyFromJson('training', 'experts', 3);
}

/**
 * @return array<string, mixed>
 */
function postmanUrbanPoliciesProgramBody(): array
{
    return postmanProgramBodyFromJson('urban-policies', 0);
}

/**
 * Urban policies build — full body from programs.json (includes directory.rows on developmentPortal).
 *
 * @return array<string, mixed>
 */
function postmanUrbanPoliciesSectionLegacyFromJson(string $tabKey, int $sortOrder): array
{
    return postmanSectionLegacyFromJson('urban-policies', $tabKey, $sortOrder, fullBody: true);
}

function postmanUrbanPoliciesTabLabelAr(string $tabKey): string
{
    return match ($tabKey) {
        'developmentPortal' => 'بوابة التنمية الحضرية العربية',
        'developmentIndex' => 'مؤشر التنمية الحضرية',
        'innovationLab' => 'معمل الابتكار الحضري',
        'practiceReports' => 'تقارير الممارسات والسياسات',
        default => $tabKey,
    };
}

function postmanUrbanPoliciesDetailsStepName(string $tabKey, int $step): string
{
    $label = postmanUrbanPoliciesTabLabelAr($tabKey);
    $fields = match ($tabKey) {
        'developmentPortal' => 'paragraphs + directory + detail + discussions',
        'developmentIndex' => 'intro',
        'innovationLab' => 'intro + video + projects[]',
        'practiceReports' => 'intro + projects[]',
        default => 'intro + body',
    };

    return sprintf('%02d — [تفاصيل] %s — %s — program-section-details', $step, $label, $fields);
}

function postmanUrbanPoliciesBuildFolderGuide(): string
{
    return <<<'MD'
### كيف تبني كل تبويب؟ (خطوتان)

| الخطوة | Endpoint | ماذا تخزّن؟ | أين يظهر على الموقع؟ |
|--------|----------|------------|----------------------|
| **أ — قسم** | `POST /api/admin/program-sections` | `programId`, `tabKey`, **عنوان التبويب**, **صورة البطاقة** | بطاقات «اقسام البرنامج» |
| **ب — تفاصيل** | `POST /api/admin/program-section-details` | `programSectionId`, `introAr/En`, **`bodyAr/En` كامل** | صفحة `?tab=` |

**تبويب بوابة التنمية (`developmentPortal`):** ضع **`directory.rows`** (مدن، مشاريع، منظمات، منشورات) داخل **`bodyAr.directory`** في خطوة التفاصيل (03) — **لا** طلبات `directory/*` منفصلة في دليل البناء.

الخادم ينسّخ `directory.rows` تلقائياً إلى جداول `directory_*` عند حفظ التفاصيل.

**تسميات الصفحة** (`back`, `sectionsLabel`): من i18n أو اختياري `about_content` (`program_urban-policies`).

Verify: `GET /api/v1/programs/urban-policies` + `GET /api/v1/programs/urban-policies/directory`
MD;
}

function postmanUrbanPoliciesTabFolderDescription(string $tabKey, int $sectionStep, int $detailsStep): string
{
    $label = postmanUrbanPoliciesTabLabelAr($tabKey);
    $bodyFields = postmanUrbanPoliciesDetailsBodyFieldsGuide($tabKey);
    $extra = postmanUrbanPoliciesTabExtraGuide($tabKey);

    return "**تبويب: {$label}** (`?tab={$tabKey}`) — خطوات {$sectionStep}–{$detailsStep}\n\n"
        ."**{$sectionStep} — قسم:** عنوان + صورة → يحفظ `{{programSectionId}}`\n\n"
        ."**{$detailsStep} — تفاصيل:**\n{$bodyFields}"
        .($extra !== '' ? "\n\n{$extra}" : '');
}

function postmanUrbanPoliciesSectionStepDescription(string $tabKey, int $step): string
{
    $label = postmanUrbanPoliciesTabLabelAr($tabKey);

    return "**الخطوة {$step} — قسم التبويب (عنوان + صورة)**\n\n"
        ."تبويب: **{$label}** (`tabKey: {$tabKey}`)\n\n"
        ."**Body:** `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`\n\n"
        ."يحفظ **`{{programSectionId}}`** للخطوة التالية.\n\n"
        ."**Public:** `GET /api/v1/programs/urban-policies` → `sections.{$tabKey}.title`, `.image`";
}

function postmanUrbanPoliciesDetailsStepDescription(string $tabKey, int $step): string
{
    $label = postmanUrbanPoliciesTabLabelAr($tabKey);
    $bodyFields = postmanUrbanPoliciesDetailsBodyFieldsGuide($tabKey);
    $extra = postmanUrbanPoliciesTabExtraGuide($tabKey);

    return "**الخطوة {$step} — تفاصيل التبويب**\n\n"
        ."تبويب: **{$label}** — بعد القسم في الخطوة ".($step - 1)."\n\n"
        ."**Body:** `programSectionId`, `introAr`, `introEn`, `bodyAr`, `bodyEn`\n\n"
        ."**حقول المحتوى:**\n{$bodyFields}\n\n"
        ."**الموقع:** `/ar/برامجنا/برنامج-السياسات-الحضرية?tab={$tabKey}`"
        .($extra !== '' ? "\n\n{$extra}" : '');
}

function postmanUrbanPoliciesDetailsBodyFieldsGuide(string $tabKey): string
{
    return match ($tabKey) {
        'developmentPortal' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `bodyAr.paragraphs[]` | فقرات المقدمة |
| `bodyAr.contributeTitle` + `contributionTypes` | قسم «ساهم من خلال…» |
| `bodyAr.contributionForm` | نموذج المساهمة (يُرسل إلى `POST /api/v1/programs/urban-policies/contribute`) |
| `bodyAr.directory` | دليل المدن (فيديو، عناوين، تبويبات، أعمدة، تسميات النقاش) |
| `bodyAr.directory.rows.cities[]` | جدول المدن + `detail` + `discussions[]` |
| `bodyAr.directory.rows.projects[]` | جدول المشاريع + `detail` + `discussions[]` |
| `bodyAr.directory.rows.organizations[]` | جدول المنظمات — `{number, name, type, country, countryCode, address, phone, email, website, founded, employees, budget, interventionAreas, interventionFields[], interventionTypes[], socialLinks[]}` |
| `bodyAr.directory.organizationFields` | تسميات حقول صفحة تفاصيل المنظمة |
| `bodyAr.directory.rows.publications[]` | جدول المنشورات + `detail` + `discussions[]` |
| `bodyAr.directory.discussionTitle` | عنوان قسم النقاش في صفحة التفاصيل |
| `bodyAr.directory.shareLabel` / `downloadLabel` | أزرار مشاركة / تحميل في صفحة المدينة |
| `bodyAr.directory.addressLabel` / `sourceLabel` | تسميات تعليقات الصور |
| `bodyAr.directory.relatedProjectsTitle` | عنوان «مشاريع ذات صلة» |
| `bodyAr.directory.rows.cities[].slug` | رابط الصفحة — مثل `al-baha` |
| `bodyAr.directory.rows.*.detail.title` | اسم المدينة في صفحة التفاصيل |
| `bodyAr.directory.rows.*.detail.country` | الدولة تحت العنوان |
| `bodyAr.directory.rows.*.detail.population` | عدد السكان — `(750,000 نسمة)` |
| `bodyAr.directory.rows.*.detail.sections[]` | أقسام المحتوى — `{title, paragraphs[], bullets?, image?, figures?}` |
| `bodyAr.directory.rows.*.detail.sections[].figures[]` | صور مع `{image, caption, address, source}` |
| `bodyAr.directory.rows.*.detail.relatedProjects[]` | مشاريع ذات صلة — `{city, country, dateRange, image, href}` |
| `bodyAr.directory.rows.*.detail.cta` | دعوة للتواصل — `{title, description, button, href}` |
| `bodyAr.directory.rows.*.discussions[]` | تعليقات النقاش — `{author, body}` |
MD,
        'developmentIndex' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة تحت العنوان |
MD,
        'innovationLab' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة أعلى الصفحة |
| `bodyAr.video` | فيديو معمل الابتكار (`?tab=innovationLab`) |
| `bodyAr.videoPoster` | صورة غلاف الفيديو |
| `bodyAr.projectsTitle` | عنوان «المشاريع التي تم تنفيذها» |
| `bodyAr.viewIssue` | نص زر «عرض الإصدار» |
| `bodyAr.projects[]` | بطاقات المشاريع — `{title, date, image, href}` |
MD,
        'practiceReports' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة أعلى الصفحة |
| `bodyAr.projectsTitle` | عنوان «المشاريع التي تم تنفيذها» |
| `bodyAr.viewIssue` | نص زر «عرض الإصدار» |
| `bodyAr.projects[]` | بطاقات التقارير — `{title, date, image, href}` |
MD,
        default => '- راجع `messages/ar/programs.json` → `urbanPolicies.'.$tabKey.'`',
    };
}

function postmanUrbanPoliciesTabExtraGuide(string $tabKey): string
{
    return match ($tabKey) {
        'developmentPortal' => '**`directory.rows` في `bodyAr/En`:** يُنسّخ تلقائياً إلى `directory_*` + `directory_discussions`. تفاصيل المدن: `messages/data/{slug}-detail.{ar,en}.json` (6 مدن). المنظمات: `directory.rows.organizations[]` (4 منظمات — PLATFORMA كاملة + 3 مختصرة). القائمة: `GET /api/v1/programs/urban-policies/directory?tab=organizations`. التفاصيل: `GET .../directory/organizations/{01–04}`.',
        default => '',
    };
}

function postmanUrbanPoliciesTabContentGuide(): string
{
    return <<<'MD'
### محتوى كل تبويب — Urban Policies (?tab=)

| `?tab=` | خطوات | قسم | تفاصيل | داخل body |
|---------|--------|-----|--------|-----------|
| `developmentPortal` | 02–03 | 02 | 03 | paragraphs, directory, `directory.rows` + detail + discussions |
| `developmentIndex` | 04–05 | 04 | 05 | `intro` |
| `innovationLab` | 06–07 | 06 | 07 | `intro`, `video`, `projects[]` |
| `practiceReports` | 08–09 | 08 | 09 | `intro`, `projects[]` |
MD;
}

/**
 * @return array<string, mixed>
 */
function postmanDevelopmentPortalSectionBody(): array
{
    return postmanSectionLegacyFromJson('urban-policies', 'developmentPortal', 0);
}

/**
 * Course rows — used for CRUD reference examples only (build guide uses bodyAr.courses[]).
 *
 * @return array<int, array<string, mixed>>
 */
function postmanTrainingCourses(): array
{
    $courses = [
        ['titleAr' => 'التخطيط والتطوير الحضري', 'titleEn' => 'Urban Planning and Development', 'countAr' => '3 دورات تدريبية', 'countEn' => '3 training courses'],
        ['titleAr' => 'تطوير العمل البلدي للقيادات', 'titleEn' => 'Municipal Leadership Development', 'countAr' => '3 دورات تدريبية', 'countEn' => '3 training courses'],
        ['titleAr' => 'الاستثمار في القطاع البلدي', 'titleEn' => 'Investment in the Municipal Sector', 'countAr' => '3 دورات تدريبية', 'countEn' => '3 training courses'],
    ];

    $rows = [];

    foreach ($courses as $index => $course) {
        $rows[] = array_merge($course, ['sortOrder' => $index]);
        $rows[] = array_merge($course, ['sortOrder' => $index + 3]);
    }

    return $rows;
}

/**
 * Expert carousel rows — used for CRUD reference examples only (build guide uses bodyAr.experts[]).
 *
 * @return array<int, array<string, mixed>>
 */
function postmanTrainingExperts(): array
{
    return [
        [
            'nameAr' => 'د. إبراهيم باهر الدين',
            'nameEn' => 'Dr. Ibrahim Baher El-Din',
            'specialtyAr' => 'التصميم الحضري والتخطيط التشاركي',
            'specialtyEn' => 'Urban Design and Participatory Planning',
            'imageUrl' => '/emp/1.png',
            'sortOrder' => 0,
        ],
        [
            'nameAr' => 'د. خالد الوزني',
            'nameEn' => 'Dr. Khaled Al-Wazni',
            'specialtyAr' => 'سياسات التنمية الاقتصادية',
            'specialtyEn' => 'Economic Development Policies',
            'imageUrl' => '/emp/2.png',
            'sortOrder' => 1,
        ],
        [
            'nameAr' => 'توماس ميرفي',
            'nameEn' => 'Thomas Murphy',
            'specialtyAr' => 'عمدة سابق لمدينة بيتسبرغ',
            'specialtyEn' => 'Former Mayor of Pittsburgh',
            'imageUrl' => '/emp/3.png',
            'sortOrder' => 2,
        ],
    ];
}

/**
 * Training program sections for Postman folder 00 steps 03–06.
 *
 * @return array<int, array{name: string, body: array<string, mixed>}>
 */
function postmanTrainingProgramSections(): array
{
    return array_map(
        fn (array $section) => [
            'name' => $section['name'],
            'body' => postmanSectionLegacyFromJson('training', $section['tabKey'], $section['sortOrder']),
        ],
        postmanProgramSectionPairs('training'),
    );
}

/**
 * @return array<int, array{name: string, body: array<string, mixed>}>
 */
function postmanUrbanPoliciesProgramSections(): array
{
    return array_map(
        fn (array $section) => [
            'name' => $section['name'],
            'body' => postmanSectionLegacyFromJson('urban-policies', $section['tabKey'], $section['sortOrder']),
        ],
        postmanProgramSectionPairs('urban-policies'),
    );
}

/**
 * Create section — programId + tabKey + title + image in ONE request.
 *
 * @param  array<string, mixed>  $legacy
 * @return array<string, mixed>
 */
function postmanSectionCreateBody(array $legacy): array
{
    $body = [
        'programId' => '{{programId}}',
        'tabKey' => $legacy['tabKey'],
        'sortOrder' => $legacy['sortOrder'],
        'titleAr' => $legacy['titleAr'] ?? null,
        'titleEn' => $legacy['titleEn'] ?? null,
    ];

    if (! empty($legacy['imageUrl'])) {
        $body['imageUrl'] = $legacy['imageUrl'];
    }

    return $body;
}

/** @deprecated Use postmanSectionCreateBody */
function postmanSectionShellBody(array $legacy): array
{
    return postmanSectionCreateBody($legacy);
}

/**
 * Section title + image — FK programSectionId.
 *
 * @param  array<string, mixed>  $legacy
 * @return array<string, mixed>
 */
function postmanSectionAboutBodyFromLegacy(array $legacy): array
{
    $body = [
        'programSectionId' => '{{programSectionId}}',
        'titleAr' => $legacy['titleAr'] ?? null,
        'titleEn' => $legacy['titleEn'] ?? null,
    ];

    if (! empty($legacy['imageUrl'])) {
        $body['imageUrl'] = $legacy['imageUrl'];
    }

    return $body;
}

/**
 * Section details — intro, body, optional detail title/image (can differ from section).
 *
 * @param  array<string, mixed>  $legacy
 * @return array<string, mixed>
 */
function postmanSectionDetailsBodyFromLegacy(array $legacy): array
{
    $body = [
        'programSectionId' => '{{programSectionId}}',
        'introAr' => $legacy['introAr'] ?? null,
        'introEn' => $legacy['introEn'] ?? null,
    ];

    if (! empty($legacy['detailTitleAr'])) {
        $body['titleAr'] = $legacy['detailTitleAr'];
    }
    if (! empty($legacy['detailTitleEn'])) {
        $body['titleEn'] = $legacy['detailTitleEn'];
    }
    if (! empty($legacy['detailImageUrl'])) {
        $body['imageUrl'] = $legacy['detailImageUrl'];
    }

    $bodyAr = $legacy['bodyAr'] ?? [];
    $bodyEn = $legacy['bodyEn'] ?? [];

    if (is_array($bodyAr) && $bodyAr !== []) {
        $body['bodyAr'] = $bodyAr;
    }
    if (is_array($bodyEn) && $bodyEn !== []) {
        $body['bodyEn'] = $bodyEn;
    }

    return $body;
}

/**
 * All program-section-details example bodies (training + partnerships).
 *
 * @return array<int, array{name: string, body: array<string, mixed>}>
 */
function postmanAllProgramSectionDetailExamples(): array
{
    $examples = [];

    foreach (['urban-policies', 'training', 'partnerships'] as $slug) {
        foreach (postmanProgramSectionPairs($slug) as $section) {
            $legacy = postmanSectionLegacyFromJson($slug, $section['tabKey'], $section['sortOrder']);
            $examples[] = [
                'name' => $slug.' — '.$section['tabKey'],
                'body' => postmanSectionDetailsBodyFromLegacy($legacy),
                'publicPath' => '/api/v1/programs/'.$slug,
            ];
        }
    }

    return $examples;
}

/**
 * @return array<string, mixed>
 */
function postmanPartnershipsProgramBody(): array
{
    return postmanProgramBodyFromJson('partnerships', 2);
}

/**
 * Postman test script lines — save created id to a collection variable.
 *
 * @return list<string>
 */
function postmanSaveCollectionIdTests(string $variable): array
{
    return [
        "pm.test('save {$variable}', function () {",
        '    pm.expect(pm.response.code).to.be.oneOf([200, 201]);',
        '    const data = pm.response.json().data;',
        '    pm.expect(data.id).to.be.a(\'number\');',
        "    pm.collectionVariables.set('{$variable}', String(data.id));",
        '});',
    ];
}

/**
 * Create section row linked to category via programId (dynamic FK).
 *
 * @return array<string, mixed>
 */
function postmanPartnershipsSectionShellBody(string $tabKey, int $sortOrder): array
{
    return postmanSectionCreateBody(
        postmanSectionLegacyFromJson('partnerships', $tabKey, $sortOrder),
    );
}

/** @deprecated Section title+image are in postmanSectionCreateBody — not about-content */
function postmanPartnershipsSectionAboutBody(string $tabKey): array
{
    return postmanSectionCreateBody(
        postmanSectionLegacyFromJson('partnerships', $tabKey, (int) (array_search($tabKey, postmanProgramTabKeys('partnerships'), true) ?: 0)),
    );
}

/**
 * Step 3 per section — detail text via program-section-details (FK programSectionId).
 *
 * @return array<string, mixed>
 */
function postmanPartnershipsSectionDetailsBody(string $tabKey, int $sortOrder): array
{
    return postmanSectionDetailsBodyFromLegacy(
        postmanSectionLegacyFromJson('partnerships', $tabKey, $sortOrder),
    );
}

/**
 * @return array<int, array{name: string, tabKey: string, sortOrder: int}>
 */
function postmanPartnershipsProgramSectionPairs(): array
{
    return postmanProgramSectionPairs('partnerships');
}

/**
 * Partnership section details for CRUD examples.
 *
 * @return array<int, array{name: string, body: array<string, mixed>}>
 */
function postmanPartnershipsProgramSections(): array
{
    return array_map(
        fn (array $section) => [
            'name' => $section['name'],
            'body' => postmanPartnershipsSectionDetailsBody($section['tabKey'], $section['sortOrder']),
        ],
        postmanPartnershipsProgramSectionPairs(),
    );
}

/** @deprecated Use postmanPartnershipsSectionAboutBody + postmanPartnershipsSectionDetailsBody */
function postmanPartnershipsSectionBody(string $tabKey, int $sortOrder): array
{
    return array_merge(
        postmanPartnershipsSectionAboutBody($tabKey),
        postmanPartnershipsSectionDetailsBody($tabKey, $sortOrder),
    );
}

function postmanTrainingTabLabelAr(string $tabKey): string
{
    return match ($tabKey) {
        'trainingPrograms' => 'البرامج التدريبية',
        'consulting' => 'الاستشارات الفنية',
        'executive' => 'البرنامج التنفيذي',
        'experts' => 'خبراء مركز الدعم',
        default => $tabKey,
    };
}

function postmanTrainingTabStepRange(string $tabKey, int $sectionStep, int $detailsStep): int
{
    return $detailsStep;
}

function postmanTrainingDetailsStepName(string $tabKey, int $step): string
{
    $label = postmanTrainingTabLabelAr($tabKey);
    $fields = match ($tabKey) {
        'trainingPrograms' => 'intro + formats + courses[]',
        'consulting' => 'intro + nav + sections',
        'executive' => 'intro + programs + topics',
        'experts' => 'experts[]',
        default => 'intro + body',
    };

    return sprintf('%02d — [تفاصيل] %s — %s — program-section-details', $step, $label, $fields);
}

function postmanTrainingBuildFolderGuide(): string
{
    return <<<'MD'
### كيف تبني كل تبويب؟ (خطوتان)

| الخطوة | Endpoint | ماذا تخزّن؟ | أين يظهر على الموقع؟ |
|--------|----------|------------|----------------------|
| **أ — قسم** | `POST /api/admin/program-sections` | `programId`, `tabKey`, **عنوان التبويب**, **صورة البطاقة** | بطاقات «اقسام البرنامج» + صورة أعلى صفحة التبويب |
| **ب — تفاصيل** | `POST /api/admin/program-section-details` | `programSectionId`, **المقدمة** (`intro`), **محتوى الصفحة** (`bodyAr/En`) | محتوى صفحة `?tab=` عند النقر على التبويب |

**مهم:** `programSectionId` يُحفظ تلقائياً بعد خطوة **أ** — استخدمه في خطوة **ب** (`{{programSectionId}}`).

**تبويب البرامج التدريبية:** ضع `courses[]` داخل **`bodyAr` / `bodyEn`** في خطوة التفاصيل (03) — **لا** طلبات منفصلة.

**تبويب الخبراء:** ضع `experts[]` داخل **`bodyAr` / `bodyEn`** في خطوة التفاصيل (09) — **لا** طلبات منفصلة.

الخادم ينسّخ `courses[]` و `experts[]` تلقائياً إلى جداولها عند حفظ التفاصيل.

**تسميات الصفحة** (`back`, `sectionsLabel`): من ملفات i18n — **لا** تُنشأ عبر Admin.

Verify: `GET /api/v1/programs/training` + `Accept-Language: ar`
MD;
}

function postmanTrainingTabFolderDescription(string $tabKey, int $sectionStep, int $detailsStep, int $lastStep): string
{
    $label = postmanTrainingTabLabelAr($tabKey);
    $bodyFields = postmanTrainingDetailsBodyFieldsGuide($tabKey);
    $extra = postmanTrainingTabExtraRequestsGuide($tabKey, $detailsStep);

    $extraBlock = $extra !== '' ? "\n\n{$extra}" : '';

    return "**تبويب: {$label}** (`?tab={$tabKey}`) — خطوات {$sectionStep}–{$lastStep}\n\n"
        ."**{$sectionStep} — قسم:** عنوان + صورة البطاقة → يحفظ `{{programSectionId}}`\n\n"
        ."**{$detailsStep} — تفاصيل:** مقدمة + محتوى الصفحة:\n{$bodyFields}"
        .$extraBlock;
}

function postmanTrainingSectionStepDescription(string $tabKey, int $step): string
{
    $label = postmanTrainingTabLabelAr($tabKey);

    return "**الخطوة {$step} — قسم التبويب (عنوان + صورة)**\n\n"
        ."تبويب: **{$label}** (`tabKey: {$tabKey}`)\n\n"
        ."**Body:** `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`\n\n"
        ."يحفظ **`{{programSectionId}}`** — مطلوب للخطوة التالية (تفاصيل).\n\n"
        ."**لا تضع** `intro` أو `body` هنا — ضعها في خطوة **تفاصيل** التبويب.\n\n"
        ."**Public:** `GET /api/v1/programs/training` → `sections.{$tabKey}.title`, `.image`";
}

function postmanTrainingDetailsStepDescription(string $tabKey, int $step): string
{
    $label = postmanTrainingTabLabelAr($tabKey);
    $bodyFields = postmanTrainingDetailsBodyFieldsGuide($tabKey);
    $extra = postmanTrainingTabExtraRequestsGuide($tabKey, $step);
    $extraBlock = $extra !== '' ? "\n\n{$extra}" : '';

    return "**الخطوة {$step} — تفاصيل التبويب (مقدمة + محتوى الصفحة)**\n\n"
        ."تبويب: **{$label}** — بعد إنشاء القسم في الخطوة ".($step - 1)."\n\n"
        ."**Body:** `programSectionId` (= `{{programSectionId}}`), `introAr`, `introEn`, `bodyAr`, `bodyEn`\n\n"
        ."**حقول `bodyAr` / `bodyEn` لهذا التبويب:**\n{$bodyFields}\n\n"
        ."**يظهر على الموقع:** `/ar/برامجنا/مركز-دعم-المدن?tab={$tabKey}` → `sections.{$tabKey}`"
        .$extraBlock;
}

function postmanTrainingDetailsBodyFieldsGuide(string $tabKey): string
{
    return match ($tabKey) {
        'trainingPrograms' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة تحت العنوان |
| `bodyAr.formatsTitle` + `formats[]` | «حيث يتم تقديم هذه البرامج…» + 3 صناديق |
| `bodyAr.coursesTitle` | عنوان «البرامج التدريبية ٢٠٢٣–٢٠٢٤» |
| `bodyAr.heroImage` | صورة GIF أعلى الصفحة |
| `bodyAr.coursesImage` | صورة أسفل قائمة الدورات |
| `bodyAr.courses[]` | شبكة «البرامج التدريبية ٢٠٢٣–٢٠٢٤» — `{title, count}` لكل صف |
MD,
        'consulting' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة |
| `bodyAr.nav[]` | 3 أزرار التنقل (استشارات هندسية…، مشاركة التجارب…، تبادل الخبرات…) |
| `bodyAr.sections[]` | 3 كتل (title + description لكل كتلة) |
| `bodyAr.detailImage` | صورة المتحدث `/projects/consulting-presenter.png` |
MD,
        'executive' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `introAr/En` | فقرة المقدمة |
| `bodyAr.offersTitle` + `programs[]` | «يقدم البرنامج التنفيذي» + صندوقان |
| `bodyAr.heroVideo` | فيديو `/icons/program/executive.mp4` |
| `bodyAr.topicsTitle` + `topics[]` | كاروسيل الموضوعات (title + image) |
MD,
        'experts' => <<<'MD'
| الحقل | على الموقع |
|-------|------------|
| `bodyAr.experts[]` | بطاقات الخبراء — `{name, specialty, image}` لكل خبير |
MD,
        default => '- راجع `messages/ar/programs.json` → `training.'.$tabKey.'`',
    };
}

function postmanTrainingTabExtraRequestsGuide(string $tabKey, int $detailsStep): string
{
    return match ($tabKey) {
        'trainingPrograms' => '**`courses[]` في `bodyAr/En`:** الخادم ينسّخها تلقائياً إلى `training_courses` عند حفظ التفاصيل.',
        'experts' => '**`experts[]` في `bodyAr/En`:** الخادم ينسّخها تلقائياً إلى `experts` عند حفظ التفاصيل. العنوان في خطوة **القسم** (08).',
        default => '',
    };
}

function postmanProgramsTabContentGuide(): string
{
    return <<<'MD'
### محتوى كل تبويب — Training (?tab=)

كل صفحة تبويب = **قسم** (02/04/06/08) + **تفاصيل** (03/05/07/09) — جسم كامل مثل `programs.json`:

| `?tab=` | خطوات | قسم | تفاصيل | داخل `bodyAr/En` |
|---------|--------|-----|--------|------------------|
| `trainingPrograms` | 02–03 | 02 | 03 | `courses[]` في body |
| `consulting` | 04–05 | 04 | 05 | — |
| `executive` | 06–07 | 06 | 07 | — |
| `experts` | 08–09 | 08 | 09 | `experts[]` في body |

**طلب واحد** `GET /api/v1/programs/training` يدمج كل الأقسام + الدورات + الخبراء.
MD;
}

function postmanProgramsSectionGuide(): string
{
    return <<<'MD'
## Programs map | خريطة البرامج

| Page on site (Arabic URL) | API slug | Public endpoint |
|---------------------------|----------|-----------------|
| [/برامجنا/برنامج-السياسات-الحضرية](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية) | `urban-policies` | `GET /api/v1/programs/urban-policies` |
| [/برامجنا/مركز-دعم-المدن](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن) | `training` | `GET /api/v1/programs/training` |
| [/برامجنا/الشراكات](https://audi-ten.vercel.app/ar/برامجنا/الشراكات) | `partnerships` | `GET /api/v1/programs/partnerships` |

**Category (program)** → `programs` table → `POST /api/admin/programs` → save **`{{programId}}`**  
**Section (tab)** → `program_sections` → `POST /api/admin/program-sections` with **`programId`**, **`tabKey`**, **`titleAr/En`**, **`imageUrl`** → save **`{{programSectionId}}`**  
**Section details (intro/body)** → `program_section_details` → `POST /api/admin/program-section-details` with **`programSectionId`**

### Partnerships page sections (اقسام البرنامج)

| Tab on site | `tabKey` | Step A: program-sections | Step B: program-section-details |
|-------------|----------|--------------------------|--------------------------------|
| حوار المدن العربية الأوروبية | `euroArabDialogue` | `programId`, `tabKey`, title, image | `programSectionId`, `introAr/En` |
| الأمين يتحدث | `secretarySpeaks` | same | same |
| جوائز التنمية الحضرية | `urbanAwards` | same | same |
| دليل شركاء التنمية الحضرية | `partnersGuide` | same | same |

**Program page labels** (`back`, `sectionsLabel`) for **training** and **partnerships**: frontend i18n only — `messages/{locale}/programs.json`. **Not** an admin API step.

Update: `PUT /api/admin/programs/{{programId}}`, `PUT /api/admin/program-sections/{{programSectionId}}`, `PUT /api/admin/program-section-details/{{programSectionDetailId}}`.

### CRUD folders (Postman)

| Resource | Folder | Endpoints |
|----------|--------|-----------|
| Category | `01 — مرجع CRUD` → `البرامج` | GET list, POST, GET show, PUT, DELETE |
| Section | `01 — مرجع CRUD` → `أقسام البرنامج` | GET list (`?programId=`), POST, GET/PUT/DELETE `{{programSectionId}}`, reorder |
| Details | `01 — مرجع CRUD` → `تفاصيل أقسام البرنامج` | GET list (`?programId=&programSectionId=`), POST, GET/PUT/DELETE `{{programSectionDetailId}}` |

## Tab item pages (`?tab=` — frontend only)

When user clicks a tab in **اقسام البرنامج**, the site navigates to the same program URL with `?tab={tabKey}`.  
**There is no separate API endpoint per tab** — one call loads all sections:

```http
GET /api/v1/programs/training
Accept-Language: ar
```

| Live tab URL | `?tab=` value | API field | Admin to edit content |
|--------------|---------------|-----------|------------------------|
| […?tab=trainingPrograms](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=trainingPrograms) | `trainingPrograms` | `sections.trainingPrograms` | steps 02–03 (body includes `courses[]`) |
| […?tab=consulting](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=consulting) | `consulting` | `sections.consulting` | steps 04–05 |
| […?tab=executive](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=executive) | `executive` | `sections.executive` | steps 06–07 |
| […?tab=experts](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=experts) | `experts` | `sections.experts` | steps 08–09 (body includes `experts[]`) |
MD;
}
