# دليل بناء الصفحة الرئيسية — Homepage Admin Guide

> **المعهد العربي لإنماء المدن** — Arab Urban Development Institute  
> يُولَّد تلقائياً من `postman-home-examples.php` مع مجموعة Postman  
> الموقع المرجعي: [https://audi-ten.vercel.app/ar](https://audi-ten.vercel.app/ar)

---

## نظرة عامة | Overview

هذا الدليل يشرح **33 خطوة** لبناء الصفحة الرئيسية كاملة عبر واجهة الإدارة، مع أمثلة جاهزة في Postman.

| العنصر | القيمة |
|--------|--------|
| **مجموعة Postman** | `AUDI-API.postman_collection.json` |
| **المجلد** | `Admin` → `الرئيسية — Home` → `00 — بناء الصفحة الرئيسية — 00 — Build Full Homepage` |
| **التحقق** | `GET /api/v1/home` مع `Accept-Language: ar` |
| **المصادقة** | `POST /api/admin/login` → `adminToken` |

### خريطة سريعة | Quick map

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

---

## تدفق العمل | Workflow

```
1. Login → adminToken
2. Run steps 01–33 in order (or Collection Runner on folder 00)
3. GET /api/v1/home (Accept-Language: ar)
```

### ملاحظات على الأصول الثابتة | Static assets (not in admin API)

| Asset | Path |
|-------|------|
| Hero images | `/slider/1.png` … `/slider/4.png` |
| Stats icons | `/icons/num1.svg` … `/icons/num4.svg` |
| Media images | `/blog/1.png` … `/blog/4.png` |
| Resource images | `/our-sources/1.png`, `2.png`, `4.png` |
| Knowledge carousel logos | `/knowledgeCenter/icon1.png` … `icon3.png` |

---

## الخطوات 01–04 — شرائح الهيرو | Hero slider

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **01** | `01 — شريحة هيرو 1` | POST | `/api/admin/hero-slides` | **تطوير تقني** |
| **02** | `02 — شريحة هيرو 2` | POST | `/api/admin/hero-slides` | **تنمية عمرانية** |
| **03** | `03 — شريحة هيرو 3` | POST | `/api/admin/hero-slides` | **شراكات مستدامة** |
| **04** | `04 — شريحة هيرو 4` | POST | `/api/admin/hero-slides` | **بحوث ومبادرات** |

**Key fields:** `titleAr`, `titleEn`, `imageUrl`, `sortOrder`, `isActive: true`

**Public API:** `GET /api/v1/home` → `slider[]`

---

## الخطوة 05 — عن المعهد + رسالة + رؤية | About intro

| Step | Postman request | Method | Endpoint |
|------|-----------------|--------|----------|
| **05** | `05 — عن المعهد — home_about_intro` | POST | `/api/admin/about-content` |

**Body:** `sectionKey: "home_about_intro"`

| Field | Arabic (site) |
|-------|---------------|
| `titleAr` | المعهد العربي لإنماء المدن |
| `bodyAr.description` | تأسس المعهد العربي لإنماء المدن عام 1980، ومقره الرئيسي في الرياض… |
| `bodyAr.cta` | المزيد |
| `bodyAr.mission.title` | رسالتنا |
| `bodyAr.mission.description` | مؤسسة عالمية رائدة تسهم في خلق مستقبل عمراني أفضل… |
| `bodyAr.vision.title` | رؤيتنا |
| `bodyAr.vision.description` | دعم المدن والبلديات العربية لمواجهة تحديات التنمية العمرانية… |

**Public API:** `aboutIntro`

---

## الخطوات 06–10 — المعهد في أرقام | Institute in numbers

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **06** | `06 — المعهد في أرقام (عنوان) — home_stats` | POST | `/api/admin/about-content` | Title + subtitle |
| **07** | `07 — إحصائية 1` | POST | `/api/admin/home-stats` | **+25** اتفاقية — الاتفاقيات |
| **08** | `08 — إحصائية 2` | POST | `/api/admin/home-stats` | **+10** نشرة — نشرة مدننا |
| **09** | `09 — إحصائية 3` | POST | `/api/admin/home-stats` | **+500** مشارك — المشاركين في برامج القيادات البلدية |
| **10** | `10 — إحصائية 4` | POST | `/api/admin/home-stats` | **+400** مشروع — مشروع في تقارير السياسات الحضرية |

**Step 06:** `sectionKey: "home_stats"`, `titleAr: "المعهد في أرقام"`, `bodyAr.subtitle: "مؤسسة عالمية رائدة تساهم في صنع مستقبل حضري أفضل قائم على"`

**Steps 07–10 fields:** `value`, `labelAr`, `labelEn`, `descriptionAr`, `descriptionEn`, `sortOrder`

**Public API:** `stats.title`, `stats.subtitle`, `stats.items[]`

---

## الخطوات 11–13 — المدن الأعضاء | Member cities

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **11** | `11 — المدن الأعضاء (عنوان) — home_member_cities` | POST | `/api/admin/about-content` | Title **المدن الأعضاء** |
| **12** | `12 — إحصائيات المدن — member-cities/stats` | PUT | `/api/admin/member-cities/stats` | **12** دولة / **400** مدينة / **1240** عضو |
| **13** | `13 — مدينة على الخريطة (مثال الرياض) — member city` | POST | `/api/admin/member-cities/cities` | Map pin (Riyadh) |

**Step 11:** `sectionKey: "home_member_cities"`, `titleAr: "المدن الأعضاء"`

**Step 12 keys:** `countries` → 12, `cities` → 400, `members` → 1240

**Step 13:** `countryCode: "SA"`, `nameAr: "الرياض"`, `latitude: 24.7136`, `longitude: 46.6753`, `isActive: true`

**Full map:** repeat step 13 or use `Admin → المدن الأعضاء → استيراد جماعي — Bulk Import Cities`

**Public API:** `memberCities` + `GET /api/v1/home/member-cities`

---

## الخطوات 14–17 — برامجنا | Programs

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **14** | `14 — برامجنا (عنوان) — home_programs` | POST | `/api/admin/about-content` | Title **برامجنا** + CTA **استكشف** |
| **15** | `15 — برنامج urban-policies` | POST | `/api/admin/programs` | **السياسات الحضرية** |
| **16** | `16 — برنامج training` | POST | `/api/admin/programs` | **التدريب و تطوير القدرات** |
| **17** | `17 — برنامج partnerships` | POST | `/api/admin/programs` | **الشراكات** |

**Step 14:** `sectionKey: "home_programs"`, `bodyAr.cta: "استكشف"`

**Program fields:** `slug`, `titleAr`, `titleEn`, `cardDescriptionAr`, `cardDescriptionEn`, `sortOrder`

**Public API:** `programs.title`, `programs.cta`, `programs.items[]`

---

## الخطوات 18–24 — المركز الإعلامي | Media center

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **18** | `18 — المركز الإعلامي (عناوين) — home_media_center` | POST | `/api/admin/about-content` | Title, subtitle, قراءة المزيد, عرض الكل |
| **19** | `19 — خبر director-dialogue-session` | POST | `/api/admin/media` | مدير عام المعهد يشارك في جلسة حوارية للفطيم حول ال… |
| **20** | `20 — خبر uae-contractors-league` | POST | `/api/admin/media` | الدوري الإماراتي لمقاولي العمران يفتتح بمشاركة الق… |
| **21** | `21 — خبر municipal-cooperation-network` | POST | `/api/admin/media` | المعهد العربي ينظم الاجتماع السنوي لشبكة التعاون ا… |
| **22** | `22 — خبر sustainable-urban-planning` | POST | `/api/admin/media` | إطلاق مبادرة جديدة لدعم التخطيط الحضري المستدام في… |
| **23** | `23 — خبر municipal-governance-workshop` | POST | `/api/admin/media` | ورشة عمل حول الحوكمة البلدية الرشيدة بمشاركة خبراء… |
| **24** | `24 — خبر urban-development-conference` | POST | `/api/admin/media` | مؤتمر التنمية الحضرية 2025 يناقش مستقبل المدن العر… |

**Step 18:** `sectionKey: "home_media_center"`

**News articles (19–24):** always `"category": "news"`, `"isPublished": true`, plus `titleAr`, `descriptionAr`, `publishedDate`, `imageUrl` (`/blog/*.png`)

**Public API:** `mediaCenter.featured[]` (newest 4 by date) + `mediaCenter.items[]` (articles 5–8)

---

## الخطوات 25–31 — مركز المعرفة | Knowledge center

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **25** | `25 — مركز المعرفة (أزرار) — home_knowledge_center labels` | POST | `/api/admin/about-content` | عرض الإصدار / تنزيل PDF |
| **26** | `26 — تصنيف knowledge-center` | POST | `/api/admin/knowledge-categories` | **مركز المعرفة** (carousel tab) |
| **27** | `27 — تصنيف mudununa` | POST | `/api/admin/knowledge-categories` | **مدننا** |
| **28** | `28 — تصنيف meetings-platform` | POST | `/api/admin/knowledge-categories` | **منصة الاجتماعات** |
| **29** | `29 — مصدر solid-waste-management` | POST | `/api/admin/resources` | إدارة النفايات الصلبة في المدن العربية… (`knowledgeCategoryId: 1`) |
| **30** | `30 — مصدر urban-tourism` | POST | `/api/admin/resources` | المدينة، وجهة تكتشف السياحة الحضرية في ا… (`knowledgeCategoryId: 1`) |
| **31** | `31 — مصدر green-infrastructure` | POST | `/api/admin/resources` | البنية التحتية الخضراء نحو منظومة خضراء … (`knowledgeCategoryId: 1`) |

**Step 25:** `sectionKey: "home_knowledge_center"` — only `viewIssue` + `downloadPdf` (no carousel text here).

**Steps 26–28:** create categories. Response `id`: **1** = مركز المعرفة, **2** = مدننا, **3** = منصة الاجتماعات.

**Steps 29–31:** resource cards — set `"knowledgeCategoryId": 1` to link to **مركز المعرفة** (the 3 cards in the screenshot).

| Resource field | Example |
|----------------|---------|
| `knowledgeCategoryId` | `1` (FK from step 26) |
| `slug`, `titleAr`, `publishedDate`, `imageUrl`, `fileUrl` | see Postman body |
| `isPublished` | `true` |

**Public API:** `knowledgeCenter.categories[].items[]` — cards change when carousel tab changes.

---

## الخطوات 32–33 — عضوية + تواصل | Membership & contact

| Step | Postman request | Method | Endpoint | On website |
|------|-----------------|--------|----------|------------|
| **32** | `32 — عضوية المعهد — home_membership_contact` | POST | `/api/admin/about-content` | انضم الى عضوية المعهد + انضم الآن |
| **33** | `33 — تواصل معنا (هاتف/عنوان) — contact-info` | PUT | `/api/admin/contact-info` | Phone, fax, email, كود, address, map |

### Step 32 — labels

| Field | Arabic |
|-------|--------|
| `bodyAr.membership.title` | انضم الى عضوية المعهد |
| `bodyAr.membership.subtitle` | لنتشارك في صنع مستقبل واعد لمدننا العربية |
| `bodyAr.membership.cta` | انضم الآن |
| `bodyAr.contact.title` | تواصل معنا |
| `bodyAr.contact.addressTitle` | العنوان |

### Step 33 — contact data

| Field | Value |
|-------|-------|
| `addressAr` | شارع عبدالله بن حذافة السهمي، الحي الدبلوماسي 12521-3803 الرياض، المملكة العربية السعودية |
| `itemsAr[0]` | الهاتف — +966 114802555 |
| `itemsAr[1]` | فاكس — +966 114802666 |
| `itemsAr[2]` | ايميل — Info@Araburban.Org |
| `itemsAr[3]` | كود — 11452 6892 الرياض |
| `mapTitleAr` | موقع المعهد العربي لإنماء المدن |
| `mapEmbedUrlAr` | Google Maps embed URL |

**Public API:** `membershipContact.membership`, `membershipContact.contact`  
**Contact page:** `GET /api/v1/contact`

---

## ملخص التدفق | Flow summary

```
Login
  ↓
01–04  POST /api/admin/hero-slides           → slider
05     POST /api/admin/about-content         → aboutIntro (home_about_intro)
06     POST /api/admin/about-content         → stats title (home_stats)
07–10  POST /api/admin/home-stats            → stats counters
11     POST /api/admin/about-content         → memberCities title
12     PUT  /api/admin/member-cities/stats   → 12 / 400 / 1240
13     POST /api/admin/member-cities/cities  → map pins
14     POST /api/admin/about-content         → programs title (home_programs)
15–17  POST /api/admin/programs              → program cards
18     POST /api/admin/about-content         → media labels (home_media_center)
19–24  POST /api/admin/media (category:news) → news carousel + grid
25     POST /api/admin/about-content         → knowledge button labels
26–28  POST /api/admin/knowledge-categories → مركز المعرفة / مدننا / منصة الاجتماعات
29–31  POST /api/admin/resources             → cards (`knowledgeCategoryId`)
32     POST /api/admin/about-content         → membership labels
33     PUT  /api/admin/contact-info           → phone / address / map
  ↓
GET /api/v1/home  ✓
```

---

## مجلدات Postman ذات صلة | Related Postman folders

| Folder | Purpose |
|--------|---------|
| `00 — بناء الصفحة الرئيسية` | All 33 steps (recommended) |
| `تصنيفات مركز المعرفة` | 3 categories (مركز المعرفة / مدننا / منصة الاجتماعات) |
| `شرائح الهيرو — Hero Slides` | Hero CRUD + examples |
| `إحصائيات الرئيسية — Home Stats` | Stats CRUD + 4 counters |
| `محتوى أقسام الرئيسية — Home About Content` | All `home_*` section keys |
| `بطاقات برامج الرئيسية` | 3 program cards |
| `بطاقات المركز الإعلامي` | 6 news articles |
| `بطاقات مركز المعرفة` | 3 resource cards |
| `المدن الأعضاء — إعداد الخريطة` | Stats + sample city |
| `عضوية وتواصل` | Membership + contact-info |
| `الإعدادات → معلومات التواصل` | Same contact-info body |
| `المدن الأعضاء — Member Cities` | Full cities CRUD + bulk import |

---

## إعادة التوليد | Regenerate

```bash
cd backend
php docs/postman/generate-audi-api-collection.php
```

يُحدَّث: `HOMEPAGE-ADMIN-GUIDE.md`, `AUDI-API.postman_collection.json`, `ADMIN-API.md`, `API.md`
