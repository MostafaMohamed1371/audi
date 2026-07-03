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
- [المصادقة — Auth](#auth) — تسجيل الدخول والخروج والتحقق من المستخدم الحالي.
- [رفع الملفات — Uploads](#uploads) — رفع الصور وملفات PDF واستخدام الرابط المُرجَع في حقول imageUrl وfileUrl.
- [الإعدادات — Settings](#settings) — إعدادات الموقع، روابط التواصل، ومعلومات التواصل (صفحة Contact + التذييل).
- [الرئيسية — Home](#home) — محتوى الصفحة الرئيسية: الهيرو، الإحصائيات، وأقسام about-content (home_*).
- [من نحن — About](#about) — صفحات عن المعهد: المحتوى، القيادة، المجلس، الفريق، الشركاء.
- [الاستراتيجية — Strategy](#strategy) — استراتيجية المعهد، المحاور، المخطط، ومجالات التركيز.
- [البرامج — Programs](#programs) — برامج المعهد (3 برامج)، أقسامها، الدورات، الخبراء، ودليل السياسات الحضرية.
- [المصادر — Resources](#resources) — تقارير ودراسات ومصادر المعرفة (صفحة مصادرنا + مركز المعرفة في الرئيسية).
- [المركز الإعلامي — Media](#media) — الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث.
- [الوظائف — Careers](#careers) — الوظائف الشاغرة المعروضة في صفحة اعمل معنا.
- [الأسئلة الشائعة — FAQ](#faq) — أسئلة وأجوبة صفحة FAQ.
- [الصفحات القانونية — Legal](#legal) — الشروط والأحكام وسياسة الخصوصية.
- [المدن الأعضاء — Member Cities](#member-cities) — إدارة مدن الأعضاء، الإحصائيات، واستيراد GeoJSON.
- [النماذج الواردة — Form Submissions](#form-submissions) — مراجعة رسائل التواصل، طلبات العضوية، المساهمات، التقديم على الوظائف، النشرة.

---

## لوحة التحكم — Admin /api/admin

واجهة الإدارة — تخزين حقول *Ar/*En. راجع docs/postman/ADMIN-API.md. جميع الطلبات ترسل Accept-Language: {{locale}}.

### المصادقة — Auth

<a id="auth"></a>
**الغرض | Purpose:** تسجيل الدخول والخروج والتحقق من المستخدم الحالي. / Login, logout, and current user profile.

#### POST `/api/admin/auth/login`

**الاسم | Name:** تسجيل الدخول — Login

**الغرض | Purpose:** الحصول على Bearer token للمصادقة على باقي طلبات الإدارة.

**المصادقة | Auth:** غير مطلوب

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `email` | البريد الإلكتروني | "admin@araburban.org" |
| `password` | كلمة المرور | "password" |

#### Notes | ملاحظات

يُرجع `{ token, user }`. احفظ `token` في متغير `adminToken`.

---

#### POST `/api/admin/auth/logout`

**الاسم | Name:** تسجيل الخروج — Logout

**الغرض | Purpose:** إبطال توكن الجلسة الحالي (Bearer token).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** إبطال توكن الجلسة الحالي (Bearer token).

---

#### GET `/api/admin/auth/me`

**الاسم | Name:** الملف الشخصي — Me

**الغرض | Purpose:** عرض بيانات المستخدم المسجّل حالياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض بيانات المستخدم المسجّل حالياً.

---

### رفع الملفات — Uploads

<a id="uploads"></a>
**الغرض | Purpose:** رفع الصور وملفات PDF واستخدام الرابط المُرجَع في حقول imageUrl وfileUrl. / Upload images/PDFs; use returned URL in imageUrl/fileUrl fields.

#### GET `/api/admin/uploads?page=1&limit=20`

**الاسم | Name:** عرض القائمة — List Uploads

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

---

#### POST `/api/admin/uploads`

**الاسم | Name:** رفع ملف — Upload File

**الغرض | Purpose:** رفع ملف (صورة أو PDF) والحصول على URL لاستخدامه في حقول imageUrl / fileUrl.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `file` | مطلوب. الأنواع: jpg, jpeg, png, gif, webp, pdf. الحد الأقصى 10 MB. | (ملف) |

#### Notes | ملاحظات

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

---

#### GET `/api/admin/uploads/{{id}}`

**الاسم | Name:** عرض — Show Upload

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### DELETE `/api/admin/uploads/{{id}}`

**الاسم | Name:** حذف — Delete Upload

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

### الإعدادات — Settings

<a id="settings"></a>
**الغرض | Purpose:** إعدادات الموقع، روابط التواصل، ومعلومات التواصل (صفحة Contact + التذييل). / Site settings, social links, and contact info block.
**الواجهة العامة | Public API:** `GET /api/v1/settings, GET /api/v1/contact`

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### إعدادات الموقع — Site Settings

#### GET `/api/admin/settings`

**الاسم | Name:** عرض القائمة — List Site Setting

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

مفاتيح أخرى: `site.copyright`, `contact.title`, …قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/settings`

**الاسم | Name:** إنشاء — Create إعداد الموقع

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `key` | المفتاح الفريد للسجل | "site.name" |
| `valueAr` | قيمة الإعداد بالعربية | "المعهد العربي لإنماء المدن" |
| `valueEn` | قيمة الإعداد بالإنجليزية | "Arab Urban Development Institute" |
| `group` | مجموعة الإعداد (general, contact) | "general" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

مفاتيح أخرى: `site.copyright`, `contact.title`, …**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/settings/{{id}}`

**الاسم | Name:** عرض — Show إعداد الموقع

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

مفاتيح أخرى: `site.copyright`, `contact.title`, …Use `id` from create response.

---

#### PUT `/api/admin/settings/{{id}}`

**الاسم | Name:** تحديث — Update إعداد الموقع

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `key` | المفتاح الفريد للسجل | "site.name" |
| `valueAr` | قيمة الإعداد بالعربية | "المعهد العربي لإنماء المدن" |
| `valueEn` | قيمة الإعداد بالإنجليزية | "Arab Urban Development Institute" |
| `group` | مجموعة الإعداد (general, contact) | "general" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

مفاتيح أخرى: `site.copyright`, `contact.title`, …**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/settings/{{id}}`

**الاسم | Name:** حذف — Delete إعداد الموقع

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

مفاتيح أخرى: `site.copyright`, `contact.title`, …

---

#### روابط التواصل — Social Links

#### GET `/api/admin/social-links`

**الاسم | Name:** عرض القائمة — List Social Link

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/social-links`

**الاسم | Name:** إنشاء — Create رابط تواصل

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `platform` | منصة التواصل (linkedin, twitter, …) | "linkedin" |
| `url` | الرابط (URL) | "https:\/\/linkedin.com\/company\/audi" |
| `icon` | أيقونة المنصة | "linkedin" |
| `isActive` | نشط؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/social-links/{{id}}`

**الاسم | Name:** عرض — Show رابط تواصل

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/social-links/{{id}}`

**الاسم | Name:** تحديث — Update رابط تواصل

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `platform` | منصة التواصل (linkedin, twitter, …) | "linkedin" |
| `url` | الرابط (URL) | "https:\/\/linkedin.com\/company\/audi" |
| `icon` | أيقونة المنصة | "linkedin" |
| `isActive` | نشط؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/social-links/{{id}}`

**الاسم | Name:** حذف — Delete رابط تواصل

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/settings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/social-links/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder رابط تواصل

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### POST `/api/admin/settings`

**الاسم | Name:** إنشاء إعداد تواصل (قديم) — Legacy Contact Setting

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `key` | المفتاح الفريد للسجل | "contact.title" |
| `valueAr` | قيمة الإعداد بالعربية | "تواصل معنا" |
| `valueEn` | قيمة الإعداد بالإنجليزية | "Contact Us" |
| `group` | مجموعة الإعداد (general, contact) | "contact" |

#### Notes | ملاحظات

قديم: يُفضّل `GET/PUT /api/admin/contact-info` لجميع حقول التواصل.

---

#### معلومات التواصل — Contact Info

#### GET `/api/admin/contact-info`

**الاسم | Name:** عرض — Get Contact Info

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/contact` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

حقول ثنائية اللغة (`titleAr`, `itemsAr`, …).

---

#### PUT `/api/admin/contact-info`

**الاسم | Name:** تحديث — Update Contact Info

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تواصل معنا" |
| `titleEn` | العنوان بالإنجليزية | "Contact Us" |
| `subtitleAr` | العنوان الفرعي بالعربية | "" |
| `subtitleEn` | العنوان الفرعي بالإنجليزية | "" |
| `addressLabelAr` | تسمية العنوان بالعربية | "العنوان" |
| `addressLabelEn` | تسمية العنوان بالإنجليزية | "Address" |
| `addressAr` | العنوان الكامل بالعربية | "شارع عبدالله بن حذافة السهمي، الحي الدبلوماسي … |
| `addressEn` | العنوان الكامل بالإنجليزية | "Abdullah bin Hudhafa Al-Sahmi Street, Diplomat… |
| `mapTitleAr` | عنوان الخريطة بالعربية | "موقع المعهد العربي لإنماء المدن" |
| `mapTitleEn` | عنوان الخريطة بالإنجليزية | "Arab Urban Development Institute location" |
| `mapEmbedUrlAr` | رابط تضمين خريطة Google (عربي) | "https:\/\/maps.google.com\/maps?q=Arab+Urban+D… |
| `mapEmbedUrlEn` | رابط تضمين خريطة Google (إنجليزي) | "https:\/\/maps.google.com\/maps?q=Arab+Urban+D… |
| `itemsAr` | عناصر التواصل بالعربية [{label, value, type, href}] | [{"label":"الهاتف","value":"+966 114802555","ty… |
| `itemsEn` | عناصر التواصل بالإنجليزية | [{"label":"Phone","value":"+966 114802555","typ… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/contact` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

واجهة الإدارة المفضّلة لصفحة التواصل وتذييل الرئيسية. Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** step 33.

---

### الرئيسية — Home

<a id="home"></a>
**الغرض | Purpose:** محتوى الصفحة الرئيسية: الهيرو، الإحصائيات، وأقسام about-content (home_*). / Homepage: hero slides, stats, and home_* about-content sections.
**الواجهة العامة | Public API:** `GET /api/v1/home`

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

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

#### 00 — بناء الصفحة الرئيسية — 00 — Build Full Homepage

**Start here** — run steps **01 → 33** in order after Login. Bodies match https://audi-ten.vercel.app/ar.

Each `POST` appears **once** in this folder (no duplicate requests elsewhere).

Then verify: `GET /api/v1/home` with `Accept-Language: ar`.

#### POST `/api/admin/hero-slides`

**الاسم | Name:** 01 — شريحة هيرو 1

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تطوير تقني" |
| `titleEn` | العنوان بالإنجليزية | "Technical Development" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 1: Hero slider → `slider[]`.

---

#### POST `/api/admin/hero-slides`

**الاسم | Name:** 02 — شريحة هيرو 2

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تنمية عمرانية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Development" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/2.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 2: Hero slider → `slider[]`.

---

#### POST `/api/admin/hero-slides`

**الاسم | Name:** 03 — شريحة هيرو 3

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "شراكات مستدامة" |
| `titleEn` | العنوان بالإنجليزية | "Sustainable Partnerships" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/3.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 3: Hero slider → `slider[]`.

---

#### POST `/api/admin/hero-slides`

**الاسم | Name:** 04 — شريحة هيرو 4

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "بحوث ومبادرات" |
| `titleEn` | العنوان بالإنجليزية | "Research and Initiatives" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/4.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 4: Hero slider → `slider[]`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 05 — عن المعهد — home_about_intro

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_about_intro" |
| `titleAr` | العنوان بالعربية | "المعهد العربي لإنماء المدن" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Institute" |
| `bodyAr.description` | description | "تأسس المعهد العربي لإنماء المدن عام 1980، ومقر… |
| `bodyAr.cta` | cta | "المزيد" |
| `bodyAr.mission.title` | العنوان | "رسالتنا" |
| `bodyAr.mission.description` | description | "مؤسسة عالمية رائدة تسهم في خلق مستقبل عمراني أ… |
| `bodyAr.mission.readMore` | readMore | "قراءة المزيد" |
| `bodyAr.vision.title` | العنوان | "رؤيتنا" |
| `bodyAr.vision.description` | description | "دعم المدن والبلديات العربية لمواجهة تحديات الت… |
| `bodyAr.vision.readMore` | readMore | "قراءة المزيد" |
| `bodyEn.description` | description | "Founded in 1980 and headquartered in Riyadh, t… |
| `bodyEn.cta` | cta | "Learn More" |
| `bodyEn.mission.title` | العنوان | "Our Mission" |
| `bodyEn.mission.description` | description | "A leading global institution contributing to a… |
| `bodyEn.mission.readMore` | readMore | "Read More" |
| `bodyEn.vision.title` | العنوان | "Our Vision" |
| `bodyEn.vision.description` | description | "Supporting Arab cities and municipalities in f… |
| `bodyEn.vision.readMore` | readMore | "Read More" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 5: About block → `aboutIntro`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 06 — المعهد في أرقام (عنوان) — home_stats

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_stats" |
| `titleAr` | العنوان بالعربية | "المعهد في أرقام" |
| `titleEn` | العنوان بالإنجليزية | "The Institute in Numbers" |
| `bodyAr.subtitle` | subtitle | "مؤسسة عالمية رائدة تساهم في صنع مستقبل حضري أف… |
| `bodyEn.subtitle` | subtitle | "A leading global institution contributing to c… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 6: Stats title/subtitle. Counters: steps 07–10.

---

#### POST `/api/admin/home-stats`

**الاسم | Name:** 07 — إحصائية 1

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+25" |
| `labelAr` | تسمية الإحصائية بالعربية | "اتفاقية" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Agreements" |
| `descriptionAr` | الوصف بالعربية | "الاتفاقيات" |
| `descriptionEn` | الوصف بالإنجليزية | "Partnership agreements" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 7: Counter → `stats.items[]`.

---

#### POST `/api/admin/home-stats`

**الاسم | Name:** 08 — إحصائية 2

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+10" |
| `labelAr` | تسمية الإحصائية بالعربية | "نشرة" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Newsletters" |
| `descriptionAr` | الوصف بالعربية | "نشرة مدننا" |
| `descriptionEn` | الوصف بالإنجليزية | "Our Cities Newsletter" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 8: Counter → `stats.items[]`.

---

#### POST `/api/admin/home-stats`

**الاسم | Name:** 09 — إحصائية 3

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+500" |
| `labelAr` | تسمية الإحصائية بالعربية | "مشارك" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Participants" |
| `descriptionAr` | الوصف بالعربية | "المشاركين في برامج القيادات البلدية" |
| `descriptionEn` | الوصف بالإنجليزية | "Participants in municipal leadership programs" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 9: Counter → `stats.items[]`.

---

#### POST `/api/admin/home-stats`

**الاسم | Name:** 10 — إحصائية 4

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+400" |
| `labelAr` | تسمية الإحصائية بالعربية | "مشروع" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Projects" |
| `descriptionAr` | الوصف بالعربية | "مشروع في تقارير السياسات الحضرية" |
| `descriptionEn` | الوصف بالإنجليزية | "Projects in urban policy reports" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 10: Counter → `stats.items[]`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 11 — المدن الأعضاء (عنوان) — home_member_cities

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_member_cities" |
| `titleAr` | العنوان بالعربية | "المدن الأعضاء" |
| `titleEn` | العنوان بالإنجليزية | "Member Cities" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 11: Member cities title → `memberCities.title`.

---

#### PUT `/api/admin/member-cities/stats`

**الاسم | Name:** 12 — إحصائيات المدن — member-cities/stats

**الغرض | Purpose:** استبدال/تحديث بيانات كاملة.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"key":"countries","value":12,"autoCalculate":… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 12: 12 دولة / 400 مدينة / 1240 عضو → `memberCities.stats[]`.

---

#### POST `/api/admin/member-cities/cities`

**الاسم | Name:** 13 — مدينة على الخريطة (مثال الرياض) — member city

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `countryCode` | رمز الدولة | "SA" |
| `nameAr` | الاسم بالعربية | "الرياض" |
| `nameEn` | الاسم بالإنجليزية | "Riyadh" |
| `latitude` | خط العرض | 24.7136 |
| `longitude` | خط الطول | 46.6753 |
| `infoAr` | معلومات إضافية بالعربية | "عاصمة المملكة العربية السعودية" |
| `infoEn` | معلومات إضافية بالإنجليزية | "Capital of Saudi Arabia" |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 13: Sample map pin. Repeat or use bulk import for all cities.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 14 — برامجنا (عنوان) — home_programs

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_programs" |
| `titleAr` | العنوان بالعربية | "برامجنا" |
| `titleEn` | العنوان بالإنجليزية | "Our Programs" |
| `bodyAr.cta` | cta | "استكشف" |
| `bodyEn.cta` | cta | "Explore" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 14: Programs section title + CTA. Cards: steps 15–17.

---

#### POST `/api/admin/programs`

**الاسم | Name:** 15 — برنامج urban-policies

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-policies" |
| `titleAr` | العنوان بالعربية | "السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policies" |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "إعداد دراسات وتقارير سياسات حضرية تدعم صناع ال… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Developing urban policy studies and reports th… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 15: Program card → `programs.items[]`.

---

#### POST `/api/admin/programs`

**الاسم | Name:** 16 — برنامج training

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "training" |
| `titleAr` | العنوان بالعربية | "التدريب و تطوير القدرات" |
| `titleEn` | العنوان بالإنجليزية | "Training & Capacity Building" |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "برامج تدريبية متخصصة لبناء قدرات العاملين في ا… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Specialized training programs to build the cap… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 16: Program card → `programs.items[]`.

---

#### POST `/api/admin/programs`

**الاسم | Name:** 17 — برنامج partnerships

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "partnerships" |
| `titleAr` | العنوان بالعربية | "الشراكات" |
| `titleEn` | العنوان بالإنجليزية | "Partnerships" |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "معاً لنصنع مستقبل حضري أفضل: تعرف كيف نبني جسو… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Building strategic partnerships with cities an… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 17: Program card → `programs.items[]`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 18 — المركز الإعلامي (عناوين) — home_media_center

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_media_center" |
| `titleAr` | العنوان بالعربية | "المركز الإعلامي" |
| `titleEn` | العنوان بالإنجليزية | "Media Center" |
| `bodyAr.subtitle` | subtitle | "تعرف على أخبارنا ونشرتنا الدورية ومنشوراتنا ال… |
| `bodyAr.readMore` | readMore | "قراءة المزيد" |
| `bodyAr.viewAll` | viewAll | "عرض الكل" |
| `bodyEn.subtitle` | subtitle | "Explore our news, periodic newsletter, and aud… |
| `bodyEn.readMore` | readMore | "Read more" |
| `bodyEn.viewAll` | viewAll | "View all" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 18: Media section labels. News items: steps 19–24.

---

#### POST `/api/admin/media`

**الاسم | Name:** 19 — خبر director-dialogue-session

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "director-dialogue-session" |
| `slugAr` | الرابط العربي (slug) | "جلسة-حوارية-التنمية-الحضرية" |
| `slugEn` | الرابط الإنجليزي (slug) | "director-dialogue-session" |
| `titleAr` | العنوان بالعربية | "مدير عام المعهد يشارك في جلسة حوارية للفطيم حو… |
| `titleEn` | العنوان بالإنجليزية | "Institute Director Participates in Al-Futtaim … |
| `descriptionAr` | الوصف بالعربية | "شارك مدير عام المعهد العربي لإنماء المدن في جل… |
| `descriptionEn` | الوصف بالإنجليزية | "The Director General of the Arab Urban Develop… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["شارك مدير عام المعهد العربي لإنماء المدن في ج… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Director General of the Arab Urban Develo… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/1.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `authorsAr` | المؤلفون بالعربية (مصفوفة) | null |
| `authorsEn` | المؤلفون بالإنجليزية (مصفوفة) | null |
| `eventTime` | وقت الفعالية (مثل: 10:00 - 14:00) | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 19: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/media`

**الاسم | Name:** 20 — خبر uae-contractors-league

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "uae-contractors-league" |
| `slugAr` | الرابط العربي (slug) | "دوري-المقاولين-الإماراتي" |
| `slugEn` | الرابط الإنجليزي (slug) | "uae-contractors-league" |
| `titleAr` | العنوان بالعربية | "الدوري الإماراتي لمقاولي العمران يفتتح بمشاركة… |
| `titleEn` | العنوان بالإنجليزية | "UAE Urban Contractors League Opens with Munici… |
| `descriptionAr` | الوصف بالعربية | "انطلقت فعاليات الدوري الإماراتي لمقاولي العمرا… |
| `descriptionEn` | الوصف بالإنجليزية | "The UAE Urban Contractors League launched with… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["انطلقت فعاليات الدوري الإماراتي لمقاولي العمر… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The UAE Urban Contractors League launched wit… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-01-22" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/2.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 20: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/media`

**الاسم | Name:** 21 — خبر municipal-cooperation-network

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "municipal-cooperation-network" |
| `slugAr` | الرابط العربي (slug) | "شبكة-التعاون-البلدي-العربية" |
| `slugEn` | الرابط الإنجليزي (slug) | "municipal-cooperation-network" |
| `titleAr` | العنوان بالعربية | "المعهد العربي ينظم الاجتماع السنوي لشبكة التعا… |
| `titleEn` | العنوان بالإنجليزية | "Institute Holds Annual Arab Municipal Cooperat… |
| `descriptionAr` | الوصف بالعربية | "عقد المعهد العربي لإنماء المدن اجتماعه السنوي … |
| `descriptionEn` | الوصف بالإنجليزية | "The Institute held its annual meeting of the A… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["عقد المعهد العربي لإنماء المدن اجتماعه السنوي… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Arab Urban Development Institute held its… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-02-15" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/3.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 21: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/media`

**الاسم | Name:** 22 — خبر sustainable-urban-planning

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "sustainable-urban-planning" |
| `slugAr` | الرابط العربي (slug) | "التخطيط-الحضري-المستدام" |
| `slugEn` | الرابط الإنجليزي (slug) | "sustainable-urban-planning" |
| `titleAr` | العنوان بالعربية | "إطلاق مبادرة جديدة لدعم التخطيط الحضري المستدا… |
| `titleEn` | العنوان بالإنجليزية | "New Initiative to Support Sustainable Urban Pl… |
| `descriptionAr` | الوصف بالعربية | "أعلن المعهد العربي لإنماء المدن عن مبادرة جديد… |
| `descriptionEn` | الوصف بالإنجليزية | "The Institute announced a new initiative to st… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["أعلن المعهد العربي لإنماء المدن عن مبادرة جدي… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Arab Urban Development Institute announce… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-03-08" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/4.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 22: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/media`

**الاسم | Name:** 23 — خبر municipal-governance-workshop

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "municipal-governance-workshop" |
| `slugAr` | الرابط العربي (slug) | "ورشة-الحوكمة-البلدية" |
| `slugEn` | الرابط الإنجليزي (slug) | "municipal-governance-workshop" |
| `titleAr` | العنوان بالعربية | "ورشة عمل حول الحوكمة البلدية الرشيدة بمشاركة خ… |
| `titleEn` | العنوان بالإنجليزية | "Workshop on Good Municipal Governance with Int… |
| `descriptionAr` | الوصف بالعربية | "نظم المعهد ورشة عمل متخصصة حول الحوكمة البلدية… |
| `descriptionEn` | الوصف بالإنجليزية | "The Institute organized a specialized workshop… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["نظم المعهد ورشة عمل متخصصة حول الحوكمة البلدي… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Institute organized a specialized worksho… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-04-20" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/1.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 4 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 23: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/media`

**الاسم | Name:** 24 — خبر urban-development-conference

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "urban-development-conference" |
| `slugAr` | الرابط العربي (slug) | "مؤتمر-التنمية-الحضرية-2025" |
| `slugEn` | الرابط الإنجليزي (slug) | "urban-development-conference" |
| `titleAr` | العنوان بالعربية | "مؤتمر التنمية الحضرية 2025 يناقش مستقبل المدن … |
| `titleEn` | العنوان بالإنجليزية | "Urban Development Conference 2025 Discusses th… |
| `descriptionAr` | الوصف بالعربية | "استضاف المعهد مؤتمر التنمية الحضرية بمشاركة نخ… |
| `descriptionEn` | الوصف بالإنجليزية | "The Institute hosted the Urban Development Con… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["استضاف المعهد العربي لإنماء المدن مؤتمر التنم… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Arab Urban Development Institute hosted t… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-05-10" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/2.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 5 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 24: News → `mediaCenter.featured` / `items`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 25 — مركز المعرفة (أزرار) — home_knowledge_center labels

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_knowledge_center" |
| `bodyAr.viewIssue` | viewIssue | "عرض الإصدار" |
| `bodyAr.downloadPdf` | downloadPdf | "تنزيل نسخة PDF" |
| `bodyEn.viewIssue` | viewIssue | "View Issue" |
| `bodyEn.downloadPdf` | downloadPdf | "Download PDF" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 25: Button labels (عرض الإصدار / تنزيل PDF). Categories: steps 26–28. Cards: steps 29–31.

---

#### POST `/api/admin/knowledge-categories`

**الاسم | Name:** 26 — تصنيف knowledge-center

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "knowledge-center" |
| `titleAr` | العنوان بالعربية | "مركز المعرفة" |
| `titleEn` | العنوان بالإنجليزية | "Knowledge Center" |
| `descriptionAr` | الوصف بالعربية | "منصة تجمع كل إصدارات المعهد الرقمية والصوتية، … |
| `descriptionEn` | الوصف بالإنجليزية | "A platform bringing together all of the Instit… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 26: Category → `knowledgeCenter.categories[]`. Save response `id` (first category = 1 for steps 29–31).

---

#### POST `/api/admin/knowledge-categories`

**الاسم | Name:** 27 — تصنيف mudununa

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "mudununa" |
| `titleAr` | العنوان بالعربية | "مدننا" |
| `titleEn` | العنوان بالإنجليزية | "Mudununa" |
| `descriptionAr` | الوصف بالعربية | "نشرة دورية تصدر عن المعهد العربي لإنماء المدن،… |
| `descriptionEn` | الوصف بالإنجليزية | "A periodic newsletter published by the Arab Ur… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 27: Category → `knowledgeCenter.categories[]`. Save response `id` (first category = 1 for steps 29–31).

---

#### POST `/api/admin/knowledge-categories`

**الاسم | Name:** 28 — تصنيف meetings-platform

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "meetings-platform" |
| `titleAr` | العنوان بالعربية | "منصة الاجتماعات" |
| `titleEn` | العنوان بالإنجليزية | "Meetings Platform" |
| `descriptionAr` | الوصف بالعربية | "أرشيف رقمي لفعاليات واجتماعات المعهد، يوثق الن… |
| `descriptionEn` | الوصف بالإنجليزية | "A digital archive of the Institute's events an… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 28: Category → `knowledgeCenter.categories[]`. Save response `id` (first category = 1 for steps 29–31).

---

#### POST `/api/admin/resources`

**الاسم | Name:** 29 — مصدر solid-waste-management

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "solid-waste-management" |
| `titleAr` | العنوان بالعربية | "إدارة النفايات الصلبة في المدن العربية" |
| `titleEn` | العنوان بالإنجليزية | "Solid Waste Management in Arab Cities" |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/our-sources\/1.png" |
| `fileUrl` | رابط ملف PDF أو مرفق | "\/storage\/resources\/solid-waste-management.pdf" |
| `knowledgeCategoryId` | معرّف تصنيف مركز المعرفة (FK) — اربط البطاقة بالتصنيف | 1 |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 29: Card linked via `knowledgeCategoryId` → `categories[].items[]`.

---

#### POST `/api/admin/resources`

**الاسم | Name:** 30 — مصدر urban-tourism

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-tourism" |
| `titleAr` | العنوان بالعربية | "المدينة، وجهة تكتشف السياحة الحضرية في المنطقة… |
| `titleEn` | العنوان بالإنجليزية | "The City: Discovering Urban Tourism in the Ara… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/our-sources\/2.png" |
| `fileUrl` | رابط ملف PDF أو مرفق | "\/storage\/resources\/urban-tourism.pdf" |
| `knowledgeCategoryId` | معرّف تصنيف مركز المعرفة (FK) — اربط البطاقة بالتصنيف | 1 |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 30: Card linked via `knowledgeCategoryId` → `categories[].items[]`.

---

#### POST `/api/admin/resources`

**الاسم | Name:** 31 — مصدر green-infrastructure

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "green-infrastructure" |
| `titleAr` | العنوان بالعربية | "البنية التحتية الخضراء نحو منظومة خضراء متكامل… |
| `titleEn` | العنوان بالإنجليزية | "Green Infrastructure Toward an Integrated Gree… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/our-sources\/4.png" |
| `fileUrl` | رابط ملف PDF أو مرفق | "\/storage\/resources\/green-infrastructure.pdf" |
| `knowledgeCategoryId` | معرّف تصنيف مركز المعرفة (FK) — اربط البطاقة بالتصنيف | 1 |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 31: Card linked via `knowledgeCategoryId` → `categories[].items[]`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** 32 — عضوية المعهد — home_membership_contact

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_membership_contact" |
| `bodyAr.membership.title` | العنوان | "انضم الى عضوية المعهد" |
| `bodyAr.membership.subtitle` | subtitle | "لنتشارك في صنع مستقبل واعد لمدننا العربية" |
| `bodyAr.membership.cta` | cta | "انضم الآن" |
| `bodyAr.membership.href` | رابط اختياري (mailto:, tel:) | "\/contact#membership" |
| `bodyAr.contact.title` | العنوان | "تواصل معنا" |
| `bodyAr.contact.addressTitle` | addressTitle | "العنوان" |
| `bodyEn.membership.title` | العنوان | "Join the Institute's Membership" |
| `bodyEn.membership.subtitle` | subtitle | "Let's work together to build a promising futur… |
| `bodyEn.membership.cta` | cta | "Join Now" |
| `bodyEn.membership.href` | رابط اختياري (mailto:, tel:) | "\/contact#membership" |
| `bodyEn.contact.title` | العنوان | "Contact Us" |
| `bodyEn.contact.addressTitle` | addressTitle | "Address" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 32: Membership block → `membershipContact.membership`.

---

#### PUT `/api/admin/contact-info`

**الاسم | Name:** 33 — تواصل معنا (هاتف/عنوان) — contact-info

**الغرض | Purpose:** استبدال/تحديث بيانات كاملة.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تواصل معنا" |
| `titleEn` | العنوان بالإنجليزية | "Contact Us" |
| `subtitleAr` | العنوان الفرعي بالعربية | "" |
| `subtitleEn` | العنوان الفرعي بالإنجليزية | "" |
| `addressLabelAr` | تسمية العنوان بالعربية | "العنوان" |
| `addressLabelEn` | تسمية العنوان بالإنجليزية | "Address" |
| `addressAr` | العنوان الكامل بالعربية | "شارع عبدالله بن حذافة السهمي، الحي الدبلوماسي … |
| `addressEn` | العنوان الكامل بالإنجليزية | "Abdullah bin Hudhafa Al-Sahmi Street, Diplomat… |
| `mapTitleAr` | عنوان الخريطة بالعربية | "موقع المعهد العربي لإنماء المدن" |
| `mapTitleEn` | عنوان الخريطة بالإنجليزية | "Arab Urban Development Institute location" |
| `mapEmbedUrlAr` | رابط تضمين خريطة Google (عربي) | "https:\/\/maps.google.com\/maps?q=Arab+Urban+D… |
| `mapEmbedUrlEn` | رابط تضمين خريطة Google (إنجليزي) | "https:\/\/maps.google.com\/maps?q=Arab+Urban+D… |
| `itemsAr` | عناصر التواصل بالعربية [{label, value, type, href}] | [{"label":"الهاتف","value":"+966 114802555","ty… |
| `itemsEn` | عناصر التواصل بالإنجليزية | [{"label":"Phone","value":"+966 114802555","typ… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 33: Phone, fax, email, address, map → `membershipContact.contact`. Verify: `GET /api/v1/home`.

---

#### 01 — مرجع CRUD — 01 — CRUD Reference

Generic List / Create / Show / Update / Delete. For the full homepage workflow use **00 — بناء الصفحة الرئيسية** only.

#### شرائح الهيرو — Hero Slides

#### GET `/api/admin/hero-slides`

**الاسم | Name:** عرض القائمة — List Hero Slide

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/hero-slides`

**الاسم | Name:** إنشاء — Create شريحة الهيرو

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تطوير تقني" |
| `titleEn` | العنوان بالإنجليزية | "Technical Development" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/hero-slides/{{id}}`

**الاسم | Name:** عرض — Show شريحة الهيرو

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.Use `id` from create response.

---

#### PUT `/api/admin/hero-slides/{{id}}`

**الاسم | Name:** تحديث — Update شريحة الهيرو

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تطوير تقني" |
| `titleEn` | العنوان بالإنجليزية | "Technical Development" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/slider\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/hero-slides/{{id}}`

**الاسم | Name:** حذف — Delete شريحة الهيرو

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage hero slider. Full seed: **00 — بناء الصفحة الرئيسية** steps 01–04.

---

#### POST `/api/admin/hero-slides/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder شريحة الهيرو

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### إحصائيات الرئيسية — Home Stats

#### GET `/api/admin/home-stats`

**الاسم | Name:** عرض القائمة — List Home Stat

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/home-stats`

**الاسم | Name:** إنشاء — Create إحصائية

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+25" |
| `labelAr` | تسمية الإحصائية بالعربية | "اتفاقية" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Agreements" |
| `descriptionAr` | الوصف بالعربية | "الاتفاقيات" |
| `descriptionEn` | الوصف بالإنجليزية | "Partnership agreements" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/home-stats/{{id}}`

**الاسم | Name:** عرض — Show إحصائية

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.Use `id` from create response.

---

#### PUT `/api/admin/home-stats/{{id}}`

**الاسم | Name:** تحديث — Update إحصائية

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `value` | القيمة الرقمية أو النصية (+25) | "+25" |
| `labelAr` | تسمية الإحصائية بالعربية | "اتفاقية" |
| `labelEn` | تسمية الإحصائية بالإنجليزية | "Agreements" |
| `descriptionAr` | الوصف بالعربية | "الاتفاقيات" |
| `descriptionEn` | الوصف بالإنجليزية | "Partnership agreements" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/home-stats/{{id}}`

**الاسم | Name:** حذف — Delete إحصائية

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

«المعهد في أرقام» counters. Title/subtitle: `home_stats` about-content (build step 06). Full seed: steps 07–10.

---

#### POST `/api/admin/home-stats/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder إحصائية

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### محتوى أقسام الرئيسية — Home About Content

#### GET `/api/admin/about-content`

**الاسم | Name:** عرض القائمة — List About Section

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** إنشاء — Create قسم محتوى

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_about_intro" |
| `titleAr` | العنوان بالعربية | "المعهد العربي لإنماء المدن" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Institute" |
| `bodyAr.description` | description | "تأسس المعهد العربي لإنماء المدن عام 1980، ومقر… |
| `bodyAr.cta` | cta | "المزيد" |
| `bodyAr.mission.title` | العنوان | "رسالتنا" |
| `bodyAr.mission.description` | description | "مؤسسة عالمية رائدة تسهم في خلق مستقبل عمراني أ… |
| `bodyAr.mission.readMore` | readMore | "قراءة المزيد" |
| `bodyAr.vision.title` | العنوان | "رؤيتنا" |
| `bodyAr.vision.description` | description | "دعم المدن والبلديات العربية لمواجهة تحديات الت… |
| `bodyAr.vision.readMore` | readMore | "قراءة المزيد" |
| `bodyEn.description` | description | "Founded in 1980 and headquartered in Riyadh, t… |
| `bodyEn.cta` | cta | "Learn More" |
| `bodyEn.mission.title` | العنوان | "Our Mission" |
| `bodyEn.mission.description` | description | "A leading global institution contributing to a… |
| `bodyEn.mission.readMore` | readMore | "Read More" |
| `bodyEn.vision.title` | العنوان | "Our Vision" |
| `bodyEn.vision.description` | description | "Supporting Arab cities and municipalities in f… |
| `bodyEn.vision.readMore` | readMore | "Read More" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/about-content/{{id}}`

**الاسم | Name:** عرض — Show قسم محتوى

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.Use `id` from create response.

---

#### PUT `/api/admin/about-content/{{id}}`

**الاسم | Name:** تحديث — Update قسم محتوى

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "home_about_intro" |
| `titleAr` | العنوان بالعربية | "المعهد العربي لإنماء المدن" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Institute" |
| `bodyAr.description` | description | "تأسس المعهد العربي لإنماء المدن عام 1980، ومقر… |
| `bodyAr.cta` | cta | "المزيد" |
| `bodyAr.mission.title` | العنوان | "رسالتنا" |
| `bodyAr.mission.description` | description | "مؤسسة عالمية رائدة تسهم في خلق مستقبل عمراني أ… |
| `bodyAr.mission.readMore` | readMore | "قراءة المزيد" |
| `bodyAr.vision.title` | العنوان | "رؤيتنا" |
| `bodyAr.vision.description` | description | "دعم المدن والبلديات العربية لمواجهة تحديات الت… |
| `bodyAr.vision.readMore` | readMore | "قراءة المزيد" |
| `bodyEn.description` | description | "Founded in 1980 and headquartered in Riyadh, t… |
| `bodyEn.cta` | cta | "Learn More" |
| `bodyEn.mission.title` | العنوان | "Our Mission" |
| `bodyEn.mission.description` | description | "A leading global institution contributing to a… |
| `bodyEn.mission.readMore` | readMore | "Read More" |
| `bodyEn.vision.title` | العنوان | "Our Vision" |
| `bodyEn.vision.description` | description | "Supporting Arab cities and municipalities in f… |
| `bodyEn.vision.readMore` | readMore | "Read More" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/about-content/{{id}}`

**الاسم | Name:** حذف — Delete قسم محتوى

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage section labels. Keys: `home_about_intro`, `home_stats`, `home_member_cities`, `home_programs`, `home_media_center`, `home_knowledge_center`, `home_membership_contact` — all in **00 — بناء الصفحة الرئيسية**.

---

#### تصنيفات مركز المعرفة — Home Knowledge Categories

#### GET `/api/admin/knowledge-categories`

**الاسم | Name:** عرض القائمة — List Knowledge Category

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/knowledge-categories`

**الاسم | Name:** إنشاء — Create تصنيف مركز المعرفة

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "knowledge-center" |
| `titleAr` | العنوان بالعربية | "مركز المعرفة" |
| `titleEn` | العنوان بالإنجليزية | "Knowledge Center" |
| `descriptionAr` | الوصف بالعربية | "منصة تجمع كل إصدارات المعهد الرقمية والصوتية، … |
| `descriptionEn` | الوصف بالإنجليزية | "A platform bringing together all of the Instit… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/knowledge-categories/{{id}}`

**الاسم | Name:** عرض — Show تصنيف مركز المعرفة

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.Use `id` from create response.

---

#### PUT `/api/admin/knowledge-categories/{{id}}`

**الاسم | Name:** تحديث — Update تصنيف مركز المعرفة

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "knowledge-center" |
| `titleAr` | العنوان بالعربية | "مركز المعرفة" |
| `titleEn` | العنوان بالإنجليزية | "Knowledge Center" |
| `descriptionAr` | الوصف بالعربية | "منصة تجمع كل إصدارات المعهد الرقمية والصوتية، … |
| `descriptionEn` | الوصف بالإنجليزية | "A platform bringing together all of the Instit… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/knowledge-categories/{{id}}`

**الاسم | Name:** حذف — Delete تصنيف مركز المعرفة

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Carousel tabs → `knowledgeCenter.categories[]`. Full seed: build steps 26–28. Resource cards: **المصادر** CRUD with `knowledgeCategoryId`.

---

#### POST `/api/admin/knowledge-categories/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder تصنيف مركز المعرفة

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### من نحن — About

<a id="about"></a>
**الغرض | Purpose:** صفحات عن المعهد: المحتوى، القيادة، المجلس، الفريق، الشركاء. / About pages: content, leadership, advisory board, team, partners.
**الواجهة العامة | Public API:** `GET /api/v1/about/*`

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### محتوى أقسام من نحن — About Content

#### GET `/api/admin/about-content`

**الاسم | Name:** عرض القائمة — List Institute Section

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** إنشاء — Create قسم عن المعهد

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "institute" |
| `titleAr` | العنوان بالعربية | "المعهد العربي لإنماء المدن" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Institute" |
| `bodyAr.paragraphs` | paragraphs | ["تأسس المعهد العربي لإنماء المدن بهدف تعزيز ال… |
| `bodyAr.headquartersTitle` | headquartersTitle | "المقر الرئيسي" |
| `bodyEn.paragraphs` | paragraphs | ["The Arab Urban Development Institute was esta… |
| `bodyEn.headquartersTitle` | headquartersTitle | "Headquarters" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/about-content/{{id}}`

**الاسم | Name:** عرض — Show قسم عن المعهد

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/about-content/{{id}}`

**الاسم | Name:** تحديث — Update قسم عن المعهد

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "institute" |
| `titleAr` | العنوان بالعربية | "المعهد العربي لإنماء المدن" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Institute" |
| `bodyAr.paragraphs` | paragraphs | ["تأسس المعهد العربي لإنماء المدن بهدف تعزيز ال… |
| `bodyAr.headquartersTitle` | headquartersTitle | "المقر الرئيسي" |
| `bodyEn.paragraphs` | paragraphs | ["The Arab Urban Development Institute was esta… |
| `bodyEn.headquartersTitle` | headquartersTitle | "Headquarters" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/about-content/{{id}}`

**الاسم | Name:** حذف — Delete قسم عن المعهد

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/institute` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/about-content`

**الاسم | Name:** إنشاء vision_mission — Create vision_mission

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "vision_mission" |
| `bodyAr.visionTitle` | visionTitle | "رؤيتنا" |
| `bodyAr.visionText` | visionText | "مدن عربية مزدهرة ومستدامة." |
| `bodyAr.missionTitle` | missionTitle | "رسالتنا" |
| `bodyAr.missionText` | missionText | "تعزيز قدرات المدن العربية." |
| `bodyAr.readMore` | readMore | "اقرأ المزيد" |
| `bodyAr.visionImage` | visionImage | "\/vision-mission\/1.png" |
| `bodyAr.missionImage` | missionImage | "\/vision-mission\/2.png" |
| `bodyEn.visionTitle` | visionTitle | "Our Vision" |
| `bodyEn.visionText` | visionText | "Thriving and sustainable Arab cities." |
| `bodyEn.missionTitle` | missionTitle | "Our Mission" |
| `bodyEn.missionText` | missionText | "Enhancing Arab cities capacity." |
| `bodyEn.readMore` | readMore | "Read more" |
| `bodyEn.visionImage` | visionImage | "\/vision-mission\/1.png" |
| `bodyEn.missionImage` | missionImage | "\/vision-mission\/2.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/vision-mission` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/about-content`

**الاسم | Name:** إنشاء structure — Create structure

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "structure" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/operational-structure.png" |
| `bodyAr.imageAlt` | imageAlt | "الهيكل التشغيلي للمعهد" |
| `bodyEn.imageAlt` | imageAlt | "Institute operational structure" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/structure` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### القيادة — Leadership

#### GET `/api/admin/leadership`

**الاسم | Name:** عرض القائمة — List Leadership Message

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/leadership`

**الاسم | Name:** إنشاء — Create رسالة قيادة

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `type` | النوع (director|president، publications|cities|organizations، …) | "director" |
| `honorificAr` | اللقب/التحية بالعربية | "سعادة" |
| `honorificEn` | اللقب/التحية بالإنجليزية | "His Excellency" |
| `nameAr` | الاسم بالعربية | "د. أنس المغيري" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Anas AlMugairi" |
| `positionAr` | المنصب بالعربية | "المدير العام" |
| `positionEn` | المنصب بالإنجليزية | "Director General" |
| `quoteAr` | اقتباس/كلمة بالعربية | "نعمل على بناء مدن عربية أكثر مرونة واستدامة." |
| `quoteEn` | اقتباس/كلمة بالإنجليزية | "We work to build more resilient and sustainabl… |
| `paragraphsAr` | فقرات النص بالعربية (مصفوفة) | ["يسعدني أن أرحب بكم في موقع المعهد العربي لإنم… |
| `paragraphsEn` | فقرات النص بالإنجليزية (مصفوفة) | ["I am pleased to welcome you to the Arab Urban… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/2.png" |
| `imageAltAr` | النص البديل للصورة بالعربية | "صورة المدير العام" |
| `imageAltEn` | النص البديل للصورة بالإنجليزية | "Director General photo" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/leadership/{{id}}`

**الاسم | Name:** عرض — Show رسالة قيادة

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/leadership/{{id}}`

**الاسم | Name:** تحديث — Update رسالة قيادة

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `type` | النوع (director|president، publications|cities|organizations، …) | "director" |
| `honorificAr` | اللقب/التحية بالعربية | "سعادة" |
| `honorificEn` | اللقب/التحية بالإنجليزية | "His Excellency" |
| `nameAr` | الاسم بالعربية | "د. أنس المغيري" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Anas AlMugairi" |
| `positionAr` | المنصب بالعربية | "المدير العام" |
| `positionEn` | المنصب بالإنجليزية | "Director General" |
| `quoteAr` | اقتباس/كلمة بالعربية | "نعمل على بناء مدن عربية أكثر مرونة واستدامة." |
| `quoteEn` | اقتباس/كلمة بالإنجليزية | "We work to build more resilient and sustainabl… |
| `paragraphsAr` | فقرات النص بالعربية (مصفوفة) | ["يسعدني أن أرحب بكم في موقع المعهد العربي لإنم… |
| `paragraphsEn` | فقرات النص بالإنجليزية (مصفوفة) | ["I am pleased to welcome you to the Arab Urban… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/2.png" |
| `imageAltAr` | النص البديل للصورة بالعربية | "صورة المدير العام" |
| `imageAltEn` | النص البديل للصورة بالإنجليزية | "Director General photo" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/leadership/{{id}}`

**الاسم | Name:** حذف — Delete رسالة قيادة

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/leadership/director` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### المجلس الاستشاري — Advisory Board

#### GET `/api/admin/advisory-board`

**الاسم | Name:** عرض القائمة — List Advisory Member

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/advisory-board`

**الاسم | Name:** إنشاء — Create عضو استشاري

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. فيصل بن عبدالعزيز آل سعود" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Faisal bin Abdulaziz Al Saud" |
| `roleAr` | المسمى الوظيفي بالعربية | "رئيس المجلس الاستشاري" |
| `roleEn` | المسمى الوظيفي بالإنجليزية | "Advisory Board Chair" |
| `bioAr` | السيرة/نبذة بالعربية | "خبير في التخطيط الحضري والتنمية المستدامة بخبر… |
| `bioEn` | السيرة/نبذة بالإنجليزية | "Expert in urban planning and sustainable devel… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/1.png" |
| `isFeatured` | مميز في الواجهة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/advisory-board/{{id}}`

**الاسم | Name:** عرض — Show عضو استشاري

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/advisory-board/{{id}}`

**الاسم | Name:** تحديث — Update عضو استشاري

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. فيصل بن عبدالعزيز آل سعود" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Faisal bin Abdulaziz Al Saud" |
| `roleAr` | المسمى الوظيفي بالعربية | "رئيس المجلس الاستشاري" |
| `roleEn` | المسمى الوظيفي بالإنجليزية | "Advisory Board Chair" |
| `bioAr` | السيرة/نبذة بالعربية | "خبير في التخطيط الحضري والتنمية المستدامة بخبر… |
| `bioEn` | السيرة/نبذة بالإنجليزية | "Expert in urban planning and sustainable devel… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/1.png" |
| `isFeatured` | مميز في الواجهة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/advisory-board/{{id}}`

**الاسم | Name:** حذف — Delete عضو استشاري

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/advisory-board` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/advisory-board/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder عضو استشاري

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### أقسام الفريق — Team Sections

#### GET `/api/admin/team-sections`

**الاسم | Name:** عرض القائمة — List Team Section

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/team-sections`

**الاسم | Name:** إنشاء — Create قسم فريق

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "management" |
| `titleAr` | العنوان بالعربية | "الإدارة التنفيذية" |
| `titleEn` | العنوان بالإنجليزية | "Executive Management" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/team-sections/{{id}}`

**الاسم | Name:** عرض — Show قسم فريق

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/team-sections/{{id}}`

**الاسم | Name:** تحديث — Update قسم فريق

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "management" |
| `titleAr` | العنوان بالعربية | "الإدارة التنفيذية" |
| `titleEn` | العنوان بالإنجليزية | "Executive Management" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/team-sections/{{id}}`

**الاسم | Name:** حذف — Delete قسم فريق

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/team-sections/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder قسم فريق

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### أعضاء الفريق — Team Members

#### GET `/api/admin/team-members`

**الاسم | Name:** عرض القائمة — List Team Member

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/team-members`

**الاسم | Name:** إنشاء — Create عضو فريق

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `teamSectionId` | معرّف قسم الفريق (FK) | 1 |
| `nameAr` | الاسم بالعربية | "د. أنس المغيري" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Anas AlMugairi" |
| `roleAr` | المسمى الوظيفي بالعربية | "المدير العام" |
| `roleEn` | المسمى الوظيفي بالإنجليزية | "Director General" |
| `bioAr` | السيرة/نبذة بالعربية | "يقود المعهد في تنفيذ استراتيجيته ورؤيته للتنمي… |
| `bioEn` | السيرة/نبذة بالإنجليزية | "Leads the institute in implementing its strate… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/3.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/team-members/{{id}}`

**الاسم | Name:** عرض — Show عضو فريق

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/team-members/{{id}}`

**الاسم | Name:** تحديث — Update عضو فريق

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `teamSectionId` | معرّف قسم الفريق (FK) | 1 |
| `nameAr` | الاسم بالعربية | "د. أنس المغيري" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Anas AlMugairi" |
| `roleAr` | المسمى الوظيفي بالعربية | "المدير العام" |
| `roleEn` | المسمى الوظيفي بالإنجليزية | "Director General" |
| `bioAr` | السيرة/نبذة بالعربية | "يقود المعهد في تنفيذ استراتيجيته ورؤيته للتنمي… |
| `bioEn` | السيرة/نبذة بالإنجليزية | "Leads the institute in implementing its strate… |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/3.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/team-members/{{id}}`

**الاسم | Name:** حذف — Delete عضو فريق

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/team` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/team-members/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder عضو فريق

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### تصنيفات الشركاء — Partner Categories

#### GET `/api/admin/partner-categories`

**الاسم | Name:** عرض القائمة — List Partner Category

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/partner-categories`

**الاسم | Name:** إنشاء — Create تصنيف شريك

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "international" |
| `titleAr` | العنوان بالعربية | "المؤسسات الدولية" |
| `titleEn` | العنوان بالإنجليزية | "International Organizations" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/partner-categories/{{id}}`

**الاسم | Name:** عرض — Show تصنيف شريك

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/partner-categories/{{id}}`

**الاسم | Name:** تحديث — Update تصنيف شريك

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "international" |
| `titleAr` | العنوان بالعربية | "المؤسسات الدولية" |
| `titleEn` | العنوان بالإنجليزية | "International Organizations" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/partner-categories/{{id}}`

**الاسم | Name:** حذف — Delete تصنيف شريك

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/partner-categories/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder تصنيف شريك

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### الشركاء — Partners

#### GET `/api/admin/partners`

**الاسم | Name:** عرض القائمة — List Partner

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/partners`

**الاسم | Name:** إنشاء — Create شريك

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `partnerCategoryId` | معرّف تصنيف الشريك (FK) | 1 |
| `nameAr` | الاسم بالعربية | "برنامج الأمم المتحدة للمستوطنات البشرية (UN-Ha… |
| `nameEn` | الاسم بالإنجليزية | "UN-Habitat" |
| `logoUrl` | رابط شعار الشريك (/client/…) | "\/client\/un-habitat.png" |
| `isFeatured` | مميز في الواجهة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/partners/{{id}}`

**الاسم | Name:** عرض — Show شريك

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/partners/{{id}}`

**الاسم | Name:** تحديث — Update شريك

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `partnerCategoryId` | معرّف تصنيف الشريك (FK) | 1 |
| `nameAr` | الاسم بالعربية | "برنامج الأمم المتحدة للمستوطنات البشرية (UN-Ha… |
| `nameEn` | الاسم بالإنجليزية | "UN-Habitat" |
| `logoUrl` | رابط شعار الشريك (/client/…) | "\/client\/un-habitat.png" |
| `isFeatured` | مميز في الواجهة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/partners/{{id}}`

**الاسم | Name:** حذف — Delete شريك

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/about/partners` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/partners/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder شريك

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### الاستراتيجية — Strategy

<a id="strategy"></a>
**الغرض | Purpose:** استراتيجية المعهد، المحاور، المخطط، ومجالات التركيز. / Strategy page, pillars, diagram, and focus areas.
**الواجهة العامة | Public API:** `GET /api/v1/strategy/*`

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### صفحة الاستراتيجية — Strategy Page

#### GET `/api/admin/strategy`

**الاسم | Name:** عرض — Get Strategy Page

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### PUT `/api/admin/strategy`

**الاسم | Name:** تحديث — Update Strategy Page

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `introTitleAr` | عنوان مقدمة الاستراتيجية بالعربية | "استراتيجية المعهد 2025-2026" |
| `introTitleEn` | عنوان مقدمة الاستراتيجية بالإنجليزية | "Institute Strategy 2025-2026" |
| `introSubtitleAr` | العنوان الفرعي للاستراتيجية بالعربية | "خارطة طريق للتنمية الحضرية العربية" |
| `introSubtitleEn` | العنوان الفرعي للاستراتيجية بالإنجليزية | "A roadmap for Arab urban development" |
| `bookletTitleAr` | عنوان الكتيب بالعربية | "الكتيب الاستراتيجي" |
| `bookletTitleEn` | عنوان الكتيب بالإنجليزية | "Strategy Booklet" |
| `bookletPdfUrl` | رابط PDF الكتيب الاستراتيجي | "\/storage\/strategy\/AUDI-Strategy.pdf" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Full bilingual strategy page update.

---

#### محاور الاستراتيجية — Strategy Pillars

#### GET `/api/admin/strategy-pillars`

**الاسم | Name:** عرض القائمة — List Strategy Pillar

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/strategy-pillars`

**الاسم | Name:** إنشاء — Create محور استراتيجي

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `textAr` | النص بالعربية | "تعزيز قدرات المؤسسات المحلية" |
| `textEn` | النص بالإنجليزية | "Enhancing local institutions capacity" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/strategy-pillars/{{id}}`

**الاسم | Name:** عرض — Show محور استراتيجي

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/strategy-pillars/{{id}}`

**الاسم | Name:** تحديث — Update محور استراتيجي

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `textAr` | النص بالعربية | "تعزيز قدرات المؤسسات المحلية" |
| `textEn` | النص بالإنجليزية | "Enhancing local institutions capacity" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/strategy-pillars/{{id}}`

**الاسم | Name:** حذف — Delete محور استراتيجي

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/strategy-pillars/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder محور استراتيجي

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### مخطط الاستراتيجية — Strategy Diagram

#### GET `/api/admin/strategy-diagram`

**الاسم | Name:** عرض القائمة — List Diagram Item

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/strategy-diagram`

**الاسم | Name:** إنشاء — Create عنصر مخطط

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `itemKey` | مفتاح عنصر المخطط (vision, mission, …) | "vision" |
| `titleAr` | العنوان بالعربية | "الرؤية" |
| `titleEn` | العنوان بالإنجليزية | "Vision" |
| `contentAr` | المحتوى النصي بالعربية | "مدن عربية مزدهرة ومستدامة." |
| `contentEn` | المحتوى النصي بالإنجليزية | "Thriving and sustainable Arab cities." |
| `columnsAr` | أعمدة المخطط بالعربية (مصفوفة أو null) | null |
| `columnsEn` | أعمدة المخطط بالإنجليزية | null |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/strategy-diagram/{{id}}`

**الاسم | Name:** عرض — Show عنصر مخطط

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/strategy-diagram/{{id}}`

**الاسم | Name:** تحديث — Update عنصر مخطط

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `itemKey` | مفتاح عنصر المخطط (vision, mission, …) | "vision" |
| `titleAr` | العنوان بالعربية | "الرؤية" |
| `titleEn` | العنوان بالإنجليزية | "Vision" |
| `contentAr` | المحتوى النصي بالعربية | "مدن عربية مزدهرة ومستدامة." |
| `contentEn` | المحتوى النصي بالإنجليزية | "Thriving and sustainable Arab cities." |
| `columnsAr` | أعمدة المخطط بالعربية (مصفوفة أو null) | null |
| `columnsEn` | أعمدة المخطط بالإنجليزية | null |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/strategy-diagram/{{id}}`

**الاسم | Name:** حذف — Delete عنصر مخطط

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/strategy-2025` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/strategy-diagram/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder عنصر مخطط

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### مجالات التركيز — Focus Areas

#### GET `/api/admin/focus-areas`

**الاسم | Name:** عرض القائمة — List Focus Area

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/focus-areas`

**الاسم | Name:** إنشاء — Create مجال تركيز

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-resilience" |
| `number` | الرقم الترتيبي (01, 02, …) | "02" |
| `titleAr` | العنوان بالعربية | "المرونة الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Resilience" |
| `highlightAr` | التمييز/الشارة بالعربية | "تخضير المدن" |
| `highlightEn` | التمييز/الشارة بالإنجليزية | "Green Cities" |
| `tagsAr` | الوسوم بالعربية (مصفوفة) | ["تخضير المدن","الاستدامة"] |
| `tagsEn` | الوسوم بالإنجليزية (مصفوفة) | ["Green Cities","Sustainability"] |
| `descriptionAr` | الوصف بالعربية | "يعزز هذا المحور قدرة المدن العربية على مواجهة … |
| `descriptionEn` | الوصف بالإنجليزية | "This focus area enhances Arab cities capacity … |
| `listImageUrl` | صورة القائمة (/focus-areas/…-list.png) | "\/focus-areas\/urban-resilience-list.png" |
| `detailImageUrl` | صورة التفاصيل (/focus-areas/…-detail.png) | "\/focus-areas\/urban-resilience-detail.png" |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/focus-areas/{{id}}`

**الاسم | Name:** عرض — Show مجال تركيز

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/focus-areas/{{id}}`

**الاسم | Name:** تحديث — Update مجال تركيز

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-resilience" |
| `number` | الرقم الترتيبي (01, 02, …) | "02" |
| `titleAr` | العنوان بالعربية | "المرونة الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Resilience" |
| `highlightAr` | التمييز/الشارة بالعربية | "تخضير المدن" |
| `highlightEn` | التمييز/الشارة بالإنجليزية | "Green Cities" |
| `tagsAr` | الوسوم بالعربية (مصفوفة) | ["تخضير المدن","الاستدامة"] |
| `tagsEn` | الوسوم بالإنجليزية (مصفوفة) | ["Green Cities","Sustainability"] |
| `descriptionAr` | الوصف بالعربية | "يعزز هذا المحور قدرة المدن العربية على مواجهة … |
| `descriptionEn` | الوصف بالإنجليزية | "This focus area enhances Arab cities capacity … |
| `listImageUrl` | صورة القائمة (/focus-areas/…-list.png) | "\/focus-areas\/urban-resilience-list.png" |
| `detailImageUrl` | صورة التفاصيل (/focus-areas/…-detail.png) | "\/focus-areas\/urban-resilience-detail.png" |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/focus-areas/{{id}}`

**الاسم | Name:** حذف — Delete مجال تركيز

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/focus-areas/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder مجال تركيز

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### POST `/api/admin/about-content`

**الاسم | Name:** إنشاء focus_areas_pages — Create focus_areas_pages

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `sectionKey` | مفتاح القسم (مثل: home_about_intro, institute) | "focus_areas_pages" |
| `bodyAr.title` | العنوان | "محاور الاستراتيجية" |
| `bodyAr.back` | back | "العودة" |
| `bodyAr.viewMore` | viewMore | "عرض المزيد" |
| `bodyAr.previous` | previous | "السابق" |
| `bodyAr.next` | next | "التالي" |
| `bodyEn.title` | العنوان | "Strategy Focus Areas" |
| `bodyEn.back` | back | "Back" |
| `bodyEn.viewMore` | viewMore | "View more" |
| `bodyEn.previous` | previous | "Previous" |
| `bodyEn.next` | next | "Next" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/strategy/focus-areas` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Page chrome labels for focus areas list/detail.

---

### البرامج — Programs

<a id="programs"></a>
**الغرض | Purpose:** برامج المعهد (3 برامج)، أقسامها، الدورات، الخبراء، ودليل السياسات الحضرية. / Programs, sections, training courses, experts, urban policies directory.
**الواجهة العامة | Public API:** `GET /api/v1/programs/*`

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

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

#### 00 — أدلة البناء — 00 — Build Guides

Step-by-step workflows — each `POST` appears once here. Use collection variables `programId`, `programSectionId`, `programSectionDetailId`.

#### بناء برنامج الشراكات — Build Partnerships Program

Build [الشراكات / partnerships](https://audi-ten.vercel.app/ar/برامجنا/الشراكات):

**Note:** `back` and `sectionsLabel` are **not** admin API — the frontend reads them from `messages/{locale}/programs.json` (i18n).

**FK chain (2 steps per tab):**
1. `POST /api/admin/programs` → saves **`{{programId}}`**
2. Per tab: **`program-sections`** (`titleAr`, `titleEn`, `imageUrl`) → **`program-section-details`** (`intro`, `body`, optional `title`/`image`)

Verify: `GET /api/v1/programs/partnerships`.

#### POST `/api/admin/programs`

**الاسم | Name:** 01 — برنامج الشراكات — partnerships program

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "partnerships" |
| `titleAr` | العنوان بالعربية | "الشراكات" |
| `titleEn` | العنوان بالإنجليزية | "Partnerships" |
| `heroIntroAr` | مقدمة صفحة البرنامج بالعربية | "في ظل المتغيرات التنموية التي تشهدها مدننا الع… |
| `heroIntroEn` | مقدمة صفحة البرنامج بالإنجليزية | "Amid the development shifts facing Arab cities… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "معاً لنصنع مستقبل حضري أفضل: تعرف كيف نبني جسو… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Building strategic partnerships with cities an… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 1: Program category. Saves `programId` collection variable. URL: /ar/برامجنا/الشراكات

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 02 — قسم euroArabDialogue — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "euroArabDialogue" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `titleAr` | العنوان بالعربية | "حوار المدن العربية الأوروبية" |
| `titleEn` | العنوان بالإنجليزية | "Euro-Arab Cities Dialogue" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/euro-arab-dialogue.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 2: **One request** — tab label + image: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 03 — تفاصيل euroArabDialogue — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "منصة حوار موسعة تجمع صناع القرار وقادة المدن ا… |
| `introEn` | المقدمة بالإنجليزية | "An expanded dialogue platform bringing togethe… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 3: Detail page: `introAr/En` (+ optional `titleAr/En`, `imageUrl` if different from section).

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 04 — قسم secretarySpeaks — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "secretarySpeaks" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |
| `titleAr` | العنوان بالعربية | "الأمين يتحدث" |
| `titleEn` | العنوان بالإنجليزية | "The Secretary-General Speaks" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/secretary-speaks.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 4: **One request** — tab label + image: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 05 — تفاصيل secretarySpeaks — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "سلسلة من اللقاءات والحوارات مع الأمين العام لم… |
| `introEn` | المقدمة بالإنجليزية | "A series of meetings and dialogues with the Se… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 5: Detail page: `introAr/En` (+ optional `titleAr/En`, `imageUrl` if different from section).

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 06 — قسم urbanAwards — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "urbanAwards" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |
| `titleAr` | العنوان بالعربية | "جوائز التنمية الحضرية للمدن العربية" |
| `titleEn` | العنوان بالإنجليزية | "Arab Cities Urban Development Awards" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/urban-awards.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 6: **One request** — tab label + image: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 07 — تفاصيل urbanAwards — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "مبادرة تكريمية تهدف إلى تشجيع المدن العربية عل… |
| `introEn` | المقدمة بالإنجليزية | "A recognition initiative encouraging Arab citi… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 7: Detail page: `introAr/En` (+ optional `titleAr/En`, `imageUrl` if different from section).

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 08 — قسم partnersGuide — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "partnersGuide" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |
| `titleAr` | العنوان بالعربية | "دليل شركاء التنمية الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Development Partners Guide" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/partners-guide.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 8: **One request** — tab label + image: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 09 — تفاصيل partnersGuide — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "مرجع شامل يضم شبكة شركاء المعهد من المؤسسات ال… |
| `introEn` | المقدمة بالإنجليزية | "A comprehensive reference featuring the Instit… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 9: Detail page: `introAr/En` (+ optional `titleAr/En`, `imageUrl` if different from section).

---

#### بناء برنامج التدريب — Build Training Program

Build [مركز دعم المدن / training](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن):

**Note:** `back` and `sectionsLabel` are **not** admin API — frontend i18n (`messages/{locale}/programs.json`), same as partnerships.

**Bodies from `messages/{ar,en}/programs.json`.**

01 program → **02–09** four tabs × (section → details) → **10–15** courses → **16–18** experts.

Verify: `GET /api/v1/programs/training`.

#### POST `/api/admin/programs`

**الاسم | Name:** 01 — برنامج التدريب — training program

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "training" |
| `titleAr` | العنوان بالعربية | "التدريب و تطوير القدرات" |
| `titleEn` | العنوان بالإنجليزية | "Training & Capacity Building" |
| `heroIntroAr` | مقدمة صفحة البرنامج بالعربية | "يهدف مركز دعم المدن إلى تلبية احتياجات الأمانا… |
| `heroIntroEn` | مقدمة صفحة البرنامج بالإنجليزية | "The City Support Center aims to meet the needs… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "برامج تدريبية متخصصة لبناء قدرات العاملين في ا… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Specialized training programs to build the cap… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 1: Program category. Saves `programId`. URL: /ar/برامجنا/مركز-دعم-المدن

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 02 — قسم trainingPrograms — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "trainingPrograms" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `titleAr` | العنوان بالعربية | "البرامج التدريبية" |
| `titleEn` | العنوان بالإنجليزية | "Training Programs" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/icons\/program\/6.gif" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 2: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 03 — تفاصيل trainingPrograms — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "هدف مركز دعم المدن لتلبية احتياجات ومتطلبات ال… |
| `introEn` | المقدمة بالإنجليزية | "The City Support Center aims to meet the needs… |
| `bodyAr.formatsTitle` | formatsTitle | "حيث يتم تقديم هذه البرامج التدريبية على شكل:" |
| `bodyAr.formats` | formats | ["دورات تدريبية (حضورية وعن بعد).","ورش عمل متخ… |
| `bodyAr.coursesTitle` | coursesTitle | "البرامج التدريبية ٢٠٢٣ – ٢٠٢٤" |
| `bodyAr.heroImage` | heroImage | "\/icons\/program\/6.gif" |
| `bodyAr.coursesImage` | coursesImage | "\/icons\/program\/7.png" |
| `bodyEn.formatsTitle` | formatsTitle | "These training programs are delivered through:" |
| `bodyEn.formats` | formats | ["Training courses (in-person and remote).","Sp… |
| `bodyEn.coursesTitle` | coursesTitle | "Training Programs 2023 – 2024" |
| `bodyEn.heroImage` | heroImage | "\/icons\/program\/6.gif" |
| `bodyEn.coursesImage` | coursesImage | "\/icons\/program\/7.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 3: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 04 — قسم consulting — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "consulting" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |
| `titleAr` | العنوان بالعربية | "الاستشارات الفنية ونقل الخبرات" |
| `titleEn` | العنوان بالإنجليزية | "Technical Consultations & Knowledge Transfer" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/projects\/p2.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 4: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 05 — تفاصيل consulting — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "يحرص مركز دعم المدن على تقديم الدعم الفني للبل… |
| `introEn` | المقدمة بالإنجليزية | "The City Support Center is committed to provid… |
| `bodyAr.nav` | nav | ["استشارات هندسية وإدارية","مشاركة التجارب بين … |
| `bodyAr.detailImage` | detailImage | "\/projects\/consulting-presenter.png" |
| `bodyAr.sections` | sections | [{"title":"استشارات هندسية وإدارية","descriptio… |
| `bodyEn.nav` | nav | ["Engineering and administrative consultations"… |
| `bodyEn.detailImage` | detailImage | "\/projects\/consulting-presenter.png" |
| `bodyEn.sections` | sections | [{"title":"Engineering and administrative consu… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 5: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 06 — قسم executive — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "executive" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |
| `titleAr` | العنوان بالعربية | "البرنامج التنفيذي" |
| `titleEn` | العنوان بالإنجليزية | "Executive Program" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 6: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 07 — تفاصيل executive — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "برنامج تنفيذي متخصص في التنمية الحضرية ناتج عن… |
| `introEn` | المقدمة بالإنجليزية | "An executive program specialized in urban deve… |
| `bodyAr.offersTitle` | offersTitle | "يقدم البرنامج التنفيذي" |
| `bodyAr.programs` | programs | ["الماجستير التنفيذي في التطوير البلدي","البرنا… |
| `bodyAr.topicsTitle` | topicsTitle | "يقدم البرنامج التنفيذي" |
| `bodyAr.heroVideo` | heroVideo | "\/icons\/program\/executive.mp4" |
| `bodyAr.topics` | topics | [{"title":"التغير المناخي","image":"p1.png"},{"… |
| `bodyEn.offersTitle` | offersTitle | "The executive program offers" |
| `bodyEn.programs` | programs | ["Executive Master's in Municipal Development",… |
| `bodyEn.topicsTitle` | topicsTitle | "The executive program offers" |
| `bodyEn.heroVideo` | heroVideo | "\/icons\/program\/executive.mp4" |
| `bodyEn.topics` | topics | [{"title":"Climate Change","image":"p1.png"},{"… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 7: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 08 — قسم experts — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "experts" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |
| `titleAr` | العنوان بالعربية | "خبراء مركز الدعم" |
| `titleEn` | العنوان بالإنجليزية | "Support Center Experts" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 8: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 09 — تفاصيل experts — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | null |
| `introEn` | المقدمة بالإنجليزية | null |
| `bodyAr.title` | العنوان | "خبراء مركز الدعم" |
| `bodyEn.title` | العنوان | "Support Center Experts" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 9: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 10 — دورة تدريبية — التخطيط والتطوير الحضري

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "التخطيط والتطوير الحضري" |
| `titleEn` | العنوان بالإنجليزية | "Urban Planning and Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 10: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 11 — دورة تدريبية — التخطيط والتطوير الحضري

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "التخطيط والتطوير الحضري" |
| `titleEn` | العنوان بالإنجليزية | "Urban Planning and Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 11: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 12 — دورة تدريبية — تطوير العمل البلدي للقيادات

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تطوير العمل البلدي للقيادات" |
| `titleEn` | العنوان بالإنجليزية | "Municipal Leadership Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 12: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 13 — دورة تدريبية — تطوير العمل البلدي للقيادات

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "تطوير العمل البلدي للقيادات" |
| `titleEn` | العنوان بالإنجليزية | "Municipal Leadership Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 4 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 13: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 14 — دورة تدريبية — الاستثمار في القطاع البلدي

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "الاستثمار في القطاع البلدي" |
| `titleEn` | العنوان بالإنجليزية | "Investment in the Municipal Sector" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 14: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** 15 — دورة تدريبية — الاستثمار في القطاع البلدي

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "الاستثمار في القطاع البلدي" |
| `titleEn` | العنوان بالإنجليزية | "Investment in the Municipal Sector" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 5 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 15: Course row on ?tab=trainingPrograms (البرامج التدريبية ٢٠٢٣–٢٠٢٤ grid). Merged into `sections.trainingPrograms.courses[]`.

---

#### POST `/api/admin/experts`

**الاسم | Name:** 16 — خبير — د. إبراهيم باهر الدين

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. إبراهيم باهر الدين" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Ibrahim Baher El-Din" |
| `specialtyAr` | specialtyAr | "التصميم الحضري والتخطيط التشاركي" |
| `specialtyEn` | specialtyEn | "Urban Design and Participatory Planning" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 16: Expert card on ?tab=experts carousel. Merged into `sections.experts.experts[]`.

---

#### POST `/api/admin/experts`

**الاسم | Name:** 17 — خبير — د. خالد الوزني

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. خالد الوزني" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Khaled Al-Wazni" |
| `specialtyAr` | specialtyAr | "سياسات التنمية الاقتصادية" |
| `specialtyEn` | specialtyEn | "Economic Development Policies" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/2.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 17: Expert card on ?tab=experts carousel. Merged into `sections.experts.experts[]`.

---

#### POST `/api/admin/experts`

**الاسم | Name:** 18 — خبير — توماس ميرفي

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "توماس ميرفي" |
| `nameEn` | الاسم بالإنجليزية | "Thomas Murphy" |
| `specialtyAr` | specialtyAr | "عمدة سابق لمدينة بيتسبرغ" |
| `specialtyEn` | specialtyEn | "Former Mayor of Pittsburgh" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/3.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 18: Expert card on ?tab=experts carousel. Merged into `sections.experts.experts[]`.

---

#### بناء برنامج السياسات الحضرية — Build Urban Policies Program

Build [السياسات الحضرية / urban-policies](https://audi-ten.vercel.app/ar/برامجنا/السياسات-الحضرية):

**FK chain:** 01 program → **02–09** four tabs × (program-sections → program-section-details).

Directory rows (`developmentPortal`): use **01 — مرجع CRUD → دليل المدن/المشاريع/…** after seeding the portal section.

Verify: `GET /api/v1/programs/urban-policies`.

#### POST `/api/admin/programs`

**الاسم | Name:** 01 — برنامج السياسات الحضرية — urban-policies program

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-policies" |
| `titleAr` | العنوان بالعربية | "ابحاث السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policy Research" |
| `heroIntroAr` | مقدمة صفحة البرنامج بالعربية | "يتألف مؤشر التنمية الحضرية في المدن العربية من… |
| `heroIntroEn` | مقدمة صفحة البرنامج بالإنجليزية | "The Urban Development Index in Arab cities con… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "إعداد دراسات وتقارير سياسات حضرية تدعم صناع ال… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Developing urban policy studies and reports th… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 1: Program category. Saves `programId`. URL: /ar/برامجنا/السياسات-الحضرية

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 02 — قسم developmentPortal — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "developmentPortal" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `titleAr` | العنوان بالعربية | "بوابة التنمية الحضرية العربية" |
| `titleEn` | العنوان بالإنجليزية | "Arab Urban Development Portal" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/urban-policies\/1.gif" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 2: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 03 — تفاصيل developmentPortal — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | null |
| `introEn` | المقدمة بالإنجليزية | null |
| `bodyAr.paragraphs` | paragraphs | ["تُعد بوابة التنمية الحضرية العربية منصة لجمع … |
| `bodyAr.contributeTitle` | contributeTitle | "ساهم من خلال تقديم معلومات حول" |
| `bodyAr.contributeDescription` | contributeDescription | "يمكنكم المساهمة في إثراء محتوى البوابة من خلال… |
| `bodyAr.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyAr.ctaTitle` | ctaTitle | "اختر نوع المساهمة" |
| `bodyAr.ctaDisclaimer` | ctaDisclaimer | "لا يعني إدراج أي مشروع أو منظمة في البوابة بال… |
| `bodyAr.contributionTypes` | contributionTypes | [{"id":"publications","label":"المنشورات"},{"id… |
| `bodyAr.contributionForm.openLabel` | openLabel | "قدّم مساهمة عبر الإنترنت" |
| `bodyAr.contributionForm.typeLabel` | typeLabel | "نوع المساهمة" |
| `bodyAr.contributionForm.types.publications` | publications | "المنشورات" |
| `bodyAr.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "المدن" |
| `bodyAr.contributionForm.types.organizations` | organizations | "المنظمات" |
| `bodyAr.contributionForm.emailLabel` | emailLabel | "البريد الإلكتروني" |
| `bodyAr.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyAr.contributionForm.titleLabel` | titleLabel | "العنوان" |
| `bodyAr.contributionForm.titlePlaceholder` | titlePlaceholder | "عنوان المدينة أو المنظمة أو المنشور" |
| `bodyAr.contributionForm.detailsLabel` | detailsLabel | "التفاصيل" |
| `bodyAr.contributionForm.detailsPlaceholder` | detailsPlaceholder | "صف المعلومات التي ترغب في المساهمة بها..." |
| `bodyAr.contributionForm.submit` | submit | "إرسال المساهمة" |
| `bodyAr.contributionForm.success` | success | "تم إرسال مساهمتك بنجاح." |
| `bodyAr.contributionForm.error` | error | "حدث خطأ أثناء الإرسال. يرجى المحاولة مرة أخرى." |
| `bodyAr.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyAr.directory.title` | العنوان | "دليل المدن العربية" |
| `bodyAr.directory.subtitle` | subtitle | "يسعى المعهد إلى جمع وتوثيق البيانات والإحصاءات… |
| `bodyAr.directory.filtersTitle` | filtersTitle | "التصنيف" |
| `bodyAr.directory.countryLabel` | countryLabel | "الدولة" |
| `bodyAr.directory.cityLabel` | cityLabel | "المدينة" |
| `bodyAr.directory.citySizeLabel` | citySizeLabel | "حجم المدينة" |
| `bodyAr.directory.resetLabel` | resetLabel | "إعادة ضبط" |
| `bodyAr.directory.searchLabel` | searchLabel | "بحث" |
| `bodyAr.directory.viewListLabel` | viewListLabel | "القائمة" |
| `bodyAr.directory.viewMapLabel` | viewMapLabel | "الخريطة" |
| `bodyAr.directory.mapPlaceholder` | mapPlaceholder | "عرض الخريطة قيد التطوير" |
| `bodyAr.directory.seeMoreLabel` | seeMoreLabel | "رؤية المزيد" |
| `bodyAr.directory.tabs` | tabs | [{"id":"cities","label":"المدن"},{"id":"project… |
| `bodyAr.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "الرقم" |
| `bodyAr.directory.columns.cities.name` | الاسم | "اسم المدينة" |
| `bodyAr.directory.columns.cities.details` | details | "تفاصيل" |
| `bodyAr.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "رقم المشروع" |
| `bodyAr.directory.columns.projects.city` | المدينة | "المدينة" |
| `bodyAr.directory.columns.projects.country` | country | "الدولة" |
| `bodyAr.directory.columns.projects.startDate` | تاريخ البداية | "التاريخ" |
| `bodyAr.directory.columns.projects.endDate` | تاريخ النهاية | "تاريخ النهاية" |
| `bodyEn.paragraphs` | paragraphs | ["The Arab Urban Development Portal is a platfo… |
| `bodyEn.contributeTitle` | contributeTitle | "Contribute by providing information about" |
| `bodyEn.contributeDescription` | contributeDescription | "You can help enrich the portal by sharing info… |
| `bodyEn.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyEn.ctaTitle` | ctaTitle | "Choose the type of contribution" |
| `bodyEn.ctaDisclaimer` | ctaDisclaimer | "The inclusion of any project or organization i… |
| `bodyEn.contributionTypes` | contributionTypes | [{"id":"publications","label":"Publications"},{… |
| `bodyEn.contributionForm.openLabel` | openLabel | "Submit a contribution online" |
| `bodyEn.contributionForm.typeLabel` | typeLabel | "Contribution type" |
| `bodyEn.contributionForm.types.publications` | publications | "Publications" |
| `bodyEn.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "Cities" |
| `bodyEn.contributionForm.types.organizations` | organizations | "Organizations" |
| `bodyEn.contributionForm.emailLabel` | emailLabel | "Email" |
| `bodyEn.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyEn.contributionForm.titleLabel` | titleLabel | "Title" |
| `bodyEn.contributionForm.titlePlaceholder` | titlePlaceholder | "Title of the city, organization, or publication" |
| `bodyEn.contributionForm.detailsLabel` | detailsLabel | "Details" |
| `bodyEn.contributionForm.detailsPlaceholder` | detailsPlaceholder | "Describe the information you would like to con… |
| `bodyEn.contributionForm.submit` | submit | "Submit Contribution" |
| `bodyEn.contributionForm.success` | success | "Your contribution was submitted successfully." |
| `bodyEn.contributionForm.error` | error | "An error occurred while submitting. Please try… |
| `bodyEn.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyEn.directory.title` | العنوان | "Directory of Arab Cities" |
| `bodyEn.directory.subtitle` | subtitle | "The Institute seeks to collect and document da… |
| `bodyEn.directory.filtersTitle` | filtersTitle | "Classification" |
| `bodyEn.directory.countryLabel` | countryLabel | "Country" |
| `bodyEn.directory.cityLabel` | cityLabel | "City" |
| `bodyEn.directory.citySizeLabel` | citySizeLabel | "City Size" |
| `bodyEn.directory.resetLabel` | resetLabel | "Reset" |
| `bodyEn.directory.searchLabel` | searchLabel | "Search" |
| `bodyEn.directory.viewListLabel` | viewListLabel | "List" |
| `bodyEn.directory.viewMapLabel` | viewMapLabel | "Map" |
| `bodyEn.directory.mapPlaceholder` | mapPlaceholder | "Map view coming soon" |
| `bodyEn.directory.seeMoreLabel` | seeMoreLabel | "See More" |
| `bodyEn.directory.tabs` | tabs | [{"id":"cities","label":"Cities"},{"id":"projec… |
| `bodyEn.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "No." |
| `bodyEn.directory.columns.cities.name` | الاسم | "City Name" |
| `bodyEn.directory.columns.cities.details` | details | "Details" |
| `bodyEn.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "Project No." |
| `bodyEn.directory.columns.projects.city` | المدينة | "City" |
| `bodyEn.directory.columns.projects.country` | country | "Country" |
| `bodyEn.directory.columns.projects.startDate` | تاريخ البداية | "Start Date" |
| `bodyEn.directory.columns.projects.endDate` | تاريخ النهاية | "End Date" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 3: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 04 — قسم developmentIndex — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "developmentIndex" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 1 |
| `titleAr` | العنوان بالعربية | "مؤشر التنمية الحضرية في المدن العربية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Development Index in Arab Cities" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/urban-policies\/2.gif" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 4: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 05 — تفاصيل developmentIndex — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "أداة علمية متخصصة تهدف إلى رصد مسارات وتوجهات … |
| `introEn` | المقدمة بالإنجليزية | "A specialized scientific tool for monitoring u… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 5: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 06 — قسم innovationLab — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "innovationLab" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |
| `titleAr` | العنوان بالعربية | "معمل الابتكار الحضري للمدن العربية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Innovation Lab for Arab Cities" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/urban-policies\/3.gif" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 6: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 07 — تفاصيل innovationLab — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "مساحة تجريبية لتطوير حلول مبتكرة للتحديات الحض… |
| `introEn` | المقدمة بالإنجليزية | "An experimental space for developing innovativ… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 7: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** 08 — قسم practiceReports — program-sections

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "practiceReports" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 3 |
| `titleAr` | العنوان بالعربية | "تقارير للممارسات والسياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Practices and Policies Reports" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/urban-policies\/4.gif" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 8: **One request**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Saves `programSectionId`.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** 09 — تفاصيل practiceReports — program-section-details

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | "سلسلة من التقارير والدراسات التطبيقية التي توث… |
| `introEn` | المقدمة بالإنجليزية | "A series of applied reports and studies docume… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Step 9: Details: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl` if different from section.

---

#### 01 — مرجع CRUD — 01 — CRUD Reference

Generic List / Create / Show / Update / Delete. Full program seed: **00 — أدلة البناء** only.

#### البرامج — Programs CRUD

#### GET `/api/admin/programs`

**الاسم | Name:** عرض القائمة — List Program

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/programs`

**الاسم | Name:** إنشاء — Create برنامج

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-policies" |
| `titleAr` | العنوان بالعربية | "السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policies" |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "إعداد دراسات وتقارير سياسات حضرية تدعم صناع ال… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Developing urban policy studies and reports th… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/programs/{{id}}`

**الاسم | Name:** عرض — Show برنامج

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.Use `id` from create response.

---

#### PUT `/api/admin/programs/{{id}}`

**الاسم | Name:** تحديث — Update برنامج

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "urban-policies" |
| `titleAr` | العنوان بالعربية | "السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policies" |
| `cardDescriptionAr` | وصف بطاقة البرنامج في الرئيسية (عربي) | "إعداد دراسات وتقارير سياسات حضرية تدعم صناع ال… |
| `cardDescriptionEn` | وصف بطاقة البرنامج في الرئيسية (إنجليزي) | "Developing urban policy studies and reports th… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/programs/{{id}}`

**الاسم | Name:** حذف — Delete برنامج

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage cards + program pages. Full seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 15–17, or program build guides.

---

#### أقسام البرنامج — Program Sections

#### GET `/api/admin/program-sections`

**الاسم | Name:** عرض القائمة — List Program Section

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `programId` | معرّف البرنامج (FK) | `{{programId}}` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/program-sections`

**الاسم | Name:** إنشاء — Create قسم برنامج

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "euroArabDialogue" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `titleAr` | العنوان بالعربية | "حوار المدن العربية الأوروبية" |
| `titleEn` | العنوان بالإنجليزية | "Euro-Arab Cities Dialogue" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/euro-arab-dialogue.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/program-sections/{{programSectionId}}`

**الاسم | Name:** عرض — Show قسم برنامج

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.Use `programSectionId` from create response.

---

#### PUT `/api/admin/program-sections/{{programSectionId}}`

**الاسم | Name:** تحديث — Update قسم برنامج

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programId` | معرّف البرنامج (FK) | "{{programId}}" |
| `tabKey` | مفتاح تبويب البرنامج (trainingPrograms, experts, …) | "euroArabDialogue" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |
| `titleAr` | العنوان بالعربية | "حوار المدن العربية الأوروبية" |
| `titleEn` | العنوان بالإنجليزية | "Euro-Arab Cities Dialogue" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/partnerships\/euro-arab-dialogue.png" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/program-sections/{{programSectionId}}`

**الاسم | Name:** حذف — Delete قسم برنامج

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/partnerships` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**One create**: `programId`, `tabKey`, `titleAr`, `titleEn`, `imageUrl`, `sortOrder`. Then `program-section-details`. Full examples: **00 — أدلة البناء**.

---

#### POST `/api/admin/program-sections/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder قسم برنامج

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### تفاصيل أقسام البرنامج — Program Section Details

#### GET `/api/admin/program-section-details`

**الاسم | Name:** عرض القائمة — List Program Section Detail

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `programId` | معرّف البرنامج (FK) | `{{programId}}` |
| `programSectionId` | programSectionId | `{{programSectionId}}` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/program-section-details`

**الاسم | Name:** إنشاء — Create تفاصيل قسم

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | null |
| `introEn` | المقدمة بالإنجليزية | null |
| `bodyAr.paragraphs` | paragraphs | ["تُعد بوابة التنمية الحضرية العربية منصة لجمع … |
| `bodyAr.contributeTitle` | contributeTitle | "ساهم من خلال تقديم معلومات حول" |
| `bodyAr.contributeDescription` | contributeDescription | "يمكنكم المساهمة في إثراء محتوى البوابة من خلال… |
| `bodyAr.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyAr.ctaTitle` | ctaTitle | "اختر نوع المساهمة" |
| `bodyAr.ctaDisclaimer` | ctaDisclaimer | "لا يعني إدراج أي مشروع أو منظمة في البوابة بال… |
| `bodyAr.contributionTypes` | contributionTypes | [{"id":"publications","label":"المنشورات"},{"id… |
| `bodyAr.contributionForm.openLabel` | openLabel | "قدّم مساهمة عبر الإنترنت" |
| `bodyAr.contributionForm.typeLabel` | typeLabel | "نوع المساهمة" |
| `bodyAr.contributionForm.types.publications` | publications | "المنشورات" |
| `bodyAr.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "المدن" |
| `bodyAr.contributionForm.types.organizations` | organizations | "المنظمات" |
| `bodyAr.contributionForm.emailLabel` | emailLabel | "البريد الإلكتروني" |
| `bodyAr.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyAr.contributionForm.titleLabel` | titleLabel | "العنوان" |
| `bodyAr.contributionForm.titlePlaceholder` | titlePlaceholder | "عنوان المدينة أو المنظمة أو المنشور" |
| `bodyAr.contributionForm.detailsLabel` | detailsLabel | "التفاصيل" |
| `bodyAr.contributionForm.detailsPlaceholder` | detailsPlaceholder | "صف المعلومات التي ترغب في المساهمة بها..." |
| `bodyAr.contributionForm.submit` | submit | "إرسال المساهمة" |
| `bodyAr.contributionForm.success` | success | "تم إرسال مساهمتك بنجاح." |
| `bodyAr.contributionForm.error` | error | "حدث خطأ أثناء الإرسال. يرجى المحاولة مرة أخرى." |
| `bodyAr.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyAr.directory.title` | العنوان | "دليل المدن العربية" |
| `bodyAr.directory.subtitle` | subtitle | "يسعى المعهد إلى جمع وتوثيق البيانات والإحصاءات… |
| `bodyAr.directory.filtersTitle` | filtersTitle | "التصنيف" |
| `bodyAr.directory.countryLabel` | countryLabel | "الدولة" |
| `bodyAr.directory.cityLabel` | cityLabel | "المدينة" |
| `bodyAr.directory.citySizeLabel` | citySizeLabel | "حجم المدينة" |
| `bodyAr.directory.resetLabel` | resetLabel | "إعادة ضبط" |
| `bodyAr.directory.searchLabel` | searchLabel | "بحث" |
| `bodyAr.directory.viewListLabel` | viewListLabel | "القائمة" |
| `bodyAr.directory.viewMapLabel` | viewMapLabel | "الخريطة" |
| `bodyAr.directory.mapPlaceholder` | mapPlaceholder | "عرض الخريطة قيد التطوير" |
| `bodyAr.directory.seeMoreLabel` | seeMoreLabel | "رؤية المزيد" |
| `bodyAr.directory.tabs` | tabs | [{"id":"cities","label":"المدن"},{"id":"project… |
| `bodyAr.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "الرقم" |
| `bodyAr.directory.columns.cities.name` | الاسم | "اسم المدينة" |
| `bodyAr.directory.columns.cities.details` | details | "تفاصيل" |
| `bodyAr.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "رقم المشروع" |
| `bodyAr.directory.columns.projects.city` | المدينة | "المدينة" |
| `bodyAr.directory.columns.projects.country` | country | "الدولة" |
| `bodyAr.directory.columns.projects.startDate` | تاريخ البداية | "التاريخ" |
| `bodyAr.directory.columns.projects.endDate` | تاريخ النهاية | "تاريخ النهاية" |
| `bodyEn.paragraphs` | paragraphs | ["The Arab Urban Development Portal is a platfo… |
| `bodyEn.contributeTitle` | contributeTitle | "Contribute by providing information about" |
| `bodyEn.contributeDescription` | contributeDescription | "You can help enrich the portal by sharing info… |
| `bodyEn.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyEn.ctaTitle` | ctaTitle | "Choose the type of contribution" |
| `bodyEn.ctaDisclaimer` | ctaDisclaimer | "The inclusion of any project or organization i… |
| `bodyEn.contributionTypes` | contributionTypes | [{"id":"publications","label":"Publications"},{… |
| `bodyEn.contributionForm.openLabel` | openLabel | "Submit a contribution online" |
| `bodyEn.contributionForm.typeLabel` | typeLabel | "Contribution type" |
| `bodyEn.contributionForm.types.publications` | publications | "Publications" |
| `bodyEn.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "Cities" |
| `bodyEn.contributionForm.types.organizations` | organizations | "Organizations" |
| `bodyEn.contributionForm.emailLabel` | emailLabel | "Email" |
| `bodyEn.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyEn.contributionForm.titleLabel` | titleLabel | "Title" |
| `bodyEn.contributionForm.titlePlaceholder` | titlePlaceholder | "Title of the city, organization, or publication" |
| `bodyEn.contributionForm.detailsLabel` | detailsLabel | "Details" |
| `bodyEn.contributionForm.detailsPlaceholder` | detailsPlaceholder | "Describe the information you would like to con… |
| `bodyEn.contributionForm.submit` | submit | "Submit Contribution" |
| `bodyEn.contributionForm.success` | success | "Your contribution was submitted successfully." |
| `bodyEn.contributionForm.error` | error | "An error occurred while submitting. Please try… |
| `bodyEn.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyEn.directory.title` | العنوان | "Directory of Arab Cities" |
| `bodyEn.directory.subtitle` | subtitle | "The Institute seeks to collect and document da… |
| `bodyEn.directory.filtersTitle` | filtersTitle | "Classification" |
| `bodyEn.directory.countryLabel` | countryLabel | "Country" |
| `bodyEn.directory.cityLabel` | cityLabel | "City" |
| `bodyEn.directory.citySizeLabel` | citySizeLabel | "City Size" |
| `bodyEn.directory.resetLabel` | resetLabel | "Reset" |
| `bodyEn.directory.searchLabel` | searchLabel | "Search" |
| `bodyEn.directory.viewListLabel` | viewListLabel | "List" |
| `bodyEn.directory.viewMapLabel` | viewMapLabel | "Map" |
| `bodyEn.directory.mapPlaceholder` | mapPlaceholder | "Map view coming soon" |
| `bodyEn.directory.seeMoreLabel` | seeMoreLabel | "See More" |
| `bodyEn.directory.tabs` | tabs | [{"id":"cities","label":"Cities"},{"id":"projec… |
| `bodyEn.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "No." |
| `bodyEn.directory.columns.cities.name` | الاسم | "City Name" |
| `bodyEn.directory.columns.cities.details` | details | "Details" |
| `bodyEn.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "Project No." |
| `bodyEn.directory.columns.projects.city` | المدينة | "City" |
| `bodyEn.directory.columns.projects.country` | country | "Country" |
| `bodyEn.directory.columns.projects.startDate` | تاريخ البداية | "Start Date" |
| `bodyEn.directory.columns.projects.endDate` | تاريخ النهاية | "End Date" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/program-section-details/{{programSectionDetailId}}`

**الاسم | Name:** عرض — Show تفاصيل قسم

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.Use `programSectionDetailId` from create response.

---

#### PUT `/api/admin/program-section-details/{{programSectionDetailId}}`

**الاسم | Name:** تحديث — Update تفاصيل قسم

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `programSectionId` | programSectionId | "{{programSectionId}}" |
| `introAr` | المقدمة بالعربية | null |
| `introEn` | المقدمة بالإنجليزية | null |
| `bodyAr.paragraphs` | paragraphs | ["تُعد بوابة التنمية الحضرية العربية منصة لجمع … |
| `bodyAr.contributeTitle` | contributeTitle | "ساهم من خلال تقديم معلومات حول" |
| `bodyAr.contributeDescription` | contributeDescription | "يمكنكم المساهمة في إثراء محتوى البوابة من خلال… |
| `bodyAr.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyAr.ctaTitle` | ctaTitle | "اختر نوع المساهمة" |
| `bodyAr.ctaDisclaimer` | ctaDisclaimer | "لا يعني إدراج أي مشروع أو منظمة في البوابة بال… |
| `bodyAr.contributionTypes` | contributionTypes | [{"id":"publications","label":"المنشورات"},{"id… |
| `bodyAr.contributionForm.openLabel` | openLabel | "قدّم مساهمة عبر الإنترنت" |
| `bodyAr.contributionForm.typeLabel` | typeLabel | "نوع المساهمة" |
| `bodyAr.contributionForm.types.publications` | publications | "المنشورات" |
| `bodyAr.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "المدن" |
| `bodyAr.contributionForm.types.organizations` | organizations | "المنظمات" |
| `bodyAr.contributionForm.emailLabel` | emailLabel | "البريد الإلكتروني" |
| `bodyAr.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyAr.contributionForm.titleLabel` | titleLabel | "العنوان" |
| `bodyAr.contributionForm.titlePlaceholder` | titlePlaceholder | "عنوان المدينة أو المنظمة أو المنشور" |
| `bodyAr.contributionForm.detailsLabel` | detailsLabel | "التفاصيل" |
| `bodyAr.contributionForm.detailsPlaceholder` | detailsPlaceholder | "صف المعلومات التي ترغب في المساهمة بها..." |
| `bodyAr.contributionForm.submit` | submit | "إرسال المساهمة" |
| `bodyAr.contributionForm.success` | success | "تم إرسال مساهمتك بنجاح." |
| `bodyAr.contributionForm.error` | error | "حدث خطأ أثناء الإرسال. يرجى المحاولة مرة أخرى." |
| `bodyAr.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyAr.directory.title` | العنوان | "دليل المدن العربية" |
| `bodyAr.directory.subtitle` | subtitle | "يسعى المعهد إلى جمع وتوثيق البيانات والإحصاءات… |
| `bodyAr.directory.filtersTitle` | filtersTitle | "التصنيف" |
| `bodyAr.directory.countryLabel` | countryLabel | "الدولة" |
| `bodyAr.directory.cityLabel` | cityLabel | "المدينة" |
| `bodyAr.directory.citySizeLabel` | citySizeLabel | "حجم المدينة" |
| `bodyAr.directory.resetLabel` | resetLabel | "إعادة ضبط" |
| `bodyAr.directory.searchLabel` | searchLabel | "بحث" |
| `bodyAr.directory.viewListLabel` | viewListLabel | "القائمة" |
| `bodyAr.directory.viewMapLabel` | viewMapLabel | "الخريطة" |
| `bodyAr.directory.mapPlaceholder` | mapPlaceholder | "عرض الخريطة قيد التطوير" |
| `bodyAr.directory.seeMoreLabel` | seeMoreLabel | "رؤية المزيد" |
| `bodyAr.directory.tabs` | tabs | [{"id":"cities","label":"المدن"},{"id":"project… |
| `bodyAr.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "الرقم" |
| `bodyAr.directory.columns.cities.name` | الاسم | "اسم المدينة" |
| `bodyAr.directory.columns.cities.details` | details | "تفاصيل" |
| `bodyAr.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "رقم المشروع" |
| `bodyAr.directory.columns.projects.city` | المدينة | "المدينة" |
| `bodyAr.directory.columns.projects.country` | country | "الدولة" |
| `bodyAr.directory.columns.projects.startDate` | تاريخ البداية | "التاريخ" |
| `bodyAr.directory.columns.projects.endDate` | تاريخ النهاية | "تاريخ النهاية" |
| `bodyEn.paragraphs` | paragraphs | ["The Arab Urban Development Portal is a platfo… |
| `bodyEn.contributeTitle` | contributeTitle | "Contribute by providing information about" |
| `bodyEn.contributeDescription` | contributeDescription | "You can help enrich the portal by sharing info… |
| `bodyEn.email` | البريد الإلكتروني | "infohub@araburban.org" |
| `bodyEn.ctaTitle` | ctaTitle | "Choose the type of contribution" |
| `bodyEn.ctaDisclaimer` | ctaDisclaimer | "The inclusion of any project or organization i… |
| `bodyEn.contributionTypes` | contributionTypes | [{"id":"publications","label":"Publications"},{… |
| `bodyEn.contributionForm.openLabel` | openLabel | "Submit a contribution online" |
| `bodyEn.contributionForm.typeLabel` | typeLabel | "Contribution type" |
| `bodyEn.contributionForm.types.publications` | publications | "Publications" |
| `bodyEn.contributionForm.types.cities` | مصفوفة المدن للاستيراد | "Cities" |
| `bodyEn.contributionForm.types.organizations` | organizations | "Organizations" |
| `bodyEn.contributionForm.emailLabel` | emailLabel | "Email" |
| `bodyEn.contributionForm.emailPlaceholder` | emailPlaceholder | "your@email.com" |
| `bodyEn.contributionForm.titleLabel` | titleLabel | "Title" |
| `bodyEn.contributionForm.titlePlaceholder` | titlePlaceholder | "Title of the city, organization, or publication" |
| `bodyEn.contributionForm.detailsLabel` | detailsLabel | "Details" |
| `bodyEn.contributionForm.detailsPlaceholder` | detailsPlaceholder | "Describe the information you would like to con… |
| `bodyEn.contributionForm.submit` | submit | "Submit Contribution" |
| `bodyEn.contributionForm.success` | success | "Your contribution was submitted successfully." |
| `bodyEn.contributionForm.error` | error | "An error occurred while submitting. Please try… |
| `bodyEn.directory.video` | video | "\/urban-policies\/audi-infohub-inst-2.mp4" |
| `bodyEn.directory.title` | العنوان | "Directory of Arab Cities" |
| `bodyEn.directory.subtitle` | subtitle | "The Institute seeks to collect and document da… |
| `bodyEn.directory.filtersTitle` | filtersTitle | "Classification" |
| `bodyEn.directory.countryLabel` | countryLabel | "Country" |
| `bodyEn.directory.cityLabel` | cityLabel | "City" |
| `bodyEn.directory.citySizeLabel` | citySizeLabel | "City Size" |
| `bodyEn.directory.resetLabel` | resetLabel | "Reset" |
| `bodyEn.directory.searchLabel` | searchLabel | "Search" |
| `bodyEn.directory.viewListLabel` | viewListLabel | "List" |
| `bodyEn.directory.viewMapLabel` | viewMapLabel | "Map" |
| `bodyEn.directory.mapPlaceholder` | mapPlaceholder | "Map view coming soon" |
| `bodyEn.directory.seeMoreLabel` | seeMoreLabel | "See More" |
| `bodyEn.directory.tabs` | tabs | [{"id":"cities","label":"Cities"},{"id":"projec… |
| `bodyEn.directory.columns.cities.number` | الرقم الترتيبي (01, 02, …) | "No." |
| `bodyEn.directory.columns.cities.name` | الاسم | "City Name" |
| `bodyEn.directory.columns.cities.details` | details | "Details" |
| `bodyEn.directory.columns.projects.number` | الرقم الترتيبي (01, 02, …) | "Project No." |
| `bodyEn.directory.columns.projects.city` | المدينة | "City" |
| `bodyEn.directory.columns.projects.country` | country | "Country" |
| `bodyEn.directory.columns.projects.startDate` | تاريخ البداية | "Start Date" |
| `bodyEn.directory.columns.projects.endDate` | تاريخ النهاية | "End Date" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/program-section-details/{{programSectionDetailId}}`

**الاسم | Name:** حذف — Delete تفاصيل قسم

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

After section create: `introAr/En`, `bodyAr/En`. Optional `titleAr/En` + `imageUrl`. Full examples: **00 — أدلة البناء**.

---

#### الدورات التدريبية — Training Courses

#### GET `/api/admin/training-courses`

**الاسم | Name:** عرض القائمة — List Training Course

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Training grid on ?tab=trainingPrograms. Full seed: **بناء برنامج التدريب** steps 10–15.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/training-courses`

**الاسم | Name:** إنشاء — Create دورة تدريبية

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "التخطيط والتطوير الحضري" |
| `titleEn` | العنوان بالإنجليزية | "Urban Planning and Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Training grid on ?tab=trainingPrograms. Full seed: **بناء برنامج التدريب** steps 10–15.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/training-courses/{{id}}`

**الاسم | Name:** عرض — Show دورة تدريبية

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Training grid on ?tab=trainingPrograms. Full seed: **بناء برنامج التدريب** steps 10–15.Use `id` from create response.

---

#### PUT `/api/admin/training-courses/{{id}}`

**الاسم | Name:** تحديث — Update دورة تدريبية

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "التخطيط والتطوير الحضري" |
| `titleEn` | العنوان بالإنجليزية | "Urban Planning and Development" |
| `countAr` | العدد/التعداد بالعربية (مثل: 3 دورات) | "3 دورات تدريبية" |
| `countEn` | العدد/التعداد بالإنجليزية | "3 training courses" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Training grid on ?tab=trainingPrograms. Full seed: **بناء برنامج التدريب** steps 10–15.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/training-courses/{{id}}`

**الاسم | Name:** حذف — Delete دورة تدريبية

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Training grid on ?tab=trainingPrograms. Full seed: **بناء برنامج التدريب** steps 10–15.

---

#### POST `/api/admin/training-courses/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder دورة تدريبية

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### الخبراء — Experts

#### GET `/api/admin/experts`

**الاسم | Name:** عرض القائمة — List Expert

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Experts carousel on ?tab=experts. Full seed: **بناء برنامج التدريب** steps 16–18.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/experts`

**الاسم | Name:** إنشاء — Create خبير

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. إبراهيم باهر الدين" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Ibrahim Baher El-Din" |
| `specialtyAr` | specialtyAr | "التصميم الحضري والتخطيط التشاركي" |
| `specialtyEn` | specialtyEn | "Urban Design and Participatory Planning" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Experts carousel on ?tab=experts. Full seed: **بناء برنامج التدريب** steps 16–18.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/experts/{{id}}`

**الاسم | Name:** عرض — Show خبير

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Experts carousel on ?tab=experts. Full seed: **بناء برنامج التدريب** steps 16–18.Use `id` from create response.

---

#### PUT `/api/admin/experts/{{id}}`

**الاسم | Name:** تحديث — Update خبير

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "د. إبراهيم باهر الدين" |
| `nameEn` | الاسم بالإنجليزية | "Dr. Ibrahim Baher El-Din" |
| `specialtyAr` | specialtyAr | "التصميم الحضري والتخطيط التشاركي" |
| `specialtyEn` | specialtyEn | "Urban Design and Participatory Planning" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/emp\/1.png" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Experts carousel on ?tab=experts. Full seed: **بناء برنامج التدريب** steps 16–18.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/experts/{{id}}`

**الاسم | Name:** حذف — Delete خبير

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/training` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Experts carousel on ?tab=experts. Full seed: **بناء برنامج التدريب** steps 16–18.

---

#### POST `/api/admin/experts/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder خبير

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### دليل المدن — Directory Cities

#### GET `/api/admin/directory/cities`

**الاسم | Name:** عرض القائمة — List Directory City

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/directory/cities`

**الاسم | Name:** إنشاء — Create مدينة

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "الرياض، المملكة العربية السعودية" |
| `nameEn` | الاسم بالإنجليزية | "Riyadh, Saudi Arabia" |
| `descriptionAr` | الوصف بالعربية | "عاصمة المملكة وواحدة من أكبر المدن في المنطقة." |
| `descriptionEn` | الوصف بالإنجليزية | "Capital of the Kingdom and one of the largest … |
| `countryCode` | رمز الدولة | "SA" |
| `citySize` | حجم المدينة (large|medium|small) | "large" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/directory/cities/{{id}}`

**الاسم | Name:** عرض — Show مدينة

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/directory/cities/{{id}}`

**الاسم | Name:** تحديث — Update مدينة

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "الرياض، المملكة العربية السعودية" |
| `nameEn` | الاسم بالإنجليزية | "Riyadh, Saudi Arabia" |
| `descriptionAr` | الوصف بالعربية | "عاصمة المملكة وواحدة من أكبر المدن في المنطقة." |
| `descriptionEn` | الوصف بالإنجليزية | "Capital of the Kingdom and one of the largest … |
| `countryCode` | رمز الدولة | "SA" |
| `citySize` | حجم المدينة (large|medium|small) | "large" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/directory/cities/{{id}}`

**الاسم | Name:** حذف — Delete مدينة

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/directory/cities/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder مدينة

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### دليل المشاريع — Directory Projects

#### GET `/api/admin/directory/projects`

**الاسم | Name:** عرض القائمة — List Directory Project

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/directory/projects`

**الاسم | Name:** إنشاء — Create مشروع

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `cityAr` | المدينة بالعربية | "الرياض" |
| `cityEn` | المدينة بالإنجليزية | "Riyadh" |
| `countryAr` | الدولة بالعربية | "المملكة العربية السعودية" |
| `countryEn` | الدولة بالإنجليزية | "Saudi Arabia" |
| `startDate` | تاريخ البداية | "2019" |
| `endDate` | تاريخ النهاية | "2023" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/directory/projects/{{id}}`

**الاسم | Name:** عرض — Show مشروع

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/directory/projects/{{id}}`

**الاسم | Name:** تحديث — Update مشروع

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `cityAr` | المدينة بالعربية | "الرياض" |
| `cityEn` | المدينة بالإنجليزية | "Riyadh" |
| `countryAr` | الدولة بالعربية | "المملكة العربية السعودية" |
| `countryEn` | الدولة بالإنجليزية | "Saudi Arabia" |
| `startDate` | تاريخ البداية | "2019" |
| `endDate` | تاريخ النهاية | "2023" |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/directory/projects/{{id}}`

**الاسم | Name:** حذف — Delete مشروع

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/directory/projects/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder مشروع

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### دليل المنظمات — Directory Organizations

#### GET `/api/admin/directory/organizations`

**الاسم | Name:** عرض القائمة — List Directory Organization

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/directory/organizations`

**الاسم | Name:** إنشاء — Create منظمة

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "برنامج الأمم المتحدة للمستوطنات البشرية" |
| `nameEn` | الاسم بالإنجليزية | "UN-Habitat" |
| `descriptionAr` | الوصف بالعربية | "منظمة الأمم المتحدة المعنية بالمستوطنات البشرية." |
| `descriptionEn` | الوصف بالإنجليزية | "UN agency for human settlements." |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/directory/organizations/{{id}}`

**الاسم | Name:** عرض — Show منظمة

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/directory/organizations/{{id}}`

**الاسم | Name:** تحديث — Update منظمة

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "برنامج الأمم المتحدة للمستوطنات البشرية" |
| `nameEn` | الاسم بالإنجليزية | "UN-Habitat" |
| `descriptionAr` | الوصف بالعربية | "منظمة الأمم المتحدة المعنية بالمستوطنات البشرية." |
| `descriptionEn` | الوصف بالإنجليزية | "UN agency for human settlements." |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/directory/organizations/{{id}}`

**الاسم | Name:** حذف — Delete منظمة

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/directory/organizations/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder منظمة

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### دليل المنشورات — Directory Publications

#### GET `/api/admin/directory/publications`

**الاسم | Name:** عرض القائمة — List Directory Publication

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/directory/publications`

**الاسم | Name:** إنشاء — Create منشور

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "تقرير التنمية الحضرية العربية 2024" |
| `nameEn` | الاسم بالإنجليزية | "Arab Urban Development Report 2024" |
| `descriptionAr` | الوصف بالعربية | "تقرير سنوي يرصد التطورات في التنمية الحضرية." |
| `descriptionEn` | الوصف بالإنجليزية | "Annual report monitoring urban development tre… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/directory/publications/{{id}}`

**الاسم | Name:** عرض — Show منشور

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/directory/publications/{{id}}`

**الاسم | Name:** تحديث — Update منشور

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `number` | الرقم الترتيبي (01, 02, …) | "01" |
| `nameAr` | الاسم بالعربية | "تقرير التنمية الحضرية العربية 2024" |
| `nameEn` | الاسم بالإنجليزية | "Arab Urban Development Report 2024" |
| `descriptionAr` | الوصف بالعربية | "تقرير سنوي يرصد التطورات في التنمية الحضرية." |
| `descriptionEn` | الوصف بالإنجليزية | "Annual report monitoring urban development tre… |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/directory/publications/{{id}}`

**الاسم | Name:** حذف — Delete منشور

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/programs/urban-policies/directory` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/directory/publications/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder منشور

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### المصادر — Resources

<a id="resources"></a>
**الغرض | Purpose:** تقارير ودراسات ومصادر المعرفة (صفحة مصادرنا + مركز المعرفة في الرئيسية). / Reports and knowledge resources.
**الواجهة العامة | Public API:** `GET /api/v1/resources`

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### المصادر — Resources CRUD

#### GET `/api/admin/resources`

**الاسم | Name:** عرض القائمة — List Resource

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/resources`

**الاسم | Name:** إنشاء — Create مصدر

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "green-infrastructure" |
| `titleAr` | العنوان بالعربية | "البنية التحتية الخضراء نحو منظومة خضراء متكامل… |
| `titleEn` | العنوان بالإنجليزية | "Green Infrastructure Toward an Integrated Gree… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/our-sources\/4.png" |
| `fileUrl` | رابط ملف PDF أو مرفق | "\/storage\/resources\/green-infrastructure.pdf" |
| `knowledgeCategoryId` | معرّف تصنيف مركز المعرفة (FK) — اربط البطاقة بالتصنيف | 1 |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/resources/{{id}}`

**الاسم | Name:** عرض — Show مصدر

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.Use `id` from create response.

---

#### PUT `/api/admin/resources/{{id}}`

**الاسم | Name:** تحديث — Update مصدر

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "green-infrastructure" |
| `titleAr` | العنوان بالعربية | "البنية التحتية الخضراء نحو منظومة خضراء متكامل… |
| `titleEn` | العنوان بالإنجليزية | "Green Infrastructure Toward an Integrated Gree… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/our-sources\/4.png" |
| `fileUrl` | رابط ملف PDF أو مرفق | "\/storage\/resources\/green-infrastructure.pdf" |
| `knowledgeCategoryId` | معرّف تصنيف مركز المعرفة (FK) — اربط البطاقة بالتصنيف | 1 |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 2 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/resources/{{id}}`

**الاسم | Name:** حذف — Delete مصدر

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/resources` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Resources page + homepage knowledge cards. Full homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 29–31.

---

#### POST `/api/admin/resources/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder مصدر

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### المركز الإعلامي — Media

<a id="media"></a>
**الغرض | Purpose:** الأخبار، نشرة مدننا، لقاءات حراك المدن، الأمين يتحدث. / News, newsletter, city meetings, secretary speaks articles.
**الواجهة العامة | Public API:** `GET /api/v1/media/*`

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### المقالات الإعلامية — Media Articles

#### GET `/api/admin/media`

**الاسم | Name:** عرض القائمة — List Media Article

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/media`

**الاسم | Name:** إنشاء — Create مقال إعلامي

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "director-dialogue-session" |
| `slugAr` | الرابط العربي (slug) | "جلسة-حوارية-التنمية-الحضرية" |
| `slugEn` | الرابط الإنجليزي (slug) | "director-dialogue-session" |
| `titleAr` | العنوان بالعربية | "مدير عام المعهد يشارك في جلسة حوارية للفطيم حو… |
| `titleEn` | العنوان بالإنجليزية | "Institute Director Participates in Al-Futtaim … |
| `descriptionAr` | الوصف بالعربية | "شارك مدير عام المعهد العربي لإنماء المدن في جل… |
| `descriptionEn` | الوصف بالإنجليزية | "The Director General of the Arab Urban Develop… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["شارك مدير عام المعهد العربي لإنماء المدن في ج… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Director General of the Arab Urban Develo… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/1.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `authorsAr` | المؤلفون بالعربية (مصفوفة) | null |
| `authorsEn` | المؤلفون بالإنجليزية (مصفوفة) | null |
| `eventTime` | وقت الفعالية (مثل: 10:00 - 14:00) | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/media/{{id}}`

**الاسم | Name:** عرض — Show مقال إعلامي

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.Use `id` from create response.

---

#### PUT `/api/admin/media/{{id}}`

**الاسم | Name:** تحديث — Update مقال إعلامي

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "news" |
| `key` | المفتاح الفريد للسجل | "director-dialogue-session" |
| `slugAr` | الرابط العربي (slug) | "جلسة-حوارية-التنمية-الحضرية" |
| `slugEn` | الرابط الإنجليزي (slug) | "director-dialogue-session" |
| `titleAr` | العنوان بالعربية | "مدير عام المعهد يشارك في جلسة حوارية للفطيم حو… |
| `titleEn` | العنوان بالإنجليزية | "Institute Director Participates in Al-Futtaim … |
| `descriptionAr` | الوصف بالعربية | "شارك مدير عام المعهد العربي لإنماء المدن في جل… |
| `descriptionEn` | الوصف بالإنجليزية | "The Director General of the Arab Urban Develop… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["شارك مدير عام المعهد العربي لإنماء المدن في ج… |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The Director General of the Arab Urban Develo… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-12-29" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/1.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | null |
| `authorsAr` | المؤلفون بالعربية (مصفوفة) | null |
| `authorsEn` | المؤلفون بالإنجليزية (مصفوفة) | null |
| `eventTime` | وقت الفعالية (مثل: 10:00 - 14:00) | null |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/media/{{id}}`

**الاسم | Name:** حذف — Delete مقال إعلامي

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/news` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

News articles (`category: news`). Homepage seed: **الرئيسية → 00 — بناء الصفحة الرئيسية** steps 19–24.

---

#### POST `/api/admin/media/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder مقال إعلامي

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

#### POST `/api/admin/media`

**الاسم | Name:** إنشاء نشرة — Create Newsletter Article

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "newsletter" |
| `key` | المفتاح الفريد للسجل | "newsletter-issue-12" |
| `slugAr` | الرابط العربي (slug) | "النشرة-الإخبارية-12" |
| `slugEn` | الرابط الإنجليزي (slug) | "newsletter-issue-12" |
| `titleAr` | العنوان بالعربية | "النشرة الإخبارية - العدد 12" |
| `titleEn` | العنوان بالإنجليزية | "Newsletter - Issue 12" |
| `descriptionAr` | الوصف بالعربية | null |
| `descriptionEn` | الوصف بالإنجليزية | null |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["محتوى العدد باللغة العربية."] |
| `bodyEn` | محتوى JSON بالإنجليزية | ["Issue content in English."] |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-06-01" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/2.png" |
| `pdfUrl` | رابط PDF للمقال أو النشرة | "\/storage\/newsletter\/issue-12.pdf" |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/newsletter` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin category uses underscore: newsletter. Public URL uses hyphen: /media/newsletter.

---

#### POST `/api/admin/media`

**الاسم | Name:** إنشاء لقاء مدينة — Create City Meeting

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "city_meetings" |
| `key` | المفتاح الفريد للسجل | "riyadh-city-meeting-2025" |
| `slugAr` | الرابط العربي (slug) | "لقاء-مدينة-الرياض-2025" |
| `slugEn` | الرابط الإنجليزي (slug) | "riyadh-city-meeting-2025" |
| `titleAr` | العنوان بالعربية | "لقاء مدينة الرياض 2025" |
| `titleEn` | العنوان بالإنجليزية | "Riyadh City Meeting 2025" |
| `descriptionAr` | الوصف بالعربية | "لقاء سنوي يجمع ممثلي المدن العربية." |
| `descriptionEn` | الوصف بالإنجليزية | "Annual meeting gathering representatives of Ar… |
| `bodyAr` | محتوى JSON بالعربية (فقرات، تسميات، أقسام) | ["تناول اللقاء موضوعات التخطيط الحضري والتنمية … |
| `bodyEn` | محتوى JSON بالإنجليزية | ["The meeting covered urban planning and sustai… |
| `publishedDate` | تاريخ النشر (YYYY-MM-DD) | "2025-03-15" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | "\/blog\/3.png" |
| `authorsAr` | المؤلفون بالعربية (مصفوفة) | ["فريق المركز الإعلامي"] |
| `authorsEn` | المؤلفون بالإنجليزية (مصفوفة) | ["Media Center Team"] |
| `eventTime` | وقت الفعالية (مثل: 10:00 - 14:00) | "10:00 - 14:00" |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/media/city-meetings` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Admin: city_meetings. Public: city-meetings.

---

### الوظائف — Careers

<a id="careers"></a>
**الغرض | Purpose:** الوظائف الشاغرة المعروضة في صفحة اعمل معنا. / Job openings for careers page.
**الواجهة العامة | Public API:** `GET /api/v1/careers`

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### الوظائف الشاغرة — Job Openings

#### GET `/api/admin/job-openings`

**الاسم | Name:** عرض القائمة — List Job Opening

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/job-openings`

**الاسم | Name:** إنشاء — Create وظيفة

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "باحث في السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policy Researcher" |
| `locationAr` | الموقع بالعربية | "الرياض، المملكة العربية السعودية" |
| `locationEn` | الموقع بالإنجليزية | "Riyadh, Saudi Arabia" |
| `employmentType` | نوع التوظيف (full_time, part_time, contract) | "full_time" |
| `summaryAr` | الملخص بالعربية | "نبحث عن باحث متخصص في السياسات الحضرية للانضما… |
| `summaryEn` | الملخص بالإنجليزية | "We are looking for an urban policy researcher … |
| `descriptionAr` | الوصف بالعربية | ["إعداد دراسات وبحوث في مجال السياسات الحضرية."… |
| `descriptionEn` | الوصف بالإنجليزية | ["Prepare studies and research in urban policy.… |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/job-openings/{{id}}`

**الاسم | Name:** عرض — Show وظيفة

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/job-openings/{{id}}`

**الاسم | Name:** تحديث — Update وظيفة

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `titleAr` | العنوان بالعربية | "باحث في السياسات الحضرية" |
| `titleEn` | العنوان بالإنجليزية | "Urban Policy Researcher" |
| `locationAr` | الموقع بالعربية | "الرياض، المملكة العربية السعودية" |
| `locationEn` | الموقع بالإنجليزية | "Riyadh, Saudi Arabia" |
| `employmentType` | نوع التوظيف (full_time, part_time, contract) | "full_time" |
| `summaryAr` | الملخص بالعربية | "نبحث عن باحث متخصص في السياسات الحضرية للانضما… |
| `summaryEn` | الملخص بالإنجليزية | "We are looking for an urban policy researcher … |
| `descriptionAr` | الوصف بالعربية | ["إعداد دراسات وبحوث في مجال السياسات الحضرية."… |
| `descriptionEn` | الوصف بالإنجليزية | ["Prepare studies and research in urban policy.… |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/job-openings/{{id}}`

**الاسم | Name:** حذف — Delete وظيفة

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/careers` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/job-openings/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder وظيفة

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### الأسئلة الشائعة — FAQ

<a id="faq"></a>
**الغرض | Purpose:** أسئلة وأجوبة صفحة FAQ. / FAQ questions and answers.
**الواجهة العامة | Public API:** `GET /api/v1/faqs`

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### الأسئلة — FAQs

#### GET `/api/admin/faqs`

**الاسم | Name:** عرض القائمة — List FAQ

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/faqs`

**الاسم | Name:** إنشاء — Create سؤال

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "membership" |
| `questionAr` | السؤال بالعربية | "كيف يمكنني الانضمام للمعهد كعضو؟" |
| `questionEn` | السؤال بالإنجليزية | "How can I join the institute as a member?" |
| `answerAr` | الإجابة بالعربية | "يمكنكم الانضمام عبر تعبئة نموذج العضوية في صفح… |
| `answerEn` | الإجابة بالإنجليزية | "You can join by filling out the membership for… |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/faqs/{{id}}`

**الاسم | Name:** عرض — Show سؤال

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/faqs/{{id}}`

**الاسم | Name:** تحديث — Update سؤال

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `category` | التصنيف (news, newsletter, city_meetings, membership, …) | "membership" |
| `questionAr` | السؤال بالعربية | "كيف يمكنني الانضمام للمعهد كعضو؟" |
| `questionEn` | السؤال بالإنجليزية | "How can I join the institute as a member?" |
| `answerAr` | الإجابة بالعربية | "يمكنكم الانضمام عبر تعبئة نموذج العضوية في صفح… |
| `answerEn` | الإجابة بالإنجليزية | "You can join by filling out the membership for… |
| `isPublished` | منشور للعامة؟ (true/false) | true |
| `sortOrder` | ترتيب العرض (0 = الأول) | 0 |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/faqs/{{id}}`

**الاسم | Name:** حذف — Delete سؤال

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/faqs` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### POST `/api/admin/faqs/reorder`

**الاسم | Name:** إعادة الترتيب — Reorder سؤال

**الغرض | Purpose:** تغيير ترتيب العرض عبر sortOrder.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"id":1,"sortOrder":0},{"id":2,"sortOrder":1}] |

#### Notes | ملاحظات

إعادة ترتيب العناصر — `items[].id` + `items[].sortOrder`.

---

### الصفحات القانونية — Legal

<a id="legal"></a>
**الغرض | Purpose:** الشروط والأحكام وسياسة الخصوصية. / Terms and privacy pages.
**الواجهة العامة | Public API:** `GET /api/v1/legal/{slug}`

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### الشروط والخصوصية — Legal Pages

#### GET `/api/admin/legal`

**الاسم | Name:** عرض القائمة — List Legal Page

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `search` | بحث نصي | `` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

قائمة الإدارة — تُرجع حقول `*Ar` و `*En` معاً.

---

#### POST `/api/admin/legal`

**الاسم | Name:** إنشاء — Create صفحة قانونية

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "terms" |
| `titleAr` | العنوان بالعربية | "الشروط والأحكام" |
| `titleEn` | العنوان بالإنجليزية | "Terms and Conditions" |
| `contentAr` | المحتوى النصي بالعربية | "مرحباً بكم في موقع المعهد العربي لإنماء المدن.… |
| `contentEn` | المحتوى النصي بالإنجليزية | "Welcome to the Arab Urban Development Institut… |
| `effectiveDate` | تاريخ سريان الصفحة القانونية | "2026-01-01" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم الطلب كامل** — جميع الحقول ثنائية اللغة حيث ينطبق.

---

#### GET `/api/admin/legal/{{id}}`

**الاسم | Name:** عرض — Show صفحة قانونية

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Use `id` from create response.

---

#### PUT `/api/admin/legal/{{id}}`

**الاسم | Name:** تحديث — Update صفحة قانونية

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `slug` | المعرّف اللatinي للرابط (مثل: training) | "terms" |
| `titleAr` | العنوان بالعربية | "الشروط والأحكام" |
| `titleEn` | العنوان بالإنجليزية | "Terms and Conditions" |
| `contentAr` | المحتوى النصي بالعربية | "مرحباً بكم في موقع المعهد العربي لإنماء المدن.… |
| `contentEn` | المحتوى النصي بالإنجليزية | "Welcome to the Arab Urban Development Institut… |
| `effectiveDate` | تاريخ سريان الصفحة القانونية | "2026-01-01" |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

**جسم التحديث كامل** — نفس حقول الإنشاء.

---

#### DELETE `/api/admin/legal/{{id}}`

**الاسم | Name:** حذف — Delete صفحة قانونية

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/legal/terms` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

### المدن الأعضاء — Member Cities

<a id="member-cities"></a>
**الغرض | Purpose:** إدارة مدن الأعضاء، الإحصائيات، واستيراد GeoJSON. / Member cities CRUD, stats, GeoJSON import.
**الواجهة العامة | Public API:** `GET /api/v1/home/member-cities`

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

#### GET `/api/admin/member-cities/cities?page=1&limit=20`

**الاسم | Name:** عرض قائمة المدن — List Cities

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `limit` | عدد النتائج في الصفحة | `20` |
| `countryCode` | تصفية برمز الدولة | `SA` |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

---

#### GET `/api/admin/member-cities/cities/{{id}}`

**الاسم | Name:** عرض مدينة — Show City

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### POST `/api/admin/member-cities/cities`

**الاسم | Name:** إنشاء مدينة — Create City

**الغرض | Purpose:** إنشاء سجل جديد في قاعدة البيانات.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `countryCode` | رمز الدولة | "SA" |
| `nameAr` | الاسم بالعربية | "الرياض" |
| `nameEn` | الاسم بالإنجليزية | "Riyadh" |
| `latitude` | خط العرض | 24.7136 |
| `longitude` | خط الطول | 46.6753 |
| `infoAr` | معلومات إضافية بالعربية | "عاصمة المملكة العربية السعودية" |
| `infoEn` | معلومات إضافية بالإنجليزية | "Capital of Saudi Arabia" |
| `imageUrl` | رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع) | null |
| `isActive` | نشط؟ (true/false) | true |

#### Notes | ملاحظات

Public GeoJSON uses locale-resolved name/info.

---

#### PATCH `/api/admin/member-cities/cities/{{id}}`

**الاسم | Name:** تحديث مدينة — Update City

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `nameAr` | الاسم بالعربية | "الرياض" |
| `nameEn` | الاسم بالإنجليزية | "Riyadh" |
| `infoAr` | معلومات إضافية بالعربية | "عاصمة المملكة" |
| `infoEn` | معلومات إضافية بالإنجليزية | "Kingdom capital" |
| `isActive` | نشط؟ (true/false) | true |

---

#### DELETE `/api/admin/member-cities/cities/{{id}}`

**الاسم | Name:** حذف مدينة — Delete City

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

#### GET `/api/admin/member-cities/stats`

**الاسم | Name:** عرض الإحصائيات — Get Stats

**الغرض | Purpose:** قراءة بيانات من الخادم.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Returns bilingual label/unit per stat key.

---

#### PUT `/api/admin/member-cities/stats`

**الاسم | Name:** تحديث الإحصائيات — Update Stats

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `items` | قائمة العناصر (إعادة ترتيب أو إحصائيات) | [{"key":"countries","value":12,"autoCalculate":… |

#### Notes | ملاحظات

**Public match:** `GET /api/v1/home/member-cities` — returns locale-resolved fields (`title`, `name`, …) from admin `*Ar/*En` columns.

**Locale:** set collection variable `locale` to `ar` or `en` (or use `Accept-Language` header).

Homepage «المدن الأعضاء»: 12 دولة / 400 مدينة / 1240 عضو. Stats use nested label.ar/en and unit.ar/en.

---

#### GET `/api/admin/member-cities/countries`

**الاسم | Name:** عرض الدول — List Countries

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

---

#### POST `/api/admin/member-cities/cities/import`

**الاسم | Name:** استيراد جماعي — Bulk Import Cities

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `cities` | مصفوفة المدن للاستيراد | [{"countryCode":"SA","nameAr":"الرياض","nameEn"… |
| `upsertBy` | حقول المطابقة عند التحديث | ["countryCode","nameEn"] |

---

#### POST `/api/admin/member-cities/cities/import-from-file`

**الاسم | Name:** استيراد من ملف — Import Cities from File

**الغرض | Purpose:** إرسال بيانات جديدة أو تنفيذ عملية.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `file` | GeoJSON file with member cities data. | (ملف) |

#### Notes | ملاحظات

**Content-Type:** `multipart/form-data`. Upload a GeoJSON file to bulk-import cities.

---

### النماذج الواردة — Form Submissions

<a id="form-submissions"></a>
**الغرض | Purpose:** مراجعة رسائل التواصل، طلبات العضوية، المساهمات، التقديم على الوظائف، النشرة. / Review contact, membership, contributions, job applications, newsletter.
**الواجهة العامة | Public API:** `POST /api/v1/contact, /membership, …`

#### GET `/api/admin/contact-submissions?page=1`

**الاسم | Name:** عرض رسائل التواصل — List Contact Submissions

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

From public POST /api/v1/contact.

---

#### GET `/api/admin/contact-submissions/{{id}}`

**الاسم | Name:** Show Contact Submission

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### PATCH `/api/admin/contact-submissions/{{id}}`

**الاسم | Name:** Update Contact Status

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `status` | الحالة (new, read, approved, …) | "read" |

#### Notes | ملاحظات

status: new | read | archived

---

#### DELETE `/api/admin/contact-submissions/{{id}}`

**الاسم | Name:** Delete Contact Submission

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

#### GET `/api/admin/membership-applications?page=1`

**الاسم | Name:** List Membership Applications

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

From public POST /api/v1/membership.

---

#### GET `/api/admin/membership-applications/{{id}}`

**الاسم | Name:** Show Membership Application

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### PATCH `/api/admin/membership-applications/{{id}}`

**الاسم | Name:** Update Membership Status

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `status` | الحالة (new, read, approved, …) | "reviewing" |

#### Notes | ملاحظات

status: new | reviewing | approved | rejected

---

#### DELETE `/api/admin/membership-applications/{{id}}`

**الاسم | Name:** Delete Membership Application

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

#### GET `/api/admin/portal-contributions?page=1`

**الاسم | Name:** List Portal Contributions

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

From public POST /api/v1/programs/urban-policies/contribute.

---

#### GET `/api/admin/portal-contributions/{{id}}`

**الاسم | Name:** Show Portal Contribution

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### PATCH `/api/admin/portal-contributions/{{id}}`

**الاسم | Name:** Update Portal Contribution Status

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `status` | الحالة (new, read, approved, …) | "approved" |

#### Notes | ملاحظات

status: new | reviewing | approved | rejected

---

#### DELETE `/api/admin/portal-contributions/{{id}}`

**الاسم | Name:** Delete Portal Contribution

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

#### GET `/api/admin/job-applications?page=1`

**الاسم | Name:** List Job Applications

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Query Parameters | معاملات الرابط

| المعامل | الوصف | مثال |
|---------|--------|------|
| `page` | رقم الصفحة | `1` |
| `status` | new|reviewing|shortlisted|rejected|hired | `` |

#### Notes | ملاحظات

From public POST /api/v1/careers/apply.

---

#### GET `/api/admin/job-applications/{{id}}`

**الاسم | Name:** Show Job Application

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### PATCH `/api/admin/job-applications/{{id}}`

**الاسم | Name:** Update Job Application Status

**الغرض | Purpose:** تحديث سجل موجود (PUT/PATCH).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Body Parameters | معاملات الجسم (JSON)

| الحقل | الوصف | مثال |
|-------|--------|------|
| `status` | الحالة (new, read, approved, …) | "reviewing" |

#### Notes | ملاحظات

status: new | reviewing | shortlisted | rejected | hired

---

#### DELETE `/api/admin/job-applications/{{id}}`

**الاسم | Name:** Delete Job Application

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---

#### GET `/api/admin/newsletter-subscriptions?page=1`

**الاسم | Name:** List Newsletter Subscriptions

**الغرض | Purpose:** عرض قائمة paginated مع حقول ثنائية اللغة (*Ar/*En).

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

From public POST /api/v1/newsletter/subscribe.

---

#### GET `/api/admin/newsletter-subscriptions/{{id}}`

**الاسم | Name:** Show Newsletter Subscription

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** عرض تفاصيل سجل واحد بالمعرّف {{id}}.

---

#### DELETE `/api/admin/newsletter-subscriptions/{{id}}`

**الاسم | Name:** Delete Newsletter Subscription

**الغرض | Purpose:** حذف سجل نهائياً.

**المصادقة | Auth:** Bearer `{{adminToken}}` (مطلوب)

#### Notes | ملاحظات

**الغرض | Purpose:** حذف سجل نهائياً.

---
