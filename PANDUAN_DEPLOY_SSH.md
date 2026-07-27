# Panduan Setup Deployment Laravel di Hosting via SSH (Branch gh-pages)

Dokumen ini berisi panduan lengkap setup awal dari nol hingga cara update rutin aplikasi Laravel di shared hosting / VPS menggunakan **SSH Terminal**, tanpa perlu menjalankan `composer install` atau `npm install` di server hosting.

---

## 📋 Prasyarat

Sebelum memulai di SSH Hosting, pastikan:
1. **GitHub Actions** di branch `main` sudah pernah berjalan sukses minimal 1 kali (sehingga branch `gh-pages` sudah dibuat otomatis oleh GitHub).
2. Anda sudah membuat **Database MySQL** di cPanel/Hosting (catat Nama Database, Username DB, dan Password DB).
3. Anda sudah terhubung ke hosting via **SSH Terminal** atau **Terminal cPanel**.

---

## 🚀 Tahap 1: Setup Awal dari Nol di Hosting via SSH

### Step 1: Pindah ke Folder Domain / Web Root
Masuk ke direktori web root di hosting Anda (sesuaikan path dengan struktur hosting Anda):

```bash
# Contoh untuk domain utama (public_html) atau folder subdomain
cd public_html
# ATAU jika folder subdomain:
# cd ~/subdomain.domainanda.com
```

> ⚠️ **Penting:** Pastikan folder dalam keadaan kosong sebelum melakukan clone. Jika ada file bawaan seperti `index.php` atau `cgi-bin`, hapus lebih dulu:
> ```bash
> rm -rf * .[^.]*
> ```

---

### Step 2: Clone Branch `gh-pages`
Clone **hanya branch `gh-pages`** ke folder saat ini (tanda titik `.` artinya clone ke folder tempat Anda berada):

```bash
git clone -b gh-pages https://github.com/USERNAME_ANDA/REPO_ANDA.git .
```
*(Ganti `USERNAME_ANDA/REPO_ANDA` dengan URL repository GitHub Anda)*.

---

### Step 3: Buat dan Konfigurasi File `.env`
Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` menggunakan text editor terminal (seperti `nano`):

```bash
nano .env
```

Sesuaikan konfigurasi berikut:
```env
APP_NAME="Nama Aplikasi"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_hosting
DB_USERNAME=user_db_hosting
DB_PASSWORD=password_db_hosting
```
*Tekan `Ctrl + O` lalu `Enter` untuk menyimpan, kemudian `Ctrl + X` untuk keluar dari nano.*

---

### Step 4: Jalankan Inisialisasi Perintah Laravel
Jalankan perintah-perintah Artisan berikut secara berurutan:

```bash
# 1. Generate Application Key
php artisan key:generate

# 2. Buat symlink folder storage ke public
php artisan storage:link

# 3. Jalankan migrasi database
php artisan migrate --force

# 4. Optimasi & cache konfigurasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 5: Atur Permission Folder (Jika Diperlukan)
Jika muncul error permission/hak akses saat membuka website, jalankan:

```bash
chmod -R 775 storage bootstrap/cache
```

---

### Step 6: Arahkan Document Root Web Server ke Folder `/public`
Di cPanel (Domain / Subdomain Manager):
* Edit **Document Root** domain/subdomain Anda agar mengarah ke folder **`public`**.
* Contoh: `public_html/public` atau `subdomain.domainanda.com/public`.

---

## 🔄 Tahap 2: Cara Update Rutin Setiap Ada Perubahan Code

Setelah setup awal di atas selesai, alur kerja pengubahan kode menjadi sangat sederhana:

### 1. Di Komputer Lokal Anda:
Cukup buat perubahan kode, commit, dan push ke branch `main`:
```bash
git add .
git commit -m "fitur: update aplikasi"
git push origin main
```
*Tunggu sekitar 1–2 menit sampai GitHub Actions di tab **Actions** GitHub selesai berjalan.*

### 2. Di SSH Hosting Anda:
Buka SSH Terminal hosting Anda, masuk ke folder project, lalu jalankan:

```bash
# 1. Tarik hasil build terbaru dari branch gh-pages
git pull origin gh-pages

# 2. Jalankan migrasi database & refresh cache (jika ada perubahan DB/Route/Config)
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🛠️ Troubleshooting (Kendala Umum)

### 1. Error 404 / 403 Forbidden saat membuka web
* **Sebab:** Document Root di cPanel belum mengarah ke folder `public`.
* **Solusi:** Pastikan Document Root mengarah ke `.../public`.

### 2. Error 500 Internal Server Error
* **Sebab:** Permasalahan di file `.env`, database, atau permission folder `storage`.
* **Solusi:** Cek isi file `storage/logs/laravel.log` dengan perintah `cat storage/logs/laravel.log | tail -n 50`.

### 3. Git Push di Komputer Lokal Ditolak (Rejected)
* **Solusi:** Jalankan `git pull origin main --rebase` lebih dulu di komputer lokal, kemudian lakukan `git push origin main`.
