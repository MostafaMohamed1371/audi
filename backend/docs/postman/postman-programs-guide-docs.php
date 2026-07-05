<?php

declare(strict_types=1);

function postmanGenerateProgramsAdminGuideMarkdown(): string
{
    $sections = postmanTrainingProgramSections();
    $partnershipSections = postmanPartnershipsProgramSectionPairs();

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

MD;

    foreach ($partnershipSections as $section) {
        $md .= sprintf(
            "| `%s` | `POST /api/admin/program-sections` | `POST /api/admin/program-section-details` |\n",
            $section['tabKey'],
        );
    }

    $md .= <<<'MD'

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
