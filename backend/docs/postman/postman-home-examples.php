<?php

declare(strict_types=1);

/**
 * Example admin request bodies for https://audi-ten.vercel.app/ar homepage sections.
 * Used by generate-audi-api-collection.php — keep in sync with messages/{ar,en}/home.json.
 */

function postmanHomepageSectionGuide(): string
{
    return <<<'MD'
## Homepage map | خريطة الصفحة الرئيسية

Matches live site: https://audi-ten.vercel.app/ar → public `GET /api/v1/home`

| Section on site | Admin folder / endpoint | Public field |
|-----------------|-------------------------|--------------|
| Hero slider (تطوير تقني…) | `شرائح الهيرو` → `POST /api/admin/hero-slides` | `slider[]` |
| About intro + mission/vision | `محتوى أقسام الرئيسية` → `home_about_intro` | `aboutIntro` |
| المعهد في أرقام (title/subtitle) | `home_stats` in about-content | `stats.title`, `stats.subtitle` |
| المعهد في أرقام (4 counters) | `إحصائيات الرئيسية` → `POST /api/admin/home-stats` | `stats.items[]` |
| المدن الأعضاء (title) | `home_member_cities` in about-content | `memberCities.title` |
| المدن الأعضاء (12 / 400 / 1240) | `المدن الأعضاء` → `PUT /api/admin/member-cities/stats` | `memberCities.stats[]` |
| برامجنا (title + CTA) | `home_programs` in about-content | `programs.title`, `programs.cta` |
| برامجنا (3 cards) | `البرامج` → `POST /api/admin/programs` | `programs.items[]` |
| المركز الإعلامي (labels) | `محتوى أقسام الرئيسية` → `home_media_center` | `mediaCenter.title`, `subtitle`, `readMore`, `viewAll` |
| المركز الإعلامي (news cards) | `بطاقات المركز الإعلامي` → `POST /api/admin/media` (`category: news`) | `mediaCenter.featured[]` (newest 4) + `mediaCenter.items[]` (next 4) |
| مركز المعرفة (carousel + labels) | `تصنيفات مركز المعرفة` → `POST /api/admin/knowledge-categories` | `knowledgeCenter.categories[]`, `headerSlides[]` |
| مركز المعرفة (3 cards) | `المصادر` → `POST /api/admin/resources` (`knowledgeCategoryId`) | `knowledgeCenter.categories[].items[]` |
| عضوية + تواصل (labels) | `عضوية وتواصل` → `home_membership_contact` | `membershipContact.membership`, `contact.title` |
| تواصل (phone, fax, email, address, map) | `عضوية وتواصل` → `PUT /api/admin/contact-info` | `membershipContact.contact` |

**Quick start:** run requests in folder `00 — بناء الصفحة الرئيسية` top to bottom (matches https://audi-ten.vercel.app/ar).

**Note:** Stats icons (`/icons/num1.svg`…), knowledge carousel logos (`/knowledgeCenter/icon*.png`), and hero images (`/slider/*.png`) are static frontend assets.
MD;
}

/**
 * @return array<string, mixed>
 */
function postmanHomeAboutIntroBody(): array
{
    return [
        'sectionKey' => 'home_about_intro',
        'titleAr' => 'المعهد العربي لإنماء المدن',
        'titleEn' => 'Arab Urban Development Institute',
        'bodyAr' => [
            'description' => 'تأسس المعهد العربي لإنماء المدن عام 1980، ومقره الرئيسي في الرياض، وهو مؤسسة غير ربحية تعمل في مجال البحث والتدريب في التنمية العمرانية، وتسعى إلى دعم المدن والبلديات العربية في مواجهة تحديات التنمية الحضرية المستدامة.',
            'cta' => 'المزيد',
            'mission' => [
                'title' => 'رسالتنا',
                'description' => 'مؤسسة عالمية رائدة تسهم في خلق مستقبل عمراني أفضل قائم على الإبداع والشراكة بين المدن ووكالات التنمية في العالم العربي.',
                'readMore' => 'قراءة المزيد',
            ],
            'vision' => [
                'title' => 'رؤيتنا',
                'description' => 'دعم المدن والبلديات العربية لمواجهة تحديات التنمية العمرانية، عبر الدراسات وبرامج التدريب وتعزيز ثقافة التعاون والتعلم المشترك.',
                'readMore' => 'قراءة المزيد',
            ],
        ],
        'bodyEn' => [
            'description' => 'Founded in 1980 and headquartered in Riyadh, the Arab Urban Development Institute is a non-profit organization dedicated to research and training in urban development, supporting Arab cities and municipalities in meeting the challenges of sustainable urban growth.',
            'cta' => 'Learn More',
            'mission' => [
                'title' => 'Our Mission',
                'description' => 'A leading global institution contributing to a better urban future based on innovation and partnership between cities and development agencies in the Arab world.',
                'readMore' => 'Read More',
            ],
            'vision' => [
                'title' => 'Our Vision',
                'description' => 'Supporting Arab cities and municipalities in facing urban development challenges through studies, training programs, and promoting a culture of cooperation and shared learning.',
                'readMore' => 'Read More',
            ],
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeStatsBody(): array
{
    return [
        'sectionKey' => 'home_stats',
        'titleAr' => 'المعهد في أرقام',
        'titleEn' => 'The Institute in Numbers',
        'bodyAr' => [
            'subtitle' => 'مؤسسة عالمية رائدة تساهم في صنع مستقبل حضري أفضل قائم على',
        ],
        'bodyEn' => [
            'subtitle' => 'A leading global institution contributing to creating a better urban future based on',
        ],
    ];
}

/**
 * @return array<int, array<string, mixed>>
 */
function postmanHomeStatItems(): array
{
    return [
        [
            'value' => '+25',
            'labelAr' => 'اتفاقية',
            'labelEn' => 'Agreements',
            'descriptionAr' => 'الاتفاقيات',
            'descriptionEn' => 'Partnership agreements',
            'sortOrder' => 0,
        ],
        [
            'value' => '+10',
            'labelAr' => 'نشرة',
            'labelEn' => 'Newsletters',
            'descriptionAr' => 'نشرة مدننا',
            'descriptionEn' => 'Our Cities Newsletter',
            'sortOrder' => 1,
        ],
        [
            'value' => '+500',
            'labelAr' => 'مشارك',
            'labelEn' => 'Participants',
            'descriptionAr' => 'المشاركين في برامج القيادات البلدية',
            'descriptionEn' => 'Participants in municipal leadership programs',
            'sortOrder' => 2,
        ],
        [
            'value' => '+400',
            'labelAr' => 'مشروع',
            'labelEn' => 'Projects',
            'descriptionAr' => 'مشروع في تقارير السياسات الحضرية',
            'descriptionEn' => 'Projects in urban policy reports',
            'sortOrder' => 3,
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeMemberCitiesBody(): array
{
    return [
        'sectionKey' => 'home_member_cities',
        'titleAr' => 'المدن الأعضاء',
        'titleEn' => 'Member Cities',
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeProgramsBody(): array
{
    return [
        'sectionKey' => 'home_programs',
        'titleAr' => 'برامجنا',
        'titleEn' => 'Our Programs',
        'bodyAr' => ['cta' => 'استكشف'],
        'bodyEn' => ['cta' => 'Explore'],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeMediaCenterBody(): array
{
    return [
        'sectionKey' => 'home_media_center',
        'titleAr' => 'المركز الإعلامي',
        'titleEn' => 'Media Center',
        'bodyAr' => [
            'subtitle' => 'تعرف على أخبارنا ونشرتنا الدورية ومنشوراتنا المرئية والصوتية.',
            'readMore' => 'قراءة المزيد',
            'viewAll' => 'عرض الكل',
        ],
        'bodyEn' => [
            'subtitle' => 'Explore our news, periodic newsletter, and audiovisual publications.',
            'readMore' => 'Read more',
            'viewAll' => 'View all',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeKnowledgeCenterBody(): array
{
    return [
        'sectionKey' => 'home_knowledge_center',
        'bodyAr' => [
            'viewIssue' => 'عرض الإصدار',
            'downloadPdf' => 'تنزيل نسخة PDF',
        ],
        'bodyEn' => [
            'viewIssue' => 'View Issue',
            'downloadPdf' => 'Download PDF',
        ],
    ];
}

/**
 * Homepage knowledge center carousel categories (مركز المعرفة / مدننا / منصة الاجتماعات).
 *
 * @return array<int, array<string, mixed>>
 */
function postmanHomeKnowledgeCategories(): array
{
    return [
        [
            'slug' => 'knowledge-center',
            'titleAr' => 'مركز المعرفة',
            'titleEn' => 'Knowledge Center',
            'descriptionAr' => 'منصة تجمع كل إصدارات المعهد الرقمية والصوتية، بما فيها الاجتماعات والنشرات والبودكاستات والمواد المعرفية.',
            'descriptionEn' => 'A platform bringing together all of the Institute\'s digital and audio publications, including meetings, newsletters, podcasts, and knowledge materials.',
            'sortOrder' => 0,
        ],
        [
            'slug' => 'mudununa',
            'titleAr' => 'مدننا',
            'titleEn' => 'Mudununa',
            'descriptionAr' => 'نشرة دورية تصدر عن المعهد العربي لإنماء المدن، تسلط الضوء على قضايا التنمية العمرانية والممارسات الحضرية في المدن العربية.',
            'descriptionEn' => 'A periodic newsletter published by the Arab Urban Development Institute, highlighting urban development issues and practices in Arab cities.',
            'sortOrder' => 1,
        ],
        [
            'slug' => 'meetings-platform',
            'titleAr' => 'منصة الاجتماعات',
            'titleEn' => 'Meetings Platform',
            'descriptionAr' => 'أرشيف رقمي لفعاليات واجتماعات المعهد، يوثق النقاشات والتوصيات حول التحديات الحضرية في العالم العربي.',
            'descriptionEn' => 'A digital archive of the Institute\'s events and meetings, documenting discussions and recommendations on urban challenges in the Arab world.',
            'sortOrder' => 2,
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeMembershipContactBody(): array
{
    return [
        'sectionKey' => 'home_membership_contact',
        'bodyAr' => [
            'membership' => [
                'title' => 'انضم الى عضوية المعهد',
                'subtitle' => 'لنتشارك في صنع مستقبل واعد لمدننا العربية',
                'cta' => 'انضم الآن',
                'href' => '/contact#membership',
            ],
            'contact' => [
                'title' => 'تواصل معنا',
                'addressTitle' => 'العنوان',
            ],
        ],
        'bodyEn' => [
            'membership' => [
                'title' => 'Join the Institute\'s Membership',
                'subtitle' => 'Let\'s work together to build a promising future for our Arab cities',
                'cta' => 'Join Now',
                'href' => '/contact#membership',
            ],
            'contact' => [
                'title' => 'Contact Us',
                'addressTitle' => 'Address',
            ],
        ],
    ];
}

/**
 * @return array<int, array<string, mixed>>
 */
function postmanHomeHeroSlides(): array
{
    return [
        ['titleAr' => 'تطوير تقني', 'titleEn' => 'Technical Development', 'imageUrl' => '/slider/1.png', 'sortOrder' => 0],
        ['titleAr' => 'تنمية عمرانية', 'titleEn' => 'Urban Development', 'imageUrl' => '/slider/2.png', 'sortOrder' => 1],
        ['titleAr' => 'شراكات مستدامة', 'titleEn' => 'Sustainable Partnerships', 'imageUrl' => '/slider/3.png', 'sortOrder' => 2],
        ['titleAr' => 'بحوث ومبادرات', 'titleEn' => 'Research and Initiatives', 'imageUrl' => '/slider/4.png', 'sortOrder' => 3],
    ];
}

/**
 * Homepage program cards (3 programs on /ar).
 *
 * @return array<int, array<string, mixed>>
 */
function postmanHomeProgramCards(): array
{
    return [
        [
            'slug' => 'urban-policies',
            'titleAr' => 'السياسات الحضرية',
            'titleEn' => 'Urban Policies',
            'cardDescriptionAr' => 'إعداد دراسات وتقارير سياسات حضرية تدعم صناع القرار في المدن العربية، وتعزز التخطيط الحضري المستدام.',
            'cardDescriptionEn' => 'Developing urban policy studies and reports that support decision-makers in Arab cities and promote sustainable urban planning.',
            'sortOrder' => 0,
        ],
        [
            'slug' => 'training',
            'titleAr' => 'التدريب و تطوير القدرات',
            'titleEn' => 'Training & Capacity Building',
            'cardDescriptionAr' => 'برامج تدريبية متخصصة لبناء قدرات العاملين في البلديات والمؤسسات الحضرية في العالم العربي.',
            'cardDescriptionEn' => 'Specialized training programs to build the capabilities of municipal staff and urban institutions across the Arab world.',
            'sortOrder' => 1,
        ],
        [
            'slug' => 'partnerships',
            'titleAr' => 'الشراكات',
            'titleEn' => 'Partnerships',
            'cardDescriptionAr' => 'معاً لنصنع مستقبل حضري أفضل: تعرف كيف نبني جسور التواصل بين المدن وشركاء التنمية.',
            'cardDescriptionEn' => 'Building strategic partnerships with cities and regional and international institutions to support shared urban development.',
            'sortOrder' => 2,
        ],
    ];
}

/**
 * Resource cards for homepage knowledge center (category: knowledge-center → id 1 after steps 26–28).
 *
 * @return array<int, array<string, mixed>>
 */
function postmanHomeKnowledgeCenterResources(): array
{
    return [
        [
            'slug' => 'solid-waste-management',
            'titleAr' => 'إدارة النفايات الصلبة في المدن العربية',
            'titleEn' => 'Solid Waste Management in Arab Cities',
            'publishedDate' => '2025-12-29',
            'imageUrl' => '/our-sources/1.png',
            'fileUrl' => '/storage/resources/solid-waste-management.pdf',
            'knowledgeCategoryId' => 1,
            'isPublished' => true,
            'sortOrder' => 0,
        ],
        [
            'slug' => 'urban-tourism',
            'titleAr' => 'المدينة، وجهة تكتشف السياحة الحضرية في المنطقة العربية',
            'titleEn' => 'The City: Discovering Urban Tourism in the Arab Region',
            'publishedDate' => '2025-12-29',
            'imageUrl' => '/our-sources/2.png',
            'fileUrl' => '/storage/resources/urban-tourism.pdf',
            'knowledgeCategoryId' => 1,
            'isPublished' => true,
            'sortOrder' => 1,
        ],
        [
            'slug' => 'green-infrastructure',
            'titleAr' => 'البنية التحتية الخضراء نحو منظومة خضراء متكاملة في المدن العربية',
            'titleEn' => 'Green Infrastructure Toward an Integrated Green System in Arab Cities',
            'publishedDate' => '2025-12-29',
            'imageUrl' => '/our-sources/4.png',
            'fileUrl' => '/storage/resources/green-infrastructure.pdf',
            'knowledgeCategoryId' => 1,
            'isPublished' => true,
            'sortOrder' => 2,
        ],
    ];
}

/**
 * News articles for homepage المركز الإعلامي (matches messages/{ar,en}/media.json + https://audi-ten.vercel.app/ar).
 *
 * @return array<int, array{name: string, labelAr: string, homePlacement: string, body: array<string, mixed>}>
 */
function postmanHomeMediaNewsArticles(): array
{
    return [
        [
            'name' => 'director-dialogue-session',
            'labelAr' => 'خبر 1 — جلسة حوارية الفطيم (الكاروسيل)',
            'homePlacement' => 'Large featured carousel slide on homepage. Newest `publishedDate` → first in `mediaCenter.featured[]`.',
            'body' => [
                'category' => 'news',
                'key' => 'director-dialogue-session',
                'slugAr' => 'جلسة-حوارية-التنمية-الحضرية',
                'slugEn' => 'director-dialogue-session',
                'titleAr' => 'مدير عام المعهد يشارك في جلسة حوارية للفطيم حول التنمية الحضرية',
                'titleEn' => 'Institute Director Participates in Al-Futtaim Dialogue on Urban Development',
                'descriptionAr' => 'شارك مدير عام المعهد العربي لإنماء المدن في جلسة حوارية نظمتها مجموعة الفطيم، لمناقشة مستقبل التنمية الحضرية في المنطقة.',
                'descriptionEn' => 'The Director General of the Arab Urban Development Institute participated in a dialogue session organized by Al-Futtaim Group on the future of urban development.',
                'bodyAr' => [
                    'شارك مدير عام المعهد العربي لإنماء المدن في جلسة حوارية نظمتها مجموعة الفطيم، بحضور نخبة من الخبراء وصناع القرار في مجال التنمية العمرانية.',
                    'ناقشت الجلسة أبرز التحديات التي تواجه المدن العربية في ظل التحولات العالمية، وسبل تعزيز الشراكات بين القطاعين العام والخاص لتحقيق تنمية حضرية مستدامة.',
                ],
                'bodyEn' => [
                    'The Director General of the Arab Urban Development Institute participated in a dialogue session organized by Al-Futtaim Group, attended by leading experts and decision-makers in urban development.',
                    'The session discussed key challenges facing Arab cities amid global transformations, and ways to strengthen public-private partnerships for sustainable urban development.',
                ],
                'publishedDate' => '2025-12-29',
                'imageUrl' => '/blog/1.png',
                'pdfUrl' => null,
                'authorsAr' => null,
                'authorsEn' => null,
                'eventTime' => null,
                'isPublished' => true,
                'sortOrder' => 0,
            ],
        ],
        [
            'name' => 'uae-contractors-league',
            'labelAr' => 'خبر 2 — الدوري الإماراتي لمقاولي العمران',
            'homePlacement' => 'News card on homepage grid → `mediaCenter.featured[]` or `mediaCenter.items[]` depending on publish order.',
            'body' => [
                'category' => 'news',
                'key' => 'uae-contractors-league',
                'slugAr' => 'دوري-المقاولين-الإماراتي',
                'slugEn' => 'uae-contractors-league',
                'titleAr' => 'الدوري الإماراتي لمقاولي العمران يفتتح بمشاركة القيادات البلدية',
                'titleEn' => 'UAE Urban Contractors League Opens with Municipal Leadership',
                'descriptionAr' => 'انطلقت فعاليات الدوري الإماراتي لمقاولي العمران بمشاركة واسعة من القيادات البلدية والمختصين في قطاع التنمية العمرانية.',
                'descriptionEn' => 'The UAE Urban Contractors League launched with broad participation from municipal leaders and urban development specialists.',
                'bodyAr' => [
                    'انطلقت فعاليات الدوري الإماراتي لمقاولي العمران بمشاركة واسعة من القيادات البلدية والمختصين في قطاع التنمية العمرانية.',
                    'يهدف الدوري إلى تعزيز جودة الممارسات في قطاع المقاولات العمرانية، وتبادل الخبرات بين المدن العربية في مجالات البنية التحتية والتخطيط الحضري.',
                ],
                'bodyEn' => [
                    'The UAE Urban Contractors League launched with broad participation from municipal leaders and specialists in urban development.',
                    'The league aims to enhance quality practices in the construction sector and exchange expertise among Arab cities in infrastructure and urban planning.',
                ],
                'publishedDate' => '2025-01-22',
                'imageUrl' => '/blog/2.png',
                'pdfUrl' => null,
                'isPublished' => true,
                'sortOrder' => 1,
            ],
        ],
        [
            'name' => 'municipal-cooperation-network',
            'labelAr' => 'خبر 3 — شبكة التعاون البلدي العربية',
            'homePlacement' => 'News card on homepage → `mediaCenter.items[]` when 5+ published news articles exist.',
            'body' => [
                'category' => 'news',
                'key' => 'municipal-cooperation-network',
                'slugAr' => 'شبكة-التعاون-البلدي-العربية',
                'slugEn' => 'municipal-cooperation-network',
                'titleAr' => 'المعهد العربي ينظم الاجتماع السنوي لشبكة التعاون البلدي العربية',
                'titleEn' => 'Institute Holds Annual Arab Municipal Cooperation Network Meeting',
                'descriptionAr' => 'عقد المعهد العربي لإنماء المدن اجتماعه السنوي لشبكة التعاون البلدي العربية لمناقشة سبل تعزيز التكامل بين المدن العربية.',
                'descriptionEn' => 'The Institute held its annual meeting of the Arab Municipal Cooperation Network to discuss strengthening integration among Arab cities.',
                'bodyAr' => [
                    'عقد المعهد العربي لإنماء المدن اجتماعه السنوي لشبكة التعاون البلدي العربية، بمشاركة ممثلين عن أكثر من 650 مدينة عربية.',
                    'ركز الاجتماع على تعزيز آليات التعاون بين البلديات العربية، وتبادل أفضل الممارسات في مجالات الحوكمة البلدية والتنمية الحضرية المستدامة.',
                ],
                'bodyEn' => [
                    'The Arab Urban Development Institute held its annual meeting of the Arab Municipal Cooperation Network, with representatives from more than 650 Arab cities.',
                    'The meeting focused on strengthening cooperation mechanisms among Arab municipalities and exchanging best practices in municipal governance and sustainable urban development.',
                ],
                'publishedDate' => '2025-02-15',
                'imageUrl' => '/blog/3.png',
                'pdfUrl' => null,
                'isPublished' => true,
                'sortOrder' => 2,
            ],
        ],
        [
            'name' => 'sustainable-urban-planning',
            'labelAr' => 'خبر 4 — مبادرة التخطيط الحضري المستدام',
            'homePlacement' => 'News card on homepage grid. Create after labels (`home_media_center`).',
            'body' => [
                'category' => 'news',
                'key' => 'sustainable-urban-planning',
                'slugAr' => 'التخطيط-الحضري-المستدام',
                'slugEn' => 'sustainable-urban-planning',
                'titleAr' => 'إطلاق مبادرة جديدة لدعم التخطيط الحضري المستدام في المدن العربية',
                'titleEn' => 'New Initiative to Support Sustainable Urban Planning in Arab Cities',
                'descriptionAr' => 'أعلن المعهد العربي لإنماء المدن عن مبادرة جديدة تهدف إلى تعزيز قدرات البلديات العربية في مجال التخطيط الحضري المستدام.',
                'descriptionEn' => 'The Institute announced a new initiative to strengthen Arab municipalities\' capacities in sustainable urban planning.',
                'bodyAr' => [
                    'أعلن المعهد العربي لإنماء المدن عن مبادرة جديدة تهدف إلى تعزيز قدرات البلديات العربية في مجال التخطيط الحضري المستدام.',
                    'تتضمن المبادرة برامج تدريبية متخصصة، وورش عمل، ومنصات لتبادل المعرفة بين المدن العربية في مجالات التخطيط والسياسات الحضرية.',
                ],
                'bodyEn' => [
                    'The Arab Urban Development Institute announced a new initiative to strengthen Arab municipalities\' capacities in sustainable urban planning.',
                    'The initiative includes specialized training programs, workshops, and knowledge exchange platforms among Arab cities in planning and urban policy.',
                ],
                'publishedDate' => '2025-03-08',
                'imageUrl' => '/blog/4.png',
                'pdfUrl' => null,
                'isPublished' => true,
                'sortOrder' => 3,
            ],
        ],
        [
            'name' => 'municipal-governance-workshop',
            'labelAr' => 'خبر 5 — ورشة الحوكمة البلدية',
            'homePlacement' => 'Fills `mediaCenter.items[]` row (small cards + عرض الكل). Need 5–8 published news articles to fill both carousel and grid.',
            'body' => [
                'category' => 'news',
                'key' => 'municipal-governance-workshop',
                'slugAr' => 'ورشة-الحوكمة-البلدية',
                'slugEn' => 'municipal-governance-workshop',
                'titleAr' => 'ورشة عمل حول الحوكمة البلدية الرشيدة بمشاركة خبراء دوليين',
                'titleEn' => 'Workshop on Good Municipal Governance with International Experts',
                'descriptionAr' => 'نظم المعهد ورشة عمل متخصصة حول الحوكمة البلدية الرشيدة بمشاركة خبراء ومختصين من مختلف الدول العربية.',
                'descriptionEn' => 'The Institute organized a specialized workshop on good municipal governance with experts from across the Arab region.',
                'bodyAr' => [
                    'نظم المعهد ورشة عمل متخصصة حول الحوكمة البلدية الرشيدة، بمشاركة خبراء ومختصين من مختلف الدول العربية.',
                    'تناولت الورشة آليات تعزيز الشفافية والمساءلة في البلديات، ودور التكنولوجيا في تحسين الخدمات البلدية.',
                ],
                'bodyEn' => [
                    'The Institute organized a specialized workshop on good municipal governance with experts and specialists from across the Arab region.',
                    'The workshop addressed mechanisms for enhancing transparency and accountability in municipalities, and the role of technology in improving municipal services.',
                ],
                'publishedDate' => '2025-04-20',
                'imageUrl' => '/blog/1.png',
                'pdfUrl' => null,
                'isPublished' => true,
                'sortOrder' => 4,
            ],
        ],
        [
            'name' => 'urban-development-conference',
            'labelAr' => 'خبر 6 — مؤتمر التنمية الحضرية 2025',
            'homePlacement' => 'Optional 6th article — helps fill `mediaCenter.items[]` (articles 5–8 by newest `publishedDate`).',
            'body' => [
                'category' => 'news',
                'key' => 'urban-development-conference',
                'slugAr' => 'مؤتمر-التنمية-الحضرية-2025',
                'slugEn' => 'urban-development-conference',
                'titleAr' => 'مؤتمر التنمية الحضرية 2025 يناقش مستقبل المدن العربية',
                'titleEn' => 'Urban Development Conference 2025 Discusses the Future of Arab Cities',
                'descriptionAr' => 'استضاف المعهد مؤتمر التنمية الحضرية بمشاركة نخبة من الخبراء وصناع القرار لمناقشة أبرز التحديات والفرص في المدن العربية.',
                'descriptionEn' => 'The Institute hosted the Urban Development Conference with leading experts and decision-makers discussing key challenges and opportunities in Arab cities.',
                'bodyAr' => [
                    'استضاف المعهد العربي لإنماء المدن مؤتمر التنمية الحضرية 2025، بمشاركة نخبة من الخبراء وصناع القرار من مختلف الدول العربية.',
                    'ناقش المؤتمر محاور متعددة تشمل المرونة الحضرية، والتنمية الاقتصادية المحلية، والحوكمة البلدية، والابتكار في السياسات الحضرية.',
                ],
                'bodyEn' => [
                    'The Arab Urban Development Institute hosted the Urban Development Conference 2025, with leading experts and decision-makers from across the Arab region.',
                    'The conference addressed multiple themes including urban resilience, local economic development, municipal governance, and innovation in urban policy.',
                ],
                'publishedDate' => '2025-05-10',
                'imageUrl' => '/blog/2.png',
                'pdfUrl' => null,
                'isPublished' => true,
                'sortOrder' => 5,
            ],
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeContactInfoBody(): array
{
    return [
        'titleAr' => 'تواصل معنا',
        'titleEn' => 'Contact Us',
        'subtitleAr' => '',
        'subtitleEn' => '',
        'addressLabelAr' => 'العنوان',
        'addressLabelEn' => 'Address',
        'addressAr' => 'شارع عبدالله بن حذافة السهمي، الحي الدبلوماسي 12521-3803 الرياض، المملكة العربية السعودية',
        'addressEn' => 'Abdullah bin Hudhafa Al-Sahmi Street, Diplomatic Quarter, 12521-3803 Riyadh, Kingdom of Saudi Arabia',
        'mapTitleAr' => 'موقع المعهد العربي لإنماء المدن',
        'mapTitleEn' => 'Arab Urban Development Institute location',
        'mapEmbedUrlAr' => 'https://maps.google.com/maps?q=Arab+Urban+Development+Institute,+Riyadh&output=embed',
        'mapEmbedUrlEn' => 'https://maps.google.com/maps?q=Arab+Urban+Development+Institute,+Riyadh&output=embed',
        'itemsAr' => [
            ['label' => 'الهاتف', 'value' => '+966 114802555', 'type' => 'phone', 'href' => 'tel:+966114802555'],
            ['label' => 'فاكس', 'value' => '+966 114802666', 'type' => 'fax'],
            ['label' => 'ايميل', 'value' => 'Info@Araburban.Org', 'type' => 'email', 'href' => 'mailto:Info@Araburban.Org'],
            ['label' => 'كود', 'value' => '11452 6892 الرياض', 'type' => 'mailbox'],
        ],
        'itemsEn' => [
            ['label' => 'Phone', 'value' => '+966 114802555', 'type' => 'phone', 'href' => 'tel:+966114802555'],
            ['label' => 'Fax', 'value' => '+966 114802666', 'type' => 'fax'],
            ['label' => 'Email', 'value' => 'Info@Araburban.Org', 'type' => 'email', 'href' => 'mailto:Info@Araburban.Org'],
            ['label' => 'P.O. Box', 'value' => '11452 6892 Riyadh', 'type' => 'mailbox'],
        ],
    ];
}

/**
 * Example member city for homepage map (repeat POST for more cities or use bulk import).
 *
 * @return array<string, mixed>
 */
function postmanHomeMemberCityExample(): array
{
    return [
        'countryCode' => 'SA',
        'nameAr' => 'الرياض',
        'nameEn' => 'Riyadh',
        'latitude' => 24.7136,
        'longitude' => 46.6753,
        'infoAr' => 'عاصمة المملكة العربية السعودية',
        'infoEn' => 'Capital of Saudi Arabia',
        'isActive' => true,
    ];
}

/**
 * @return array<string, mixed>
 */
function postmanHomeMemberCityStatsBody(): array
{
    return [
        'items' => [
            [
                'key' => 'countries',
                'value' => 12,
                'autoCalculate' => false,
                'label' => ['ar' => 'الدول', 'en' => 'Countries'],
                'unit' => ['ar' => 'دولة', 'en' => 'countries'],
            ],
            [
                'key' => 'cities',
                'value' => 400,
                'autoCalculate' => false,
                'label' => ['ar' => 'المدن', 'en' => 'Cities'],
                'unit' => ['ar' => 'مدينة', 'en' => 'cities'],
            ],
            [
                'key' => 'members',
                'value' => 1240,
                'autoCalculate' => false,
                'label' => ['ar' => 'الاعضاء', 'en' => 'Members'],
                'unit' => ['ar' => 'عضو', 'en' => 'members'],
            ],
        ],
    ];
}
