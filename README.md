# API Documentation — E-Commerce Sederhana

## Informasi Umum

| Item | Detail |
|------|--------|
| **Nama Project** | API E-Commerce Sederhana — UTP TIS |
| **Framework** | Laravel 12 |
| **Bahasa** | PHP 8.2+ |
| **Penyimpanan Data** | File JSON (tanpa database) |
| **Base URL** | `http://localhost:8000/api` |
| **Swagger UI** | `http://localhost:8000/api/documentation` |
| **Pembuat** | Alfi Perdiansyah Putra (245150701111028) |

---

## Cara Menjalankan Project

### 1. Clone Repository
```bash
git clone <repository-url>
cd 245150701111028-AlfiPerdiansyahPutra-utptis
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Copy Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Jalankan Server
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`.

### 5. Akses Swagger UI
Buka browser dan navigasi ke:
```
http://localhost:8000/api/documentation
```

---

## Struktur Data Barang

Setiap item barang memiliki struktur sebagai berikut:

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | integer | ID unik barang (auto-generated) |
| `nama` | string | Nama barang |
| `harga` | number | Harga barang dalam Rupiah |
| `stok` | integer | Jumlah stok tersedia |
| `kategori` | string | Kategori barang |
| `deskripsi` | string | Deskripsi detail barang |

---

## Daftar Endpoint API

### 1. `GET /api/barang` — Menampilkan Semua Barang

**Deskripsi:** Mengambil seluruh daftar barang yang tersedia.

**Request:**
```
GET http://localhost:8000/api/barang
```

**Response Sukses (200):**
```json
{
    "status": "success",
    "message": "Daftar semua barang berhasil diambil",
    "data": [
        {
            "id": 1,
            "nama": "Laptop ASUS ROG Strix",
            "harga": 18500000,
            "stok": 12,
            "kategori": "Elektronik",
            "deskripsi": "Laptop gaming high-end dengan RTX 4060"
        },
        ...
    ],
    "total": 8
}
```

---

### 2. `GET /api/barang/{id}` — Menampilkan Barang Berdasarkan ID

**Deskripsi:** Mengambil detail satu barang berdasarkan ID.

**Request:**
```
GET http://localhost:8000/api/barang/1
```

**Response Sukses (200):**
```json
{
    "status": "success",
    "message": "Detail barang berhasil diambil",
    "data": {
        "id": 1,
        "nama": "Laptop ASUS ROG Strix",
        "harga": 18500000,
        "stok": 12,
        "kategori": "Elektronik",
        "deskripsi": "Laptop gaming high-end dengan RTX 4060"
    }
}
```

**Response Error — Barang Tidak Ditemukan (404):**
```json
{
    "status": "error",
    "message": "Barang dengan ID 99 tidak ditemukan"
}
```

---

### 3. `POST /api/barang` — Membuat Barang Baru

**Deskripsi:** Menambahkan item barang baru.

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "nama": "Mousepad Gaming XL",
    "harga": 150000,
    "stok": 50,
    "kategori": "Aksesoris",
    "deskripsi": "Mousepad gaming ukuran XL anti-slip"
}
```

| Field | Wajib | Tipe | Validasi |
|-------|-------|------|----------|
| `nama` | ✅ Ya | string | Maksimal 255 karakter |
| `harga` | ✅ Ya | numeric | Minimal 0 |
| `stok` | ❌ Tidak | integer | Minimal 0 (default: 0) |
| `kategori` | ❌ Tidak | string | Maksimal 100 karakter |
| `deskripsi` | ❌ Tidak | string | Maksimal 500 karakter |

**Response Sukses (201):**
```json
{
    "status": "success",
    "message": "Barang berhasil ditambahkan",
    "data": {
        "id": 9,
        "nama": "Mousepad Gaming XL",
        "harga": 150000,
        "stok": 50,
        "kategori": "Aksesoris",
        "deskripsi": "Mousepad gaming ukuran XL anti-slip"
    }
}
```

**Response Error — Validasi Gagal (422):**
```json
{
    "status": "error",
    "message": "Validasi gagal",
    "errors": {
        "nama": ["Nama barang wajib diisi"],
        "harga": ["Harga barang wajib diisi"]
    }
}
```

---

### 4. `PUT /api/barang/{id}` — Mengedit Seluruh Data Barang

**Deskripsi:** Memperbarui seluruh field dari barang yang sudah ada. **Semua field wajib diisi.**

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request:**
```
PUT http://localhost:8000/api/barang/1
```

**Request Body:**
```json
{
    "nama": "Laptop ASUS ROG Strix G16",
    "harga": 19500000,
    "stok": 15,
    "kategori": "Elektronik",
    "deskripsi": "Laptop gaming high-end versi terbaru"
}
```

| Field | Wajib | Tipe | Validasi |
|-------|-------|------|----------|
| `nama` | ✅ Ya | string | Maksimal 255 karakter |
| `harga` | ✅ Ya | numeric | Minimal 0 |
| `stok` | ✅ Ya | integer | Minimal 0 |
| `kategori` | ✅ Ya | string | Maksimal 100 karakter |
| `deskripsi` | ✅ Ya | string | Maksimal 500 karakter |

**Response Sukses (200):**
```json
{
    "status": "success",
    "message": "Barang dengan ID 1 berhasil diperbarui secara penuh",
    "data": {
        "id": 1,
        "nama": "Laptop ASUS ROG Strix G16",
        "harga": 19500000,
        "stok": 15,
        "kategori": "Elektronik",
        "deskripsi": "Laptop gaming high-end versi terbaru"
    }
}
```

**Response Error — Barang Tidak Ditemukan (404):**
```json
{
    "status": "error",
    "message": "Barang dengan ID 99 tidak ditemukan"
}
```

---

### 5. `PATCH /api/barang/{id}` — Mengedit Sebagian Data Barang

**Deskripsi:** Memperbarui satu atau beberapa field saja. **Minimal satu field harus diisi.**

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request:**
```
PATCH http://localhost:8000/api/barang/2
```

**Request Body (contoh: hanya update harga):**
```json
{
    "harga": 950000
}
```

**Response Sukses (200):**
```json
{
    "status": "success",
    "message": "Barang dengan ID 2 berhasil diperbarui secara parsial",
    "data": {
        "id": 2,
        "nama": "Mouse Logitech G502",
        "harga": 950000,
        "stok": 45,
        "kategori": "Aksesoris",
        "deskripsi": "Mouse gaming ergonomis dengan sensor HERO 25K"
    }
}
```

**Response Error — Tidak Ada Field (422):**
```json
{
    "status": "error",
    "message": "Minimal satu field harus diisi untuk update parsial (nama, harga, stok, kategori, deskripsi)"
}
```

---

### 6. `DELETE /api/barang/{id}` — Menghapus Barang

**Deskripsi:** Menghapus item barang berdasarkan ID.

**Request:**
```
DELETE http://localhost:8000/api/barang/1
```

**Response Sukses (200):**
```json
{
    "status": "success",
    "message": "Barang dengan ID 1 berhasil dihapus",
    "data": {
        "id": 1,
        "nama": "Laptop ASUS ROG Strix",
        "harga": 18500000,
        "stok": 12,
        "kategori": "Elektronik",
        "deskripsi": "Laptop gaming high-end dengan RTX 4060"
    }
}
```

**Response Error — Barang Tidak Ditemukan (404):**
```json
{
    "status": "error",
    "message": "Barang dengan ID 99 tidak ditemukan"
}
```

---

## Ringkasan Error Handling

| HTTP Code | Deskripsi | Contoh |
|-----------|-----------|--------|
| `200` | Request berhasil | GET, PUT, PATCH, DELETE sukses |
| `201` | Resource berhasil dibuat | POST sukses |
| `404` | Resource tidak ditemukan | ID barang tidak ada |
| `422` | Validasi gagal | Field wajib kosong, tipe data salah |

Semua response menggunakan format JSON yang konsisten:

**Format Sukses:**
```json
{
    "status": "success",
    "message": "...",
    "data": { ... }
}
```

**Format Error:**
```json
{
    "status": "error",
    "message": "...",
    "errors": { ... }
}
```

---

## Teknologi yang Digunakan

- **Laravel 12** — PHP web framework
- **PHP 8.2+** — Server-side programming language
- **JSON File Storage** — Penyimpanan data tanpa database
- **L5-Swagger** — Dokumentasi API otomatis dengan Swagger/OpenAPI 3.0
