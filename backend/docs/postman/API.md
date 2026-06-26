# توثيق واجهات AUDI API

> **المعهد العربي لإنماء المدن** — Arab Urban Development Institute  
> يُولَّد تلقائياً مع مجموعة Postman

---

## الملفات | Documentation Files

| الملف | الوصف |
|-------|--------|
| **[PUBLIC-API.md](./PUBLIC-API.md)** | الواجهة العامة `/api/v1/*` — للموقع والزوار |
| **[ADMIN-API.md](./ADMIN-API.md)** | واجهة الإدارة `/api/admin/*` — لوحة التحكم |
| **[API-ERRORS.md](./API-ERRORS.md)** | أخطاء API — صيغة 400, 401, 404, 422, 500… |
| **[AUDI-API.postman_collection.json](./AUDI-API.postman_collection.json)** | مجموعة Postman كاملة (Public + Admin) |

---

## Public vs Admin

| | Public `/api/v1` | Admin `/api/admin` |
|--|------------------|---------------------|
| **الغرض** | عرض المحتوى للموقع | إنشاء/تعديل المحتوى |
| **المصادقة** | لا | Bearer token (Sanctum) |
| **اللغة** | لغة واحدة لكل طلب | حقول `*Ar` و `*En` معاً |
| **مثال** | `{ "title": "عنوان" }` | `{ "titleAr": "…", "titleEn": "…" }` |

---

## البدء السريع | Quick Start

### Public API
```http
GET /api/v1/home
Accept-Language: ar
```

### Admin API
```http
POST /api/admin/auth/login
Content-Type: application/json

{ "email": "admin@araburban.org", "password": "password" }
```
ثم استخدم `Authorization: Bearer {token}` في باقي طلبات الإدارة.

---

## إعادة التوليد | Regenerate

```bash
php docs/postman/generate-audi-api-collection.php
```

يُحدّث: Postman collection + `PUBLIC-API.md` + `ADMIN-API.md` + `API-ERRORS.md` + `API.md`