<p align="center">
  <img src="https://laravel.com/img/logomark.min.svg" width="120" alt="Laravel Logo">
</p>

<h1 align="center">Aplikasi Penjualan & Manajemen Stok – Pratama Motor</h1>

<p align="center">
  Sistem informasi penjualan dan pengelolaan stok barang berbasis web menggunakan Laravel 12.
</p>

---

## 📌 Deskripsi Aplikasi

Aplikasi **Penjualan & Manajemen Stok Pratama Motor** adalah sistem berbasis web yang digunakan untuk mengelola proses operasional toko, mulai dari pengelolaan produk, stok, transaksi penjualan, hingga laporan.

Aplikasi ini mendukung **multi-role (Admin & User)** dengan sistem autentikasi yang aman dan arsitektur modern.

---

## 🚀 Fitur Utama

### 🔐 Autentikasi & Otorisasi
- Login & Logout
- Register User
- Reset Password
- Role-based Access Control:
  - **Admin**
  - **User**
- Session handling & prevent back history

### 📦 Manajemen Produk & Stok (Admin)
- CRUD data barang
- Import produk (Excel)
- Update stok barang
- Monitoring stok masuk & keluar

### 🧾 Transaksi Penjualan (User)
- Input transaksi penjualan
- Riwayat transaksi
- Pengurangan stok otomatis

### 📊 Laporan (Admin)
- Laporan penjualan
- Export laporan ke **PDF**
- Export laporan ke **Excel**

### 👥 Manajemen Pengguna (Admin)
- Melihat daftar user
- Menghapus user

---

## 🧑‍💻 Tech Stack

### Backend
- **Laravel 12**
- **PHP 8.3**
- **MySQL / MariaDB**
- **Livewire v3**

### Frontend
- **Blade Template**
- **Bootstrap 5**
- **Tailwind CSS**
- **Alpine.js**

### Tools & Library
- **Maatwebsite Excel** (Export Excel)
- **DomPDF** (Export PDF)
- **Laravel Auth & Middleware**

---

## 📂 Role & Akses

| Role  | Hak Akses |
|------|----------|
| Admin | Dashboard, Produk, Stok, Laporan, User |
| User  | Dashboard, Transaksi, Riwayat |

---

## ⚙️ Instalasi & Setup

### 1️⃣ Clone Repository
```bash
git clone https://github.com/Skywalker0715/NAMA-REPO.git
cd NAMA-REPO
