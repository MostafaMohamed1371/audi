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
- [Home — الرئيسية](#home) — الصفحة الرئيسية: السلايدر، الإحصائيات، البرامج، المركز الإعلامي، مركز المعرفة، وخريطة المدن الأعضاء.
- [Settings — الإعدادات](#settings) — إعدادات الموقع العامة: اسم الموقع، حقوق النشر، روابط التواصل الاجتماعي.
- [About — من نحن](#about) — صفحات عن المعهد: التعريف، الرؤية والرسالة، القيادة، المجلس الاستشاري، الفريق، الهيكل، الشركاء.
- [Strategy — الاستراتيجية](#strategy) — استراتيجية المعهد ومجالات التركيز.
- [Programs — البرامج](#programs) — صفحات البرامج الثلاثة ودليل بوابة التنمية الحضرية.
- [Resources — المصادر](#resources) — قائمة التقارير والدراسات والمصادر (مع فلاتر اختيارية).
- [Media — المركز الإعلامي](#media) — الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث، وتفاصيل المقالات.
- [Careers — اعمل معنا](#careers) — عرض الوظائف الشاغرة وتقديم طلب التوظيف.
- [FAQ — الأسئلة الشائعة](#faq) — قائمة الأسئلة الشائعة (مع تصنيف اختياري).
- [Legal — الشروط والخصوصية](#legal) — صفحات الشروط والأحكام وسياسة الخصوصية.
- [Forms — النماذج](#forms) — معلومات التواصل ونماذج الإرسال: تواصل، عضوية، مساهمة، توظيف، نشرة.

---

## Public — /api/v1

Public website API — locale via `Accept-Language: {{locale}}`. Returns single-language fields. Full MD: `docs/postman/PUBLIC-API.md`.

### Home — الرئيسية

<a id="home"></a>
**الغرض | Purpose:** الصفحة الرئيسية: السلايدر، الإحصائيات، البرامج، المركز الإعلامي، مركز المعرفة، وخريطة المدن الأعضاء. / Homepage aggregate, member cities map, and GeoJSON layers.
**لوحة التحكم | Admin resources:** `hero-slides, home-stats, programs, about-content (home_*), media, resources`

#### GET `/api/v1/home`

**الاسم | Name:** Get Home (Aggregate)

**الغرض | Purpose:** جلب محتوى الصفحة الرئيسية كاملاً (سلايدر، إحصائيات، برامج، إعلام، معرفة، عضوية).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Aggregates slider, stats, programs, mediaCenter, knowledgeCenter, membershipContact.

---

#### GET `/api/v1/home/member-cities`

**الاسم | Name:** Get Member Cities Map

**الغرض | Purpose:** بيانات خريطة المدن الأعضاء: إحصائيات + GeoJSON.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Returns stats + GeoJSON. Admin: member-cities/stats + cities.

---

#### GET `/api/v1/home/member-cities/countries.geojson`

**الاسم | Name:** Get Countries GeoJSON

**الغرض | Purpose:** طبقة GeoJSON للخريطة (هندسة + أسماء حسب اللغة حيث ينطبق).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

GeoJSON — not locale-dependent geometry.

---

#### GET `/api/v1/home/member-cities/cities.geojson`

**الاسم | Name:** Get Cities GeoJSON

**الغرض | Purpose:** طبقة GeoJSON للخريطة (هندسة + أسماء حسب اللغة حيث ينطبق).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

GeoJSON — city names resolved by Accept-Language.

---

### Settings — الإعدادات

<a id="settings"></a>
**الغرض | Purpose:** إعدادات الموقع العامة: اسم الموقع، حقوق النشر، روابط التواصل الاجتماعي. / Site-wide settings and social links.
**لوحة التحكم | Admin resources:** `settings, social-links`

#### GET `/api/v1/settings`

**الاسم | Name:** Get Site Settings

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Returns siteName, copyright, socialLinks, contact. Admin: settings + social-links.

---

### About — من نحن

<a id="about"></a>
**الغرض | Purpose:** صفحات عن المعهد: التعريف، الرؤية والرسالة، القيادة، المجلس الاستشاري، الفريق، الهيكل، الشركاء. / About institute pages with locale-resolved content.
**لوحة التحكم | Admin resources:** `about-content, leadership, advisory-board, team-*, partners`

#### GET `/api/v1/about/institute`

**الاسم | Name:** Get Institute

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: about-content sectionKey=institute + home-stats.

---

#### GET `/api/v1/about/vision-mission`

**الاسم | Name:** Get Vision & Mission

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/vision-mission` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: about-content keys vision_mission, goals, values.

---

#### GET `/api/v1/about/leadership/president`

**الاسم | Name:** Get Leadership (President)

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/president` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: leadership type=president.

---

#### GET `/api/v1/about/leadership/director`

**الاسم | Name:** Get Leadership (Director)

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: leadership type=director.

---

#### GET `/api/v1/about/advisory-board`

**الاسم | Name:** Get Advisory Board

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: about-content advisory_board + advisory-board members. Public `members[].image` = admin `imageUrl`.

---

#### GET `/api/v1/about/team`

**الاسم | Name:** Get Team

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: team-sections + team-members. Public nested `members[].image` = admin `imageUrl`.

---

#### GET `/api/v1/about/structure`

**الاسم | Name:** Get Structure

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/structure` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: about-content sectionKey=structure.

---

#### GET `/api/v1/about/partners`

**الاسم | Name:** Get Partners

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: partners_hero + partner-categories + partners. Public `image` = admin `logoUrl`.

---

### Strategy — الاستراتيجية

<a id="strategy"></a>
**الغرض | Purpose:** استراتيجية المعهد ومجالات التركيز. / Strategy page and focus areas.
**لوحة التحكم | Admin resources:** `strategy, strategy-pillars, focus-areas, about-content`

#### GET `/api/v1/strategy/strategy-2025`

**الاسم | Name:** Get Strategy 2025/2026

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: strategy + strategy-pillars + strategy-diagram.

---

#### GET `/api/v1/strategy/focus-areas`

**الاسم | Name:** List Focus Areas

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: focus-areas + about-content focus_areas_pages.

---

#### GET `/api/v1/strategy/focus-areas/{{slug}}`

**الاسم | Name:** Get Focus Area Detail

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas/{slug}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: focus-areas by slug.

---

### Programs — البرامج

<a id="programs"></a>
**الغرض | Purpose:** صفحات البرامج الثلاثة ودليل بوابة التنمية الحضرية. / Program pages and urban policies directory.
**لوحة التحكم | Admin resources:** `programs, program-sections, training-courses, experts, directory/*`

#### GET `/api/v1/programs/urban-policies`

**الاسم | Name:** Get Urban Policies Program

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: programs + program-sections + directory/*.

---

#### GET `/api/v1/programs/training`

**الاسم | Name:** Get Training Program

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: programs + program-sections + training-courses + experts.

---

#### GET `/api/v1/programs/partnerships`

**الاسم | Name:** Get Partnerships Program

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: programs + program-sections.

---

#### GET `/api/v1/programs/urban-policies/directory`

**الاسم | Name:** Get Development Portal Directory

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `tab` | cities|projects|organizations|publications | `cities` |
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: directory/cities|projects|organizations|publications.

---

### Resources — المصادر

<a id="resources"></a>
**الغرض | Purpose:** قائمة التقارير والدراسات والمصادر (مع فلاتر اختيارية). / Paginated knowledge resources list.
**لوحة التحكم | Admin resources:** `resources`

#### GET `/api/v1/resources`

**الاسم | Name:** List Resources

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `type` | نوع المساهمة أو المورد | `` |
| `focusArea` | مجال التركيز | `` |
| `year` | السنة | `` |
| `search` | بحث نصي | `` |
| `page` | رقم الصفحة | `1` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: resources CRUD. Public `image` = admin `imageUrl`.

---

### Media — المركز الإعلامي

<a id="media"></a>
**الغرض | Purpose:** الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث، وتفاصيل المقالات. / Media center listings and article detail.
**لوحة التحكم | Admin resources:** `media (category news, newsletter, city_meetings, secretary_speaks)`

#### GET `/api/v1/media/news`

**الاسم | Name:** List News

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `year` | السنة | `2025` |
| `month` | الشهر | `1` |
| `search` | بحث نصي | `` |
| `page` | رقم الصفحة | `1` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: media category=news. Public `image` = admin `imageUrl`.

---

#### GET `/api/v1/media/newsletter`

**الاسم | Name:** List Newsletter

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/newsletter` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: media category=newsletter.

---

#### GET `/api/v1/media/city-meetings`

**الاسم | Name:** List City Meetings

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/city-meetings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: media category=city_meetings.

---

#### GET `/api/v1/media/secretary-speaks`

**الاسم | Name:** List Secretary Speaks

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/secretary-speaks` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: media category=secretary_speaks.

---

#### GET `/api/v1/media/{{category}}/{{slug}}`

**الاسم | Name:** Get Media Article Detail

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/{category}/{slug}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Public category uses hyphens; admin uses underscores. Response includes slugAr/slugEn for language switch.

---

### Careers — اعمل معنا

<a id="careers"></a>
**الغرض | Purpose:** عرض الوظائف الشاغرة وتقديم طلب التوظيف. / Job listings and application form.
**لوحة التحكم | Admin resources:** `job-openings, job-applications`

#### GET `/api/v1/careers`

**الاسم | Name:** List Job Openings

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: job-openings.

---

#### GET `/api/v1/careers/{{id}}`

**الاسم | Name:** Get Job Opening

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers/{id}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: job-openings/{id}.

---

#### POST `/api/v1/careers/apply`

**الاسم | Name:** Submit Job Application

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `jobOpeningId` | معرّف الوظيفة (اختياري) | 1 |
| `fullName` | الاسم الكامل | "سارة عبدالله" |
| `email` | البريد الإلكتروني | "sara@example.com" |
| `phone` | رقم الهاتف | "+966501234567" |
| `coverLetter` | خطاب التقديم | "لدي خبرة في التخطيط الحضري والعمل مع البلديات … |
| `cvUrl` | رابط السيرة الذاتية | "https:\/\/example.com\/cv.pdf" |

#### Notes | ملاحظات

Public form submission. Admin review: job-applications.

---

### FAQ — الأسئلة الشائعة

<a id="faq"></a>
**الغرض | Purpose:** قائمة الأسئلة الشائعة (مع تصنيف اختياري). / FAQ list with optional category filter.
**لوحة التحكم | Admin resources:** `faqs`

#### GET `/api/v1/faqs`

**الاسم | Name:** List FAQs

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `category` | membership | programs | general | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: faqs CRUD.

---

### Legal — الشروط والخصوصية

<a id="legal"></a>
**الغرض | Purpose:** صفحات الشروط والأحكام وسياسة الخصوصية. / Terms and privacy policy pages.
**لوحة التحكم | Admin resources:** `legal`

#### GET `/api/v1/legal/terms`

**الاسم | Name:** Get Terms

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: legal slug=terms.

---

#### GET `/api/v1/legal/privacy`

**الاسم | Name:** Get Privacy Policy

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/privacy` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: legal slug=privacy.

---

### Forms — النماذج

<a id="forms"></a>
**الغرض | Purpose:** معلومات التواصل ونماذج الإرسال: تواصل، عضوية، مساهمة، توظيف، نشرة. / Contact info and public form submissions.
**لوحة التحكم | Admin resources:** `contact-info, contact-submissions, membership-applications, portal-contributions, job-applications, newsletter-subscriptions`

#### GET `/api/v1/contact`

**الاسم | Name:** Get Contact Info

**الغرض | Purpose:** قراءة محتوى الصفحة بلغة واحدة (حسب Accept-Language أو ?locale=).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/contact` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: settings group=contact (contact.title, contact.address, …).

---

#### POST `/api/v1/contact`

**الاسم | Name:** Submit Contact Form

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `name` | الاسم | "أحمد محمد" |
| `phone` | رقم الهاتف | "+966501234567" |
| `email` | البريد الإلكتروني | "ahmed@example.com" |
| `message` | نص الرسالة | "استفسار عن برامج المعهد التدريبية والعضوية." |

#### Notes | ملاحظات

Admin review: contact-submissions.

---

#### POST `/api/v1/membership`

**الاسم | Name:** Submit Membership Application

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `organizationName` | اسم الجهة/المؤسسة | "أمانة منطقة الرياض" |
| `contactName` | اسم مسؤول التواصل | "محمد العلي" |
| `email` | البريد الإلكتروني | "contact@example.gov.sa" |
| `phone` | رقم الهاتف | "+966114802555" |
| `countryCode` | رمز الدولة | "SA" |
| `city` | المدينة | "الرياض" |
| `message` | نص الرسالة | "نرغب في الانضمام كعضو في المعهد العربي لإنماء … |

#### Notes | ملاحظات

Admin review: membership-applications.

---

#### POST `/api/v1/programs/urban-policies/contribute`

**الاسم | Name:** Submit Portal Contribution

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `type` | النوع (director|president، publications|cities|organizations، …) | "publications" |
| `email` | البريد الإلكتروني | "researcher@example.com" |
| `payload.title` | العنوان | "دراسة حول التخطيط الحضري المستدام" |
| `payload.description` | description | "بحث يتناول أفضل الممارسات في التخطيط الحضري لل… |
| `payload.author` | المؤلف | "د. خالد أحمد" |
| `payload.year` | السنة | 2025 |

#### Notes | ملاحظات

Admin review: portal-contributions. type: publications | cities | organizations.

---

#### POST `/api/v1/newsletter/subscribe`

**الاسم | Name:** Subscribe Newsletter

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `email` | البريد الإلكتروني | "subscriber@example.com" |
| `locale` | لغة المشترك (ar|en) | "ar" |

#### Notes | ملاحظات

Admin list: newsletter-subscriptions. Returns 201 (new) or 200 (existing).

---
