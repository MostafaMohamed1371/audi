<?php

declare(strict_types=1);

/**
 * Arabic labels for Postman admin collection: folders, query params, and JSON body fields.
 */

/** @return array<string, string> */
function postmanArabicFieldMap(): array
{
    return [
        // Auth
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'name' => 'الاسم',
        'city' => 'المدينة',
        'title' => 'العنوان',
        'author' => 'المؤلف',

        // Common bilingual
        'titleAr' => 'العنوان بالعربية',
        'titleEn' => 'العنوان بالإنجليزية',
        'nameAr' => 'الاسم بالعربية',
        'nameEn' => 'الاسم بالإنجليزية',
        'descriptionAr' => 'الوصف بالعربية',
        'descriptionEn' => 'الوصف بالإنجليزية',
        'subtitleAr' => 'العنوان الفرعي بالعربية',
        'subtitleEn' => 'العنوان الفرعي بالإنجليزية',
        'bodyAr' => 'محتوى JSON بالعربية (فقرات، تسميات، أقسام)',
        'bodyEn' => 'محتوى JSON بالإنجليزية',
        'contentAr' => 'المحتوى النصي بالعربية',
        'contentEn' => 'المحتوى النصي بالإنجليزية',
        'introAr' => 'المقدمة بالعربية',
        'introEn' => 'المقدمة بالإنجليزية',
        'summaryAr' => 'الملخص بالعربية',
        'summaryEn' => 'الملخص بالإنجليزية',
        'questionAr' => 'السؤال بالعربية',
        'questionEn' => 'السؤال بالإنجليزية',
        'answerAr' => 'الإجابة بالعربية',
        'answerEn' => 'الإجابة بالإنجليزية',
        'quoteAr' => 'اقتباس/كلمة بالعربية',
        'quoteEn' => 'اقتباس/كلمة بالإنجليزية',
        'bioAr' => 'السيرة/نبذة بالعربية',
        'bioEn' => 'السيرة/نبذة بالإنجليزية',
        'roleAr' => 'المسمى الوظيفي بالعربية',
        'roleEn' => 'المسمى الوظيفي بالإنجليزية',
        'positionAr' => 'المنصب بالعربية',
        'positionEn' => 'المنصب بالإنجليزية',
        'honorificAr' => 'اللقب/التحية بالعربية',
        'honorificEn' => 'اللقب/التحية بالإنجليزية',
        'paragraphsAr' => 'فقرات النص بالعربية (مصفوفة)',
        'paragraphsEn' => 'فقرات النص بالإنجليزية (مصفوفة)',
        'highlightAr' => 'التمييز/الشارة بالعربية',
        'highlightEn' => 'التمييز/الشارة بالإنجليزية',
        'textAr' => 'النص بالعربية',
        'textEn' => 'النص بالإنجليزية',
        'countAr' => 'العدد/التعداد بالعربية (مثل: 3 دورات)',
        'countEn' => 'العدد/التعداد بالإنجليزية',
        'locationAr' => 'الموقع بالعربية',
        'locationEn' => 'الموقع بالإنجليزية',
        'cityAr' => 'المدينة بالعربية',
        'cityEn' => 'المدينة بالإنجليزية',
        'countryAr' => 'الدولة بالعربية',
        'countryEn' => 'الدولة بالإنجليزية',
        'infoAr' => 'معلومات إضافية بالعربية',
        'infoEn' => 'معلومات إضافية بالإنجليزية',
        'heroIntroAr' => 'مقدمة صفحة البرنامج بالعربية',
        'heroIntroEn' => 'مقدمة صفحة البرنامج بالإنجليزية',
        'cardDescriptionAr' => 'وصف بطاقة البرنامج في الرئيسية (عربي)',
        'cardDescriptionEn' => 'وصف بطاقة البرنامج في الرئيسية (إنجليزي)',
        'introTitleAr' => 'عنوان مقدمة الاستراتيجية بالعربية',
        'introTitleEn' => 'عنوان مقدمة الاستراتيجية بالإنجليزية',
        'introSubtitleAr' => 'العنوان الفرعي للاستراتيجية بالعربية',
        'introSubtitleEn' => 'العنوان الفرعي للاستراتيجية بالإنجليزية',
        'bookletTitleAr' => 'عنوان الكتيب بالعربية',
        'bookletTitleEn' => 'عنوان الكتيب بالإنجليزية',
        'bookletPdfUrl' => 'رابط PDF الكتيب الاستراتيجي',
        'columnsAr' => 'أعمدة المخطط بالعربية (مصفوفة أو null)',
        'columnsEn' => 'أعمدة المخطط بالإنجليزية',

        // Images & files
        'imageUrl' => 'رابط الصورة (مسار كامل: /emp/1.png أو رابط رفع)',
        'logoUrl' => 'رابط شعار الشريك (/client/…)',
        'listImageUrl' => 'صورة القائمة (/focus-areas/…-list.png)',
        'detailImageUrl' => 'صورة التفاصيل (/focus-areas/…-detail.png)',
        'fileUrl' => 'رابط ملف PDF أو مرفق',
        'pdfUrl' => 'رابط PDF للمقال أو النشرة',
        'imageAltAr' => 'النص البديل للصورة بالعربية',
        'imageAltEn' => 'النص البديل للصورة بالإنجليزية',

        // Identifiers
        'slug' => 'المعرّف اللatinي للرابط (مثل: training)',
        'sectionKey' => 'مفتاح القسم (مثل: home_about_intro, institute)',
        'key' => 'المفتاح الفريد للسجل',
        'type' => 'النوع (director|president، publications|cities|organizations، …)',
        'category' => 'التصنيف (news, newsletter, city_meetings, membership, …)',
        'tabKey' => 'مفتاح تبويب البرنامج (trainingPrograms, experts, …)',
        'itemKey' => 'مفتاح عنصر المخطط (vision, mission, …)',
        'platform' => 'منصة التواصل (linkedin, twitter, …)',
        'group' => 'مجموعة الإعداد (general, contact)',
        'employmentType' => 'نوع التوظيف (full_time, part_time, contract)',
        'resourceType' => 'نوع المورد (report, study, …)',
        'status' => 'الحالة (new, read, approved, …)',

        // Relations & ordering
        'programId' => 'معرّف البرنامج (FK)',
        'teamSectionId' => 'معرّف قسم الفريق (FK)',
        'partnerCategoryId' => 'معرّف تصنيف الشريك (FK)',
        'focusAreaId' => 'معرّف مجال التركيز (FK)',
        'jobOpeningId' => 'معرّف الوظيفة (اختياري)',
        'sortOrder' => 'ترتيب العرض (0 = الأول)',
        'isActive' => 'نشط؟ (true/false)',
        'isPublished' => 'منشور للعامة؟ (true/false)',
        'isFeatured' => 'مميز في الواجهة؟ (true/false)',
        'autoCalculate' => 'حساب تلقائي من قاعدة البيانات؟',

        // Contact info
        'addressLabelAr' => 'تسمية العنوان بالعربية',
        'addressLabelEn' => 'تسمية العنوان بالإنجليزية',
        'addressAr' => 'العنوان الكامل بالعربية',
        'addressEn' => 'العنوان الكامل بالإنجليزية',
        'mapTitleAr' => 'عنوان الخريطة بالعربية',
        'mapTitleEn' => 'عنوان الخريطة بالإنجليزية',
        'mapEmbedUrlAr' => 'رابط تضمين خريطة Google (عربي)',
        'mapEmbedUrlEn' => 'رابط تضمين خريطة Google (إنجليزي)',
        'itemsAr' => 'عناصر التواصل بالعربية [{label, value, type, href}]',
        'itemsEn' => 'عناصر التواصل بالإنجليزية',

        // Stats & geo
        'value' => 'القيمة الرقمية أو النصية (+25)',
        'label' => 'التسمية (للإحصائيات: {ar, en})',
        'labelAr' => 'تسمية الإحصائية بالعربية',
        'labelEn' => 'تسمية الإحصائية بالإنجليزية',
        'unit' => 'الوحدة (للإحصائيات: {ar, en})',
        'countryCode' => 'رمز الدولة ISO (SA, AE, …)',
        'latitude' => 'خط العرض',
        'longitude' => 'خط الطول',
        'citySize' => 'حجم المدينة (large|medium|small)',
        'number' => 'الرقم الترتيبي (01, 02, …)',
        'tagsAr' => 'الوسوم بالعربية (مصفوفة)',
        'tagsEn' => 'الوسوم بالإنجليزية (مصفوفة)',
        'authorsAr' => 'المؤلفون بالعربية (مصفوفة)',
        'authorsEn' => 'المؤلفون بالإنجليزية (مصفوفة)',
        'eventTime' => 'وقت الفعالية (مثل: 10:00 - 14:00)',
        'publishedDate' => 'تاريخ النشر (YYYY-MM-DD)',
        'effectiveDate' => 'تاريخ سريان الصفحة القانونية',
        'year' => 'السنة',
        'startDate' => 'تاريخ البداية',
        'endDate' => 'تاريخ النهاية',

        // Slugs (media)
        'slugAr' => 'الرابط العربي (slug)',
        'slugEn' => 'الرابط الإنجليزي (slug)',

        // Settings
        'valueAr' => 'قيمة الإعداد بالعربية',
        'valueEn' => 'قيمة الإعداد بالإنجليزية',
        'url' => 'الرابط (URL)',
        'icon' => 'أيقونة المنصة',

        // Forms (public → admin review)
        'fullName' => 'الاسم الكامل',
        'phone' => 'رقم الهاتف',
        'message' => 'نص الرسالة',
        'organizationName' => 'اسم الجهة/المؤسسة',
        'contactName' => 'اسم مسؤول التواصل',
        'countryCode' => 'رمز الدولة',
        'coverLetter' => 'خطاب التقديم',
        'cvUrl' => 'رابط السيرة الذاتية',
        'locale' => 'لغة المشترك (ar|en)',
        'payload' => 'بيانات المساهمة (حسب النوع)',
        'items' => 'قائمة العناصر (إعادة ترتيب أو إحصائيات)',

        // Import
        'cities' => 'مصفوفة المدن للاستيراد',
        'upsertBy' => 'حقول المطابقة عند التحديث',
        'file' => 'ملف للرفع (صورة أو PDF)',

        // Nested contact item
        'label' => 'التسمية (مثل: البريد الإلكتروني)',
        'href' => 'رابط اختياري (mailto:, tel:)',
    ];
}

/** @return array<string, string> */
function postmanArabicQueryMap(): array
{
    return [
        'page' => 'رقم الصفحة',
        'limit' => 'عدد النتائج في الصفحة',
        'search' => 'بحث نصي',
        'group' => 'مجموعة الإعدادات (general, contact)',
        'slug' => 'المعرّف اللatinي',
        'category' => 'التصنيف',
        'status' => 'تصفية حسب الحالة',
        'countryCode' => 'تصفية برمز الدولة',
        'type' => 'نوع المساهمة أو المورد',
        'focusArea' => 'مجال التركيز',
        'year' => 'السنة',
        'month' => 'الشهر',
        'tab' => 'تبويب الدليل (cities|projects|organizations|publications)',
        'mimeType' => 'نوع MIME للملفات',
    ];
}

function postmanArabicFieldLabel(string $key): string
{
    $map = postmanArabicFieldMap();

    return $map[$key] ?? $key;
}

function postmanArabicQueryLabel(string $key): string
{
    $map = postmanArabicQueryMap();

    return $map[$key] ?? postmanArabicFieldLabel($key);
}

/**
 * Flatten body keys for documentation table.
 *
 * @return list<array{field: string, desc: string, example: string}>
 */
function postmanFlattenBodyFields(mixed $value, string $prefix = ''): array
{
    $rows = [];

    if (! is_array($value)) {
        return $rows;
    }

    foreach ($value as $key => $child) {
        if (! is_string($key) && ! is_int($key)) {
            continue;
        }

        $field = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

        if (is_array($child) && array_is_list($child) && ($child[0] ?? null) !== null && ! is_array($child[0])) {
            $rows[] = [
                'field' => $field,
                'desc' => postmanArabicFieldLabel((string) $key),
                'example' => json_encode($child, JSON_UNESCAPED_UNICODE),
            ];
        } elseif (is_array($child) && ! array_is_list($child)) {
            $rows = array_merge($rows, postmanFlattenBodyFields($child, $field));
        } else {
            $example = is_scalar($child) || $child === null
                ? (string) json_encode($child, JSON_UNESCAPED_UNICODE)
                : json_encode($child, JSON_UNESCAPED_UNICODE);
            $rows[] = [
                'field' => $field,
                'desc' => postmanArabicFieldLabel((string) $key),
                'example' => $example,
            ];
        }
    }

    return $rows;
}

function postmanArabicBodyDocs(mixed $body): string
{
    if ($body === null) {
        return '';
    }

    $data = is_array($body) ? $body : json_decode((string) $body, true);

    if (! is_array($data)) {
        return '';
    }

    $rows = postmanFlattenBodyFields($data);

    if ($rows === []) {
        return '';
    }

    $lines = ["### المعاملات (Body Parameters)", '', '| الحقل | الوصف | مثال |', '|-------|--------|------|'];

    foreach ($rows as $row) {
        $example = str_replace('|', '\\|', $row['example']);
        if (mb_strlen($example) > 60) {
            $example = mb_substr($example, 0, 57).'…';
        }
        $lines[] = "| `{$row['field']}` | {$row['desc']} | {$example} |";
    }

    return implode("\n", $lines);
}

function postmanAppendBodyDocs(string $description, mixed $body, bool $enabled = true): string
{
    if (! $enabled || $body === null) {
        return $description;
    }

    $docs = postmanArabicBodyDocs($body);

    if ($docs === '') {
        return $description;
    }

    return trim($description."\n\n".$docs);
}

function postmanAdminFolder(string $nameAr, string $nameEn, array $items, string $description = ''): array
{
    $name = $nameEn !== '' ? "{$nameAr} — {$nameEn}" : $nameAr;

    return folder($name, $items, $description);
}

function postmanApplyArabicQueryDescriptions(array $query): array
{
    return array_map(static function (array $q) {
        if (empty($q['description'])) {
            $q['description'] = postmanArabicQueryLabel((string) $q['key']);
        }

        return $q;
    }, $query);
}
