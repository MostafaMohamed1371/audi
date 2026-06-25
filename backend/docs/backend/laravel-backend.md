# AUDI — Laravel Backend Documentation

> دليل شامل لبناء الـ Backend (Laravel) لموقع **المعهد العربي لإنماء المدن (AUDI)**.
> هذا المستند يشرح الـ Frontend الحالي خطوة بخطوة، ويحدد الكيانات (Entities)، جداول قاعدة البيانات، و الـ API endpoints المطلوبة.

- **Frontend:** Next.js 16 (App Router) + `next-intl` (locales: `ar` افتراضي، `en`) + Tailwind v4.
- **Live site:** https://audi-ten.vercel.app/ar
- **Backend:** Laravel 13 (REST API, JSON) — يخدم محتوى الموقع + لوحة تحكم (Admin). **مُنفّذ بالكامل** (Phases 0–6).

---

## 1. كيف يعمل الـ Frontend حالياً (الوضع الراهن)

النقطة الأهم لفهم نطاق العمل: **الموقع حالياً ثابت بالكامل (static / content-driven).** كل المحتوى مكتوب يدوياً في ملفات JSON للترجمة، ولا يوجد أي اتصال بـ API فعلي تقريباً.

مصادر المحتوى الحالية:

| المصدر | الموقع | الدور |
|--------|--------|-------|
| ملفات الترجمة | `messages/ar/*.json` و `messages/en/*.json` | **كل نصوص وبيانات الموقع** (أخبار، فريق، شركاء، برامج…) |
| بيانات مساعدة | `lib/*.ts` | tabs / slugs / helpers (ليست بيانات حقيقية، فقط ثوابت TypeScript) |
| الخريطة | `public/data/arab-countries.geojson` + `public/data/member-cities.geojson` | يتم جلبها عبر `fetch()` في `member-cities-map.tsx` |
| نموذج التواصل | `app/components/contact/contact-form-section.tsx` | حالياً **يزيّف الإرسال** (`setTimeout` فقط، لا يرسل لأي API) |

أماكن الاتصال الفعلي الوحيدة في الكود:

```109:110:app/components/home/member-cities/member-cities-map.tsx
        fetch("/data/arab-countries.geojson"),
        fetch("/data/member-cities.geojson"),
```

```74:91:app/components/contact/contact-form-section.tsx
  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await new Promise((resolve) => setTimeout(resolve, 600));
      setStatus("success");
```

### هدف الـ Backend
1. تحويل المحتوى من ملفات JSON ثابتة إلى **API ديناميكي** (DB-backed).
2. توفير **لوحة تحكم (Admin)** لإدارة كل قسم (CRUD).
3. استقبال **نماذج** (تواصل، عضوية، توظيف، مساهمات البوابة).
4. خدمة بيانات الخريطة (المدن/الدول/الإحصائيات).

> ملاحظة: هناك مواصفة جاهزة لقسم الخريطة في `backend/docs/postman/AUDI-Member-Cities.postman_collection.json` — هذا المستند يبني عليها ويغطّي باقي الموقع.

---

## 2. مبدأ الترجمة (i18n) — قرار معماري مهم

كل محتوى تقريباً له نسختان: عربي (`ar`) وإنجليزي (`en`). يجب أن يدعم الـ Backend ذلك.

**التوصية:** استخدام أعمدة مزدوجة (`*_ar` / `*_en`) لأنها أبسط للفريق وتطابق بنية `messages/`. (البديل هو حزمة `spatie/laravel-translatable` بأعمدة JSON — مقبول أيضاً).

اتفاقية الاستجابة العامة (Public API):
- يقرأ الـ header `Accept-Language: ar|en` (أو باراميتر `?locale=ar`).
- يرجّع الحقل المترجم المناسب فقط باسم محايد (مثل `title`, `description`).
- لوحة التحكم (Admin) ترجّع **كلا اللغتين** (`titleAr`, `titleEn`) للتحرير.

اتفاقيات عامة:
- كل الـ endpoints تحت `‎/api/v1/...` للعام، و `‎/api/admin/...` للإدارة.
- Admin محمي بـ **Sanctum** (Bearer token) + Roles.
- الردود JSON، الأخطاء بصيغة موحّدة `{ "message": "...", "errors": { } }`.
- الصور تُرفع وتُخزّن، ويُرجَّع `imageUrl` كامل (CDN/Storage).

---

## 3. خريطة الصفحات → الكيانات (Page → Entity Map)

مسارات الموقع معرّفة في `i18n/pathnames.ts`. الجدول التالي يربط كل صفحة بمصدر بياناتها والكيان المقابل في الـ Backend.

| المسار (route key) | Frontend source | الكيان / الجدول في الـ Backend |
|--------------------|-----------------|-------------------------------|
| `/` (الرئيسية) | `messages/*/home.json` | تجميعة من عدة كيانات (انظر §5.2) |
| `/about` | `about.json → institute` | `site_settings` / `about_institute` |
| `/about/vision-mission` | `about.json → visionMission` | `about_content` |
| `/about/president-speech` | `about.json → presidentSpeech` | `leadership_messages` |
| `/about/director-message` | `about.json → directorMessage` | `leadership_messages` |
| `/about/advisory-board` | `about.json → advisoryBoard.members[]` | `advisory_board_members` |
| `/about/team` | `about.json → team.sections[].members[]` | `team_sections` + `team_members` |
| `/about/structure` | `about.json → structure` | `about_content` (صورة الهيكل) |
| `/about/partners` | `about.json → partners` | `partner_categories` + `partners` |
| `/strategy/strategy-2025` | `strategy.json → strategy2025, diagram` | `strategy_page` + `strategy_pillars` + `strategy_diagram_items` |
| `/strategy/focus-areas` | `strategy.json → focusAreas.items[]` | `focus_areas` |
| `/strategy/focus-areas/[slug]` | نفس المصدر | `focus_areas` (by slug) |
| `/programs/urban-policies` | `programs.json → urbanPolicies` | `program_urban_policies` + `directory_*` |
| `/programs/training` | `programs.json → training` | `program_training` + `training_courses` + `experts` |
| `/programs/partnerships` | `programs.json → partnerships` | `program_partnerships` |
| `/resources` | `resources.json → items[]` | `resources` |
| `/media/news` (+`/[slug]`) | `media.json → news.items[]` | `media_articles` (category=news) |
| `/media/newsletter` (+`/[slug]`) | `media.json → newsletter.items[]` | `media_articles` (category=newsletter) |
| `/media/city-meetings` (+`/[slug]`) | `media.json → cityMeetings.items[]` | `media_articles` (category=city_meetings) |
| `/media/secretary-speaks` (+`/[slug]`) | (footer) `الأمين يتحدث` | `media_articles` (category=secretary_speaks) |
| `/contact` | `contact.json` + form | `contact_info` + `contact_submissions` |
| (Footer) العضوية | `home.json → membershipContact` | `membership_applications` |
| (Footer) اعمل معنا | `common.json → footer.community` | `job_openings` + `job_applications` |
| (Footer) الأسئلة الشائعة | `common.json → footer.quickLinks.faq` | `faqs` |
| (Footer) الشروط والأحكام / سياسة الخصوصية | `common.json → footer.quickLinks` | `legal_pages` |

---

## 4. مخطط قاعدة البيانات (Database Schema)

الجداول مجمّعة حسب الوحدة (module). الأعمدة `id`, `created_at`, `updated_at` مفترضة في كل جدول. `sort_order` (int) للترتيب اليدوي، `is_active`/`is_published` (bool) للتحكم بالظهور.

### 4.1 الإعدادات العامة (Global)

**`site_settings`** — قيم مفردة (key/value) للموقع بأكمله.
| column | type | notes |
|--------|------|-------|
| `key` | string, unique | مثل `site_name`, `founded_year`, `email`, `phone`, `fax`, `po_box`, `address`, `map_embed_url` |
| `value_ar` | text nullable | |
| `value_en` | text nullable | |
| `group` | string | `general` / `contact` / `seo` |

**`social_links`** — روابط التواصل (تظهر في الـ footer / contact).
| column | type | notes |
|--------|------|-------|
| `platform` | string | `twitter`, `linkedin`, `youtube`, `instagram`… |
| `url` | string | |
| `icon` | string nullable | اسم الأيقونة |
| `sort_order` | int | |
| `is_active` | bool | |

> الـ navigation و الـ footer menu نصوصها ثابتة في `common.json`. يمكن إبقاؤها في الـ Frontend، أو إدارتها عبر `site_settings`/جدول `navigation_items` إذا أردتم تحكماً كاملاً (اختياري).

### 4.2 الرئيسية (Home)

**`home_hero_slides`** — `slider.slides[]` (حالياً عنوان فقط + صورة خلفية).
| column | type | notes |
|--------|------|-------|
| `title_ar`, `title_en` | string | |
| `image_url` | string nullable | |
| `sort_order`, `is_active` | | |

**`home_stats`** — `stats.items[]` (المعهد في أرقام).
| column | type | notes |
|--------|------|-------|
| `value` | string | مثل `+25` (نص لأنه يحوي `+`) |
| `label_ar`, `label_en` | string | مثل «اتفاقية» |
| `description_ar`, `description_en` | string | |
| `sort_order` | int | |

> أقسام الرئيسية الأخرى (aboutIntro, programs preview, mediaCenter, knowledgeCenter, membershipContact) **مشتقّة** من كيانات أخرى أو نصوص ثابتة — انظر §5.2 للـ aggregate endpoint.

### 4.3 المدن الأعضاء + الخريطة (Member Cities)
انظر التفصيل الكامل في `backend/docs/postman/AUDI-Member-Cities.postman_collection.json`. ملخص:

**`countries`** (مرجع — من GeoJSON)
| column | type | notes |
|--------|------|-------|
| `code_a2` | char(2), PK | `AE`, `SA`… |
| `code_a3` | char(3) | |
| `name_en`, `name_ar` | string | |
| `geojson` | **longText** nullable | MultiPolygon (longText لأن بعض الدول تتجاوز حدّ JSON/TEXT في MySQL) |

**`member_cities`**
| column | type | notes |
|--------|------|-------|
| `country_code` | char(2) FK→countries | |
| `name_ar`, `name_en` | string | |
| `latitude` | decimal(9,6) | -90..90 |
| `longitude` | decimal(9,6) | -180..180 |
| `info_ar`, `info_en` | text nullable | |
| `image_url` | string nullable | |
| `is_active` | bool | default true |

**`member_city_stats`** (الدول/المدن/الأعضاء)
| column | type | notes |
|--------|------|-------|
| `key` | enum(`countries`,`cities`,`members`) PK | |
| `value` | int nullable | null إذا `auto_calculate` |
| `label_ar`, `label_en`, `unit_ar`, `unit_en` | string | |
| `auto_calculate` | bool | للمدن: يُحسب من عدد `is_active` |

### 4.4 من نحن (About)

**`about_content`** — أقسام نصية مفردة (institute, vision/mission, structure…). نمط key/value مثل `site_settings` لكن للمحتوى الطويل.
| column | type | notes |
|--------|------|-------|
| `section_key` | string unique | `institute`, `vision_mission`, `structure` |
| `title_ar`, `title_en` | string nullable | |
| `body_ar`, `body_en` | json/text | فقرات (مصفوفة) |
| `image_url` | string nullable | مثل صورة الهيكل التشغيلي |

**`leadership_messages`** — كلمة الرئيس + رسالة المدير (president-speech, director-message).
| column | type | notes |
|--------|------|-------|
| `type` | enum(`president`,`director`) | |
| `name_ar`, `name_en` | string | |
| `position_ar`, `position_en` | string | |
| `honorific_ar`, `honorific_en` | string nullable | «صاحب السمو الأمير» |
| `quote_ar`, `quote_en` | text | |
| `paragraphs_ar`, `paragraphs_en` | json | مصفوفة فقرات |
| `image_url`, `image_alt_ar`, `image_alt_en` | | |

**`advisory_board_members`** — `advisoryBoard.members[]`.
| column | type | notes |
|--------|------|-------|
| `name_ar`, `name_en` | string | |
| `role_ar`, `role_en` | string | «رئيس المجلس»… |
| `bio_ar`, `bio_en` | text | |
| `image_url` | string | |
| `is_featured` | bool | (العنصر الأول `featured: true`) |
| `sort_order` | int | |

**`team_sections`** — أقسام الفريق (الإدارة، الباحثين، الخدمات المشتركة).
| column | type | notes |
|--------|------|-------|
| `slug` | string | `management`, `researchers`, `shared-services` |
| `title_ar`, `title_en` | string | |
| `sort_order` | int | |

**`team_members`**
| column | type | notes |
|--------|------|-------|
| `team_section_id` | FK | |
| `name_ar`, `name_en` | string | |
| `role_ar`, `role_en` | string | |
| `bio_ar`, `bio_en` | text | |
| `image_url` | string | |
| `sort_order` | int | |

**`partner_categories`** — `partners.categories[]` (دولية / بحثية / مدن).
| column | type | notes |
|--------|------|-------|
| `slug` | string | `international`, `research`, `cities` |
| `title_ar`, `title_en` | string | |
| `sort_order` | int | |

**`partners`**
| column | type | notes |
|--------|------|-------|
| `partner_category_id` | FK nullable | null = شعار featured في الـ hero |
| `name_ar`, `name_en` | string | |
| `logo_url` | string | |
| `is_featured` | bool | شعارات الـ slider العلوي |
| `sort_order` | int | |

### 4.5 الاستراتيجية (Strategy)

**`strategy_pages`** — محتوى صفحة 2025/2026 (سجل واحد افتراضي `slug='strategy-2025'`):
- `intro_title_*`, `intro_subtitle_*`, `booklet_pdf_url`, `booklet_title_*`.

**`strategy_pillars`** — `strategy2025.pillars[]`.
| column | type | notes |
|--------|------|-------|
| `number` | string | `01`,`02`,`03` |
| `text_ar`, `text_en` | text | |
| `sort_order` | int | |

**`strategy_diagram_items`** — `diagram.items[]` (vision, goals, values, programs…).
| column | type | notes |
|--------|------|-------|
| `item_key` | string | `vision`, `goals`… |
| `title_ar`, `title_en` | string | |
| `content_ar`, `content_en` | text nullable | |
| `columns_ar`, `columns_en` | json nullable | للعناصر متعددة الأعمدة (goals) |

> تخطيط الـ diagram (`rows`, `leftSpan`…) هو **layout** يفضّل إبقاؤه في الـ Frontend.

**`focus_areas`** — `focusAreas.items[]` (مجالات التركيز) — لها صفحة تفصيل بـ slug.
| column | type | notes |
|--------|------|-------|
| `slug` | string unique | `urban-resilience`… |
| `number` | string | `01`… |
| `title_ar`, `title_en` | string | |
| `highlight_ar`, `highlight_en` | string | |
| `tags_ar`, `tags_en` | json | مصفوفة وسوم |
| `description_ar`, `description_en` | text | |
| `list_image_url`, `detail_image_url` | string | |
| `sort_order`, `is_published` | | |

### 4.6 البرامج (Programs)
ثلاثة برامج، كل منها صفحة بها tabs.

**`programs`** — السجل الأساسي لكل برنامج.
| column | type | notes |
|--------|------|-------|
| `slug` | enum(`urban-policies`,`training`,`partnerships`) | |
| `title_ar`, `title_en` | string | |
| `hero_intro_ar`, `hero_intro_en` | text | |

**`program_sections`** — أقسام/تبويبات كل برنامج (tabs مرنة لكل البرامج الثلاثة).
| column | type | notes |
|--------|------|-------|
| `program_id` | FK | |
| `tab_key` | string | `trainingPrograms`, `developmentPortal`, `euroArabDialogue`… |
| `title_ar`, `title_en` | string | |
| `intro_ar`, `intro_en` | text | |
| `body_ar`, `body_en` | json nullable | فقرات/قوائم إضافية |
| `image_url` | string nullable | |
| `sort_order` | int | |

**`training_courses`** — `training.trainingPrograms.courses[]`.
| column | type | notes |
|--------|------|-------|
| `title_ar`, `title_en` | string | |
| `count_ar`, `count_en` | string | «3 دورات تدريبية» |
| `sort_order` | int | |

**`experts`** — `training.experts.experts[]` (خبراء مركز الدعم).
| column | type | notes |
|--------|------|-------|
| `name_ar`, `name_en` | string | |
| `specialty_ar`, `specialty_en` | string | |
| `image_url` | string | |
| `sort_order` | int | |

**Directory (بوابة التنمية الحضرية)** — `urbanPolicies.developmentPortal.directory`. يحوي 4 جداول مفهرسة (cities/projects/organizations/publications). يمكن جدول واحد polymorphic أو جداول منفصلة:

`directory_cities` (`number`, `name_*`, `description_*`, `country_code`, `city_size`)
`directory_projects` (`number`, `city_*`, `country_*`, `start_date`, `end_date`)
`directory_organizations` (`number`, `name_*`, `description_*`)
`directory_publications` (`number`, `name_*`, `description_*`)

### 4.7 المصادر (Resources)

**`resources`** — `resources.json → items[]`.
| column | type | notes |
|--------|------|-------|
| `slug` | string unique | |
| `title_ar`, `title_en` | string | |
| `published_date` | date | |
| `image_url` | string | الغلاف |
| `file_url` | string | ملف PDF للتنزيل |
| `resource_type` | string nullable | للفلترة (نوع المصدر) |
| `focus_area_id` | FK nullable | للفلترة (مجال التركيز) |
| `year` | smallint nullable | للفلترة |
| `is_published`, `sort_order` | | |

### 4.8 المركز الإعلامي (Media)
ثلاث فئات بحقول مختلفة قليلاً → جدول واحد مع أعمدة nullable حسب الفئة، أو single-table مع `category`.

**`media_articles`**
| column | type | notes |
|--------|------|-------|
| `category` | enum(`news`,`newsletter`,`city_meetings`,`secretary_speaks`) | `secretary_speaks` = «الأمين يتحدث» |
| `key` | string unique | معرّف ثابت يربط الـ slug عبر اللغتين |
| `slug_ar`, `slug_en` | string | الـ slug مترجم (انظر `lib/media-slugs.ts`) |
| `title_ar`, `title_en` | string | |
| `description_ar`, `description_en` | text nullable | (news فقط) |
| `body_ar`, `body_en` | json | مصفوفة فقرات |
| `published_date` | date | |
| `image_url` | string | |
| `pdf_url` | string nullable | (newsletter) |
| `authors_ar`, `authors_en` | json nullable | (city_meetings) |
| `event_time` | string nullable | (city_meetings) «6:00 م - 7:00 م» |
| `is_published`, `sort_order` | | |

> **مهم:** الـ Frontend يطابق اللغات عبر حقل `key` ثابت بينما الـ `slug` يختلف لكل لغة (راجع `lib/media-slugs.ts` و `i18n/pathnames.ts`). احرص أن يرجّع الـ API الـ `key` دائماً ليعمل مبدّل اللغة على صفحات التفاصيل.

### 4.9 النماذج (Form Submissions)

**`contact_submissions`** — من نموذج `/contact`.
| column | type | notes |
|--------|------|-------|
| `name` | string | |
| `phone` | string | |
| `email` | string | |
| `message` | text | |
| `status` | enum(`new`,`read`,`archived`) | default new |
| `ip_address`, `user_agent` | string nullable | |

**`membership_applications`** — زر «انضم الآن» (العضوية).
| column | type | notes |
|--------|------|-------|
| `organization_name`, `contact_name`, `email`, `phone`, `country_code`, `city`, `message` | | الحقول حسب نموذج العضوية النهائي |
| `status` | enum(`new`,`reviewing`,`approved`,`rejected`) | |

**`portal_contributions`** — «ساهم» في بوابة التنمية (`developmentPortal.contributionTypes`).
| column | type | notes |
|--------|------|-------|
| `type` | enum(`publications`,`cities`,`organizations`) | |
| `payload` | json | حقول متغيّرة حسب النوع |
| `email`, `status` | | |

**`newsletter_subscriptions`** (اختياري — اشتراك بريدي) | `email`, `locale`, `is_confirmed`.

### 4.9.1 روابط الـ Footer (Footer Features) — مُنفّذة

**`faqs`** — «الأسئلة الشائعة».
| column | type | notes |
|--------|------|-------|
| `category` | string nullable | تصنيف اختياري (`membership`/`programs`/`general`) |
| `question_ar`, `question_en` | varchar(500) | |
| `answer_ar`, `answer_en` | text | |
| `is_published`, `sort_order` | | |

**`legal_pages`** — «الشروط والأحكام» / «سياسة الخصوصية» (محتوى ثابت قابل للتحرير).
| column | type | notes |
|--------|------|-------|
| `slug` | string unique | `terms` \| `privacy` |
| `title_ar`, `title_en` | string | |
| `content_ar`, `content_en` | text | |
| `effective_date` | date nullable | |

**`job_openings`** — «اعمل معنا» (الوظائف المتاحة).
| column | type | notes |
|--------|------|-------|
| `title_ar`, `title_en` | string | |
| `location_ar`, `location_en` | string nullable | |
| `employment_type` | string | `full_time`/`part_time`/`contract`/`internship` |
| `summary_ar`, `summary_en` | text nullable | |
| `description_ar`, `description_en` | json nullable | مصفوفة فقرات (TEXT في dump الـ SQL) |
| `is_published`, `sort_order` | | |

**`job_applications`** — طلبات التوظيف.
| column | type | notes |
|--------|------|-------|
| `job_opening_id` | FK nullable → `job_openings` (set null) | فارغ = طلب عام |
| `full_name`, `email`, `phone` | string | `phone` nullable |
| `cover_letter` | text nullable | |
| `cv_url` | string nullable | رابط الملف بعد الرفع |
| `status` | enum(`new`,`reviewing`,`shortlisted`,`rejected`,`hired`) | default new |

### 4.10 الإدارة (Auth)
**`users`** (Laravel default) + عمود `role` (enum: `admin`, `editor`) — المصادقة عبر **Laravel Sanctum** (`personal_access_tokens`). لا توجد حزمة صلاحيات خارجية.

---

## 5. كتالوج الـ API Endpoints

### 5.1 العام (Public) — `‎/api/v1`
كلها `GET`، تقرأ `Accept-Language`، بدون مصادقة، قابلة للـ caching.

| Method | Endpoint | يخدم |
|--------|----------|------|
| GET | `/home` | كامل بيانات الرئيسية (aggregate — §5.2) |
| GET | `/home/member-cities` | stats + countriesGeoJson + citiesGeoJson |
| GET | `/settings` | site_settings + social_links + contact_info |
| GET | `/about/institute` | نص «عن المعهد» + الإحصائيات |
| GET | `/about/vision-mission` | الرؤية والرسالة + القيم |
| GET | `/about/leadership/{type}` | `president` أو `director` |
| GET | `/about/advisory-board` | الأعضاء |
| GET | `/about/team` | الأقسام + الأعضاء |
| GET | `/about/structure` | صورة + نص الهيكل |
| GET | `/about/partners` | الفئات + الشركاء |
| GET | `/strategy/strategy-2025` | المحتوى + pillars + diagram |
| GET | `/strategy/focus-areas` | القائمة |
| GET | `/strategy/focus-areas/{slug}` | تفصيل |
| GET | `/programs/{slug}` | برنامج كامل + أقسامه (`urban-policies`/`training`/`partnerships`) |
| GET | `/programs/urban-policies/directory?tab=cities&...` | جدول البوابة (paginated + فلترة) |
| GET | `/resources?type=&focusArea=&year=&search=&page=` | المصادر (paginated + فلترة) |
| GET | `/media/{category}?year=&month=&search=&page=` | قائمة (news/newsletter/city-meetings/secretary-speaks) |
| GET | `/media/{category}/{slug}` | تفصيل مقال (يرجّع `key` للتبديل بين اللغات) |
| GET | `/careers` | قائمة الوظائف المتاحة (`job_openings`) |
| GET | `/careers/{jobOpening}` | تفصيل وظيفة |
| GET | `/faqs?category=` | الأسئلة الشائعة (`faqs`) |
| GET | `/legal/{slug}` | صفحة قانونية (`terms` \| `privacy`) |
| GET | `/contact` | معلومات التواصل + الخريطة |

**النماذج (Public POST):**
| Method | Endpoint | Body |
|--------|----------|------|
| POST | `/contact` | `{ name, phone, email, message }` → `contact_submissions` |
| POST | `/membership` | حقول العضوية → `membership_applications` |
| POST | `/programs/urban-policies/contribute` | `{ type, ...payload }` → `portal_contributions` |
| POST | `/careers/apply` | `{ jobOpeningId?, fullName, email, phone?, coverLetter?, cvUrl? }` → `job_applications` |
| POST | `/newsletter/subscribe` | `{ email, locale? }` → `newsletter_subscriptions` |

> أضِف rate-limiting + CAPTCHA/honeypot لكل الـ POST العامة.

### 5.2 الـ Aggregate endpoint للرئيسية
`GET /api/v1/home` يجمع كل ما تحتاجه الصفحة الرئيسية في طلب واحد لتقليل الـ round-trips:
```jsonc
{
  "slider": [ { "title": "تطوير تقني", "imageUrl": "..." } ],
  "stats": [ { "value": "+25", "label": "اتفاقية", "description": "الاتفاقيات" } ],
  "memberCities": { "stats": [...] },
  "programs": [ { "slug": "urban-policies", "title": "...", "description": "...", "href": "/programs/urban-policies" } ],
  "mediaCenter": { "featured": [...], "items": [...] },   // أحدث المقالات
  "knowledgeCenter": { "headerSlides": [...], "items": [...] },  // أحدث المصادر/النشرات
  "membership": { "title": "...", "subtitle": "...", "cta": "..." }
}
```

### 5.3 لوحة التحكم (Admin) — `‎/api/admin`
كلها تتطلب `Authorization: Bearer <token>` (Sanctum). نمط RESTful موحّد لكل مورد:

```
GET    /api/admin/{resource}            # list (paginated, search, filter)
POST   /api/admin/{resource}            # create
GET    /api/admin/{resource}/{id}       # show (كلا اللغتين)
PUT    /api/admin/{resource}/{id}       # update
DELETE /api/admin/{resource}/{id}       # delete (يفضّل soft delete)
POST   /api/admin/{resource}/reorder    # تحديث sort_order دفعة واحدة
```

الموارد (`{resource}`):
`hero-slides`, `home-stats`, `member-cities`, `member-cities/stats`, `countries`,
`about-content`, `leadership`, `advisory-board`, `team-sections`, `team-members`,
`partners`, `partner-categories`, `strategy`, `strategy-pillars`, `strategy-diagram`,
`focus-areas`, `programs`, `program-sections`, `training-courses`, `experts`,
`directory/cities`, `directory/projects`, `directory/organizations`, `directory/publications`,
`resources`, `media` (مع `?category=`), `faqs`, `job-openings`, `legal`, `social-links`, `settings`.

**استثناءات النمط (حسب التنفيذ الفعلي):**
- `strategy`: سجل مفرد — `GET /api/admin/strategy` + `PUT /api/admin/strategy` (بدون `{id}`).
- `member-cities/stats`: `GET` + `PUT` (دفعة واحدة، بدون `{id}`).
- `settings`, `about-content`, `leadership`, `programs`, `legal`: **CRUD كامل** (بدون reorder).
- `social-links`, `faqs`, `job-openings`: **CRUD كامل** + `reorder`.
- `uploads`: `GET` list/show + `POST` upload + `DELETE` (حذف الملف من التخزين).
- `contact-submissions`, `membership-applications`, `portal-contributions`, `job-applications`: `GET` + `PATCH status` + `DELETE` (+ `portal-contributions` و `job-applications` لديهم `show`).
- `newsletter-subscriptions`: `GET` list/show + `DELETE` فقط (الإنشاء عبر `POST /api/v1/newsletter/subscribe`).

النماذج الواردة (قراءة/إدارة):
`contact-submissions`, `membership-applications`, `portal-contributions`, `job-applications`, `newsletter-subscriptions`.

رفع الملفات:
- `POST /api/admin/uploads` (image/pdf) → يرجّع `{ url }` يُستخدم في حقول `*_url`.

المصادقة:
- `POST /api/admin/auth/login` → `{ token, user }`
- `POST /api/admin/auth/logout`
- `GET /api/admin/auth/me`

---

## 6. اقتراح بنية مشروع Laravel

```
app/
  Models/                # Eloquent: MediaArticle, FocusArea, TeamMember, MemberCity ...
  Http/
    Controllers/
      Api/V1/            # Public controllers
      Api/Admin/         # Admin controllers
    Requests/            # FormRequest validation
    Resources/           # JSON API Resources (locale-aware transform)
    Middleware/          # SetLocale (Accept-Language)
  Services/              # HomeAggregator, GeoJsonBuilder ...
database/
  migrations/
  seeders/               # تُهاجِر بيانات messages/*.json الحالية كـ seed
routes/
  api.php                # v1 + admin groups
```

ملاحظات:
- **API Resources** هي المكان المثالي لاختيار اللغة (`title => $request->locale==='ar' ? $this->title_ar : $this->title_en`).
- **Middleware `SetLocale`** يقرأ `Accept-Language`/`?locale` ويضبط `app()->setLocale()`.
- **Seeders:** اكتب seeders تقرأ ملفات `messages/ar/*.json` و `messages/en/*.json` الحالية وتعبّئ الجداول — هذه أسرع طريقة لنقل المحتوى الموجود (المدخلات نفسها التي يستهلكها الموقع الآن).

---

## 7. كيفية ربط الـ Frontend لاحقاً (Migration plan)

عند جاهزية الـ API:
1. أضِف `NEXT_PUBLIC_API_URL` في البيئة.
2. استبدل قراءة `t.raw("items")` / استيراد JSON بـ `fetch` من الـ API (في Server Components — `app/[locale]/.../page.tsx`).
3. الخريطة: استبدل
   ```ts
   fetch("/data/arab-countries.geojson")
   fetch("/data/member-cities.geojson")
   ```
   بـ `fetch(`${API}/api/v1/home/member-cities`)`.
4. نموذج التواصل: استبدل الـ `setTimeout` بـ `POST ${API}/api/v1/contact`.
5. أبقِ `next-intl` للنصوص الثابتة (nav، footer، الأزرار) — أو انقلها للـ API تدريجياً.

> يفضّل استخدام Next.js ISR/`revalidate` على الـ fetch للمحتوى شبه الثابت لتقليل الحمل على الـ Backend.

---

## 8. أولويات التنفيذ (Phases)

| Phase | النطاق | المخرجات |
|-------|--------|----------|
| **0** | الإعداد | Laravel + Sanctum + Roles + Migrations الأساسية + Upload |
| **1** | النماذج | `contact`, `membership` (قيمة فورية: استقبال الطلبات) |
| **2** | الخريطة | `member-cities` (CRUD + GeoJSON public) — المواصفة جاهزة |
| **3** | المركز الإعلامي + المصادر | `media_articles`, `resources` (الأكثر تحديثاً) |
| **4** | من نحن + الاستراتيجية | leadership, team, advisory-board, partners, focus-areas |
| **5** | البرامج + البوابة | programs, sections, directory, experts |
| **6** | الرئيسية | aggregate `/home` + hero/stats + لوحة التحكم الكاملة |

---

## 9. مراجع داخل المستودع
- مسارات وروابط الصفحات: `i18n/pathnames.ts`
- منطق ربط الـ media slugs بين اللغات: `lib/media-slugs.ts`
- **Postman — API كامل:** `backend/docs/postman/AUDI-API.postman_collection.json` (192 طلب — مولّد عبر `generate-audi-api-collection.php`)
- **Postman — بيئة محلية:** `backend/docs/postman/AUDI.postman_environment.json`
- **Postman — الخريطة (تفصيلي):** `backend/docs/postman/AUDI-Member-Cities.postman_collection.json`
- **مخطط SQL كامل (MySQL 8):** `backend/database/schema/audi_mysql_schema.sql` (+ `README.md` للتشغيل)
- **Laravel backend (مُنفّذ Phases 0–6):** `backend/` — migrations, models, controllers, services, seeders
- **Backend README:** `backend/README.md`
- مصدر المحتوى الحالي (للـ seeding): `messages/ar/*.json` و `messages/en/*.json`
- أنواع البيانات (tabs/slugs): `lib/focus-areas.ts`, `lib/media.ts`, `lib/programs-*.ts`
