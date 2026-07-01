<?php

declare(strict_types=1);

/**
 * Example admin bodies for program pages — matches messages/{ar,en}/programs.json
 * and https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن (slug: training).
 */

/**
 * @return array<string, mixed>
 */
function postmanTrainingProgramBody(): array
{
    return [
        'slug' => 'training',
        'titleAr' => 'التدريب و تطوير القدرات',
        'titleEn' => 'Training & Capacity Building',
        'heroIntroAr' => 'يهدف مركز دعم المدن إلى تلبية احتياجات الأمانات والبلديات العربية من خلال برامج تدريبية واستشارات فنية تعزز القدرات المؤسسية وتنقل الخبرات بين المدن.',
        'heroIntroEn' => 'The City Support Center aims to meet the needs of Arab municipalities through training programs and technical consultations that strengthen institutional capacities and facilitate knowledge exchange among cities.',
        'cardDescriptionAr' => 'برامج تدريبية متخصصة لبناء قدرات العاملين في البلديات والمؤسسات الحضرية في العالم العربي.',
        'cardDescriptionEn' => 'Specialized training programs to build the capabilities of municipal staff and urban institutions across the Arab world.',
        'sortOrder' => 1,
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanProgramTrainingLabelsBody(): array
{
    return [
        'sectionKey' => 'program_training',
        'bodyAr' => [
            'back' => 'رجوع',
            'sectionsLabel' => 'اقسام البرنامج',
        ],
        'bodyEn' => [
            'back' => 'Back',
            'sectionsLabel' => 'Program Sections',
        ],
    ];
}

/**
 * Full tab content for ?tab=trainingPrograms — intro, formats, coursesTitle live in program-section;
 * course rows (screenshot 2) come from POST /api/admin/training-courses (steps 07–12).
 *
 * @return array<string, mixed>
 */
function postmanTrainingProgramsSectionBody(): array
{
    return [
        'programId' => 2,
        'tabKey' => 'trainingPrograms',
        'titleAr' => 'البرامج التدريبية',
        'titleEn' => 'Training Programs',
        'introAr' => 'هدف مركز دعم المدن لتلبية احتياجات ومتطلبات الامانات والبلديات لتطوير القدرات الفنية من خلال رفع كفاءة العاملين في الأمانات والبلديات، القدرة على حل المشكلات والتحديات الوظيفية، ومواكبة التطورات التكنولوجية في مجال العمل. لذلك يوفر منظومة من الخدمات تتضمن سلسلة برامج تدريبية تواكب التغيرات العالمية الحديثة لرفع مستوى جودة الاعمال.',
        'introEn' => 'The City Support Center aims to meet the needs and requirements of municipalities for developing technical capacities by raising the efficiency of municipal employees, building problem-solving skills, and keeping pace with technological developments in the field. It provides a suite of services including training programs aligned with global changes to improve the quality of work.',
        'bodyAr' => [
            'formatsTitle' => 'حيث يتم تقديم هذه البرامج التدريبية على شكل:',
            'formats' => [
                'دورات تدريبية (حضورية وعن بعد).',
                'ورش عمل متخصصة بطبيعة البلديات في المدن العربية معززة بأمثلة تطبيقية من واقع العمل البلدي',
                'منصة تدريب بلدي (MOOC).',
            ],
            'coursesTitle' => 'البرامج التدريبية ٢٠٢٣ – ٢٠٢٤',
            'heroImage' => '/icons/program/6.gif',
            'coursesImage' => '/icons/program/7.png',
        ],
        'bodyEn' => [
            'formatsTitle' => 'These training programs are delivered through:',
            'formats' => [
                'Training courses (in-person and remote).',
                'Specialized workshops tailored to Arab municipalities, enriched with practical examples from municipal work',
                'Municipal training platform (MOOC).',
            ],
            'coursesTitle' => 'Training Programs 2023 – 2024',
            'heroImage' => '/icons/program/6.gif',
            'coursesImage' => '/icons/program/7.png',
        ],
        'imageUrl' => '/icons/program/6.gif',
        'sortOrder' => 0,
    ];
}

/**
 * ?tab=consulting — nav pills + detail sections (screenshots 3–4).
 *
 * @return array<string, mixed>
 */
function postmanConsultingSectionBody(): array
{
    return [
        'programId' => 2,
        'tabKey' => 'consulting',
        'titleAr' => 'الاستشارات الفنية ونقل الخبرات',
        'titleEn' => 'Technical Consultations & Knowledge Transfer',
        'introAr' => 'يحرص مركز دعم المدن على تقديم الدعم الفني للبلديات في إدارة وتنفيذ المشاريع البلدية، رفع جودة الإجراءات والخدمات التي تقدمها البلديات، والاطلاع على الممارسات الفضلى في الأمانات والبلديات.',
        'introEn' => 'The City Support Center is committed to providing technical support to municipalities in managing and implementing municipal projects, improving the quality of procedures and services, and learning from best practices in municipalities.',
        'bodyAr' => [
            'nav' => [
                'استشارات هندسية وإدارية',
                'مشاركة التجارب بين المدن',
                'تبادل الخبرات بين الافراد',
            ],
            'detailImage' => '/projects/consulting-presenter.png',
            'sections' => [
                [
                    'title' => 'استشارات هندسية وإدارية',
                    'description' => 'تعزز هذه الاستشارات قدرات المدن العربية بالمجالات الحضرية المختلفة، وقدرتها على اعداد وتنفيذ الدراسات والمشاريع المتعلقة بالتنمية والتطوير الحضري. وتشمل الاستشارات الفنية اعداد الوثائق الفنية والشروط المرجعية للمشاريع الحضرية، وتمثيل الامانات والبلديات من خلال مراجعة اعمال وتقارير الجهات الاستشارية المنفذة لتلك المشاريع.',
                ],
                [
                    'title' => 'مشاركة التجارب بين المدن',
                    'description' => 'لقاءات تهدف إلى إبراز المبادرات والممارسات المثلى ليتم الاستفادة منها في حل مشكلات حضرية قد تعاني منها أي من المدن الأعضاء. حيث يتشكل من هذه اللقاءات مجتمع تنموي تفاعلي يسمح للبلديات اكتشاف الاهتمامات والاحتياجات الحضرية المشتركة.',
                ],
                [
                    'title' => 'تبادل الخبرات بين الافراد',
                    'description' => 'لقاءات تهدف إلى إبراز المبادرات والممارسات المثلى ليتم الاستفادة منها في حل مشكلات حضرية قد تعاني منها أي من المدن الأعضاء. حيث يتشكل من هذه اللقاءات مجتمع تنموي تفاعلي يسمح للبلديات اكتشاف الاهتمامات والاحتياجات الحضرية المشتركة.',
                ],
            ],
        ],
        'bodyEn' => [
            'nav' => [
                'Engineering and administrative consultations',
                'City experience sharing',
                'Individual knowledge exchange',
            ],
            'detailImage' => '/projects/consulting-presenter.png',
            'sections' => [
                [
                    'title' => 'Engineering and administrative consultations',
                    'description' => 'These consultations enhance Arab cities\' capacities in various urban fields and their ability to prepare and implement studies and projects related to urban development. Technical consultations include preparing technical documents and reference specifications for urban projects, and representing municipalities by reviewing work and reports of consulting entities.',
                ],
                [
                    'title' => 'City experience sharing',
                    'description' => 'Meetings aimed at highlighting best initiatives and practices to address urban challenges faced by member cities, forming an interactive development community that allows municipalities to discover shared urban interests and needs.',
                ],
                [
                    'title' => 'Individual knowledge exchange',
                    'description' => 'Meetings aimed at highlighting best initiatives and practices to address urban challenges faced by member cities, forming an interactive development community that allows municipalities to discover shared urban interests and needs.',
                ],
            ],
        ],
        'imageUrl' => '/projects/p2.png',
        'sortOrder' => 1,
    ];
}

/**
 * ?tab=executive — offers list + topics carousel (screenshots 5–6).
 *
 * @return array<string, mixed>
 */
function postmanExecutiveSectionBody(): array
{
    return [
        'programId' => 2,
        'tabKey' => 'executive',
        'titleAr' => 'البرنامج التنفيذي',
        'titleEn' => 'Executive Program',
        'introAr' => 'برنامج تنفيذي متخصص في التنمية الحضرية ناتج عن تعاون بين المركز وجامعات عالمية. وتم تصميم البرامج التنفيذية بهدف تعزيز منسوبي الامانات والبلديات بالمهارات والمعارف العالمية والإلمام بأحدث النظريات والممارسات في العمل البلدي كما تتاح لهم فرص الاطلاع على كيفية توظيف التكنولوجيا في حلول المشاكل الحضرية.',
        'introEn' => 'An executive program specialized in urban development resulting from cooperation between the Center and international universities. Executive programs are designed to enhance municipal staff with global skills and knowledge, familiarity with the latest theories and practices in municipal work, and opportunities to learn how to employ technology in urban problem-solving.',
        'bodyAr' => [
            'offersTitle' => 'يقدم البرنامج التنفيذي',
            'programs' => [
                'الماجستير التنفيذي في التطوير البلدي',
                'البرنامج الدولي في الابتكار والإدارة الحضرية',
            ],
            'topicsTitle' => 'يقدم البرنامج التنفيذي',
            'heroVideo' => '/icons/program/executive.mp4',
            'topics' => [
                ['title' => 'التغير المناخي', 'image' => 'p1.png'],
                ['title' => 'الاقتصاد الدائري', 'image' => 'p2.png'],
                ['title' => 'التنقل الذكي', 'image' => 'p3.png'],
            ],
        ],
        'bodyEn' => [
            'offersTitle' => 'The executive program offers',
            'programs' => [
                'Executive Master\'s in Municipal Development',
                'International Program in Innovation and Urban Management',
            ],
            'topicsTitle' => 'The executive program offers',
            'heroVideo' => '/icons/program/executive.mp4',
            'topics' => [
                ['title' => 'Climate Change', 'image' => 'p1.png'],
                ['title' => 'Circular Economy', 'image' => 'p2.png'],
                ['title' => 'Smart Mobility', 'image' => 'p3.png'],
            ],
        ],
        'sortOrder' => 2,
    ];
}

/**
 * ?tab=experts — title only in section; expert cards (screenshot 7) from POST /api/admin/experts (steps 13–15).
 *
 * @return array<string, mixed>
 */
function postmanExpertsSectionBody(): array
{
    return [
        'programId' => 2,
        'tabKey' => 'experts',
        'titleAr' => 'خبراء مركز الدعم',
        'titleEn' => 'Support Center Experts',
        'introAr' => null,
        'introEn' => null,
        'bodyAr' => [
            'title' => 'خبراء مركز الدعم',
        ],
        'bodyEn' => [
            'title' => 'Support Center Experts',
        ],
        'sortOrder' => 3,
    ];
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
    return [
        ['name' => 'trainingPrograms', 'body' => postmanTrainingProgramsSectionBody()],
        ['name' => 'consulting', 'body' => postmanConsultingSectionBody()],
        ['name' => 'executive', 'body' => postmanExecutiveSectionBody()],
        ['name' => 'experts', 'body' => postmanExpertsSectionBody()],
    ];
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

**Category (program)** → `programs` table → `POST /api/admin/programs`  
**Items (sections/tabs)** → `program_sections` table → `POST /api/admin/program-sections` with **`programId`**

### Training page sections (اقسام البرنامج)

| Tab on site | `tabKey` | Section step | Extra steps for full tab page |
|-------------|----------|--------------|-------------------------------|
| البرامج التدريبية | `trainingPrograms` | **03** | **07–12** `training-courses` (6 rows) |
| الاستشارات الفنية | `consulting` | **04** | full `bodyAr.nav` + `bodyAr.sections` in step 04 |
| البرنامج التنفيذي | `executive` | **05** | full `bodyAr.programs` + `bodyAr.topics` in step 05 |
| خبراء مركز الدعم | `experts` | **06** | **13–15** `experts` (3 cards) |

## Tab item pages (`?tab=` — frontend only)

When user clicks a tab in **اقسام البرنامج**, the site navigates to the same program URL with `?tab={tabKey}`.  
**There is no separate API endpoint per tab** — one call loads all sections:

```http
GET /api/v1/programs/training
Accept-Language: ar
```

| Live tab URL | `?tab=` value | API field | Admin to edit content |
|--------------|---------------|-----------|------------------------|
| […?tab=trainingPrograms](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=trainingPrograms) | `trainingPrograms` | `sections.trainingPrograms` | steps 03 + 07–12 |
| […?tab=consulting](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=consulting) | `consulting` | `sections.consulting` | step 04 |
| […?tab=executive](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=executive) | `executive` | `sections.executive` | step 05 |
| […?tab=experts](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=experts) | `experts` | `sections.experts` | steps 06 + 13–15 |
MD;
}
