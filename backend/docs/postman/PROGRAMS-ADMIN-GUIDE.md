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
| **Training courses** | `training_courses` | `POST /api/admin/training-courses` | تظهر في tab `trainingPrograms` |
| **Experts** | `experts` | `POST /api/admin/experts` | تظهر في tab `experts` |

**Postman folders:**
- `Admin` → `البرامج — Programs` → `00 — أدلة البناء` → `بناء برنامج التدريب` (18 steps)
- `Admin` → `البرامج — Programs` → `00 — أدلة البناء` → `بناء برنامج الشراكات` (9 steps)

**All section bodies are loaded from `messages/{ar,en}/programs.json`** — the same source as [audi-ten.vercel.app/ar](https://audi-ten.vercel.app/ar).

**Verify:** `GET /api/v1/programs/training` or `GET /api/v1/programs/partnerships` with `Accept-Language: ar`

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

---


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
| 02–03 | `trainingPrograms` | `program-sections` (title+image) → `program-section-details` (`formats`, `coursesTitle`, …) |
| 04–05 | `consulting` | section → details (`nav[]`, `sections[]`, …) |
| 06–07 | `executive` | section → details (`programs[]`, `topics[]`, …) |
| 08–09 | `experts` | section → details (`title` only — cards in steps 16–18) |

### Steps 10–15 — Training courses (trainingPrograms grid)

```http
POST /api/admin/training-courses
```

Six rows (3 unique titles × 2) for the **البرامج التدريبية ٢٠٢٣ – ٢٠٢٤** list. Merged into `sections.trainingPrograms.courses[]`.

### Steps 16–18 — Experts (experts carousel)

```http
POST /api/admin/experts
```

Three expert cards with `nameAr`, `specialtyAr`, `imageUrl`. Merged into `sections.experts.experts[]`.

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
