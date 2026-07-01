<?php

declare(strict_types=1);

function postmanGenerateProgramsAdminGuideMarkdown(): string
{
    $sections = postmanTrainingProgramSections();

    $md = <<<'MD'
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

MD;

    $md .= "\n".postmanProgramsTabContentGuide()."\n\n---\n\n";
    $md .= "\n".postmanProgramsSectionGuide()."\n\n---\n\n";

    $md .= <<<'MD'
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

MD;

    $md .= <<<'MD'
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

MD;

    foreach ($sections as $i => $section) {
        $md .= sprintf(
            "| `%s` | `%s` | `POST /api/admin/program-sections` |\n",
            $section['name'],
            $section['body']['tabKey'],
        );
    }

    $md .= <<<'MD'

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

MD;

    return $md;
}
