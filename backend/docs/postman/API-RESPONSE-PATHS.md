# مسارات الملفات في الاستجابة — API Response File Paths

> **المعهد العربي لإنماء المدن** — كيف تظهر روابط الصور والملفات في JSON
> يُولَّد من `postman-response-paths-docs.php`

---

## نظرة عامة | Overview

الواجهة **العامة** (`/api/v1`) تُرجع روابط **كاملة (absolute URL)** جاهزة للاستخدام في `<img src>` أو التحميل.
لوحة **الإدارة** (`/api/admin`) تخزن مسارات نسبية أو روابط رفع.

| البيئة | المتغير | القيمة الحالية |
|--------|---------|----------------|
| Frontend (أصول ثابتة) | `FRONTEND_URL` | `https://audi-ten.vercel.app` |
| Backend (رفع Laravel) | `APP_URL` | `http://localhost:8000` |

### قواعد التحويل | Transformation rules

| ما يُخزَّن في Admin | ما يُرجع في Public Response |
|---------------------|------------------------------|
| `/slider/1.png` | `https://audi-ten.vercel.app/slider/1.png` |
| `/emp/2.png` | `https://audi-ten.vercel.app/emp/2.png` |
| `/blog/3.png` | `https://audi-ten.vercel.app/blog/3.png` |
| `/client/logo.png` | `https://audi-ten.vercel.app/client/logo.png` |
| `/our-sources/1.png` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `/storage/uploads/…/file.jpg` | `http://localhost:8000/storage/uploads/…/file.jpg` |
| `https://…` (مطلق مسبقاً) | نفس الرابط دون تغيير |

**مهم:** لا يُرجع API أسماء ملفات عارية مثل `1.png` — دائماً `/مجلد/ملف` أو `https://…`

### مجلدات الأصول الثابتة | Static asset directories

| المجلد | الاستخدام |
|--------|-----------|
| `/slider/` | شرائح الهيرو في الرئيسية |
| `/emp/` | صور الفريق، المجلس، القيادة، الخبراء |
| `/client/` | شعارات الشركاء |
| `/blog/` | صور الأخبار والمركز الإعلامي |
| `/our-sources/` | صور المصادر والتقارير |
| `/focus-areas/` | صور مجالات التركيز |
| `/vision-mission/` | صور الرؤية والرسالة |
| `/storage/uploads/` | ملفات مرفوعة عبر Admin Upload |

---

## جدول الحقول | Field mapping by endpoint

| Endpoint | حقل الاستجابة | حقل Admin | مثال المسار الكامل |
|----------|---------------|-----------|---------------------|
| `GET /api/v1/home` | `slider[].imageUrl` | `hero-slides.imageUrl` | `https://audi-ten.vercel.app/slider/1.png` |
| `GET /api/v1/home` | `mediaCenter.featured[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |
| `GET /api/v1/home` | `mediaCenter.items[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/2.png` |
| `GET /api/v1/home` | `knowledgeCenter.items[].image` | `resources.imageUrl` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `GET /api/v1/about/leadership/director` | `image` | `leadership.imageUrl` | `https://audi-ten.vercel.app/emp/2.png` |
| `GET /api/v1/about/leadership/president` | `image` | `leadership.imageUrl` | `https://audi-ten.vercel.app/emp/1.png` |
| `GET /api/v1/about/advisory-board` | `members[].image` | `advisory-board.imageUrl` | `https://audi-ten.vercel.app/emp/1.png` |
| `GET /api/v1/about/team` | `sections[].members[].image` | `team-members.imageUrl` | `https://audi-ten.vercel.app/emp/3.png` |
| `GET /api/v1/about/structure` | `imageUrl` | `about-content.body.*.imageUrl` | `https://audi-ten.vercel.app/operational-structure.png` |
| `GET /api/v1/about/partners` | `featured[].image` | `partners.logoUrl` | `https://audi-ten.vercel.app/client/un-habitat.png` |
| `GET /api/v1/about/partners` | `categories[].logos[].image` | `partners.logoUrl` | `https://audi-ten.vercel.app/client/world-bank.png` |
| `GET /api/v1/about/vision-mission` | `vision.image` | `about-content.body.visionImage` | `https://audi-ten.vercel.app/vision-mission/1.png` |
| `GET /api/v1/about/vision-mission` | `mission.image` | `about-content.body.missionImage` | `https://audi-ten.vercel.app/vision-mission/2.png` |
| `GET /api/v1/strategy/focus-areas` | `items[].listImage` | `focus-areas.listImageUrl` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-list.png` |
| `GET /api/v1/strategy/focus-areas` | `items[].detailImage` | `focus-areas.detailImageUrl` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-detail.png` |
| `GET /api/v1/resources` | `data[].image` | `resources.imageUrl` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `GET /api/v1/resources` | `data[].downloadHref` | `resources.fileUrl` | `http://localhost:8000/storage/uploads/2026/06/report.pdf` |
| `GET /api/v1/media/news` | `data[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |
| `GET /api/v1/media/{category}/{slug}` | `image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |
| `GET /api/v1/media/{category}/{slug}` | `pdfHref` | `media.pdfUrl` | `http://localhost:8000/storage/uploads/2026/06/newsletter.pdf` |
| `GET /api/v1/programs/training` | `sections[].image` | `program-sections.imageUrl` | `https://audi-ten.vercel.app/programs/training-section.png` |
| `GET /api/v1/programs/training` | `experts[].image` | `experts.imageUrl` | `https://audi-ten.vercel.app/emp/4.png` |
| `POST /api/admin/uploads` | `data.url` | `(response only)` | `http://localhost:8000/storage/uploads/2026/06/uuid.jpg` |
| `POST /api/admin/uploads` | `data.path` | `(response only)` | `uploads/2026/06/uuid.jpg` |

---

## تفاصيل حسب القسم | Sections

### GET /api/v1/home

**الغرض:** الصفحة الرئيسية — سلايدر، مركز إعلامي، مركز معرفة

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `slider[].imageUrl` | `hero-slides.imageUrl` | `https://audi-ten.vercel.app/slider/1.png` |
| `mediaCenter.featured[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |
| `mediaCenter.items[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/2.png` |
| `knowledgeCenter.items[].image` | `resources.imageUrl` | `https://audi-ten.vercel.app/our-sources/1.png` |

### GET /api/v1/about/leadership/director

**الغرض:** كلمة المدير العام

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `image` | `leadership.imageUrl` | `https://audi-ten.vercel.app/emp/2.png` |

### GET /api/v1/about/leadership/president

**الغرض:** كلمة رئيس المجلس

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `image` | `leadership.imageUrl` | `https://audi-ten.vercel.app/emp/1.png` |

### GET /api/v1/about/advisory-board

**الغرض:** المجلس الاستشاري

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `members[].image` | `advisory-board.imageUrl` | `https://audi-ten.vercel.app/emp/1.png` |

### GET /api/v1/about/team

**الغرض:** فريق العمل

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `sections[].members[].image` | `team-members.imageUrl` | `https://audi-ten.vercel.app/emp/3.png` |

### GET /api/v1/about/structure

**الغرض:** الهيكل التنظيمي

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `imageUrl` | `about-content.body.*.imageUrl` | `https://audi-ten.vercel.app/operational-structure.png` |

### GET /api/v1/about/partners

**الغرض:** الشركاء

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `featured[].image` | `partners.logoUrl` | `https://audi-ten.vercel.app/client/un-habitat.png` |
| `categories[].logos[].image` | `partners.logoUrl` | `https://audi-ten.vercel.app/client/world-bank.png` |

### GET /api/v1/about/vision-mission

**الغرض:** الرؤية والرسالة

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `vision.image` | `about-content.body.visionImage` | `https://audi-ten.vercel.app/vision-mission/1.png` |
| `mission.image` | `about-content.body.missionImage` | `https://audi-ten.vercel.app/vision-mission/2.png` |

### GET /api/v1/strategy/focus-areas

**الغرض:** مجالات التركيز

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `items[].listImage` | `focus-areas.listImageUrl` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-list.png` |
| `items[].detailImage` | `focus-areas.detailImageUrl` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-detail.png` |

### GET /api/v1/resources

**الغرض:** المصادر والتقارير

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `data[].image` | `resources.imageUrl` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `data[].downloadHref` | `resources.fileUrl` | `http://localhost:8000/storage/uploads/2026/06/report.pdf` |

### GET /api/v1/media/news

**الغرض:** الأخبار

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `data[].image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |

### GET /api/v1/media/{category}/{slug}

**الغرض:** تفاصيل مقال إعلامي

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `image` | `media.imageUrl` | `https://audi-ten.vercel.app/blog/1.png` |
| `pdfHref` | `media.pdfUrl` | `http://localhost:8000/storage/uploads/2026/06/newsletter.pdf` |

### GET /api/v1/programs/training

**الغرض:** برنامج التدريب — أقسام وخبراء

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `sections[].image` | `program-sections.imageUrl` | `https://audi-ten.vercel.app/programs/training-section.png` |
| `experts[].image` | `experts.imageUrl` | `https://audi-ten.vercel.app/emp/4.png` |

### POST /api/admin/uploads

**الغرض:** رفع ملف — يُرجع رابطاً مطلقاً

| حقل JSON | Admin | مثال في الاستجابة |
|----------|-------|-------------------|
| `data.url` | `(response only)` | `http://localhost:8000/storage/uploads/2026/06/uuid.jpg` |
| `data.path` | `(response only)` | `uploads/2026/06/uuid.jpg` |

---

## أمثلة حية من API | Live response samples

### GET /api/v1/home

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `slider[].imageUrl` | `https://audi-ten.vercel.app/slider/1.png` |
| `slider[].imageUrl` | `https://audi-ten.vercel.app/slider/2.png` |
| `slider[].imageUrl` | `https://audi-ten.vercel.app/slider/3.png` |
| `slider[].imageUrl` | `https://audi-ten.vercel.app/slider/4.png` |
| `mediaCenter.featured[].image` | `https://audi-ten.vercel.app/blog/1.png` |
| `mediaCenter.featured[].image` | `https://audi-ten.vercel.app/blog/2.png` |
| `mediaCenter.featured[].image` | `https://audi-ten.vercel.app/blog/1.png` |
| `mediaCenter.featured[].image` | `https://audi-ten.vercel.app/blog/4.png` |
| `mediaCenter.items[].image` | `https://audi-ten.vercel.app/blog/3.png` |
| `mediaCenter.items[].image` | `https://audi-ten.vercel.app/blog/2.png` |
| `knowledgeCenter.items[].image` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `knowledgeCenter.items[].image` | `https://audi-ten.vercel.app/our-sources/4.png` |
| … | *(المزيد من الحقول في الاستجابة)* |

### GET /api/v1/about/advisory-board

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `members[].image` | `https://audi-ten.vercel.app/emp/1.png` |
| `members[].image` | `https://audi-ten.vercel.app/emp/2.png` |
| `members[].image` | `https://audi-ten.vercel.app/emp/3.png` |
| `members[].image` | `https://audi-ten.vercel.app/emp/4.png` |
| `members[].image` | `https://audi-ten.vercel.app/emp/5.png` |
| `members[].image` | `https://audi-ten.vercel.app/emp/6.png` |

### GET /api/v1/about/team

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/2.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/7.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/8.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/9.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/10.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/11.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/12.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/13.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/14.png` |
| `sections[].members[].image` | `https://audi-ten.vercel.app/emp/15.png` |

### GET /api/v1/about/partners

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `featured[].image` | `https://audi-ten.vercel.app/client/1.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/2.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/3.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/4.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/5.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/6.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/7.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/8.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/9.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/10.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/11.png` |
| `featured[].image` | `https://audi-ten.vercel.app/client/12.png` |
| … | *(المزيد من الحقول في الاستجابة)* |

### GET /api/v1/about/leadership/director

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `image` | `https://audi-ten.vercel.app/emp/2.png` |

### GET /api/v1/strategy/focus-areas

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `items[].listImage` | `https://audi-ten.vercel.app/focus-areas/governance-list.png` |
| `items[].detailImage` | `https://audi-ten.vercel.app/focus-areas/governance-detail.png` |
| `items[].listImage` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-list.png` |
| `items[].detailImage` | `https://audi-ten.vercel.app/focus-areas/urban-resilience-detail.png` |
| `items[].listImage` | `https://audi-ten.vercel.app/focus-areas/local-economic-list.png` |
| `items[].detailImage` | `https://audi-ten.vercel.app/focus-areas/local-economic-detail.png` |
| `items[].listImage` | `https://audi-ten.vercel.app/focus-areas/cities-for-people-list.png` |
| `items[].detailImage` | `https://audi-ten.vercel.app/focus-areas/cities-for-people-detail.png` |

### GET /api/v1/resources

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `data[].image` | `https://audi-ten.vercel.app/our-sources/1.png` |
| `data[].image` | `https://audi-ten.vercel.app/our-sources/4.png` |
| `data[].image` | `https://audi-ten.vercel.app/our-sources/2.png` |
| `data[].image` | `https://audi-ten.vercel.app/our-sources/3.png` |
| `meta.links[].url` | `http://localhost:8000/api/v1/resources?page=1` |
| `meta.path` | `http://localhost:8000/api/v1/resources` |

### GET /api/v1/media/news

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `data[].image` | `https://audi-ten.vercel.app/blog/1.png` |
| `data[].image` | `https://audi-ten.vercel.app/blog/2.png` |
| `data[].image` | `https://audi-ten.vercel.app/blog/1.png` |
| `data[].image` | `https://audi-ten.vercel.app/blog/4.png` |
| `data[].image` | `https://audi-ten.vercel.app/blog/3.png` |
| `data[].image` | `https://audi-ten.vercel.app/blog/2.png` |
| `meta.links[].url` | `http://localhost:8000/api/v1/media/news?page=1` |
| `meta.path` | `http://localhost:8000/api/v1/media/news` |

### GET /api/v1/programs/training

| حقل JSON | القيمة الكاملة في الاستجابة |
|----------|------------------------------|
| `sections.experts.experts[].image` | `https://audi-ten.vercel.app/emp/1.png` |
| `sections.experts.experts[].image` | `https://audi-ten.vercel.app/emp/2.png` |
| `sections.experts.experts[].image` | `https://audi-ten.vercel.app/emp/3.png` |

---

## مثال JSON — Home | Sample response snippet

```json
{
  "slider": [
    {
      "title": "تطوير تقني للمدن العربية",
      "imageUrl": "https://audi-ten.vercel.app/slider/1.png"
    }
  ],
  "mediaCenter": {
    "featured": [
      { "title": "…", "image": "https://audi-ten.vercel.app/blog/1.png" }
    ]
  },
  "knowledgeCenter": {
    "items": [
      { "title": "…", "image": "https://audi-ten.vercel.app/our-sources/1.png" }
    ]
  }
}
```

## مثال JSON — Upload | Admin upload response

```json
{
  "data": {
    "id": 1,
    "url": "http://localhost:8000/storage/uploads/2026/06/abc-uuid.jpg",
    "path": "uploads/2026/06/abc-uuid.jpg",
    "mimeType": "image/jpeg",
    "originalName": "photo.jpg"
  }
}
```

**استخدم `data.url`** في حقول Admin مثل `imageUrl` — سيظهر نفس الرابط في Public API.

---

## التحقق | Verify

```bash
php docs/postman/smoke-test-image-endpoints.php http://127.0.0.1:8000 ar
```

يتحقق أن كل حقول الصور تبدأ بـ `/` أو `http` — وليس اسم ملف عارٍ.
