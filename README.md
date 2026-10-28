# PORMIN — Panduan Menjalankan di Server Lokal

Portal Pendaftaran Murid Baru Al-Azhar Cairo Palembang · Laravel 13 + MySQL + Blade.

---

## 1. Download Source Code

Di halaman Emergent, klik **"Save to GitHub"** di kolom chat → hubungkan akun GitHub → push ke repository. Kemudian di komputer/server lokal Anda:

```bash
git clone https://github.com/<username-anda>/<nama-repo>.git
cd <nama-repo>/pormin
```

**Alternatif tanpa GitHub:** minta admin Emergent untuk export ZIP folder `/app/pormin` dan `/app/pormin-data` (data MySQL) lalu upload ke server Anda.

---

## 2. Prasyarat Server

| Kebutuhan | Versi | Cek |
|-----------|-------|-----|
| PHP | ≥ 8.3 | `php -v` |
| Composer | ≥ 2.5 | `composer --version` |
| MySQL / MariaDB | ≥ 8.0 / 10.6 | `mysql --version` |
| Ekstensi PHP wajib | `mbstring, curl, pdo_mysql, xml, zip, bcmath, gd` | `php -m` |

### Install cepat di Ubuntu/Debian
```bash
sudo apt update
sudo apt install -y php8.3-cli php8.3-mysql php8.3-mbstring php8.3-curl \
                    php8.3-xml php8.3-zip php8.3-bcmath php8.3-gd \
                    mariadb-server composer git unzip
```

### Install cepat di Windows
Gunakan **Laragon** (https://laragon.org) atau **XAMPP** dengan PHP 8.3+ — sudah include Apache/Nginx + MySQL + Composer.

### Install cepat di macOS
```bash
brew install php@8.3 composer mariadb
brew services start mariadb
```

---

## 3. Setup Database

Buka MySQL/MariaDB console lalu jalankan:

```sql
CREATE DATABASE pormin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pormin'@'localhost' IDENTIFIED BY 'pormin_password';
GRANT ALL PRIVILEGES ON pormin.* TO 'pormin'@'localhost';
FLUSH PRIVILEGES;
```

*(Ganti `pormin_password` dengan password yang lebih aman untuk production.)*

---

## 4. Setup Aplikasi

Masuk ke folder `pormin` (root Laravel), lalu:

```bash
# 1. Install dependency PHP
composer install

# 2. Salin & sesuaikan environment file
cp .env.example .env    # jika .env belum ada
```

Edit file `.env`:
```env
APP_NAME="PORMIN"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pormin
DB_USERNAME=pormin
DB_PASSWORD=pormin_password

SESSION_DRIVER=database
```

Lalu:
```bash
# 3. Generate app key
php artisan key:generate

# 4. Migrasi + seed data awal (grade, kampus, admin, dll)
php artisan migrate:fresh --seed --force

# 5. Symlink storage untuk upload logo
php artisan storage:link

# 6. Beri izin tulis
chmod -R 775 storage bootstrap/cache public/uploads
```

---

## 5. Jalankan Aplikasi

### Mode Development (paling gampang)
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Buka: **http://localhost:8000**

### Mode Production (nginx + PHP-FPM)

Contoh konfigurasi nginx (`/etc/nginx/sites-available/pormin`):
```nginx
server {
    listen 80;
    server_name pormin.sekolah.sch.id;
    root /var/www/pormin/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    location ~ /\.ht { deny all; }
}
```
Aktifkan:
```bash
sudo ln -s /etc/nginx/sites-available/pormin /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo chown -R www-data:www-data /var/www/pormin
```

---

## 6. Login Admin Default

- URL: **/alazcaplg/login**
- Email: `admin@alazharcairoplg.sch.id`
- Password: `Admin@12345`

> **Wajib ganti password** setelah login pertama untuk keamanan.

---

## 7. Struktur Folder Utama

```
pormin/
├── app/
│   ├── Http/Controllers/         # Controller (Admin/, RegistrationController)
│   ├── Models/                   # Eloquent models
│   └── Services/                 # Business logic (Age, Duplicate, RegNumber, PDF, WA, BatchImport)
├── database/
│   ├── migrations/               # Schema
│   └── seeders/DatabaseSeeder.php # Data awal (grade, kampus, admin)
├── resources/views/              # Blade template
│   ├── layouts/                  # Layout public & admin
│   ├── admin/                    # Halaman admin panel
│   ├── registration/             # Form pendaftaran & hasil
│   └── pdf/                      # Template PDF bukti & surat observasi
├── routes/web.php                # Semua route aplikasi
├── public/                       # Web root (index.php, uploads/logo)
└── .env                          # Konfigurasi environment
```

---

## 8. Menambah / Menonaktifkan Grade

1. Login admin → **Grade & Aturan Usia**
2. Klik tombol status (**Aktif** hijau / **Nonaktif** abu-abu) untuk toggle cepat — grade yang nonaktif tidak muncul di halaman pendaftaran publik.
3. Untuk edit detail atau menambah grade baru (mis. SMA belum siap), pakai form di kolom kiri
4. Aturan usia per grade di-set lewat tombol **slider** di baris grade (min/max tahun/bulan/hari + tanggal acuan)

---

## 9. Fitur Utama

- ✅ Form pendaftaran online 6 jenjang (PG, TK-A, TK-B, SD, SMP, SMA)
- ✅ Validasi usia otomatis pada tanggal 1 Juli tahun ajaran (configurable)
- ✅ Deteksi duplikat berdasarkan nama + tanggal lahir + grade + tahun ajaran
- ✅ Nomor pendaftaran otomatis aman-concurrent (row-locked sequence)
- ✅ Bukti Pendaftaran PDF + QR Code verifikasi
- ✅ Surat Panggilan Observasi PDF
- ✅ Halaman verifikasi publik `/verify/{token}` (data terbatas untuk privasi)
- ✅ Admin dashboard + statistik per grade + timeline status
- ✅ Workflow: Terdaftar → Lolos Admin → Layak Observasi → Dipanggil → Diterima
- ✅ Upload logo sekolah (tampil di navbar + PDF)
- ✅ Papan pengumuman running text di landing
- ✅ Import batch CSV data pendaftar lama
- ✅ Notifikasi WhatsApp otomatis saat jadwal observasi (mode wa.me gratis atau Fonnte API)
- ✅ Export CSV data pendaftaran
- ✅ Audit log semua aksi admin

---

## 10. Backup Data

```bash
# Backup DB
mysqldump -u pormin -p pormin > backup-pormin-$(date +%F).sql

# Backup uploads (logo & file)
tar czf uploads-$(date +%F).tar.gz public/uploads storage/app/public
```

Restore:
```bash
mysql -u pormin -p pormin < backup-pormin-2026-01-01.sql
```

---

## 11. Perintah Berguna

```bash
# Reset DB & seed ulang
php artisan migrate:fresh --seed --force

# Bersihkan cache
php artisan optimize:clear

# Buat admin baru via tinker
php artisan tinker
>>> App\Models\User::create(['name'=>'Nama','email'=>'x@y.com','password'=>bcrypt('rahasia123')]);
```

---

## 12. Troubleshooting

**"SQLSTATE[HY000] [2002] Connection refused"**  
→ MySQL/MariaDB belum jalan. Jalankan `sudo systemctl start mariadb`.

**"Error 500 di halaman apapun"**  
→ Cek `storage/logs/laravel.log`. Biasanya `APP_KEY` belum di-generate (`php artisan key:generate`) atau folder `storage` tidak writable.

**"Route [login] not defined"**  
→ Sudah dihandle otomatis. Pastikan `bootstrap/app.php` tidak dimodifikasi.

**"Unable to write file" saat upload logo**  
→ `chmod -R 775 public/uploads` dan pastikan owner sesuai user PHP (www-data / apache).

**PDF Bukti kosong/error**  
→ Pastikan ekstensi PHP `gd` dan `mbstring` terpasang, dan `storage/framework/views` writable.

---

## 13. Update Aplikasi

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
sudo systemctl reload nginx
```

---

## Kredensial Ringkas

| Item | Nilai |
|------|-------|
| Admin URL | `/alazcaplg/login` |
| Admin Email | `admin@alazharcairoplg.sch.id` |
| Admin Password | `Admin@12345` |
| DB Name | `pormin` |
| DB User | `pormin` |
| DB Password | `pormin_password` |

**Selamat digunakan! 🎓**  
PORMIN © Al-Azhar Cairo Palembang
