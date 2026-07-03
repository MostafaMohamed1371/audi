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
    }

    return $cache[$locale];
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
 * Body fields stored in program_section_details (matches ProgramsSeeder).
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
 * Full section row split into shell / about / details layers.
 *
 * @return array<string, mixed>
 */
function postmanSectionLegacyFromJson(string $slug, string $tabKey, int $sortOrder): array
{
    $jsonKey = postmanProgramJsonKey($slug);
    $arSection = postmanLoadProgramsJson('ar')[$jsonKey][$tabKey] ?? [];
    $enSection = postmanLoadProgramsJson('en')[$jsonKey][$tabKey] ?? [];

    return [
        'programId' => '{{programId}}',
        'tabKey' => $tabKey,
        'sortOrder' => $sortOrder,
        'titleAr' => $arSection['title'] ?? null,
        'titleEn' => $enSection['title'] ?? null,
        'introAr' => $arSection['intro'] ?? null,
        'introEn' => $enSection['intro'] ?? null,
        'imageUrl' => $arSection['image'] ?? null,
        'bodyAr' => postmanSectionBodyFromProgramsJson($arSection, $tabKey),
        'bodyEn' => postmanSectionBodyFromProgramsJson($enSection, $tabKey),
    ];
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
 * @return array<string, mixed>
 */
function postmanDevelopmentPortalSectionBody(): array
{
    return postmanSectionLegacyFromJson('urban-policies', 'developmentPortal', 0);
}

/**
 * Course rows on ?tab=trainingPrograms (second screen) — NOT stored inside program-section.
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
 * Expert carousel on ?tab=experts — NOT stored inside program-section body.
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

function postmanProgramsTabContentGuide(): string
{
    return <<<'MD'
### Why section Postman bodies looked different from the live tab pages

Each `?tab=` page is built from **multiple admin sources**, not only `program-sections`:

| Tab URL | UI blocks (your screenshots) | Admin source | JSON fields |
|---------|------------------------------|--------------|-------------|
| `?tab=trainingPrograms` | Title + intro paragraph | Step 03 `program-sections` | `titleAr`, `introAr` |
| | Top hero gif | Step 03 `imageUrl` + `bodyAr.heroImage` | `/icons/program/6.gif` |
| | 3 format boxes | Step 03 `bodyAr.formats[]` | `formatsTitle`, `formats` |
| | **البرامج التدريبية ٢٠٢٣–٢٠٢٤** + course rows | Steps **07–12** `training-courses` | `coursesTitle` + `titleAr`/`countAr` per course |
| | Courses section photo | Step 03 `bodyAr.coursesImage` | `/icons/program/7.png` |
| `?tab=consulting` | Title + intro | Step 04 `program-sections` | `titleAr`, `introAr` |
| | Hero photo (nav row) | Step 04 `imageUrl` | `/projects/p2.png` |
| | 3 nav pills | Step 04 `bodyAr.nav[]` | |
| | 3 detail blocks with long text | Step 04 `bodyAr.sections[]` | `title` + `description` each |
| | Presenter photo | Step 04 `bodyAr.detailImage` | `/projects/consulting-presenter.png` |
| `?tab=executive` | Title + intro + 2 program boxes | Step 05 `program-sections` | `offersTitle`, `programs[]` |
| | Hero video | Step 05 `bodyAr.heroVideo` | `/icons/program/executive.mp4` |
| | Topics carousel | Step 05 `bodyAr.topics[]` | `topicsTitle`, `topics[{title, image}]` → `p1.png`… |
| `?tab=experts` | Section title | Step 06 `program-sections` | `bodyAr.title` |
| | Expert cards carousel | Steps **13–15** `experts` | `nameAr`, `specialtyAr`, `imageUrl` (`/emp/1.png`…) |

**One API call** `GET /api/v1/programs/training` merges section JSON + all `training-courses` + all `experts`.
MD;
}

function postmanProgramsSectionGuide(): string
{
    return <<<'MD'
## Programs map | خريطة البرامج

| Page on site (Arabic URL) | API slug | Public endpoint |
|---------------------------|----------|-----------------|
| [/برامجنا/برنامج-السياسات-الحضرية](https://audi-ten.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية) | `urban-policies` | `GET /api/v1/programs/urban-policies` |
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
| […?tab=trainingPrograms](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=trainingPrograms) | `trainingPrograms` | `sections.trainingPrograms` | steps 02–03 + 10–15 |
| […?tab=consulting](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=consulting) | `consulting` | `sections.consulting` | steps 04–05 |
| […?tab=executive](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=executive) | `executive` | `sections.executive` | steps 06–07 |
| […?tab=experts](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=experts) | `experts` | `sections.experts` | steps 08–09 + 16–18 |
MD;
}
