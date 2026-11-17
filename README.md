# CampusSpace — paket kode (overlay Laravel + React + Inertia)

> **STATUS: BELUM DIUJI.** Kode ini ditulis tanpa PHP/Composer dan tanpa akses jaringan, jadi **test, `npm run build`, dan koneksi TiDB/Groq belum pernah dijalankan**. Jalankan langkah di bagian 4 dan perbaiki error yang muncul sebelum dipakai.

Paket ini adalah *overlay*: file-file yang ditumpuk di atas proyek Laravel (Breeze + React/Inertia). Autentikasi (register, login, logout, lupa/reset password, profil) memakai **Laravel Breeze** agar teruji; role dan otorisasi ditambahkan di sini.

## 1. Isi paket
- `database/migrations/…campusspace_safe_schema.php` — **non-destruktif**: hanya membuat tabel/kolom yang belum ada (`hasTable/hasColumn`), tanpa drop/truncate.
- `app/Models`, `app/Services` (Booking, Availability, CheckIn, QRCode, GroqAI, Notification), `app/Policies`, `app/Http/Controllers` (siswa + `Admin/`), `app/Console/Commands/ProcessBookings.php`.
- `resources/js` — layout (App/Admin), komponen, 17 halaman. `routes/web.php`, `routes/console.php`.
- `tests/Feature` — booking, bentrok, satu booking aktif, hanya hari ini, QR check-in, batas 10 menit, scheduler expire, akses role, review, AI.
- `Dockerfile`, `Caddyfile`, `vercel.json`, `.dockerignore`, `.env.campusspace.example`, `install.sh`.

## 2. Instalasi
Dari root proyek Laravel Anda (jika proyek sudah ada, jangan buat ulang):
```bash
bash /path/ke/campusspace/install.sh   # composer: breeze (jika belum), simple-qrcode; npm: lucide-react, html5-qrcode; salin overlay
```
File yang ditimpa dicadangkan di `.campusspace-backup/`. Lalu manual:
1. **`config/services.php`**: salin isi array dari `config/services.campusspace.php` (kunci `groq` dan `cron`).
2. **`.env`**: ikuti `.env.campusspace.example` (kredensial TiDB, `MYSQL_ATTR_SSL_CA`, `GROQ_API_KEY`, `CRON_SECRET`, `APP_TIMEZONE`). Di `config/database.php` koneksi `mysql` pastikan `options` memakai `PDO::MYSQL_ATTR_SSL_CA` dari `env('MYSQL_ATTR_SSL_CA')` (bawaan Laravel sudah membacanya).
3. **Dark mode Tailwind**: v3 → `darkMode: 'class'` di `tailwind.config.js`; v4 → tambahkan di `resources/css/app.css`: `@custom-variant dark (&:where(.dark, .dark *));`
4. Pastikan alias `@/` → `resources/js` (default Breeze).

## 3. Database TiDB (PENTING sebelum migrate)
Skema Anda yang sudah ada belum bisa saya lihat. Bandingkan dulu:
```bash
php artisan migrate --pretend      # lihat SQL tanpa menjalankan
```
Jika nama/kolom tabel Anda berbeda (mis. `status` bertipe enum, tabel `notifications` sendiri), sesuaikan model/migration — jangan jalankan `migrate:fresh`. Catatan desain: notifikasi memakai tabel **`campus_notifications`** karena tabel `notifications` bawaan Laravel berskema uuid; `users.role` default `student`; status disimpan sebagai string.
```bash
php artisan migrate
php artisan db:seed --class=CampusSpaceSeeder   # DEMO saja: admin/staff/student@campusspace.test, password dari SEED_PASSWORD
```

## 4. Menjalankan & menguji
```bash
composer run dev            # atau: php artisan serve  +  npm run dev
php artisan test            # jalankan semua test
npm run build
php artisan schedule:work   # lokal: memproses expired/selesai/pengingat tiap menit (atau: php artisan bookings:process)
```

## 5. Aturan bisnis yang diterapkan
Satu booking aktif per akun; hanya hari ini (`APP_TIMEZONE`); cek bentrok + transaksi + `lockForUpdate` (user dan unit); status hanya diubah backend; check-in QR wajib ≤ batas (default 10 menit, bisa diubah di System Settings); durasi min/maks **dikosongkan sampai admin menetapkannya** (tidak ada angka karangan). QR = token acak 48 karakter per unit; QR tidak membuat booking dan tidak bisa dipakai untuk booking orang lain. AI hanya membaca data DB yang disuntikkan ke prompt dan tidak punya jalur untuk mengubah booking.

## 6. Deployment Vercel (Docker + FrankenPHP)
Pendekatan ini didukung dokumentasi Vercel (diperiksa 17 Nov 2026): `vercel.json` dengan `services.runtime = "container"`, Dockerfile FrankenPHP, env diinjeksi saat runtime.
1. Set env di Vercel: semua variabel `.env` (termasuk `APP_KEY` dari `php artisan key:generate --show`, `DB_*`, `GROQ_API_KEY`, `CRON_SECRET`). Jangan memasukkan `.env` ke image.
2. `vercel deploy --prod`.
3. **Migrasi**: jalankan `php artisan migrate --force` sebagai langkah rilis/CI terpisah, bukan saat container start. Sesi/cache memakai database (butuh tabel `sessions` & `cache` dari migrasi bawaan Laravel).
4. **Scheduler/queue tidak otomatis jalan di Vercel.** Dipakai Vercel Cron memanggil `GET /internal/cron/process-bookings` dengan header `Authorization: Bearer <CRON_SECRET>` (sudah ada di `vercel.json`, tiap menit). **Cek batas frekuensi cron pada paket Vercel Anda**; bila tidak mendukung per menit, gunakan pemicu eksternal (mis. cron-job.org) ke URL yang sama dengan header tersebut.
5. Filesystem container tidak persisten; paket ini tidak menyimpan upload (foto saung berupa URL).
6. Koneksi TiDB: gunakan TLS (CA sistem Debian: `/etc/ssl/certs/ca-certificates.crt`) dan jaga jumlah koneksi (banyak instance serverless).

## 7. Yang sengaja berbeda dari brief
- shadcn/ui tidak dipasang; komponen Tailwind sendiri + Lucide.
- Halaman fasilitas satu file `Facilities/Index.jsx` untuk tiga kategori (bukan tiga folder).
- Admin: pengelolaan fasilitas/unit sudah CRUD dasar (edit lengkap fasilitas via prompt sederhana); layak dipoles.
- Status `pending` didukung di model/UI, tetapi booking baru langsung `confirmed` (tidak ada alur persetujuan).
