<?php

declare(strict_types=1);

/**
 * Maps live site sections (https://audi-ten.vercel.app/ar) to Public GET + Admin folders.
 */

function postmanPublicHomeSiteMap(): string
{
    return <<<'MD'
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
MD;
}

function postmanPublicSitePagesMap(): string
{
    return <<<'MD'
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
MD;
}

function postmanPublicAuditSummary(): string
{
    return <<<'MD'
**نتيجة المراجعة:** جميع مسارات `GET/POST /api/v1/*` في الخادم موجودة في Postman (37 طلباً عاماً). لا توجد واجهة عامة ناقصة مقارنة بـ `routes/api.php`.

**للتحقق بعد بناء المحتوى:** شغّل `GET /api/v1/home` ثم قارن الحقول مع [الموقع المباشر](https://audi-ten.vercel.app/ar).
MD;
}
