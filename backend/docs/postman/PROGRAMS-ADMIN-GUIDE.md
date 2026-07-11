# دليل بناء صفحات البرامج — Programs Admin Guide

> **المعهد العربي لإنماء المدن**  
> يُولَّد تلقائياً من `postman-program-examples.php`  
> مثال: [برنامج التدريب — مركز دعم المدن](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن)

---

## نظرة عامة | Overview

كل **برنامج** (category) له **أقسام** (items/tabs) مربوطة بـ **`programId`**.

| الطبقة | الجدول | Admin endpoint | الربط |
|--------|--------|----------------|-------|
| **Program (category)** | `programs` | `POST /api/admin/programs` | `slug` — save **`{{programId}}`** |
| **Section shell (tab)** | `program_sections` | `POST /api/admin/program-sections` | **`programId`** + `tabKey` + `titleAr/En` + `imageUrl` — save **`{{programSectionId}}`** |
| **Section details (intro/body)** | `program_section_details` | `POST /api/admin/program-section-details` | **`programSectionId`** |
| **Page labels** (`back`, `sectionsLabel`) | — | **Frontend i18n** | `messages/{locale}/programs.json` — **not** admin API for training/partnerships |
| **Page labels** (urban-policies only) | `about_content` | `POST /api/admin/about-content` | `sectionKey`: `program_urban-policies` (optional; frontend falls back to i18n) |
| **Training courses** | `training_courses` | `POST /api/admin/training-courses` | اختياري — أو ضمّ `courses[]` في `program-section-details` (تبويب trainingPrograms) |
| **Experts** | `experts` | `POST /api/admin/experts` | اختياري — أو ضمّ `experts[]` في `program-section-details` (تبويب experts) |

**Postman folders:**
- `Admin` → `البرامج — Programs` → `00 — أدلة البناء` → `بناء برنامج التدريب` (9 steps)
- `Admin` → `البرامج — Programs` → `00 — أدلة البناء` → `بناء برنامج الشراكات` (9 steps)

**All section bodies are loaded from `messages/{ar,en}/programs.json`** — the same source as [audi-ten.vercel.app/ar](https://audi-ten.vercel.app/ar).

**Verify:** `GET /api/v1/programs/training` or `GET /api/v1/programs/partnerships` with `Accept-Language: ar`

### محتوى كل تبويب — Training (?tab=)

كل صفحة تبويب = **قسم** (02/04/06/08) + **تفاصيل** (03/05/07/09) — جسم كامل مثل `programs.json`:

| `?tab=` | خطوات | قسم | تفاصيل | داخل `bodyAr/En` |
|---------|--------|-----|--------|------------------|
| `trainingPrograms` | 02–03 | 02 | 03 | `courses[]` في body |
| `consulting` | 04–05 | 04 | 05 | — |
| `executive` | 06–07 | 06 | 07 | — |
| `experts` | 08–09 | 08 | 09 | `experts[]` في body |

**طلب واحد** `GET /api/v1/programs/training` يدمج كل الأقسام + الدورات + الخبراء.

---


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

---

## Tab item pages — when user clicks اقسام البرنامج

The URLs you shared use **`?tab=`** (frontend routing only). The API does **not** accept `?tab=` — it always returns **all** sections in one response.

| Site URL | Shows | Data from API |
|----------|-------|---------------|
| `/برامجنا/مركز-دعم-المدن` | Hero + tab cards | `title`, `heroIntro`, `tabs[]` |
| `?tab=trainingPrograms` | البرامج التدريبية panel | `sections.trainingPrograms` + courses from `training-courses` |
| `?tab=consulting` | الاستشارات الفنية panel | `sections.consulting` |
| `?tab=executive` | البرنامج التنفيذي panel | `sections.executive` |
| `?tab=experts` | خبراء مركز الدعم panel | `sections.experts` + rows from `experts` |

**Status:** ✅ All 4 tabs are supported — each needs a `program_sections` row with matching `tabKey` and `programId`.

---
## بناء برنامج التدريب (مركز دعم المدن) — Step by step

### Step 1 — Create / update program (category)

```http
POST /api/admin/programs
```

```json
{
  "slug": "training",
  "titleAr": "التدريب و تطوير القدرات",
  "titleEn": "Training & Capacity Building",
  "heroIntroAr": "يهدف مركز دعم المدن إلى تلبية احتياجات الأمانات والبلديات العربية...",
  "sortOrder": 1
}
```

**Save response `id`** — usually **`2`** if programs were seeded in order (urban-policies=1, training=2).

**Arabic URL** `/برامجنا/مركز-دعم-المدن` is frontend routing; API uses slug **`training`**.

> **Page labels:** `back` and `sectionsLabel` are **not** created via admin API. The site uses `messages/ar/programs.json` and `messages/en/programs.json` (same as partnerships).

### Steps 02–09 — Per tab: section → details

Each tab uses **two** requests. Bodies match `messages/ar/programs.json` and `messages/en/programs.json`.

| Steps | tabKey | Notes |
|-------|--------|-------|
| 02–03 | `trainingPrograms` | section → details — **`bodyAr.courses[]`** included (full JSON from programs.json) |
| 04–05 | `consulting` | section → details (`nav[]`, `sections[]`, …) |
| 06–07 | `executive` | section → details (`programs[]`, `topics[]`, …) |
| 08–09 | `experts` | section → details — **`bodyAr.experts[]`** included |

When you save details for `trainingPrograms` or `experts`, the API **syncs** `courses[]` / `experts[]` from the body to their tables automatically.

Optional: edit individual rows later via **01 — مرجع CRUD** → `training-courses` / `experts`.

---

## بناء برنامج الشراكات — Step by step

Live page: [برامجنا/الشراكات](https://audi-ten.vercel.app/ar/برامجنا/الشراكات)

### Step 1 — Create / update program (category)

```http
POST /api/admin/programs
```

```json
{
  "slug": "partnerships",
  "titleAr": "الشراكات",
  "titleEn": "Partnerships",
  "heroIntroAr": "في ظل المتغيرات التنموية التي تشهدها مدننا العربية...",
  "heroIntroEn": "...",
  "cardDescriptionAr": "معاً لنصنع مستقبل حضري أفضل...",
  "sortOrder": 2
}
```

**Save response `id`** as **`{{programId}}`** (Postman test script saves it automatically).

> **Page labels:** `back` and `sectionsLabel` are **not** created via admin API. The site uses `messages/ar/programs.json` and `messages/en/programs.json` (training and partnerships).

### Steps 02–09 — For each section (section → details)

Per tab, run **two** requests. Postman saves **`{{programId}}`** after step 1 and **`{{programSectionId}}`** after each section create.

**A — Section** (`program-sections` — title + image in same body):

```http
POST /api/admin/program-sections
```

```json
{
  "programId": "{{programId}}",
  "tabKey": "euroArabDialogue",
  "titleAr": "حوار المدن العربية الأوروبية",
  "titleEn": "Euro-Arab Cities Dialogue",
  "imageUrl": "/partnerships/euro-arab-dialogue.png",
  "sortOrder": 0
}
```

**B — Details** (`program-section-details`):

```http
POST /api/admin/program-section-details
```

```json
{
  "programSectionId": "{{programSectionId}}",
  "introAr": "منصة حوار موسعة...",
  "introEn": "An expanded dialogue platform...",
  "bodyAr": {},
  "bodyEn": {}
}
```

Training tabs use rich `bodyAr`/`bodyEn` (formats, nav, sections, topics, etc.) — see Postman **بناء برنامج التدريب**.

| tabKey | Update section (title/image) | Update details |
|--------|------------------------------|----------------|
| `euroArabDialogue` | `PUT /api/admin/program-sections/{{programSectionId}}` | `PUT /api/admin/program-section-details/{{programSectionDetailId}}` |
| `secretarySpeaks` | same | same |
| `urbanAwards` | same | same |
| `partnersGuide` | same | same |

### Verify

```http
GET /api/v1/programs/partnerships
Accept-Language: ar
```

Response includes `tabs[]` (4 items) and `sections.euroArabDialogue` with `title`, `intro`, `image`.

---

## All partnerships sections (Postman bodies)

| tabKey | Section | Details |
|--------|---------|---------|
| `euroArabDialogue` | `POST /api/admin/program-sections` | `POST /api/admin/program-section-details` |
| `secretarySpeaks` | `POST /api/admin/program-sections` | `POST /api/admin/program-section-details` |
| `urbanAwards` | `POST /api/admin/program-sections` | `POST /api/admin/program-section-details` |
| `partnersGuide` | `POST /api/admin/program-sections` | `POST /api/admin/program-section-details` |

---

## All training sections (Postman bodies)

| Postman request | tabKey | Endpoint |
|-----------------|--------|----------|
| `trainingPrograms` | `trainingPrograms` | `POST /api/admin/program-sections` |
| `consulting` | `consulting` | `POST /api/admin/program-sections` |
| `executive` | `executive` | `POST /api/admin/program-sections` |
| `experts` | `experts` | `POST /api/admin/program-sections` |

---

## Public API response shape

```http
GET /api/v1/programs/training
Accept-Language: ar
```

```json
{
  "slug": "training",
  "title": "التدريب و تطوير القدرات",
  "heroIntro": "...",
  "sectionsLabel": "اقسام البرنامج",
  "tabs": [
    { "id": "trainingPrograms", "label": "البرامج التدريبية" },
    { "id": "consulting", "label": "الاستشارات الفنية ونقل الخبرات" }
  ],
  "sections": {
    "trainingPrograms": { "title": "...", "intro": "...", "courses": [...] },
    "consulting": { ... },
    "executive": { ... },
    "experts": { "experts": [...] }
  }
}
```

---

## Regenerate

```bash
cd backend
php docs/postman/generate-audi-api-collection.php
```
