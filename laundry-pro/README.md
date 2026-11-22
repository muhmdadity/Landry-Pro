# LaundryPro v2.0 — Sistem POS & Manajemen Laundry Profesional

Aplikasi web manajemen operasional laundry modern berbasis **PHP 8+**, **PDO MySQL**, dan **Bootstrap 5.3**, dilengkapi konfigurasi workspace **VS Code** siap pakai.

## Fitur Utama & Peningkatan dari Versi Sebelumnya
1. **Keamanan Standar Industri:**
   - Seluruh query database menggunakan **PDO Prepared Statements** (bebas SQL Injection).
   - Proteksi **CSRF Token** pada semua formulir aksi.
   - Enkripsi password modern dengan `password_hash()` & `password_verify()`.
2. **Database Baru & Relasional (`db_laundry_pro`):**
   - Tabel `layanan` dinamis (tarif per Kg/Pcs/Meter dan estimasi jam dapat diatur langsung dari dashboard).
   - Tabel `transaksi` dilengkapi kode invoice unik (`INV-YYYYMMDD-XXXXX`), metode bayar (Tunai, QRIS, Transfer), status lunas/belum lunas, diskon, serta estimasi selesai otomatis.
   - Tabel `pengaturan_toko` untuk kustomisasi nama usaha, alamat, tarif ongkir default, dan footer struk cetak.
3. **Halaman Publik & Cek Resi Tanpa Login (`index.php`):**
   - Pelanggan dapat mengecek progres cucian langsung menggunakan Nomor Invoice atau No HP, serta melihat daftar tarif layanan.
4. **Workspace VS Code Terkonfigurasi (`.vscode/`):**
   - Dilengkapi `settings.json`, `extensions.json` (rekomendasi Intelephense, PHP Debug, SQLTools), dan `launch.json` untuk menjalankan PHP Built-in Server langsung dari VS Code (`F5`).

## Cara Instalasi
1. Ekstrak folder `laundry-pro` ke dalam direktori web server Anda (`htdocs` pada XAMPP atau `www` pada Laragon), lalu buka folder tersebut di **VS Code**.
2. Buka **phpMyAdmin**, buat database baru bernama `db_laundry_pro`, lalu impor file `database/db_laundry_pro.sql`.
3. Periksa konfigurasi port di `config/database.php` (sudah mendukung auto-fallback antara port `3306` dan `3307`).
4. Akses aplikasi di browser melalui `http://localhost/laundry-pro/`.

## Akun Login Default
- **Admin:** Username `admin` | Password `admin123`
- **Pelanggan Contoh:** No HP `081234567890` | Password `laundry_7890`
