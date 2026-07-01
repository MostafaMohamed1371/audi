# دليل بناء صفحات البرامج — Programs Admin Guide

> **المعهد العربي لإنماء المدن**  
> يُولَّد تلقائياً من `postman-program-examples.php`  
> مثال: [برنامج التدريب — مركز دعم المدن](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن)

---

## نظرة عامة | Overview

كل **برنامج** (category) له **أقسام** (items/tabs) مربوطة بـ **`programId`**.

| الطبقة | الجدول | Admin endpoint | الربط |
|--------|--------|----------------|-------|
| **Program** | `programs` | `POST /api/admin/programs` | `slug`: `training`, `urban-policies`, `partnerships` |
| **Section tab** | `program_sections` | `POST /api/admin/program-sections` | **`programId`** + `tabKey` |
| **Page labels** | `about_content` | `POST /api/admin/about-content` | `sectionKey`: `program_{slug}` |
| **Training courses** | `training_courses` | `POST /api/admin/training-courses` | تظهر في tab `trainingPrograms` |
| **Experts** | `experts` | `POST /api/admin/experts` | تظهر في tab `experts` |

**Postman folder:** `Admin` → `البرامج — Programs` → `00 — بناء برنامج التدريب` (15 steps)

**Verify:** `GET /api/v1/programs/training` with `Accept-Language: ar`

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

### Step 2 — Page labels (اقسام البرنامج)

```http
POST /api/admin/about-content
```

```json
{
  "sectionKey": "program_training",
  "bodyAr": { "back": "رجوع", "sectionsLabel": "اقسام البرنامج" }
}
```

### Steps 03–06 — Create sections (one per `?tab=`)

```http
POST /api/admin/program-sections
```

| Step | tabKey | Live URL | What the body must include |
|------|--------|----------|----------------------------|
| 03 | `trainingPrograms` | [?tab=trainingPrograms](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=trainingPrograms) | `introAr`, `imageUrl`, `bodyAr.heroImage`, `bodyAr.formats[]`, `bodyAr.coursesTitle`, `bodyAr.coursesImage` |
| 04 | `consulting` | [?tab=consulting](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=consulting) | `introAr`, `imageUrl`, `bodyAr.nav[]`, `bodyAr.detailImage`, `bodyAr.sections[]` |
| 05 | `executive` | [?tab=executive](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=executive) | `introAr`, `bodyAr.heroVideo`, `bodyAr.programs[]`, `bodyAr.topics[]` |
| 06 | `experts` | [?tab=experts](https://audi-ten.vercel.app/ar/برامجنا/مركز-دعم-المدن?tab=experts) | `bodyAr.title` — expert photos in steps **13–15** (`imageUrl`: `/emp/1.png`…) |

Use full Arabic text from Postman step bodies (matches `messages/ar/programs.json`).

### Steps 07–12 — Training courses (trainingPrograms grid)

```http
POST /api/admin/training-courses
```

Six rows (3 unique titles × 2) for the **البرامج التدريبية ٢٠٢٣ – ٢٠٢٤** list. Merged into `sections.trainingPrograms.courses[]`.

### Steps 13–15 — Experts (experts carousel)

```http
POST /api/admin/experts
```

Three expert cards with `nameAr`, `specialtyAr`, `imageUrl`. Merged into `sections.experts.experts[]`.

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
