# UPDATE_LOG

## 2026-03-13

### Security & Access Control
- Admin access ke modul **Pendaftaran** dan **Rekam Medis** sudah ditutup di route backend.
- Navigasi UI diperbarui agar admin tidak melihat menu Pendaftaran/Rekam Medis.

### Auth & Production Readiness
- Route publik **/register** dinonaktifkan (akun tidak bisa daftar mandiri).
- Penyesuaian timezone aplikasi via env: `APP_TIMEZONE` (default `Asia/Jakarta`).
- Menambahkan template environment produksi: `.env.production.example`.

### Testing Stabilization
- Menyesuaikan auth test dari login email ke login username.
- Menyesuaikan test profile agar menggunakan field `username` (sesuai implementasi aktual).
- Menyesuaikan test untuk route yang memang tidak dipakai (register/password reset/email verification) agar sesuai behavior aplikasi.
- Menyesuaikan test password policy agar sesuai rule keamanan aktif.
- Konfigurasi test base diperbarui agar CSRF middleware tidak mengganggu test web form.

### Verification
- `php artisan test` ✅ (20 passed)
- `php artisan migrate --force` ✅ (Nothing to migrate)

### Notes for Deployment
- Saat deploy ke server, gunakan `.env.production.example` sebagai acuan dan isi nilai sensitif secara manual.
- Setelah set env produksi, jalankan:
  - `php artisan optimize:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
