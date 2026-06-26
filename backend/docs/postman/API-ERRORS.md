# أخطاء API — AUDI API Error Responses

> صيغة JSON موحّدة لجميع أخطاء `/api/*`  
> Unified JSON format for all `/api/*` errors

---

## الصيغة العامة | Response shape

كل خطأ يُرجع:

```json
{
  "status": 401,
  "message": "Unauthenticated.",
  "error": "unauthenticated"
}
```

| الحقل | الوصف |
|-------|--------|
| `status` | رمز HTTP (نفس حالة الاستجابة) |
| `message` | رسالة قصيرة للمطوّر / الواجهة |
| `error` | معرّف ثابت للمعالجة البرمجية |
| `errors` | (اختياري) تفاصيل حقول التحقق — عند `validation_error` فقط |
| `debug` | (اختياري) تفاصيل تقنية — فقط عند `APP_DEBUG=true` |

---

## جدول الأخطاء | Error catalog

### 400 — Bad Request | `bad_request`

طلب غير صالح (JSON تالف، معاملات خاطئة).

```json
{
  "status": 400,
  "message": "Malformed JSON.",
  "error": "bad_request"
}
```

---

### 401 — Unauthenticated | `unauthenticated`

**متى:** طلب Admin بدون `Authorization: Bearer {token}` أو توكن منتهي/غير صالح.

```json
{
  "status": 401,
  "message": "Unauthenticated.",
  "error": "unauthenticated"
}
```

**الحل:** `POST /api/admin/auth/login` → استخدم `token` في الرأس.

---

### 403 — Forbidden | `forbidden`

**متى:** المستخدم مسجّل لكن لا يملك صلاحية العملية.

```json
{
  "status": 403,
  "message": "Forbidden.",
  "error": "forbidden"
}
```

---

### 404 — Not Found | `not_found`

**متى:** مسار API غير موجود، أو سجل بالمعرّف `{{id}}` غير موجود.

```json
{
  "status": 404,
  "message": "Endpoint not found.",
  "error": "not_found"
}
```

```json
{
  "status": 404,
  "message": "Resource not found.",
  "error": "not_found"
}
```

---

### 405 — Method Not Allowed | `method_not_allowed`

**متى:** HTTP method خاطئ (مثل `POST` على endpoint يقبل `GET` فقط).

```json
{
  "status": 405,
  "message": "Method not allowed.",
  "error": "method_not_allowed"
}
```

---

### 422 — Validation Error | `validation_error`

**متى:** حقول مطلوبة ناقصة أو قيم غير صالحة (Login، Create، Update، نماذج Public).

```json
{
  "status": 422,
  "message": "The email field is required. (and 1 more error)",
  "error": "validation_error",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

**مثال — بيانات دخول خاطئة:**

```json
{
  "status": 422,
  "message": "The provided credentials are incorrect.",
  "error": "validation_error",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 429 — Too Many Requests | `too_many_requests`

**متى:** تجاوز حد الطلبات (rate limiting إن وُجد).

```json
{
  "status": 429,
  "message": "Too Many Attempts.",
  "error": "too_many_requests"
}
```

---

### 500 — Server Error | `server_error`

**متى:** خطأ داخلي غير متوقع.

**Production** (`APP_DEBUG=false`):

```json
{
  "status": 500,
  "message": "Server error.",
  "error": "server_error"
}
```

**Development** (`APP_DEBUG=true`) — يُضاف `debug`:

```json
{
  "status": 500,
  "message": "Server error.",
  "error": "server_error",
  "debug": {
    "exception": "Exception",
    "file": "/path/to/file.php",
    "line": 42
  }
}
```

> **ملاحظة:** أخطاء 4xx لا تُرجع stack trace — فقط `status`, `message`, `error` (+ `errors` للتحقق).

---

## أمثلة اختبار | cURL examples

```bash
# 401 — بدون توكن
curl -s http://localhost:8000/api/admin/auth/me -H "Accept: application/json"

# 422 — تحقق من الحقول
curl -s -X POST http://localhost:8000/api/admin/auth/login \
  -H "Content-Type: application/json" -d '{}'

# 404 — مسار غير موجود
curl -s http://localhost:8000/api/v1/not-found -H "Accept: application/json"

# 405 — method خاطئ
curl -s -X POST http://localhost:8000/api/v1/home -H "Accept: application/json"
```

---

## Postman

في **Tests** يمكنك التحقق:

```javascript
if (pm.response.code >= 400) {
  const body = pm.response.json();
  pm.test('Error has status field', () => pm.expect(body.status).to.eql(pm.response.code));
  pm.test('Error has message', () => pm.expect(body.message).to.be.a('string'));
  pm.test('Error has error code', () => pm.expect(body.error).to.be.a('string'));
}
```
