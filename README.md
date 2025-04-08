# POS Bengkel - Laravel Filament

Aplikasi Point of Sale (POS) untuk bengkel yang dibangun menggunakan Laravel dan Filament Admin Panel.

## Deskripsi

POS Bengkel adalah aplikasi manajemen bengkel yang memungkinkan pengguna untuk mengelola transaksi, pelanggan, suku cadang, supplier, dan analitik berdasarkan data penjualan. Aplikasi ini dibangun dengan Laravel sebagai framework back-end dan Filament sebagai panel admin.

## Fitur

- Dashboard analitik untuk monitoring kinerja bisnis
- Manajemen kategori produk
- Manajemen pelanggan
- Inventaris suku cadang (spareparts)
- Manajemen supplier
- Sistem transaksi lengkap
- Laporan dan analitik data

## Persyaratan Sistem

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Instalasi

### 1. Clone repositori

```bash
git clone https://github.com/username/pos-bengkel.git
cd pos-bengkel
```

### 2. Instal dependencies PHP

```bash
composer install
```

### 3. Instal dependencies JavaScript

```bash
npm install
npm run build
```

### 4. Setup lingkungan

- Salin file `.env.example` menjadi `.env`
```bash
cp .env.example .env
```

- Generate application key
```bash
php artisan key:generate
```

- Konfigurasi database Anda di file `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_bengkel
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi database dan seeder

```bash
php artisan migrate --seed
```

### 6. Instal dan publikasikan Filament

```bash
composer require filament/filament:"^3.0-stable"
php artisan filament:install --panels
```

### 7. Jalankan aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Kontribusi

Silahkan buat pull request untuk berkontribusi pada proyek ini.

## Lisensi

[MIT](https://opensource.org/licenses/MIT)
