# API Endpoint Documentation – Residential Management System

Dokumen ini berisi spesifikasi lengkap seluruh endpoint API, mencakup kegunaan, cara request, contoh response sukses, dan seluruh kemungkinan error yang dapat terjadi.

**Base URL:** `http://127.0.0.1:8000`

**Content-Type default request:** `application/json` (kecuali upload file, gunakan `multipart/form-data`)

**Autentikasi:** Semua endpoint kecuali `POST /auth/login` wajib menyertakan header:
```
Authorization: Bearer <token>
```

---

## Daftar Isi

1. [Auth](#1-auth)
   - [POST /auth/login](#post-authlogin)
   - [GET /auth/me](#get-authme)
   - [POST /auth/logout](#post-authlogout)
2. [Residents](#2-residents)
   - [GET /residents](#get-residents)
   - [GET /residents/{id}](#get-residentsid)
   - [POST /residents](#post-residents)
   - [PUT /residents/{id}](#put-residentsid)
   - [DELETE /residents/{id}](#delete-residentsid)
3. [Houses](#3-houses)
   - [GET /houses](#get-houses)
   - [GET /houses/{id}](#get-housesid)
   - [GET /houses/{id}/resident_histories](#get-housesidresident_histories)
   - [GET /houses/{id}/payment_histories](#get-housesidpayment_histories)
   - [POST /houses](#post-houses)
   - [PUT /houses/{id}](#put-housesid)
   - [DELETE /houses/{id}](#delete-housesid)
4. [Bills](#4-bills)
   - [GET /bills](#get-bills)
   - [GET /bills/{id}](#get-billsid)
   - [POST /bills](#post-bills)
   - [PUT /bills/{id}](#put-billsid)
   - [PATCH /bills/{id}/pay](#patch-billsidpay)
   - [DELETE /bills/{id}](#delete-billsid)
5. [Payments](#5-payments)
   - [POST /payments](#post-payments)
6. [Expenses](#6-expenses)
   - [GET /expenses](#get-expenses)
   - [GET /expenses/{id}](#get-expensesid)
   - [POST /expenses](#post-expenses)
   - [PUT /expenses/{id}](#put-expensesid)
   - [DELETE /expenses/{id}](#delete-expensesid)
7. [Report](#7-report)
   - [GET /report/summary](#get-reportsummary)
   - [GET /report/balances](#get-reportbalances)

---

## 1. Auth

### `POST /auth/login`

**Kegunaan:** Login ke sistem dan mendapatkan token autentikasi. Ini satu-satunya endpoint yang **tidak** memerlukan token.

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "admin@rt.com",
  "password": "password123"
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `email` | string | ✅ | Email terdaftar |
| `password` | string | ✅ | Password akun |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "Abc123XYZ...",
    "expired_at": "2026-03-04T08:00:00+07:00",
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "user_name": "admin",
      "email": "admin@rt.com"
    }
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 422 | `email` atau `password` kosong | `"Validation failed"` + `errors` |
| 401 | Email tidak ditemukan atau password salah | `"Email atau password salah."` |

**Contoh error validasi:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

### `GET /auth/me`

**Kegunaan:** Memvalidasi token yang sedang digunakan dan mengembalikan data user yang sedang login. Berguna untuk memverifikasi token masih aktif dari sisi frontend.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Authenticated",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "user_name": "admin",
    "email": "admin@rt.com"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Header `Authorization` tidak ada | `"Token tidak ditemukan. Harap login terlebih dahulu."` |
| 401 | Token tidak ditemukan di database | `"Token tidak valid."` |
| 401 | Token sudah expired | `"Sesi Anda telah berakhir. Harap login kembali."` |
| 401 | Token sudah di-logout | `"Sesi tidak aktif."` |

---

### `POST /auth/logout`

**Kegunaan:** Mengakhiri sesi aktif. Token yang digunakan akan diinvalidasi sehingga tidak bisa dipakai lagi.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid / tidak ada | *(sama seperti `GET /auth/me`)* |

---

## 2. Residents

### `GET /residents`

**Kegunaan:** Mengambil daftar seluruh penghuni yang terdaftar dalam sistem, dalam format halaman (paginated).

**Query Params:** *(tidak ada filter)*

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Residents retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "full_name": "Budi Santoso",
      "ktp_photo": "http://localhost/storage/ktp/foto.jpg",
      "is_contract": false,
      "phone_number": "08123456789",
      "is_married": true,
      "created_at": "2026-01-01 08:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 19,
    "last_page": 2
  }
}
```

**Error:**

| HTTP | Kondisi |
|------|---------|
| 401 | Token tidak valid / tidak ada |

---

### `GET /residents/{id}`

**Kegunaan:** Mengambil detail lengkap satu penghuni berdasarkan UUID-nya.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Resident retrieved successfully",
  "data": {
    "id": "uuid",
    "full_name": "Budi Santoso",
    "ktp_photo": "http://localhost/storage/ktp/foto.jpg",
    "is_contract": false,
    "phone_number": "08123456789",
    "is_married": true,
    "created_at": "2026-01-01 08:00:00"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan di database | `"Resident not found."` |

---

### `POST /residents`

**Kegunaan:** Mendaftarkan penghuni baru ke dalam sistem, beserta upload foto KTP.

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer <token>
```

**Request Body (form-data):**

| Field | Type | Required | Validasi |
|-------|------|----------|----------|
| `full_name` | string | ✅ | Maks 255 karakter |
| `ktp_photo` | file | ✅ | Tipe: `jpeg`, `png`; maks 2MB |
| `is_contract` | boolean | ✅ | `true` / `false` / `1` / `0` |
| `phone_number` | string | ✅ | Maks 20 karakter |
| `is_married` | boolean | ✅ | `true` / `false` / `1` / `0` |

> File KTP disimpan di `storage/app/public/ktp/` dan dapat diakses via URL `APP_URL/storage/ktp/namafile.jpg`.

**Response 201 – Sukses:**
```json
{
  "success": true,
  "message": "Resident created successfully",
  "data": {
    "id": "uuid",
    "full_name": "Siti Rahayu",
    "ktp_photo": "http://localhost/storage/ktp/abc123.jpg",
    "is_contract": false,
    "phone_number": "08129876543",
    "is_married": false,
    "created_at": "2026-02-25 09:00:00"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | Field wajib kosong | `"Validation failed"` + `errors` |
| 422 | `ktp_photo` bukan jpeg/png | `"Validation failed"` – `ktp_photo: ["The ktp photo field must be a file of type: jpeg, png."]` |
| 422 | `ktp_photo` lebih dari 2MB | `"Validation failed"` – `ktp_photo: ["The ktp photo field must not be greater than 2048 kilobytes."]` |

---

### `PUT /residents/{id}`

**Kegunaan:** Memperbarui data penghuni yang sudah ada. Semua field bersifat opsional — kirim hanya field yang ingin diubah.

> ⚠️ **Catatan PHP multipart:** Jika perlu mengupload foto KTP baru saat update, PHP tidak mem-parsing `multipart/form-data` pada method `PUT`. Gunakan method `POST` dan tambahkan field `_method=PUT` di form-data.

**Request Body (form-data atau JSON):**

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `full_name` | string | ❌ | |
| `ktp_photo` | file | ❌ | Jpeg/png, maks 2MB |
| `is_contract` | boolean | ❌ | |
| `phone_number` | string | ❌ | |
| `is_married` | boolean | ❌ | |

**Response 200 – Sukses:** Data penghuni setelah diperbarui (format sama dengan GET detail).

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Resident not found."` |
| 422 | Nilai field tidak valid | `"Validation failed"` + `errors` |

---

### `DELETE /residents/{id}`

**Kegunaan:** Menghapus penghuni dari sistem. Penghapusan akan **ditolak** jika penghuni masih aktif menghuni rumah atau memiliki tagihan belum lunas.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Resident deleted successfully"
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Resident not found."` |
| 422 | Penghuni masih aktif menghuni rumah | `"Penghuni masih aktif menghuni rumah dan tidak dapat dihapus."` |
| 422 | Penghuni memiliki tagihan belum lunas | `"Penghuni memiliki tagihan yang belum lunas."` |

---

## 3. Houses

### `GET /houses`

**Kegunaan:** Mengambil daftar seluruh rumah yang terdaftar, beserta status hunian saat ini.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Houses retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "house_number": "A1",
      "address": "Jl. Melati Blok A No. 1",
      "is_occupied": true,
      "created_at": "2026-01-01 08:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 20,
    "last_page": 2
  }
}
```

**Error:**

| HTTP | Kondisi |
|------|---------|
| 401 | Token tidak valid |

---

### `GET /houses/{id}`

**Kegunaan:** Mengambil detail satu rumah beserta data penghuni yang sedang aktif menghuni saat ini (jika ada).

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "House retrieved successfully",
  "data": {
    "id": "uuid",
    "house_number": "A1",
    "address": "Jl. Melati Blok A No. 1",
    "is_occupied": true,
    "created_at": "2026-01-01 08:00:00",
    "current_resident": {
      "history_id": "uuid",
      "move_in_date": "2024-01-15",
      "resident": {
        "id": "uuid",
        "full_name": "Budi Santoso",
        "phone_number": "08123456789",
        "is_contract": false,
        "is_married": true
      }
    }
  }
}
```

> `current_resident` akan bernilai `null` jika rumah sedang kosong (`is_occupied = false`).

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"House not found."` |

---

### `GET /houses/{id}/resident_histories`

**Kegunaan:** Mengambil riwayat seluruh penghuni yang pernah tinggal di rumah ini, diurutkan dari yang terbaru. Berguna untuk menampilkan histori hunian sebuah rumah.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Resident histories retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "resident": {
        "id": "uuid",
        "full_name": "Budi Santoso",
        "phone_number": "08123456789",
        "is_contract": false,
        "is_married": true
      },
      "move_in_date": "2024-01-15",
      "move_out_date": null,
      "is_active": true,
      "created_at": "2024-01-15 10:00:00"
    },
    {
      "id": "uuid",
      "resident": {
        "id": "uuid",
        "full_name": "Ani Wijayanti",
        "phone_number": "08111222333",
        "is_contract": true,
        "is_married": false
      },
      "move_in_date": "2022-03-01",
      "move_out_date": "2023-12-31",
      "is_active": false,
      "created_at": "2022-03-01 09:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 2,
    "last_page": 1
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` rumah tidak ditemukan | `"House not found."` |

---

### `GET /houses/{id}/payment_histories`

**Kegunaan:** Mengambil seluruh riwayat tagihan di rumah ini, dilengkapi dengan jenis iuran, penghuni yang bersangkutan, status pembayaran, dan tanggal bayar. Berguna untuk halaman detail riwayat keuangan per rumah.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Payment histories retrieved successfully",
  "data": [
    {
      "bill_id": "uuid",
      "fee_type": {
        "id": "uuid",
        "fee_name": "Satpam",
        "default_amount": 100000
      },
      "resident": {
        "id": "uuid",
        "full_name": "Budi Santoso"
      },
      "period_start": "2025-10-01",
      "period_end": "2025-10-31",
      "total_amount": 100000,
      "is_paid": true,
      "payment_date": "2025-10-11",
      "created_at": "2026-02-25T08:00:00+07:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 42,
    "last_page": 3
  }
}
```

> `payment_date` akan bernilai `null` jika tagihan belum lunas (`is_paid = false`).

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` rumah tidak ditemukan | `"House not found."` |

---

### `POST /houses`

**Kegunaan:** Mendaftarkan rumah baru ke dalam sistem.

**Request Body (JSON):**
```json
{
  "house_number": "D5",
  "address": "Jl. Anggrek Blok D No. 5",
  "is_occupied": false
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `house_number` | string | ✅ | Nomor/kode rumah, unik |
| `address` | string | ❌ | Alamat lengkap |
| `is_occupied` | boolean | ❌ | Default `false` |

**Response 201 – Sukses:**
```json
{
  "success": true,
  "message": "House created successfully",
  "data": {
    "id": "uuid",
    "house_number": "D5",
    "address": "Jl. Anggrek Blok D No. 5",
    "is_occupied": false,
    "created_at": "2026-02-25 09:00:00"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | `house_number` kosong | `"Validation failed"` + `errors` |

---

### `PUT /houses/{id}`

**Kegunaan:** Memperbarui data rumah. Semua field opsional.

**Request Body (JSON):**
```json
{
  "house_number": "D5",
  "address": "Jl. Anggrek Blok D No. 5 – Update",
  "is_occupied": true
}
```

**Response 200 – Sukses:** Data rumah setelah diperbarui.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"House not found."` |
| 422 | Nilai tidak valid | `"Validation failed"` + `errors` |

---

### `DELETE /houses/{id}`

**Kegunaan:** Menghapus rumah dari sistem. Penghapusan akan **ditolak** jika rumah masih dihuni atau memiliki riwayat tagihan.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "House deleted successfully"
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"House not found."` |
| 422 | Rumah masih dihuni (`is_occupied = true`) | `"Rumah masih dihuni dan tidak dapat dihapus."` |
| 422 | Rumah memiliki riwayat tagihan | `"Rumah memiliki riwayat tagihan dan tidak dapat dihapus."` |

---

## 4. Bills

### `GET /bills`

**Kegunaan:** Mengambil daftar tagihan dengan dukungan filter. Berguna untuk menampilkan daftar tagihan berdasarkan rumah tertentu, status lunas, jenis iuran, atau periode.

**Query Params (semua opsional):**

| Param | Type | Contoh | Keterangan |
|-------|------|--------|------------|
| `house_id` | uuid | `?house_id=abc-123` | Filter per rumah |
| `fee_type_id` | uuid | `?fee_type_id=def-456` | Filter per jenis iuran |
| `is_paid` | boolean | `?is_paid=false` | Filter status pembayaran |
| `month` | integer 1–12 | `?month=10` | Filter bulan dari `period_start` |
| `year` | integer | `?year=2025` | Filter tahun dari `period_start` |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Bills retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "house": {
        "id": "uuid",
        "house_number": "A1",
        "address": "Jl. Melati Blok A No. 1"
      },
      "resident": {
        "id": "uuid",
        "full_name": "Budi Santoso"
      },
      "fee_type": {
        "id": "uuid",
        "fee_name": "Satpam",
        "default_amount": 100000
      },
      "period_start": "2025-10-01",
      "period_end": "2025-10-31",
      "total_amount": 100000,
      "is_paid": false,
      "payment_date": null,
      "created_at": "2026-02-25T08:00:00+07:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 38,
    "last_page": 3
  }
}
```

---

### `GET /bills/{id}`

**Kegunaan:** Mengambil detail lengkap satu tagihan beserta relasi rumah, penghuni, dan jenis iuran.

**Response 200 – Sukses:** Format sama seperti satu item di `GET /bills`.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Bill not found."` |

---

### `POST /bills`

**Kegunaan:** Membuat tagihan baru secara manual. Backend akan secara otomatis menghitung `total_amount` berdasarkan `default_amount` jenis iuran dikali jumlah bulan dalam periode. `resident_id` juga diambil otomatis dari penghuni aktif rumah saat tagihan dibuat.

**Request Body (JSON):**
```json
{
  "house_id": "uuid-rumah",
  "fee_type_id": "uuid-jenis-iuran",
  "period_start": "2025-10-01",
  "period_end": "2025-10-31"
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `house_id` | uuid | ✅ | ID rumah |
| `fee_type_id` | uuid | ✅ | ID jenis iuran |
| `period_start` | date (Y-m-d) | ✅ | Tanggal awal periode |
| `period_end` | date (Y-m-d) | ✅ | Tanggal akhir periode, harus ≥ `period_start` |

**Logika kalkulasi `total_amount`:**
- Jumlah bulan dihitung inklusif per batas bulan.
- Contoh: `2025-10-01` → `2025-10-31` = **1 bulan** → `100.000 × 1 = 100.000`
- Contoh: `2025-01-01` → `2025-12-31` = **12 bulan** → `100.000 × 12 = 1.200.000`

**Response 201 – Sukses:**
```json
{
  "success": true,
  "message": "Bill created successfully",
  "data": {
    "id": "uuid",
    "house": { "id": "uuid", "house_number": "A1", "address": "..." },
    "resident": { "id": "uuid", "full_name": "Budi Santoso" },
    "fee_type": { "id": "uuid", "fee_name": "Satpam", "default_amount": 100000 },
    "period_start": "2025-10-01",
    "period_end": "2025-10-31",
    "total_amount": 100000,
    "is_paid": false,
    "payment_date": null,
    "created_at": "2026-02-25T08:00:00+07:00"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | Field wajib kosong / format salah | `"Validation failed"` + `errors` |
| 422 | `period_end` sebelum `period_start` | `"Validation failed"` – `period_end: [...]` |
| 422 | Rumah tidak sedang dihuni (`is_occupied = false`) | `"Rumah tidak sedang dihuni."` |
| 422 | Tagihan duplikat `(house_id, fee_type_id, period_start)` | `"Tagihan untuk rumah ini dengan jenis iuran dan periode yang sama sudah ada."` |
| 404 | `house_id` tidak ditemukan | `"House not found."` |
| 404 | `fee_type_id` tidak ditemukan | `"Fee type not found."` |

---

### `PUT /bills/{id}`

**Kegunaan:** Memperbarui data tagihan yang belum lunas. `total_amount` akan dihitung ulang otomatis setiap kali ada perubahan. Tidak bisa mengubah tagihan yang sudah lunas.

**Request Body (JSON) – semua opsional:**
```json
{
  "house_id": "uuid",
  "fee_type_id": "uuid",
  "period_start": "2025-10-01",
  "period_end": "2025-12-31"
}
```

**Response 200 – Sukses:** Data tagihan setelah diperbarui (format sama dengan GET detail).

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Bill not found."` |
| 422 | Tagihan sudah lunas | `"Tagihan sudah lunas dan tidak dapat diubah."` |
| 422 | Nilai tidak valid | `"Validation failed"` + `errors` |

---

### `PATCH /bills/{id}/pay`

**Kegunaan:** Menandai tagihan sebagai lunas sekaligus mencatat record pembayaran. Ini cara tercepat untuk mencatat pembayaran karena hanya butuh `bill_id` dari URL.

**Request Body (JSON):**
```json
{
  "payment_date": "2025-10-15",
  "amount_paid": 100000,
  "notes": "Bayar tunai via RT"
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `payment_date` | date (Y-m-d) | ✅ | Tanggal pembayaran |
| `amount_paid` | numeric | ✅ | Jumlah yang dibayarkan |
| `notes` | string | ❌ | Catatan tambahan |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Bill paid successfully",
  "data": {
    "id": "uuid",
    "house": { "id": "uuid", "house_number": "A1", "address": "..." },
    "resident": { "id": "uuid", "full_name": "Budi Santoso" },
    "fee_type": { "id": "uuid", "fee_name": "Satpam", "default_amount": 100000 },
    "period_start": "2025-10-01",
    "period_end": "2025-10-31",
    "total_amount": 100000,
    "is_paid": true,
    "payment_date": "2025-10-15",
    "created_at": "2026-02-25T08:00:00+07:00"
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Bill not found."` |
| 422 | Field wajib kosong | `"Validation failed"` + `errors` |
| 422 | Tagihan sudah lunas | `"Tagihan sudah lunas."` |

---

### `DELETE /bills/{id}`

**Kegunaan:** Menghapus tagihan. Tidak bisa menghapus tagihan yang sudah lunas karena riwayat keuangan harus tetap terjaga.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Bill deleted successfully"
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Bill not found."` |
| 422 | Tagihan sudah lunas | `"Tagihan sudah lunas dan tidak dapat dihapus."` |

---

## 5. Payments

### `POST /payments`

**Kegunaan:** Mencatat pembayaran secara manual untuk sebuah tagihan. Berbeda dengan `PATCH /bills/{id}/pay` yang menggunakan ID tagihan dari URL, endpoint ini menerima `bill_id` di body — cocok untuk use case frontend yang memisahkan form pembayaran dari konteks tagihan. Otomatis mengubah status `is_paid` tagihan menjadi `true`.

**Request Body (JSON):**
```json
{
  "bill_id": "uuid-tagihan",
  "payment_date": "2025-10-15",
  "amount_paid": 100000,
  "notes": "Bayar via transfer BCA"
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `bill_id` | uuid | ✅ | ID tagihan yang akan dibayar |
| `payment_date` | date (Y-m-d) | ✅ | Tanggal pembayaran |
| `amount_paid` | numeric | ✅ | Jumlah yang dibayarkan |
| `notes` | string | ❌ | Catatan tambahan |

**Response 201 – Sukses:**
```json
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "id": "uuid",
    "payment_date": "2025-10-15",
    "amount_paid": 100000,
    "notes": "Bayar via transfer BCA",
    "created_at": "2026-02-25T08:00:00+07:00",
    "bill": {
      "id": "uuid",
      "period_start": "2025-10-01",
      "period_end": "2025-10-31",
      "total_amount": 100000,
      "is_paid": true,
      "house": { "id": "uuid", "house_number": "A1" },
      "resident": { "id": "uuid", "full_name": "Budi Santoso" },
      "fee_type": { "id": "uuid", "fee_name": "Satpam" }
    }
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | Field wajib kosong / format salah | `"Validation failed"` + `errors` |
| 422 | Tagihan sudah lunas | `"Tagihan sudah lunas."` |
| 404 | `bill_id` tidak ditemukan | `"Bill not found."` |

---

## 6. Expenses

### `GET /expenses`

**Kegunaan:** Mengambil daftar pengeluaran dengan dukungan filter bulan, tahun, dan jenis pengeluaran (rutin/insidental).

**Query Params (semua opsional):**

| Param | Type | Contoh | Keterangan |
|-------|------|--------|------------|
| `month` | integer 1–12 | `?month=10` | Filter bulan dari `expense_date` |
| `year` | integer | `?year=2025` | Filter tahun dari `expense_date` |
| `is_monthly` | boolean | `?is_monthly=true` | `true` = rutin bulanan, `false` = insidental |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Expenses retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "expense_name": "Gaji Satpam",
      "expense_date": "2025-10-05",
      "amount": 1500000,
      "description": "Gaji bulan Oktober",
      "is_monthly": true,
      "created_at": "2026-02-25T08:00:00+07:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 20,
    "last_page": 2
  }
}
```

---

### `GET /expenses/{id}`

**Kegunaan:** Mengambil detail satu pengeluaran berdasarkan ID.

**Response 200 – Sukses:** Format sama seperti satu item di `GET /expenses`.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Expense not found."` |

---

### `POST /expenses`

**Kegunaan:** Mencatat pengeluaran baru — baik rutin bulanan (gaji satpam, token listrik) maupun insidental (perbaikan jalan, dll).

**Request Body (JSON):**
```json
{
  "expense_name": "Gaji Satpam",
  "expense_date": "2025-10-05",
  "amount": 1500000,
  "description": "Gaji bulan Oktober",
  "is_monthly": true
}
```

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| `expense_name` | string | ✅ | Nama pengeluaran |
| `expense_date` | date (Y-m-d) | ✅ | Tanggal pengeluaran |
| `amount` | numeric | ✅ | Jumlah pengeluaran (> 0) |
| `description` | string | ❌ | Keterangan tambahan |
| `is_monthly` | boolean | ✅ | `true` = rutin bulanan |

**Response 201 – Sukses:** Data pengeluaran yang baru dibuat.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | Field wajib kosong / format salah | `"Validation failed"` + `errors` |

---

### `PUT /expenses/{id}`

**Kegunaan:** Memperbarui data pengeluaran. Semua field opsional.

> **Catatan:** Field `is_monthly = false` dan `description = null` tetap diproses dengan benar — tidak akan diabaikan hanya karena nilainya falsy.

**Request Body (JSON) – semua opsional:**
```json
{
  "expense_name": "Gaji Satpam Oktober",
  "amount": 1600000,
  "is_monthly": false
}
```

**Response 200 – Sukses:** Data pengeluaran setelah diperbarui.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Expense not found."` |
| 422 | Nilai tidak valid | `"Validation failed"` + `errors` |

---

### `DELETE /expenses/{id}`

**Kegunaan:** Menghapus catatan pengeluaran.

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Expense deleted successfully"
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 404 | `id` tidak ditemukan | `"Expense not found."` |

---

## 7. Report

### `GET /report/summary`

**Kegunaan:** Mengambil rekap keuangan per bulan selama satu tahun penuh (12 entri). Cocok sebagai data source untuk grafik bar/line di dashboard — menampilkan tren pemasukan vs pengeluaran sepanjang tahun.

**Query Params:**

| Param | Type | Required | Contoh |
|-------|------|----------|--------|
| `year` | integer (≥ 2000) | ✅ | `?year=2025` |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Summary retrieved successfully",
  "data": [
    { "month": 1,  "year": 2025, "total_income": 3105000, "total_expense": 1500000, "ending_balance": 1605000 },
    { "month": 2,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 3,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 4,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 5,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 6,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 7,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 8,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 9,  "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 10, "year": 2025, "total_income": 1955000, "total_expense": 2600000, "ending_balance": -645000 },
    { "month": 11, "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 },
    { "month": 12, "year": 2025, "total_income": 0,       "total_expense": 0,       "ending_balance": 0 }
  ]
}
```

> Response selalu berisi tepat **12 elemen** (satu per bulan). Bulan yang tidak ada transaksi akan bernilai `0`.

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | `year` tidak diisi | `"Parameter year wajib diisi."` |
| 422 | `year` bukan angka atau < 2000 | `"Parameter year harus berupa angka minimal 2000."` |

**Contoh error validasi:**
```json
{
  "success": false,
  "message": "Parameter year wajib diisi.",
  "errors": {
    "year": ["Parameter year wajib diisi."]
  }
}
```

---

### `GET /report/balances`

**Kegunaan:** Mengambil detail pemasukan dan pengeluaran untuk **bulan tertentu**. Response mencakup list seluruh pembayaran (pemasukan) beserta detail tagihan, rumah, dan penghuni terkait — serta list seluruh pengeluaran pada bulan tersebut. Berguna untuk halaman laporan keuangan bulanan yang menampilkan rincian transaksi.

**Query Params:**

| Param | Type | Required | Contoh |
|-------|------|----------|--------|
| `month` | integer 1–12 | ✅ | `?month=10` |
| `year` | integer (≥ 2000) | ✅ | `?year=2025` |

**Response 200 – Sukses:**
```json
{
  "success": true,
  "message": "Balances retrieved successfully",
  "data": {
    "month": 10,
    "year": 2025,
    "total_income": 1955000,
    "total_expense": 2600000,
    "ending_balance": -645000,
    "incomes": [
      {
        "payment_id": "uuid",
        "payment_date": "2025-10-11",
        "amount_paid": 100000,
        "notes": "Bayar tunai via RT",
        "bill": {
          "id": "uuid",
          "period_start": "2025-10-01",
          "period_end": "2025-10-31",
          "total_amount": 100000,
          "fee_type": {
            "id": "uuid",
            "fee_name": "Satpam",
            "default_amount": 100000
          }
        },
        "house": {
          "id": "uuid",
          "house_number": "A1",
          "address": "Jl. Melati Blok A No. 1"
        },
        "resident": {
          "id": "uuid",
          "full_name": "Budi Santoso"
        }
      }
    ],
    "expenses": [
      {
        "id": "uuid",
        "expense_name": "Gaji Satpam",
        "expense_date": "2025-10-05",
        "amount": 1500000,
        "description": "Gaji bulan Oktober",
        "is_monthly": true
      }
    ]
  }
}
```

**Error:**

| HTTP | Kondisi | `message` |
|------|---------|-----------|
| 401 | Token tidak valid | *(standar)* |
| 422 | `month` atau `year` tidak diisi | `"Validation failed"` + `errors` |
| 422 | `month` di luar rentang 1–12 | Pesan error validasi per field |
| 422 | `year` < 2000 | Pesan error validasi per field |

---

## Referensi Error Autentikasi (401)

Berlaku untuk semua endpoint yang dilindungi middleware:

| Kondisi | `message` |
|---------|-----------|
| Header `Authorization` tidak ada | `"Token tidak ditemukan. Harap login terlebih dahulu."` |
| Format bukan `Bearer <token>` | `"Token tidak ditemukan. Harap login terlebih dahulu."` |
| Token tidak ada di database | `"Token tidak valid."` |
| Token sudah melewati `expired_at` | `"Sesi Anda telah berakhir. Harap login kembali."` |
| Token sudah di-logout (`logged_out_at` terisi) | `"Sesi tidak aktif."` |

---

## Ringkasan Seluruh Endpoint

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | `/auth/login` | ❌ | Login, dapatkan token |
| GET | `/auth/me` | ✅ | Data user aktif |
| POST | `/auth/logout` | ✅ | Invalidate token |
| GET | `/residents` | ✅ | List penghuni |
| GET | `/residents/{id}` | ✅ | Detail penghuni |
| POST | `/residents` | ✅ | Tambah penghuni |
| PUT | `/residents/{id}` | ✅ | Update penghuni |
| DELETE | `/residents/{id}` | ✅ | Hapus penghuni |
| GET | `/houses` | ✅ | List rumah |
| GET | `/houses/{id}` | ✅ | Detail rumah + penghuni aktif |
| GET | `/houses/{id}/resident_histories` | ✅ | Riwayat penghuni di rumah |
| GET | `/houses/{id}/payment_histories` | ✅ | Riwayat tagihan di rumah |
| POST | `/houses` | ✅ | Tambah rumah |
| PUT | `/houses/{id}` | ✅ | Update rumah |
| DELETE | `/houses/{id}` | ✅ | Hapus rumah |
| GET | `/bills` | ✅ | List tagihan (filterable) |
| GET | `/bills/{id}` | ✅ | Detail tagihan |
| POST | `/bills` | ✅ | Buat tagihan baru |
| PUT | `/bills/{id}` | ✅ | Update tagihan |
| PATCH | `/bills/{id}/pay` | ✅ | Bayar tagihan |
| DELETE | `/bills/{id}` | ✅ | Hapus tagihan |
| POST | `/payments` | ✅ | Catat pembayaran |
| GET | `/expenses` | ✅ | List pengeluaran (filterable) |
| GET | `/expenses/{id}` | ✅ | Detail pengeluaran |
| POST | `/expenses` | ✅ | Tambah pengeluaran |
| PUT | `/expenses/{id}` | ✅ | Update pengeluaran |
| DELETE | `/expenses/{id}` | ✅ | Hapus pengeluaran |
| GET | `/report/summary` | ✅ | Rekap 12 bulan |
| GET | `/report/balances` | ✅ | Detail keuangan bulanan |
