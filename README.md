# Residential Management System – Backend API

Sistem administrasi keuangan dan penghuni perumahan yang dikelola oleh RT. Mencakup pengelolaan penghuni, rumah, tagihan iuran bulanan, pengeluaran, dan laporan keuangan.

**Stack:** Laravel 11 · PHP 8.2 · MySQL · Token-based Auth (custom sessions table)

---

## Daftar Isi

- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Folder](#struktur-folder)
- [Autentikasi](#autentikasi)
- [Format Response](#format-response)
- [API Endpoints](#api-endpoints)
- [Error Codes](#error-codes)

---

## Persyaratan

- PHP >= 8.2 (dengan ekstensi: `pdo_mysql`, `fileinfo`, `gd`)
- Composer >= 2
- MySQL >= 8.0
- Node.js >= 18 (opsional, untuk build aset)

---

## Instalasi

### 1. Clone repositori

```bash
git clone <repository-url> residential-management
cd residential-management
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Salin file environment

```bash
cp .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Konfigurasi database

Edit file `.env`, sesuaikan bagian berikut:

```env
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=residential_management
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
```

> `APP_URL` wajib disesuaikan dengan URL server Anda — digunakan untuk membangun URL publik foto KTP penghuni.

### 6. Jalankan migrasi dan seeder

```bash
php artisan migrate --seed
```

Perintah ini akan membuat seluruh tabel dan mengisi data awal:
- 2 jenis iuran (Satpam Rp100.000, Kebersihan Rp15.000)
- 1 akun admin (`admin@rt.com` / `password123`)
- 20 rumah (blok A–D)
- 19 penghuni beserta riwayat hunian
- Data tagihan, pembayaran, dan pengeluaran contoh

### 7. Buat symbolic link storage

```bash
php artisan storage:link
```

Diperlukan agar foto KTP dapat diakses via URL publik.

---

## Konfigurasi

### Environment penting

| Key | Nilai yang disarankan | Keterangan |
|-----|-----------------------|------------|
| `APP_URL` | `http://localhost` | Digunakan untuk membangun URL foto KTP |
| `APP_DEBUG` | `false` (production) | `true` = tampilkan detail error; `false` = pesan generik |
| `APP_TIMEZONE` | `Asia/Jakarta` | Zona waktu semua timestamp |
| `SESSION_DRIVER` | `file` | **Wajib `file`** — jangan `database`, karena tabel `sessions` dipakai untuk auth custom |

---

## Menjalankan Aplikasi

### Development

```bash
php artisan serve
```

API siap diakses di `http://127.0.0.1:8000`.

### Verifikasi instalasi

Setelah server berjalan, pastikan API aktif dengan mencoba login menggunakan akun yang sudah di-seed:

```bash
curl -X POST http://127.0.0.1:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@rt.com","password":"password123"}'
```

Jika berhasil, response akan mengembalikan `token` yang dapat digunakan untuk mengakses endpoint lainnya.

### Production (shared hosting / server)

Arahkan document root web server ke folder `public/`.

---

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/        # Menerima request, memanggil Service, mengembalikan response JSON
│   │   ├── AuthController.php
│   │   ├── ResidentController.php
│   │   ├── HouseController.php
│   │   ├── BillController.php
│   │   ├── PaymentController.php
│   │   ├── ExpenseController.php
│   │   └── ReportController.php
│   ├── Middleware/
│   │   └── AuthTokenMiddleware.php  # Validasi Bearer token di setiap request
│   └── Requests/           # Form Request — validasi input & pesan error terstruktur
│       ├── StoreBillRequest.php
│       ├── UpdateBillRequest.php
│       ├── PayBillRequest.php
│       ├── StorePaymentRequest.php
│       ├── StoreExpenseRequest.php
│       └── UpdateExpenseRequest.php
├── Models/                 # Eloquent models dengan UUID, fillable, casts, relasi
│   ├── User.php
│   ├── Session.php         # Custom sessions untuk token auth (bukan Laravel session)
│   ├── House.php
│   ├── Resident.php
│   ├── ResidentHouseHistory.php
│   ├── FeeType.php
│   ├── Bill.php
│   ├── Payment.php
│   ├── Expense.php
│   └── MonthlyBalance.php
├── Services/               # Business logic — dipanggil oleh Controller
│   ├── AuthService.php
│   ├── ResidentService.php
│   ├── HouseService.php
│   ├── BillService.php
│   ├── PaymentService.php
│   ├── ExpenseService.php
│   └── ReportService.php
└── Providers/
    └── AppServiceProvider.php

bootstrap/
└── app.php                 # Konfigurasi middleware, alias, dan global exception handler

config/
├── cors.php                # CORS: semua origin diizinkan (paths = ['*'])
└── ...                     # Konfigurasi Laravel standar lainnya

database/
├── migrations/             # Skrip pembuatan tabel
├── seeders/                # Data awal (FeeType, User, House, Resident, dll)
└── factories/

routes/
└── web.php                 # Seluruh 22 endpoint API didefinisikan di sini

storage/
└── app/public/ktp/         # Foto KTP penghuni tersimpan di sini
```

### Pola arsitektur

Proyek ini menggunakan **Service Layer Pattern**:

```
Request → Controller → Service → Model (Eloquent) → Database
                ↓
         JSON Response
```

- **Controller** hanya bertanggung jawab menerima request dan mengembalikan response.
- **Service** menampung seluruh business logic (validasi domain, kalkulasi, query kompleks).
- **Model** hanya mendefinisikan struktur data, relasi, dan casting.

---

## Autentikasi

Semua endpoint **kecuali** `POST /auth/login` memerlukan token di header:

```
Authorization: Bearer <token>
```

Token diperoleh dari response `POST /auth/login` dan berlaku selama **7 hari**.

---

## Format Response

### Sukses

```json
{
  "success": true,
  "message": "Deskripsi singkat",
  "data": {}
}
```

### Sukses dengan pagination

```json
{
  "success": true,
  "message": "...",
  "data": [],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### Error validasi (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Pesan error"]
  }
}
```

### Error umum

```json
{
  "success": false,
  "message": "Deskripsi error yang informatif"
}
```

---

## API Endpoints

Dokumentasi lengkap seluruh endpoint — mencakup request body, contoh response, dan seluruh kemungkinan error — tersedia di:

**[→ endpoint.md](endpoint.md)**

### Ringkasan endpoint

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/login` | Login, dapatkan token |
| GET | `/auth/me` | Data user aktif |
| POST | `/auth/logout` | Invalidate token |
| GET | `/residents` | List penghuni |
| GET | `/residents/{id}` | Detail penghuni |
| POST | `/residents` | Tambah penghuni |
| PUT | `/residents/{id}` | Update penghuni |
| DELETE | `/residents/{id}` | Hapus penghuni |
| GET | `/houses` | List rumah |
| GET | `/houses/{id}` | Detail rumah + penghuni aktif |
| GET | `/houses/{id}/resident_histories` | Riwayat penghuni di rumah |
| GET | `/houses/{id}/payment_histories` | Riwayat tagihan di rumah |
| POST | `/houses` | Tambah rumah |
| PUT | `/houses/{id}` | Update rumah |
| DELETE | `/houses/{id}` | Hapus rumah |
| GET | `/bills` | List tagihan (filterable) |
| GET | `/bills/{id}` | Detail tagihan |
| POST | `/bills` | Buat tagihan baru |
| PUT | `/bills/{id}` | Update tagihan |
| PATCH | `/bills/{id}/pay` | Bayar tagihan |
| DELETE | `/bills/{id}` | Hapus tagihan |
| POST | `/payments` | Catat pembayaran |
| GET | `/expenses` | List pengeluaran (filterable) |
| GET | `/expenses/{id}` | Detail pengeluaran |
| POST | `/expenses` | Tambah pengeluaran |
| PUT | `/expenses/{id}` | Update pengeluaran |
| DELETE | `/expenses/{id}` | Hapus pengeluaran |
| GET | `/report/summary` | Rekap keuangan 12 bulan |
| GET | `/report/balances` | Detail keuangan bulanan |

---

## Error Codes

| HTTP Code | Kondisi |
|-----------|---------|
| `200` | Sukses |
| `201` | Resource berhasil dibuat |
| `401` | Token tidak ada, tidak valid, sudah expired, atau sudah logout |
| `404` | Endpoint tidak ditemukan, atau resource tidak ditemukan |
| `405` | HTTP method tidak diizinkan untuk endpoint tersebut |
| `422` | Validasi gagal, atau business rule dilanggar (duplikat, resource masih dipakai, dll) |
| `500` | Server error tak terduga |
