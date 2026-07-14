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
- [الرئيسية — Home](#) — الصفحة الرئيسية كاملة — يطابق https://audi-ten.vercel.app/ar
- [الإعدادات — Settings](#) — إعدادات الموقع، حقوق النشر، روابط التواصل (التذييل).
- [من نحن — About](#) — صفحات عن المعهد: التعريف، الرؤية والرسالة، القيادة، المجلس، الفريق، الهيكل، الشركاء.
- [الاستراتيجية — Strategy](#) — استراتيجية المعهد ومجالات التركيز.
- [البرامج — Programs](#) — صفحات البرامج الثلاثة ودليل بوابة التنمية الحضرية.
- [المصادر — Resources](#) — قائمة التقارير والدراسات (صفحة مصادرنا + مركز المعرفة).
- [المركز الإعلامي — Media](#) — الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث.
- [اعمل معنا — Careers](#) — عرض الوظائف الشاغرة وتقديم طلب التوظيف.
- [الأسئلة الشائعة — FAQ](#) — قائمة الأسئلة الشائعة (مع تصنيف اختياري).
- [الصفحات القانونية — Legal](#) — صفحات الشروط والأحكام وسياسة الخصوصية.
- [النماذج — Forms](#) — معلومات التواصل ونماذج الإرسال من الموقع.
- [الواجهة العامة — Public /api/v1](#) — جميع واجهات الموقع العام — للتحقق بعد بناء المحتوى من Admin.

---

## الواجهة العامة — Public /api/v1

<a id=""></a>
**الغرض | Purpose:** جميع واجهات الموقع العام — للتحقق بعد بناء المحتوى من Admin. / All public website endpoints — verify after admin content build.
**لوحة التحكم | Admin resources:** `See each subfolder for Admin mapping`

**الغرض:** جميع واجهات الموقع العام — للتحقق بعد بناء المحتوى من Admin.

**Purpose:** All public website endpoints — verify after admin content build.

**Admin resources:** `See each subfolder for Admin mapping`

### خريطة صفحات الموقع | Site pages ↔ Public API

| الصفحة على الموقع | Public API | Admin (Postman) |
|-------------------|------------|-------------------|
| `/ar` الرئيسية | `GET /api/v1/home` | `الرئيسية` → `00 — بناء الصفحة الرئيسية` |
| من نحن / الرؤية / الفريق… | `GET /api/v1/about/*` | `من نحن` |
| الاستراتيجية / مجالات التركيز | `GET /api/v1/strategy/*` | `الاستراتيجية` |
| السياسات الحضرية / التدريب / الشراكات | `GET /api/v1/programs/{slug}` | `البرامج` → `00 — أدلة البناء` |
| بوابة التنمية (دليل) | `GET /api/v1/programs/urban-policies/directory` | `البرامج` → `دليل المدن/…` |
| مصادرنا | `GET /api/v1/resources` | `المصادر` |
| الأخبار / نشرة مدننا / لقاءات… | `GET /api/v1/media/{category}` | `المركز الإعلامي` |
| اعمل معنا | `GET /api/v1/careers` | `الوظائف` |
| الأسئلة الشائعة | `GET /api/v1/faqs` | `الأسئلة الشائعة` |
| الشروط / الخصوصية | `GET /api/v1/legal/{slug}` | `الصفحات القانونية` |
| تواصل معنا (بيانات) | `GET /api/v1/contact` | `الإعدادات` → `معلومات التواصل` |
| إعدادات عامة + تذييل | `GET /api/v1/settings` | `الإعدادات` |

**نتيجة المراجعة:** جميع مسارات `GET/POST /api/v1/*` في الخادم موجودة في Postman (37 طلباً عاماً). لا توجد واجهة عامة ناقصة مقارنة بـ `routes/api.php`.

**للتحقق بعد بناء المحتوى:** شغّل `GET /api/v1/home` ثم قارن الحقول مع [الموقع المباشر](https://audi-ten.vercel.app/ar).

اللغة: `Accept-Language: {{locale}}` (`ar` أو `en`). التفاصيل: `docs/postman/PUBLIC-API.md`.

### الرئيسية — Home

<a id=""></a>
**الغرض | Purpose:** الصفحة الرئيسية كاملة — يطابق https://audi-ten.vercel.app/ar / Homepage aggregate, member cities map, and GeoJSON layers.
**لوحة التحكم | Admin resources:** `الرئيسية → 00 — بناء الصفحة الرئيسية (hero-slides, home-stats, about-content, programs, media, resources, contact-info)`

#### GET `/api/v1/home`

**الاسم | Name:** جلب الصفحة الرئيسية — Get Home (Aggregate)

**الغرض | Purpose:** جلب محتوى الصفحة الرئيسية كاملاً (سلايدر، إحصائيات، برامج، إعلام، معرفة، عضوية).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

### خريطة الصفحة الرئيسية | Homepage ↔ API

الموقع: [audi-ten.vercel.app/ar](https://audi-ten.vercel.app/ar) — طلب واحد: **`GET /api/v1/home`**

| القسم على الموقع | حقل JSON | إدارة المحتوى (Postman Admin) |
|------------------|----------|-------------------------------|
| سلايدر الهيرو (تطوير تقني…) | `slider[]` | `الرئيسية` → `00 — بناء الصفحة الرئيسية` خطوات **01–04** → `hero-slides` |
| عن المعهد + رسالة + رؤية | `aboutIntro` | خطوة **05** → `about-content` (`home_about_intro`) |
| المعهد في أرقام (عنوان) | `stats.title`, `stats.subtitle` | خطوة **06** → `home_stats` |
| المعهد في أرقام (4 عدادات) | `stats.items[]` | خطوات **07–10** → `home-stats` |
| المدن الأعضاء (عنوان + أرقام) | `memberCities.title`, `memberCities.stats[]` | خطوات **11–12** → `about-content` + `member-cities/stats` |
| خريطة المدن (GeoJSON) | — | `GET /api/v1/home/member-cities` + `.geojson` — خطوة **13** + مجلد `المدن الأعضاء` |
| برامجنا (3 بطاقات) | `programs.items[]` | خطوات **14–17** → `programs` |
| المركز الإعلامي | `mediaCenter.*` | خطوات **18–24** → `about-content` + `media` |
| مركز المعرفة | `knowledgeCenter.*` | خطوات **25–31** → `about-content` + `knowledge-categories` + `resources` |
| عضوية + تواصل | `membershipContact.*` | خطوات **32–33** → `about-content` + `contact-info` |

**التذييل (Footer):** `GET /api/v1/settings` — حقوق النشر + روابط التواصل → Admin `الإعدادات`.

**نتيجة المراجعة:** جميع مسارات `GET/POST /api/v1/*` في الخادم موجودة في Postman (37 طلباً عاماً). لا توجد واجهة عامة ناقصة مقارنة بـ `routes/api.php`.

**للتحقق بعد بناء المحتوى:** شغّل `GET /api/v1/home` ثم قارن الحقول مع [الموقع المباشر](https://audi-ten.vercel.app/ar).

---

#### GET `/api/v1/home/member-cities`

**الاسم | Name:** خريطة المدن الأعضاء — Get Member Cities Map

**الغرض | Purpose:** بيانات خريطة المدن الأعضاء: إحصائيات + GeoJSON.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قسم «المدن الأعضاء» على الرئيسية — إحصائيات + GeoJSON. Admin: `member-cities/stats` + `member-cities/cities`.

---

#### GET `/api/v1/home/member-cities/countries.geojson`

**الاسم | Name:** GeoJSON الدول — Get Countries GeoJSON

**الغرض | Purpose:** طبقة GeoJSON للخريطة (هندسة + أسماء حسب اللغة حيث ينطبق).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

طبقة حدود الدول للخريطة — لا تعتمد على اللغة.

---

#### GET `/api/v1/home/member-cities/cities.geojson`

**الاسم | Name:** GeoJSON المدن — Get Cities GeoJSON

**الغرض | Purpose:** طبقة GeoJSON للخريطة (هندسة + أسماء حسب اللغة حيث ينطبق).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

طبقة نقاط المدن — أسماء المدن حسب Accept-Language.

---

### الإعدادات — Settings

<a id=""></a>
**الغرض | Purpose:** إعدادات الموقع، حقوق النشر، روابط التواصل (التذييل). / Site-wide settings and social links.
**لوحة التحكم | Admin resources:** `settings, social-links`

#### GET `/api/v1/settings`

**الاسم | Name:** إعدادات الموقع والتذييل — Get Site Settings

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

اسم الموقع، حقوق النشر، روابط التواصل (Footer). Admin: `settings` + `social-links`.

---

### من نحن — About

<a id=""></a>
**الغرض | Purpose:** صفحات عن المعهد: التعريف، الرؤية والرسالة، القيادة، المجلس، الفريق، الهيكل، الشركاء. / About institute pages with locale-resolved content.
**لوحة التحكم | Admin resources:** `about-content, leadership, advisory-board, team-*, partners`

#### GET `/api/v1/about/institute`

**الاسم | Name:** عن المعهد — Get Institute

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `about-content` (`institute`) + `home-stats`.

---

#### GET `/api/v1/about/vision-mission`

**الاسم | Name:** الرؤية والرسالة — Get Vision & Mission

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/vision-mission` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `about-content` (`vision_mission`, `goals`, `values`).

---

#### GET `/api/v1/about/leadership/president`

**الاسم | Name:** كلمة رئيس المعهد — Get Leadership (President)

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/president` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `leadership` type=president.

---

#### GET `/api/v1/about/leadership/director`

**الاسم | Name:** رسالة المدير العام — Get Leadership (Director)

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `leadership` type=director.

---

#### GET `/api/v1/about/advisory-board`

**الاسم | Name:** المجلس الاستشاري — Get Advisory Board

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `about-content` + `advisory-board`.

---

#### GET `/api/v1/about/team`

**الاسم | Name:** فريق العمل — Get Team

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `team-sections` + `team-members`.

---

#### GET `/api/v1/about/structure`

**الاسم | Name:** الهيكل التشغيلي — Get Structure

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/structure` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `about-content` (`structure`).

---

#### GET `/api/v1/about/partners`

**الاسم | Name:** الشركاء — Get Partners

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `partner-categories` + `partners`.

---

### الاستراتيجية — Strategy

<a id=""></a>
**الغرض | Purpose:** استراتيجية المعهد ومجالات التركيز. / Strategy page and focus areas.
**لوحة التحكم | Admin resources:** `strategy, strategy-pillars, focus-areas, about-content`

#### GET `/api/v1/strategy/strategy-2025`

**الاسم | Name:** استراتيجية 2025–2026 — Get Strategy 2025/2026

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `strategy` + `strategy-pillars` + `strategy-diagram`.

---

#### GET `/api/v1/strategy/focus-areas`

**الاسم | Name:** قائمة مجالات التركيز — List Focus Areas

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `focus-areas` + `about-content` (`focus_areas_pages`).

---

#### GET `/api/v1/strategy/focus-areas/{{slug}}`

**الاسم | Name:** تفاصيل مجال تركيز — Get Focus Area Detail

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas/{slug}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `focus-areas` by slug.

---

### البرامج — Programs

<a id=""></a>
**الغرض | Purpose:** صفحات البرامج الثلاثة ودليل بوابة التنمية الحضرية. / Program pages and urban policies directory.
**لوحة التحكم | Admin resources:** `programs, program-sections, program-section-details, training-courses, experts, directory/*`

#### GET `/api/v1/programs/urban-policies`

**الاسم | Name:** السياسات الحضرية — Get Urban Policies Program

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `00 — بناء برنامج السياسات الحضرية` (9 خطوات).

---

#### GET `/api/v1/programs/training`

**الاسم | Name:** التدريب وتطوير القدرات — Get Training Program

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `00 — بناء برنامج التدريب` (9 خطوات).

---

#### GET `/api/v1/programs/partnerships`

**الاسم | Name:** الشراكات — Get Partnerships Program

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `00 — بناء برنامج الشراكات` (9 خطوات).

---

#### GET `/api/v1/programs/urban-policies/directory`

**الاسم | Name:** دليل بوابة التنمية — Get Development Portal Directory

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

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

Directory list. Build: step 03 `directory.rows`. Detail page: `GET .../directory/{tab}/{number}`.

**صفحات المدن على الموقع | City detail pages**

`detail.layout`: **`rich`** = Al Baha (images, figures, related projects, discussions). **`simple`** = Riyadh, Jeddah, Cairo, Amman, Beirut (geography text + CTA only — no images, no discussions on live site).

| رقم | slug | المدينة | layout | رابط الموقع |
|-----|------|---------|--------|-------------|
| `01` | `al-baha` | الباحة | rich — صور + نقاشات | [/al-baha](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/al-baha) |
| `02` | `riyadh` | الرياض | simple — نص فقط | [/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/riyadh) |
| `03` | `jeddah` | جدة | simple — نص فقط | [/jeddah](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/jeddah) |
| `04` | `cairo` | القاهرة | simple — نص فقط | [/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/cairo) |
| `05` | `amman` | عمان | simple — نص فقط | [/amman](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/amman) |
| `06` | `beirut` | بيروت | simple — نص فقط | [/beirut](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/beirut) |

**صفحات المنظمات على الموقع | Organization detail pages**

List tab: `?tab=developmentPortal&directory=organizations`. List shows **type** + **country** (with flag). Detail (`?item=01`–`04`) shows org **name** + profile fields (`organizationFields`).

| رقم | المنظمة | رابط الموقع |
|-----|---------|-------------|
| `01` | PLATFORMA | [?item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=01) |
| `02` | منظمة التعاون والتنمية الاقتصادية | [?item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=02) |
| `03` | الاتحاد الدولي للمواصلات العامة | [?item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=03) |
| `04` | المجلس الأوروبي للبلديات والمناطق | [?item=04](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=04) |

**صفحات المشاريع على الموقع | Project detail pages**

List tab: `?tab=developmentPortal&directory=projects`. Detail slug URL: `/بوابة-التنمية/المشاريع/{slug}`.

| رقم | slug | المشروع | layout | رابط الموقع |
|-----|------|---------|--------|-------------|
| `01` | `cairo` | القاهرة | rich — مصادر + مؤسسون + مراجع + مشاريع ذات صلة | [/المشاريع/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/cairo) |
| `02` | `riyadh` | الرياض | simple — وصف + قيم + أدوات سياسات | [/المشاريع/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/riyadh) |
| `03` | `kuwait` | الكويت | simple | [/المشاريع/kuwait](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/kuwait) |
| `04` | `dubai` | دبي | simple | [/المشاريع/dubai](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/dubai) |
| `05` | `tunis` | تونس | simple | [/المشاريع/tunis](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/tunis) |
| `06` | `manama` | المنامة | simple | [/المشاريع/manama](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/manama) |

**صفحات المنشورات على الموقع | Publication detail pages**

List tab: `?tab=developmentPortal&directory=publications`. Detail (`?item=01`–`03`) opens modal **تفاصيل المنشور** with fields from the screenshot:
`organizationName`, `organizationType`, `publicationCountry`, `languages[]`, `publicationDate`, `publicationType`, `topics[]`, `publicationLink`, `coverImage`, `languageVersions`.

| رقم | المنشور | رابط الموقع |
|-----|---------|-------------|
| `01` | إعادة إعمار المدن العربية | [?item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=01) |
| `02` | البنية التحتية الخضراء | [?item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=02) |
| `03` | دليل السياسات الحضرية | [?item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=03) |

---

#### تفاصيل المدن — City Detail Pages

#### GET `/api/v1/programs/urban-policies/directory/cities/01`

**الاسم | Name:** الباحة — Al Baha — Get City Detail — al-baha

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/01` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/al-baha](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/al-baha). Admin: step 03 `directory.rows.cities[]` + `messages/data/al-baha-detail.{ar,en}.json`.

---

#### GET `/api/v1/programs/urban-policies/directory/cities/02`

**الاسم | Name:** الرياض — Riyadh — Get City Detail — riyadh

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/02` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/riyadh). Admin: step 03 `directory.rows.cities[]` + `messages/data/riyadh-detail.{ar,en}.json`.

---

#### GET `/api/v1/programs/urban-policies/directory/cities/03`

**الاسم | Name:** جدة — Jeddah — Get City Detail — jeddah

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/03` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/jeddah](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/jeddah). Admin: step 03 `directory.rows.cities[]` + `messages/data/jeddah-detail.{ar,en}.json`.

---

#### GET `/api/v1/programs/urban-policies/directory/cities/04`

**الاسم | Name:** القاهرة — Cairo — Get City Detail — cairo

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/04` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/cairo). Admin: step 03 `directory.rows.cities[]` + `messages/data/cairo-detail.{ar,en}.json`.

---

#### GET `/api/v1/programs/urban-policies/directory/cities/05`

**الاسم | Name:** عمان — Amman — Get City Detail — amman

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/05` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/amman](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/amman). Admin: step 03 `directory.rows.cities[]` + `messages/data/amman-detail.{ar,en}.json`.

---

#### GET `/api/v1/programs/urban-policies/directory/cities/06`

**الاسم | Name:** بيروت — Beirut — Get City Detail — beirut

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/cities/06` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [audi-w.vercel.app/.../المدن/beirut](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/beirut). Admin: step 03 `directory.rows.cities[]` + `messages/data/beirut-detail.{ar,en}.json`.

---

#### تفاصيل المنظمات — Organization Detail Pages

#### GET `/api/v1/programs/urban-policies/directory/organizations/01`

**الاسم | Name:** PLATFORMA — PLATFORMA — Get Organization Detail — 01

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/organizations/01` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=organizations&item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=01). Admin: step 03 `directory.rows.organizations[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/organizations/02`

**الاسم | Name:** منظمة التعاون والتنمية الاقتصادية — OECD — Get Organization Detail — 02

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/organizations/02` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=organizations&item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=02). Admin: step 03 `directory.rows.organizations[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/organizations/03`

**الاسم | Name:** الاتحاد الدولي للمواصلات العامة — UITP — Get Organization Detail — 03

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/organizations/03` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=organizations&item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=03). Admin: step 03 `directory.rows.organizations[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/organizations/04`

**الاسم | Name:** المجلس الأوروبي للبلديات والمناطق — CEMR — Get Organization Detail — 04

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/organizations/04` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=organizations&item=04](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=04). Admin: step 03 `directory.rows.organizations[]`.

---

#### تفاصيل المشاريع — Project Detail Pages

#### GET `/api/v1/programs/urban-policies/directory/projects/01`

**الاسم | Name:** القاهرة — Cairo — Get Project Detail — cairo

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/01` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/cairo). Admin: step 03 `directory.rows.projects[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/projects/02`

**الاسم | Name:** الرياض — Riyadh — Get Project Detail — riyadh

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/02` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/riyadh). Admin: step 03 `directory.rows.projects[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/projects/03`

**الاسم | Name:** الكويت — Kuwait — Get Project Detail — kuwait

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/03` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/kuwait](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/kuwait). Admin: step 03 `directory.rows.projects[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/projects/04`

**الاسم | Name:** دبي — Dubai — Get Project Detail — dubai

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/04` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/dubai](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/dubai). Admin: step 03 `directory.rows.projects[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/projects/05`

**الاسم | Name:** تونس — Tunis — Get Project Detail — tunis

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/05` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/tunis](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/tunis). Admin: step 03 `directory.rows.projects[]`.

---

#### GET `/api/v1/programs/urban-policies/directory/projects/06`

**الاسم | Name:** المنامة — Manama — Get Project Detail — manama

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/projects/06` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [/المشاريع/manama](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/manama). Admin: step 03 `directory.rows.projects[]`.

---

#### تفاصيل المنشورات — Publication Detail Pages

#### GET `/api/v1/programs/urban-policies/directory/publications/01`

**الاسم | Name:** إعادة إعمار المدن العربية — Arab Cities Reconstruction — Get Publication Detail — 01

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/publications/01` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=publications&item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=01). Admin: step 03 `directory.rows.publications[]` + fields from «تفاصيل المنشور» modal.

---

#### GET `/api/v1/programs/urban-policies/directory/publications/02`

**الاسم | Name:** البنية التحتية الخضراء — Green Infrastructure — Get Publication Detail — 02

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/publications/02` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=publications&item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=02). Admin: step 03 `directory.rows.publications[]` + fields from «تفاصيل المنشور» modal.

---

#### GET `/api/v1/programs/urban-policies/directory/publications/03`

**الاسم | Name:** دليل السياسات الحضرية — Urban Policy Guide — Get Publication Detail — 03

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/publications/03` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Live: [?directory=publications&item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=03). Admin: step 03 `directory.rows.publications[]` + fields from «تفاصيل المنشور» modal.

---

#### GET `/api/v1/programs/urban-policies/directory/{{directoryTab}}/{{directoryNumber}}`

**الاسم | Name:** تفاصيل عنصر الدليل (متغير) — Get Directory Item Detail (variables)

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/{tab}/{number}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Set `directoryTab` to `cities|organizations|projects|publications` and `directoryNumber` accordingly. See folders **تفاصيل المدن**, **تفاصيل المنظمات**, **تفاصيل المشاريع**, **تفاصيل المنشورات**.

**صفحات المدن على الموقع | City detail pages**

`detail.layout`: **`rich`** = Al Baha (images, figures, related projects, discussions). **`simple`** = Riyadh, Jeddah, Cairo, Amman, Beirut (geography text + CTA only — no images, no discussions on live site).

| رقم | slug | المدينة | layout | رابط الموقع |
|-----|------|---------|--------|-------------|
| `01` | `al-baha` | الباحة | rich — صور + نقاشات | [/al-baha](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/al-baha) |
| `02` | `riyadh` | الرياض | simple — نص فقط | [/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/riyadh) |
| `03` | `jeddah` | جدة | simple — نص فقط | [/jeddah](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/jeddah) |
| `04` | `cairo` | القاهرة | simple — نص فقط | [/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/cairo) |
| `05` | `amman` | عمان | simple — نص فقط | [/amman](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/amman) |
| `06` | `beirut` | بيروت | simple — نص فقط | [/beirut](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المدن/beirut) |

**صفحات المنظمات على الموقع | Organization detail pages**

List tab: `?tab=developmentPortal&directory=organizations`. List shows **type** + **country** (with flag). Detail (`?item=01`–`04`) shows org **name** + profile fields (`organizationFields`).

| رقم | المنظمة | رابط الموقع |
|-----|---------|-------------|
| `01` | PLATFORMA | [?item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=01) |
| `02` | منظمة التعاون والتنمية الاقتصادية | [?item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=02) |
| `03` | الاتحاد الدولي للمواصلات العامة | [?item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=03) |
| `04` | المجلس الأوروبي للبلديات والمناطق | [?item=04](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=organizations&item=04) |

**صفحات المشاريع على الموقع | Project detail pages**

List tab: `?tab=developmentPortal&directory=projects`. Detail slug URL: `/بوابة-التنمية/المشاريع/{slug}`.

| رقم | slug | المشروع | layout | رابط الموقع |
|-----|------|---------|--------|-------------|
| `01` | `cairo` | القاهرة | rich — مصادر + مؤسسون + مراجع + مشاريع ذات صلة | [/المشاريع/cairo](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/cairo) |
| `02` | `riyadh` | الرياض | simple — وصف + قيم + أدوات سياسات | [/المشاريع/riyadh](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/riyadh) |
| `03` | `kuwait` | الكويت | simple | [/المشاريع/kuwait](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/kuwait) |
| `04` | `dubai` | دبي | simple | [/المشاريع/dubai](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/dubai) |
| `05` | `tunis` | تونس | simple | [/المشاريع/tunis](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/tunis) |
| `06` | `manama` | المنامة | simple | [/المشاريع/manama](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية/بوابة-التنمية/المشاريع/manama) |

**صفحات المنشورات على الموقع | Publication detail pages**

List tab: `?tab=developmentPortal&directory=publications`. Detail (`?item=01`–`03`) opens modal **تفاصيل المنشور** with fields from the screenshot:
`organizationName`, `organizationType`, `publicationCountry`, `languages[]`, `publicationDate`, `publicationType`, `topics[]`, `publicationLink`, `coverImage`, `languageVersions`.

| رقم | المنشور | رابط الموقع |
|-----|---------|-------------|
| `01` | إعادة إعمار المدن العربية | [?item=01](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=01) |
| `02` | البنية التحتية الخضراء | [?item=02](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=02) |
| `03` | دليل السياسات الحضرية | [?item=03](https://audi-w.vercel.app/ar/برامجنا/برنامج-السياسات-الحضرية?tab=developmentPortal&directory=publications&item=03) |

---

#### POST `/api/v1/programs/urban-policies/directory/{{directoryTab}}/{{directoryNumber}}/discussions`

**الاسم | Name:** إضافة تعليق على عنصر الدليل — Post Directory Discussion

**الغرض | Purpose:** إرسال نموذج أو بيانات من زائر الموقع.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `authorName` | authorName | "د. سارة العتيبي" |
| `body` | body | "تعليق جديد حول هذا العنصر في الدليل." |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory/{tab}/{number}/discussions` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Public comment (pending approval). Admin review: `directory/discussions`.

---

### المصادر — Resources

<a id=""></a>
**الغرض | Purpose:** قائمة التقارير والدراسات (صفحة مصادرنا + مركز المعرفة). / Paginated knowledge resources list.
**لوحة التحكم | Admin resources:** `resources, knowledge-categories`

#### GET `/api/v1/resources`

**الاسم | Name:** قائمة المصادر — List Resources

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

صفحة مصادرنا + بطاقات مركز المعرفة. Admin: `resources` + `knowledge-categories`.

---

### المركز الإعلامي — Media

<a id=""></a>
**الغرض | Purpose:** الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث. / Media center listings and article detail.
**لوحة التحكم | Admin resources:** `media (news, newsletter, city_meetings, secretary_speaks)`

#### GET `/api/v1/media/news`

**الاسم | Name:** الأخبار — List News

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

**الاسم | Name:** نشرة مدننا — List Newsletter

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/newsletter` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `media` category=newsletter.

---

#### GET `/api/v1/media/city-meetings`

**الاسم | Name:** لقاءات حراك المدن — List City Meetings

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/city-meetings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `media` category=city_meetings.

---

#### GET `/api/v1/media/secretary-speaks`

**الاسم | Name:** الأمين يتحدث — List Secretary Speaks

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/secretary-speaks` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `media` category=secretary_speaks.

---

#### GET `/api/v1/media/{{category}}/{{slug}}`

**الاسم | Name:** تفاصيل مقال — Get Media Article Detail

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/{category}/{slug}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Public: شرطة في الرابط (news). Admin: underscore (news).

---

### اعمل معنا — Careers

<a id=""></a>
**الغرض | Purpose:** عرض الوظائف الشاغرة وتقديم طلب التوظيف. / Job listings and application form.
**لوحة التحكم | Admin resources:** `job-openings, job-applications`

#### GET `/api/v1/careers`

**الاسم | Name:** الوظائف الشاغرة — List Job Openings

**الغرض | Purpose:** قائمة paginated — حقول بلغة واحدة حسب Accept-Language.

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `job-openings`.

---

#### GET `/api/v1/careers/{{id}}`

**الاسم | Name:** تفاصيل وظيفة — Get Job Opening

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers/{id}` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `job-openings/{id}`.

---

#### POST `/api/v1/careers/apply`

**الاسم | Name:** تقديم على وظيفة — Submit Job Application

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

نموذج التقديم — يُراجع في Admin → `النماذج الواردة` → `job-applications`.

---

### الأسئلة الشائعة — FAQ

<a id=""></a>
**الغرض | Purpose:** قائمة الأسئلة الشائعة (مع تصنيف اختياري). / FAQ list with optional category filter.
**لوحة التحكم | Admin resources:** `faqs`

#### GET `/api/v1/faqs`

**الاسم | Name:** قائمة الأسئلة — List FAQs

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

### الصفحات القانونية — Legal

<a id=""></a>
**الغرض | Purpose:** صفحات الشروط والأحكام وسياسة الخصوصية. / Terms and privacy policy pages.
**لوحة التحكم | Admin resources:** `legal`

#### GET `/api/v1/legal/terms`

**الاسم | Name:** الشروط والأحكام — Get Terms

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `legal` slug=terms.

---

#### GET `/api/v1/legal/privacy`

**الاسم | Name:** سياسة الخصوصية — Get Privacy Policy

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/privacy` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: `legal` slug=privacy.

---

### النماذج — Forms

<a id=""></a>
**الغرض | Purpose:** معلومات التواصل ونماذج الإرسال من الموقع. / Contact info and public form submissions.
**لوحة التحكم | Admin resources:** `contact-info, contact-submissions, membership-applications, portal-contributions, job-applications, newsletter-subscriptions`

#### GET `/api/v1/contact`

**الاسم | Name:** بيانات التواصل — Get Contact Info

**الغرض | Purpose:** قراءة بيانات عامة من الخادم (لغة واحدة لكل طلب).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Notes | ملاحظات

**Public match:** `GET /api/v1/contact` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

صفحة تواصل معنا + تذييل الرئيسية. Admin: `contact-info` (ليس settings).

---

#### POST `/api/v1/contact`

**الاسم | Name:** إرسال رسالة تواصل — Submit Contact Form

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

نموذج تواصل — Admin → `contact-submissions`.

---

#### POST `/api/v1/membership`

**الاسم | Name:** طلب عضوية — Submit Membership Application

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

نموذج انضم الى عضوية المعهد — Admin → `membership-applications`.

---

#### POST `/api/v1/programs/urban-policies/contribute`

**الاسم | Name:** مساهمة بوابة التنمية — Submit Portal Contribution

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

مساهمة في دليل بوابة التنمية — Admin → `portal-contributions`. type: publications | cities | organizations.

---

#### POST `/api/v1/newsletter/subscribe`

**الاسم | Name:** الاشتراك في النشرة — Subscribe Newsletter

**الغرض | Purpose:** إرسال نموذج من الموقع — يُراجع في لوحة التحكم (Admin).

**المصادقة | Auth:** غير مطلوب (واجهة عامة)
**اللغة | Language:** `Accept-Language: {{locale}}` أو `?locale=ar|en`

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `email` | البريد الإلكتروني | "subscriber@example.com" |
| `locale` | لغة المشترك (ar|en) | "ar" |

#### Notes | ملاحظات

نموذج النشرة في التذييل — Admin → `newsletter-subscriptions`.

---
