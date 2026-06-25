<?php
/**
 * Generates AUDI-API.postman_collection.json from endpoint definitions.
 * Run: php backend/docs/postman/generate-audi-api-collection.php
 */

declare(strict_types=1);

$baseDir = dirname(__DIR__, 2);
$output = $baseDir . '/docs/postman/AUDI-API.postman_collection.json';

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
        'description' => $description,
    ];

    if ($body !== null) {
        $request['header'][] = ['key' => 'Content-Type', 'value' => 'application/json'];
        $request['body'] = [
            'mode' => 'raw',
            'raw' => is_string($body) ? $body : json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ];
    }

    return ['name' => $name, 'request' => $request, 'response' => $opts['response'] ?? []];
}

function folder(string $name, array $items, string $description = ''): array
{
    return [
        'name' => $name,
        'description' => $description,
        'item' => $items,
    ];
}

function adminCrud(string $resource, string $label, array $createBody, string $updateBody = '', bool $withReorder = true): array
{
    $base = "/api/admin/{$resource}";
    $updateBody = $updateBody ?: $createBody;

    $items = [
        req("List {$label}", 'GET', $base . '?page=1&limit=20', [
            'auth' => true,
            'query' => [
                ['key' => 'page', 'value' => '1'],
                ['key' => 'limit', 'value' => '20'],
                ['key' => 'search', 'value' => '', 'disabled' => true],
            ],
        ]),
        req("Create {$label}", 'POST', $base, [
            'auth' => true,
            'body' => $createBody,
        ]),
        req("Show {$label}", 'GET', $base . '/{{id}}', ['auth' => true]),
        req("Update {$label}", 'PUT', $base . '/{{id}}', [
            'auth' => true,
            'body' => $updateBody ?: $createBody,
        ]),
        req("Delete {$label}", 'DELETE', $base . '/{{id}}', ['auth' => true]),
    ];

    if ($withReorder) {
        $items[] = req("Reorder {$label}", 'POST', $base . '/reorder', [
            'auth' => true,
            'body' => ['items' => [['id' => 1, 'sortOrder' => 0], ['id' => 2, 'sortOrder' => 1]]],
        ]);
    }

    return $items;
}

/** index + show + update only (no create/delete/reorder) */
function adminReadUpdate(string $resource, string $label, array $updateBody): array
{
    $base = "/api/admin/{$resource}";

    return [
        req("List {$label}", 'GET', $base . '?page=1&limit=20', [
            'auth' => true,
            'query' => [
                ['key' => 'page', 'value' => '1'],
                ['key' => 'limit', 'value' => '20'],
            ],
        ]),
        req("Show {$label}", 'GET', $base . '/{{id}}', ['auth' => true]),
        req("Update {$label}", 'PUT', $base . '/{{id}}', [
            'auth' => true,
            'body' => $updateBody,
        ]),
    ];
}

/** Phase 0 stub routes — all verbs return placeholder JSON */
function adminStubCrud(string $resource, string $label): array
{
    return adminCrud($resource, $label, ['note' => 'Phase 0 stub — implement controller']);
}

$localeHeader = ['key' => 'Accept-Language', 'value' => '{{locale}}', 'description' => 'ar | en'];

$collection = [
    'info' => [
        '_postman_id' => 'audi-api-full-v1',
        'name' => 'AUDI — Full API (المعهد العربي لإنماء المدن)',
        'description' => "مواصفات الـ API الكاملة لموقع AUDI.\n\n## المتغيرات\n- `baseUrl` — عنوان الـ API (مثل http://localhost:8000)\n- `locale` — ar | en\n- `adminToken` — Bearer token بعد تسجيل الدخول\n- `id`, `slug`, `category` — للطلبات التفصيلية\n\n## المجموعات\n- **Public /api/v1** — للموقع (بدون مصادقة)\n- **Admin /api/admin** — لوحة التحكم (Sanctum Bearer)\n\n> تفاصيل إضافية لقسم الخريطة: `AUDI-Member-Cities.postman_collection.json`",
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

// --- Public ---
$publicHome = folder('Home — الرئيسية', [
    req('Get Home (Aggregate)', 'GET', '/api/v1/home', [
        'headers' => [$localeHeader],
        'description' => 'يجمع slider, stats, memberCities stats, programs preview, mediaCenter, knowledgeCenter, membership.',
    ]),
    req('Get Member Cities Map', 'GET', '/api/v1/home/member-cities', [
        'headers' => [$localeHeader],
        'description' => 'stats + countriesGeoJson + citiesGeoJson. انظر أيضاً AUDI-Member-Cities collection.',
    ]),
    req('Get Countries GeoJSON', 'GET', '/api/v1/home/member-cities/countries.geojson'),
    req('Get Cities GeoJSON', 'GET', '/api/v1/home/member-cities/cities.geojson'),
]);

$publicSettings = folder('Settings — الإعدادات', [
    req('Get Site Settings', 'GET', '/api/v1/settings', [
        'headers' => [$localeHeader],
        'description' => 'siteName, copyright, socialLinks, contact (aggregated from site_settings + social_links).',
    ]),
]);

$publicAbout = folder('About — من نحن', [
    req('Get Institute', 'GET', '/api/v1/about/institute', ['headers' => [$localeHeader]]),
    req('Get Vision & Mission', 'GET', '/api/v1/about/vision-mission', ['headers' => [$localeHeader]]),
    req('Get Leadership (President)', 'GET', '/api/v1/about/leadership/president', ['headers' => [$localeHeader]]),
    req('Get Leadership (Director)', 'GET', '/api/v1/about/leadership/director', ['headers' => [$localeHeader]]),
    req('Get Advisory Board', 'GET', '/api/v1/about/advisory-board', ['headers' => [$localeHeader]]),
    req('Get Team', 'GET', '/api/v1/about/team', ['headers' => [$localeHeader]]),
    req('Get Structure', 'GET', '/api/v1/about/structure', ['headers' => [$localeHeader]]),
    req('Get Partners', 'GET', '/api/v1/about/partners', ['headers' => [$localeHeader]]),
]);

$publicStrategy = folder('Strategy — الاستراتيجية', [
    req('Get Strategy 2025/2026', 'GET', '/api/v1/strategy/strategy-2025', ['headers' => [$localeHeader]]),
    req('List Focus Areas', 'GET', '/api/v1/strategy/focus-areas', ['headers' => [$localeHeader]]),
    req('Get Focus Area Detail', 'GET', '/api/v1/strategy/focus-areas/{{slug}}', ['headers' => [$localeHeader]]),
]);

$publicPrograms = folder('Programs — البرامج', [
    req('Get Urban Policies Program', 'GET', '/api/v1/programs/urban-policies', ['headers' => [$localeHeader]]),
    req('Get Training Program', 'GET', '/api/v1/programs/training', ['headers' => [$localeHeader]]),
    req('Get Partnerships Program', 'GET', '/api/v1/programs/partnerships', ['headers' => [$localeHeader]]),
    req('Get Development Portal Directory', 'GET', '/api/v1/programs/urban-policies/directory', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'tab', 'value' => 'cities', 'description' => 'cities|projects|organizations|publications'],
            ['key' => 'page', 'value' => '1'],
            ['key' => 'limit', 'value' => '20'],
            ['key' => 'countryCode', 'value' => 'SA', 'disabled' => true],
            ['key' => 'search', 'value' => '', 'disabled' => true],
        ],
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
    ]),
    req('List Newsletter', 'GET', '/api/v1/media/newsletter', ['headers' => [$localeHeader]]),
    req('List City Meetings', 'GET', '/api/v1/media/city-meetings', ['headers' => [$localeHeader]]),
    req('List Secretary Speaks', 'GET', '/api/v1/media/secretary-speaks', [
        'headers' => [$localeHeader],
        'description' => 'الأمين يتحدث — رابعة تصنيفات المركز الإعلامي.',
    ]),
    req('Get Media Article Detail', 'GET', '/api/v1/media/{{category}}/{{slug}}', [
        'headers' => [$localeHeader],
        'description' => 'category: news | newsletter | city-meetings | secretary-speaks. يرجّع `key` للتبديل بين اللغات.',
    ]),
]);

$publicCareers = folder('Careers — اعمل معنا', [
    req('List Job Openings', 'GET', '/api/v1/careers', ['headers' => [$localeHeader]]),
    req('Get Job Opening', 'GET', '/api/v1/careers/{{id}}', ['headers' => [$localeHeader]]),
    req('Submit Job Application', 'POST', '/api/v1/careers/apply', [
        'headers' => [$localeHeader],
        'body' => [
            'jobOpeningId' => 1,
            'fullName' => 'سارة عبدالله',
            'email' => 'sara@example.com',
            'phone' => '+966501234567',
            'coverLetter' => 'لدي خبرة في التخطيط الحضري...',
            'cvUrl' => 'https://example.com/cv.pdf',
        ],
    ]),
]);

$publicFaq = folder('FAQ — الأسئلة الشائعة', [
    req('List FAQs', 'GET', '/api/v1/faqs', [
        'headers' => [$localeHeader],
        'query' => [
            ['key' => 'category', 'value' => '', 'disabled' => true, 'description' => 'membership | programs | general'],
        ],
    ]),
]);

$publicLegal = folder('Legal — الشروط والخصوصية', [
    req('Get Terms', 'GET', '/api/v1/legal/terms', ['headers' => [$localeHeader]]),
    req('Get Privacy Policy', 'GET', '/api/v1/legal/privacy', ['headers' => [$localeHeader]]),
]);

$publicForms = folder('Forms — النماذج', [
    req('Get Contact Info', 'GET', '/api/v1/contact', ['headers' => [$localeHeader]]),
    req('Submit Contact Form', 'POST', '/api/v1/contact', [
        'body' => [
            'name' => 'أحمد محمد',
            'phone' => '+966501234567',
            'email' => 'ahmed@example.com',
            'message' => 'استفسار عن برامج المعهد',
        ],
    ]),
    req('Submit Membership Application', 'POST', '/api/v1/membership', [
        'body' => [
            'organizationName' => 'أمانة منطقة الرياض',
            'contactName' => 'محمد العلي',
            'email' => 'contact@example.gov.sa',
            'phone' => '+966114802555',
            'countryCode' => 'SA',
            'city' => 'الرياض',
            'message' => 'طلب عضوية',
        ],
    ]),
    req('Submit Portal Contribution', 'POST', '/api/v1/programs/urban-policies/contribute', [
        'body' => [
            'type' => 'publications',
            'email' => 'researcher@example.com',
            'payload' => ['title' => 'دراسة حضرية', 'description' => '...'],
        ],
    ]),
    req('Subscribe Newsletter', 'POST', '/api/v1/newsletter/subscribe', [
        'body' => ['email' => 'subscriber@example.com', 'locale' => 'ar'],
        'description' => 'Email newsletter signup. Returns 201 for new, 200 if already subscribed.',
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
], 'Endpoints عامة بدون مصادقة — يستهلكها موقع Next.js.');

// --- Admin Auth ---
$adminAuth = folder('Auth — المصادقة', [
    req('Login', 'POST', '/api/admin/auth/login', [
        'body' => ['email' => 'admin@araburban.org', 'password' => 'password'],
        'description' => 'يرجّع `{ token, user }` — احفظ token في `adminToken`.',
    ]),
    req('Logout', 'POST', '/api/admin/auth/logout', ['auth' => true]),
    req('Me', 'GET', '/api/admin/auth/me', ['auth' => true]),
]);

$adminUploads = folder('Uploads — رفع الملفات', [
    req('List Uploads', 'GET', '/api/admin/uploads?page=1&limit=20', ['auth' => true]),
    req('Upload File', 'POST', '/api/admin/uploads', [
        'auth' => true,
        'description' => 'multipart/form-data: file (jpg/jpeg/png/gif/webp/pdf, max 10MB). Returns { data: { id, url, mimeType, originalName, size } }.',
    ]),
    req('Show Upload', 'GET', '/api/admin/uploads/{{id}}', ['auth' => true]),
    req('Delete Upload', 'DELETE', '/api/admin/uploads/{{id}}', ['auth' => true]),
]);

$localizedCreate = [
    'titleAr' => 'عنوان',
    'titleEn' => 'Title',
    'sortOrder' => 0,
    'isActive' => true,
];

$adminResources = folder('Admin — Content CRUD', [
    folder('Settings', adminCrud('settings', 'Site Setting', [
        'key' => 'site.name',
        'valueAr' => 'المعهد العربي لإنماء المدن',
        'valueEn' => 'Arab Urban Development Institute',
        'group' => 'general',
    ], '', false)),
    folder('Social Links', adminCrud('social-links', 'Social Link', [
        'platform' => 'linkedin',
        'url' => 'https://linkedin.com/company/audi',
        'icon' => 'linkedin',
        'isActive' => true,
        'sortOrder' => 0,
    ])),
    folder('Hero Slides', adminCrud('hero-slides', 'Hero Slide', [
        'titleAr' => 'تطوير تقني',
        'titleEn' => 'Technical Development',
        'imageUrl' => null,
        'sortOrder' => 0,
        'isActive' => true,
    ])),
    folder('Home Stats', adminCrud('home-stats', 'Home Stat', [
        'value' => '+25',
        'labelAr' => 'اتفاقية',
        'labelEn' => 'agreements',
        'descriptionAr' => 'الاتفاقيات',
        'descriptionEn' => 'Agreements',
        'sortOrder' => 0,
    ])),
    folder('About Content', adminCrud('about-content', 'About Section', [
        'sectionKey' => 'institute',
        'titleAr' => 'المعهد',
        'titleEn' => 'Institute',
        'bodyAr' => ['فقرة 1'],
        'bodyEn' => ['Paragraph 1'],
    ], '', false)),
    folder('Leadership', adminCrud('leadership', 'Leadership Message', [
        'type' => 'director',
        'nameAr' => 'د. انس المغيري',
        'nameEn' => 'Dr. Anas AlMugairi',
        'positionAr' => 'المدير العام',
        'positionEn' => 'Director General',
        'quoteAr' => '...',
        'quoteEn' => '...',
        'paragraphsAr' => ['...'],
        'paragraphsEn' => ['...'],
        'imageUrl' => '/storage/leadership/director.jpg',
    ], '', false)),
    folder('Advisory Board', adminCrud('advisory-board', 'Advisory Member', [
        'nameAr' => 'د. فيصل بن عبدالعزيز',
        'nameEn' => 'Dr. Faisal',
        'roleAr' => 'رئيس المجلس',
        'roleEn' => 'Board Chair',
        'bioAr' => '...',
        'bioEn' => '...',
        'imageUrl' => '/storage/board/1.png',
        'isFeatured' => true,
        'sortOrder' => 0,
    ])),
    folder('Team Sections', adminCrud('team-sections', 'Team Section', [
        'slug' => 'management',
        'titleAr' => 'الإدارة',
        'titleEn' => 'Management',
        'sortOrder' => 0,
    ])),
    folder('Team Members', adminCrud('team-members', 'Team Member', [
        'teamSectionId' => 1,
        'nameAr' => 'د. أنس المغيري',
        'nameEn' => 'Dr. Anas',
        'roleAr' => 'مدير عام',
        'roleEn' => 'Director General',
        'bioAr' => '...',
        'bioEn' => '...',
        'imageUrl' => '/storage/team/1.png',
        'sortOrder' => 0,
    ])),
    folder('Partner Categories', adminCrud('partner-categories', 'Partner Category', [
        'slug' => 'international',
        'titleAr' => 'المؤسسات الدولية',
        'titleEn' => 'International Organizations',
        'sortOrder' => 0,
    ])),
    folder('Partners', adminCrud('partners', 'Partner', [
        'partnerCategoryId' => 1,
        'nameAr' => 'UN-Habitat',
        'nameEn' => 'UN-Habitat',
        'logoUrl' => '/storage/partners/un-habitat.png',
        'isFeatured' => false,
        'sortOrder' => 0,
    ])),
    folder('Strategy', [
        req('Get Strategy Page', 'GET', '/api/admin/strategy', ['auth' => true]),
        req('Update Strategy Page', 'PUT', '/api/admin/strategy', [
            'auth' => true,
            'body' => [
                'introTitleAr' => '...',
                'introTitleEn' => '...',
                'bookletPdfUrl' => '/storage/strategy/AUDI-Strategy.pdf',
            ],
        ]),
    ]),
    folder('Strategy Pillars', adminCrud('strategy-pillars', 'Strategy Pillar', [
        'number' => '01',
        'textAr' => '...',
        'textEn' => '...',
        'sortOrder' => 0,
    ])),
    folder('Strategy Diagram', adminCrud('strategy-diagram', 'Diagram Item', [
        'itemKey' => 'vision',
        'titleAr' => 'الرؤية',
        'titleEn' => 'Vision',
        'contentAr' => '...',
        'contentEn' => '...',
    ])),
    folder('Focus Areas', adminCrud('focus-areas', 'Focus Area', [
        'slug' => 'urban-resilience',
        'number' => '02',
        'titleAr' => 'المرونة الحضرية',
        'titleEn' => 'Urban Resilience',
        'highlightAr' => 'تخضير المدن',
        'highlightEn' => 'Green Cities',
        'tagsAr' => ['تخضير المدن'],
        'tagsEn' => ['Green Cities'],
        'descriptionAr' => '...',
        'descriptionEn' => '...',
        'listImageUrl' => '/focus-areas/list.png',
        'detailImageUrl' => '/focus-areas/detail.png',
        'isPublished' => true,
        'sortOrder' => 0,
    ])),
    folder('Programs', adminCrud('programs', 'Program', [
        'slug' => 'training',
        'titleAr' => 'التدريب',
        'titleEn' => 'Training',
        'heroIntroAr' => '...',
        'heroIntroEn' => '...',
    ], '', false)),
    folder('Program Sections', adminCrud('program-sections', 'Program Section', [
        'programId' => 1,
        'tabKey' => 'trainingPrograms',
        'titleAr' => 'البرامج التدريبية',
        'titleEn' => 'Training Programs',
        'introAr' => '...',
        'introEn' => '...',
        'sortOrder' => 0,
    ])),
    folder('Training Courses', adminCrud('training-courses', 'Training Course', [
        'titleAr' => 'التخطيط الحضري',
        'titleEn' => 'Urban Planning',
        'countAr' => '3 دورات',
        'countEn' => '3 courses',
        'sortOrder' => 0,
    ])),
    folder('Experts', adminCrud('experts', 'Expert', [
        'nameAr' => 'د. إبراهيم',
        'nameEn' => 'Dr. Ibrahim',
        'specialtyAr' => 'التصميم الحضري',
        'specialtyEn' => 'Urban Design',
        'imageUrl' => '/emp/1.png',
        'sortOrder' => 0,
    ])),
    folder('Directory — Cities', adminCrud('directory/cities', 'Directory City', [
        'number' => '01',
        'nameAr' => 'الرياض، السعودية',
        'nameEn' => 'Riyadh, Saudi Arabia',
        'descriptionAr' => 'مدينة كبيرة',
        'descriptionEn' => 'Large city',
        'countryCode' => 'SA',
        'citySize' => 'large',
    ])),
    folder('Directory — Projects', adminCrud('directory/projects', 'Directory Project', [
        'number' => '01',
        'cityAr' => 'الرياض',
        'cityEn' => 'Riyadh',
        'countryAr' => 'السعودية',
        'countryEn' => 'Saudi Arabia',
        'startDate' => '2019',
        'endDate' => '2023',
    ])),
    folder('Directory — Organizations', adminCrud('directory/organizations', 'Directory Organization', [
        'number' => '01',
        'nameAr' => 'UN-Habitat',
        'nameEn' => 'UN-Habitat',
        'descriptionAr' => 'منظمة دولية',
        'descriptionEn' => 'International org',
    ])),
    folder('Directory — Publications', adminCrud('directory/publications', 'Directory Publication', [
        'number' => '01',
        'nameAr' => 'تقرير 2024',
        'nameEn' => 'Report 2024',
        'descriptionAr' => 'تقرير سنوي',
        'descriptionEn' => 'Annual report',
    ])),
    folder('Resources', adminCrud('resources', 'Resource', [
        'slug' => 'urban-greening',
        'titleAr' => '60 مشروع تخضير',
        'titleEn' => '60 Urban Greening Projects',
        'publishedDate' => '2025-05-29',
        'imageUrl' => '/resources/3.png',
        'fileUrl' => '/storage/resources/greening.pdf',
        'resourceType' => 'report',
        'year' => 2025,
        'isPublished' => true,
        'sortOrder' => 0,
    ])),
    folder('Media Articles', adminCrud('media', 'Media Article', [
        'category' => 'news',
        'key' => 'director-dialogue-session',
        'slugAr' => 'جلسة-حوارية',
        'slugEn' => 'director-dialogue-session',
        'titleAr' => 'مدير عام المعهد يشارك...',
        'titleEn' => 'Director participates...',
        'descriptionAr' => '...',
        'descriptionEn' => '...',
        'bodyAr' => ['فقرة 1'],
        'bodyEn' => ['Paragraph 1'],
        'publishedDate' => '2025-12-29',
        'imageUrl' => '/media/1.png',
        'isPublished' => true,
        'sortOrder' => 0,
    ])),
    folder('FAQs', adminCrud('faqs', 'FAQ', [
        'category' => 'membership',
        'questionAr' => 'كيف يمكنني الانضمام؟',
        'questionEn' => 'How can I join?',
        'answerAr' => 'عبر تعبئة نموذج الانضمام.',
        'answerEn' => 'By filling out the membership form.',
        'isPublished' => true,
        'sortOrder' => 0,
    ])),
    folder('Job Openings', adminCrud('job-openings', 'Job Opening', [
        'titleAr' => 'باحث في السياسات الحضرية',
        'titleEn' => 'Urban Policy Researcher',
        'locationAr' => 'الرياض، السعودية',
        'locationEn' => 'Riyadh, Saudi Arabia',
        'employmentType' => 'full_time',
        'summaryAr' => 'ملخص الوظيفة...',
        'summaryEn' => 'Job summary...',
        'descriptionAr' => ['مسؤولية 1', 'مسؤولية 2'],
        'descriptionEn' => ['Responsibility 1', 'Responsibility 2'],
        'isPublished' => true,
        'sortOrder' => 0,
    ])),
    folder('Legal Pages', adminCrud('legal', 'Legal Page', [
        'slug' => 'terms',
        'titleAr' => 'الشروط والأحكام',
        'titleEn' => 'Terms and Conditions',
        'contentAr' => 'نص الشروط...',
        'contentEn' => 'Terms text...',
        'effectiveDate' => '2026-01-01',
    ], '', false)),
]);

$adminMemberCities = folder('Admin — Member Cities', [
    req('List Cities', 'GET', '/api/admin/member-cities/cities?page=1&limit=20', [
        'auth' => true,
        'query' => [
            ['key' => 'page', 'value' => '1'],
            ['key' => 'limit', 'value' => '20'],
            ['key' => 'countryCode', 'value' => 'AE', 'disabled' => true],
        ],
    ]),
    req('Show City', 'GET', '/api/admin/member-cities/cities/{{id}}', ['auth' => true]),
    req('Create City', 'POST', '/api/admin/member-cities/cities', [
        'auth' => true,
        'body' => [
            'countryCode' => 'AE',
            'nameAr' => 'دبي',
            'nameEn' => 'Dubai',
            'latitude' => 25.2048,
            'longitude' => 55.2708,
            'isActive' => true,
        ],
    ]),
    req('Update City', 'PATCH', '/api/admin/member-cities/cities/{{id}}', ['auth' => true, 'body' => ['isActive' => true]]),
    req('Delete City', 'DELETE', '/api/admin/member-cities/cities/{{id}}', ['auth' => true]),
    req('Get Stats', 'GET', '/api/admin/member-cities/stats', ['auth' => true]),
    req('Update Stats', 'PUT', '/api/admin/member-cities/stats', [
        'auth' => true,
        'body' => ['items' => [['key' => 'members', 'value' => 1240, 'autoCalculate' => false]]],
    ]),
    req('List Countries (no geometry)', 'GET', '/api/admin/member-cities/countries', ['auth' => true]),
    req('Bulk Import Cities', 'POST', '/api/admin/member-cities/cities/import', [
        'auth' => true,
        'body' => ['cities' => [['countryCode' => 'SA', 'nameAr' => 'الرياض', 'nameEn' => 'Riyadh', 'latitude' => 24.7136, 'longitude' => 46.6753]], 'upsertBy' => ['countryCode', 'nameEn']],
    ]),
    req('Import Cities from File', 'POST', '/api/admin/member-cities/cities/import-from-file', [
        'auth' => true,
        'description' => 'multipart/form-data: file (JSON/CSV). Bulk import from uploaded file.',
    ]),
], 'CRUD المدن + الإحصائيات. تفاصيل GeoJSON في AUDI-Member-Cities collection.');

$adminSubmissions = folder('Admin — Form Submissions', [
    req('List Contact Submissions', 'GET', '/api/admin/contact-submissions?page=1', ['auth' => true]),
    req('Show Contact Submission', 'GET', '/api/admin/contact-submissions/{{id}}', ['auth' => true]),
    req('Update Contact Status', 'PATCH', '/api/admin/contact-submissions/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'read'],
    ]),
    req('Delete Contact Submission', 'DELETE', '/api/admin/contact-submissions/{{id}}', ['auth' => true]),
    req('List Membership Applications', 'GET', '/api/admin/membership-applications?page=1', ['auth' => true]),
    req('Show Membership Application', 'GET', '/api/admin/membership-applications/{{id}}', ['auth' => true]),
    req('Update Membership Status', 'PATCH', '/api/admin/membership-applications/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'reviewing'],
    ]),
    req('Delete Membership Application', 'DELETE', '/api/admin/membership-applications/{{id}}', ['auth' => true]),
    req('List Portal Contributions', 'GET', '/api/admin/portal-contributions?page=1', ['auth' => true]),
    req('Show Portal Contribution', 'GET', '/api/admin/portal-contributions/{{id}}', ['auth' => true]),
    req('Update Portal Contribution Status', 'PATCH', '/api/admin/portal-contributions/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'reviewed'],
    ]),
    req('Delete Portal Contribution', 'DELETE', '/api/admin/portal-contributions/{{id}}', ['auth' => true]),
    req('List Job Applications', 'GET', '/api/admin/job-applications?page=1', [
        'auth' => true,
        'query' => [
            ['key' => 'page', 'value' => '1'],
            ['key' => 'status', 'value' => '', 'disabled' => true, 'description' => 'new|reviewing|shortlisted|rejected|hired'],
        ],
    ]),
    req('Show Job Application', 'GET', '/api/admin/job-applications/{{id}}', ['auth' => true]),
    req('Update Job Application Status', 'PATCH', '/api/admin/job-applications/{{id}}', [
        'auth' => true,
        'body' => ['status' => 'reviewing'],
    ]),
    req('Delete Job Application', 'DELETE', '/api/admin/job-applications/{{id}}', ['auth' => true]),
    req('List Newsletter Subscriptions', 'GET', '/api/admin/newsletter-subscriptions?page=1', ['auth' => true]),
    req('Show Newsletter Subscription', 'GET', '/api/admin/newsletter-subscriptions/{{id}}', ['auth' => true]),
    req('Delete Newsletter Subscription', 'DELETE', '/api/admin/newsletter-subscriptions/{{id}}', ['auth' => true]),
]);

$admin = folder('Admin — /api/admin', [
    $adminAuth,
    $adminUploads,
    $adminMemberCities,
    $adminResources,
    $adminSubmissions,
], 'لوحة التحكم — Bearer token مطلوب.');

$collection['item'] = [$public, $admin];

$json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "JSON encode failed\n");
    exit(1);
}

file_put_contents($output, $json . "\n");
echo "Written: {$output}\n";
