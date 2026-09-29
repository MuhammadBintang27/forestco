# ForestCo - Platform Sewa Kos / Rumah / Ruko

Aplikasi Laravel untuk mengelola katalog properti sewa (Kos, Rumah, Ruko) beserta alur
reservasi → verifikasi admin → jadwal cek unit (opsional) → pembayaran → sewa aktif → perpanjangan.

## Fitur Utama

- **Login berbasis email** (register, login, lupa sandi) - role `admin` dan `penyewa`.
- **Katalog properti** dengan maksimal 3 foto per properti; tipe Kos punya daftar kamar yang bisa dipilih penyewa.
- **Alur reservasi lengkap**: ajukan → verifikasi admin → (opsional) ajukan & setujui jadwal cek → upload bukti transfer → verifikasi pembayaran admin → resmi menyewa.
- **Redirect WhatsApp otomatis** di setiap aksi ajukan/verifikasi (memakai `wa.me`, tidak butuh API berbayar).
- **Pengingat perpanjangan H-7** lewat email terjadwal, dengan alur perpanjangan yang memakai ulang mekanisme upload bukti transfer.
- Panel Admin (`/admin`) untuk kelola properti, kamar, reservasi, pembayaran, perpanjangan, dan pengaturan rekening/No. WA.

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Database

Proyek ini memakai **MySQL** (via XAMPP). Konfigurasi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kos_rental
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan service MySQL di XAMPP menyala, lalu buat database `kos_rental` (lewat phpMyAdmin
atau `CREATE DATABASE kos_rental;`) sebelum menjalankan migrasi.

### Migrasi & Data Contoh

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeder membuat:
- Akun **Admin**: `admin@forestco.test` / `password`
- Akun **Penyewa** contoh: `penyewa@forestco.test` / `password`
- 3 properti contoh (1 Kos dengan 12 kamar, 1 Rumah, 1 Ruko) beserta fasilitas.
- Setting rekening & No. WA admin contoh - **ganti di `/admin/pengaturan` sebelum go-live**.

> Catatan: karena model bisnisnya single-admin, akun Admin baru **tidak** bisa self-register
> lewat halaman publik - buat lewat `php artisan tinker` atau seeder tambahan bila perlu staf lain.

### Build Frontend

```bash
npm run build     # produksi
npm run dev       # development (hot reload)
```

### Menjalankan

```bash
php artisan serve
```

## Pengingat Perpanjangan Sewa (H-7)

Command `php artisan rentals:remind-expiring` mengirim email ke penyewa yang masa sewanya
berakhir 7 hari lagi, dan hanya sekali per siklus sewa (ditandai lewat kolom
`rentals.reminder_sent_at`, direset otomatis setiap kali perpanjangan disetujui).

Command ini didaftarkan di `routes/console.php` untuk berjalan setiap hari jam 08:00.
Agar scheduler benar-benar berjalan, perlu ada proses yang memicu `php artisan schedule:run`
setiap menit:

- **Development**: jalankan `php artisan schedule:work` di terminal terpisah.
- **Produksi (Windows)**: buat Task Scheduler entry yang menjalankan
  `php artisan schedule:run` setiap menit di direktori proyek ini.
- **Produksi (cPanel/Linux)**: tambahkan cron job standar Laravel:
  `* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1`

### Konfigurasi Email

Default `.env` memakai `MAIL_MAILER=log` (email hanya ditulis ke `storage/logs/laravel.log`,
tidak benar-benar terkirim) - cocok untuk development. Untuk produksi, isi kredensial SMTP
sesungguhnya di `.env` (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_USERNAME`, dst).

## Integrasi WhatsApp

Setiap aksi "ajukan"/"verifikasi" (reservasi, jadwal cek, bukti transfer, perpanjangan)
melakukan redirect ke `https://wa.me/<nomor>?text=<pesan>` - nomor tujuan adalah nomor WA
admin (diambil dari `/admin/pengaturan`) atau nomor WA penyewa terkait (diisi saat
registrasi/di halaman Profil). Tidak ada API key atau biaya tambahan yang diperlukan.

## Struktur Alur Data Singkat

```
Reservation: pending → verified → (awaiting_payment setelah upload bukti) → active → ended
                     ↘ rejected
Inspection : pending → approved → done   (atau → rejected, bisa ajukan ulang)
Payment    : review → verified  (atau → rejected, bisa upload ulang)
Rental     : dibuat otomatis saat Payment reservasi diverifikasi
RentalExtension: requested → awaiting_payment → approved (memperpanjang rentals.end_date)
```
