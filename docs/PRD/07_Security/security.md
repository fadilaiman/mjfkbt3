# PRD 07 — Keselamatan Sistem (Security)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Gambaran Keseluruhan Model Keselamatan

Laman web ini mempunyai **satu pengguna admin** dan pengunjung awam yang tidak memerlukan log masuk. Model ancaman adalah:

| Aktor | Potensi Ancaman |
|-------|----------------|
| Pengunjung awam biasa | Tiada ancaman (hanya baca) |
| Bot automatik | Scraping, brute force login, spam |
| Penyerang luaran | Eksploit input form, file upload berbahaya, SQL injection |
| Pengguna dalam (compromised admin) | Muat naik fail berbahaya, akses data sensitif |
| Pihak ketiga API | Token/key dicuri jika terdedah |

Prinsip utama: **Laman web ini tidak menyimpan data peribadi pengguna awam dalam fasa 1.** Ini mengurangkan risiko keselamatan secara drastik.

---

## 2. Pengesahan Admin (Authentication)

### 2.1 Mekanisme Log Masuk

Sistem menggunakan **Laravel's built-in authentication** dengan guard khas untuk admin:

```php
// config/auth.php
'guards' => [
    'admin' => [
        'driver' => 'session',
        'provider' => 'admin_users',
    ],
],
'providers' => [
    'admin_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\AdminUser::class,
    ],
],
```

**Proses log masuk:**
1. Admin masukkan e-mel dan kata laluan.
2. Sistem semak `admin_users` table.
3. Kata laluan disahkan menggunakan `Hash::check()` (bcrypt).
4. Sesi diwujudkan; ID sesi disimpan dalam cookie HTTPS-only.
5. Tindakan admin dilog dalam `admin_logs` table.

### 2.2 Konfigurasi Kata Laluan

- **Minimum panjang:** 12 aksara.
- **Keperluan:** Sekurang-kurangnya satu huruf besar, satu huruf kecil, satu nombor, satu aksara khas.
- **Hashing:** `bcrypt` dengan kos 12 (Laravel default).
- **Jangan simpan** kata laluan plaintext di mana-mana (log, DB, email).

Dalam code Laravel:
```php
'password' => ['required', 'string', 'min:12',
    Password::min(12)->mixedCase()->numbers()->symbols()
]
```

### 2.3 "Ingat Saya" (Remember Me)

- Jika admin pilih "Ingat Saya", token disimpan dalam cookie `remember_token`.
- Token ini adalah random 60-aksara yang disimpan (hashed) dalam `admin_users.remember_token`.
- Cookie `remember_token` mesti: `HttpOnly`, `Secure`, `SameSite=Strict`.
- Tempoh: 30 hari. Selepas itu, admin perlu log masuk semula.

### 2.4 Log Keluar

- Hapus sesi dari server.
- Hapus cookie sesi dari browser.
- Jika "Ingat Saya" aktif, padam `remember_token` dari DB dan dari cookie.
- Redirect ke `/admin/login`.

### 2.5 Fungsi Lupa Kata Laluan

- Admin masukkan e-mel.
- Sistem hantar e-mel dengan link reset (token one-time, luput dalam 60 minit).
- Token disimpan dalam `password_reset_tokens` table (Laravel standard).
- Link reset: `https://mjfkbt3.saasku.my/admin/reset-kata-laluan?token={TOKEN}&email={EMAIL}`.
- Selepas reset berjaya, semua sesi lain dimansuhkan.

---

## 3. Kawalan Akses (Access Control)

### 3.1 Middleware Stack untuk Admin

Setiap request ke `/admin/*` (kecuali `/admin/login`) melalui:

```
1. StartSession          — Mulakan sesi
2. VerifyCsrfToken       — Semak CSRF token
3. Authenticate (admin)  — Pastikan pengguna sudah log masuk sebagai admin
4. AdminActive           — Pastikan akaun admin aktif (admin_users.is_active = true)
5. LogAdminActivity      — Log request untuk audit (untuk POST/PUT/DELETE sahaja)
```

### 3.2 Tiada Pendaftaran Awam

- Tiada endpoint untuk cipta akaun admin dari luar.
- Akaun admin **hanya boleh dicipta melalui Artisan command atau terus dalam DB** oleh pembangun.
- Ini mengelakkan serangan pendaftaran tidak sah.

```bash
# Cara cipta akaun admin (satu kali sahaja)
php artisan admin:create --name="Admin MJFKBT3" --email="admin@mjfkbt3.saasku.my"
```

---

## 4. Pengurusan API Keys

### 4.1 Penyimpanan Keys

**Wajib:**
- Semua API keys, secrets, dan tokens disimpan **HANYA dalam fail `.env`**.
- Fail `.env` mesti ada dalam `.gitignore` — **jangan commit ke Git**.
- Jangan log nilai API key dalam mana-mana log.
- Jangan pass API key melalui URL query string.

**Struktur `.env` untuk keys:**
```
# Tidak boleh dibaca oleh frontend Vue/JS — hanya backend PHP
YOUTUBE_API_KEY=AIza...
FACEBOOK_PAGE_ACCESS_TOKEN=EAA...
FACEBOOK_APP_SECRET=...
```

**Pengesahan:** Jalankan `git status` sebelum commit untuk pastikan `.env` tidak tersenarai.

### 4.2 Hadkan Skop API Key

| API Key | Hadkan Kepada |
|---------|--------------|
| YouTube API Key | - Domain: `mjfkbt3.saasku.my` sahaja<br>- API: YouTube Data API v3 sahaja<br>- Di Google Cloud Console → Credentials → Edit → Application restrictions |
| Facebook App | - Allowed Domains: `mjfkbt3.saasku.my`<br>- OAuth Redirect URIs: URL tertentu sahaja |

### 4.3 Rotasi Keys

Jika ada syak wasangka key telah terdedah (contoh: accidentally committed to Git):

1. Revoke key segera dalam konsol provider (Google Cloud / Meta Developer).
2. Jana key baru.
3. Kemas kini `.env` di server.
4. Restart PHP-FPM: `sudo systemctl restart php8.x-fpm`.
5. Semak log untuk tanda-tanda penyalahgunaan.

---

## 5. Perlindungan XSS (Cross-Site Scripting)

### 5.1 Perlindungan Automatik Laravel + Blade

Laravel Blade secara automatik escape output:
```php
{{ $announcement->title }}  // Selamat — auto-escaped
{!! $announcement->body !!}  // BERBAHAYA — raw HTML, jangan guna melainkan perlu
```

**Peraturan ketat:** Jangan gunakan `{!! !!}` untuk mana-mana data yang dimasukkan oleh pengguna atau diambil dari API luaran.

### 5.2 Sanitasi Input

Untuk field yang membenarkan HTML (contoh: badan pengumuman dari WYSIWYG editor):
- Gunakan library `HTMLPurifier` atau `league/html-to-markdown` untuk strip tag berbahaya.
- Whitelist tag yang dibenarkan: `<p>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<h3>`, `<h4>`, `<br>`.
- Tag yang dilarang: `<script>`, `<iframe>`, `<object>`, `<embed>`, `<form>`, `on*` event attributes.

```php
// Contoh dalam FormRequest
protected function prepareForValidation()
{
    $this->merge([
        'body' => clean($this->body), // HTMLPurifier
    ]);
}
```

### 5.3 Perlindungan Dalam Vue 3

Vue 3 secara default escape semua content yang di-bind menggunakan `{{ }}` atau `v-text`. Hanya `v-html` yang tidak auto-escape.

**Peraturan:** Jangan gunakan `v-html` untuk data yang datang dari input pengguna atau API luaran yang tidak disahkan.

Pengecualian yang dibenarkan (dengan sanitasi sisi server terlebih dahulu):
- `v-html="video.oembed_html"` — untuk TikTok embed (HTML datang dari TikTok API, bukan input pengguna)
- `v-html="announcement.sanitized_body"` — field yang sudah diproses oleh HTMLPurifier

### 5.4 Content Security Policy (CSP)

Tetapkan header CSP untuk menyekat sumber kandungan yang tidak dijangka:

```
Content-Security-Policy:
  default-src 'self';
  script-src 'self' 'nonce-{NONCE}' https://www.youtube.com https://www.tiktok.com https://cdn.tailwindcss.com;
  frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.tiktok.com https://www.google.com;
  img-src 'self' data: https: blob:;
  style-src 'self' 'nonce-{NONCE}' https://fonts.googleapis.com;
  font-src 'self' https://fonts.gstatic.com;
  connect-src 'self';
```

Dalam Laravel, tambah middleware untuk set header ini:

```php
// app/Http/Middleware/ContentSecurityPolicy.php
public function handle(Request $request, Closure $next)
{
    $nonce = base64_encode(random_bytes(16));
    app()->instance('csp-nonce', $nonce);

    $response = $next($request);
    $response->headers->set('Content-Security-Policy', $this->buildCspHeader($nonce));
    return $response;
}
```

---

## 6. Perlindungan CSRF (Cross-Site Request Forgery)

### 6.1 Laravel CSRF Middleware

Laravel's `VerifyCsrfToken` middleware diaktifkan secara default untuk semua route `web`. Pastikan ia **tidak** ditambah ke dalam `except` array melainkan ada keperluan yang sangat spesifik.

```php
// Dalam Inertia + Vue, CSRF token dihantar automatik melalui
// header X-XSRF-TOKEN yang dibaca dari cookie
```

### 6.2 Webhook Endpoints

Jika ada webhook dari payment gateway (Fasa 2):
- Webhook endpoint diletakkan dalam `MiddlewareGroup::api` (tiada CSRF untuk API).
- Guna **signature verification** dari gateway untuk sahkan authenticity.
- Jangan letakkan webhook URL dalam senarai `except` CSRF tanpa ada alternatif.

---

## 7. Pengehadan Kadar (Rate Limiting)

### 7.1 Had Login Admin

Guna Laravel's built-in throttle untuk halaman login:

```php
// routes/web.php
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:5,15');  // 5 percubaan per 15 minit per IP
```

Apabila had dicapai:
- Balas dengan status HTTP 429.
- Papar mesej: "Terlalu banyak percubaan log masuk. Sila cuba lagi selepas 15 minit."
- Log percubaan (IP, e-mel, timestamp) dalam `admin_logs`.

### 7.2 Had API Scraping Endpoints

Jika ada endpoint yang boleh dicall oleh frontend untuk refresh data:

```php
Route::get('/api/waktu-solat', [PrayerController::class, 'today'])
    ->middleware('throttle:60,1');  // 60 request per minit per IP
```

### 7.3 Had Global

Tambah rate limiting global untuk semua halaman awam:

```php
// app/Http/Kernel.php — dalam throttle middleware
'global' => Limit::perMinute(120)->by(request()->ip()),
```

Ini mencukupi untuk laman web dengan < 500 pengunjung sehari.

---

## 8. Keselamatan Upload Fail

### 8.1 Validasi Fail

Validasi yang ketat dalam `FormRequest`:

```php
'attachment' => [
    'nullable',
    'file',
    'max:10240',  // 10MB dalam kilobytes
    'mimes:pdf,jpg,jpeg,png,webp',
    // Semak MIME type sebenar (bukan hanya extension)
    function ($attribute, $value, $fail) {
        $realMime = mime_content_type($value->path());
        $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($realMime, $allowed)) {
            $fail('Jenis fail tidak dibenarkan.');
        }
    }
]
```

**Kenapa dua lapisan semakan?**
- `mimes:` semak file extension sahaja — mudah ditipu dengan menukar extension.
- `mime_content_type()` baca magic bytes fail sebenar — tidak boleh ditipu.

### 8.2 Penyimpanan Fail yang Selamat

```php
// MediaUploadService.php

public function store(UploadedFile $file): MediaFile
{
    // 1. Jana nama fail UUID — elak nama asal pengguna
    $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();

    // 2. Simpan dalam storage/app/public/uploads/ (bukan dalam public/uploads)
    //    Akses melalui symlink atau serve melalui controller
    $path = $file->storeAs('uploads', $storedName, 'public');

    // 3. Rekod dalam DB
    return MediaFile::create([
        'original_name' => $file->getClientOriginalName(),
        'stored_name'   => $storedName,
        'path'          => $path,
        'mime_type'     => $file->getMimeType(),
        'file_size'     => $file->getSize(),
        'type'          => str_contains($file->getMimeType(), 'pdf') ? 'pdf' : 'image',
        'uploaded_by'   => auth('admin')->id(),
    ]);
}
```

### 8.3 Perkara yang TIDAK Boleh Dilakukan

- **Jangan** simpan fail dalam `/public` terus tanpa sanitasi nama.
- **Jangan** benarkan upload fail PHP, JS, HTML, EXE, SH, atau mana-mana fail boleh laksana.
- **Jangan** gunakan nama fail asal pengguna sebagai nama simpanan (risiko path traversal).
- **Jangan** serve fail terus — route melalui controller yang semak authorization.

### 8.4 Melayan Fail ke Pengguna

```php
// routes/web.php
Route::get('/fail/{filename}', [MediaController::class, 'serve'])
    ->where('filename', '[a-zA-Z0-9\-\.]+');  // Regex untuk elak traversal

// MediaController.php
public function serve(string $filename)
{
    $file = MediaFile::where('stored_name', $filename)->firstOrFail();

    // Semak fail wujud
    if (!Storage::disk('public')->exists($file->path)) {
        abort(404);
    }

    return Storage::disk('public')->download($file->path, $file->original_name);
}
```

---

## 9. Pematuhan PDPA (Perlindungan Data Peribadi)

### 9.1 Data yang Diproses dan Asas Perundangan

| Data | Sumber | Asas Perundangan | Tempoh Penyimpanan |
|------|--------|-----------------|-------------------|
| IP admin + tindakan | Log masuk admin | Kepentingan sah (audit keselamatan) | 90 hari |
| Session cookies | Sesi admin | Diperlukan untuk fungsi sistem | Tamat sesi |
| Remember token | Pilihan admin | Kebenaran (admin pilih "Ingat Saya") | 30 hari |
| Reset password token | Permintaan reset | Diperlukan untuk fungsi | 60 minit |

### 9.2 Data yang TIDAK Dikumpul (Fasa 1)

- Nama, e-mel, nombor telefon pengunjung awam.
- Cookies analytics (Google Analytics, Facebook Pixel).
- Data geolokasi pengguna.
- Rekod aktiviti pelayaran pengguna dalam DB aplikasi.

### 9.3 Keperluan Halaman Dasar Privasi

Halaman `/dasar-privasi` mesti mengandungi (dalam Bahasa Melayu):

1. **Nama dan alamat pengendali data:** Masjid Jamek Fastabiqul Khayrat Batu 3, Persiaran Jubli Perak Batu 3, Shah Alam, Selangor.
2. **Data yang dikumpul:** Hanya data teknikal standard (server logs, cookies sesi untuk admin).
3. **Tujuan:** Operasi laman web dan keselamatan.
4. **Pihak ketiga:** YouTube (Google), Facebook (Meta), TikTok, Google Maps — dengan pautan ke dasar privasi mereka.
5. **Hak anda:** Hak untuk bertanya tentang data yang dipegang.
6. **Hubungi kami:** Melalui WhatsApp atau e-mel yang tertera.
7. **Tarikh berkuat kuasa:** Tarikh halaman ini dikemas kini terakhir.

### 9.4 Implikasi Cookies

Jika menggunakan **hanya cookies yang diperlukan** untuk operasi (sesi, CSRF), **tiada cookie banner diperlukan** mengikut interpretasi ketat PDPA Malaysia. Walau bagaimanapun, jika analytics atau cookies pihak ketiga ditambah (Fasa 2), maka perlu ada:
- Cookie consent banner.
- Pilihan opt-in/opt-out.

---

## 10. Keselamatan Pangkalan Data

### 10.1 Elak SQL Injection

Laravel Eloquent ORM dan Query Builder menggunakan **prepared statements** secara automatik. Semua query melalui Eloquent adalah selamat dari SQL injection.

**Berbahaya (jangan lakukan):**
```php
// JANGAN — query raw dengan input pengguna
DB::statement("SELECT * FROM users WHERE name = '{$request->name}'");
```

**Selamat:**
```php
// Elok — Eloquent auto-escape
AdminUser::where('email', $request->email)->first();

// Elok — Query Builder dengan binding
DB::select('SELECT * FROM admin_users WHERE email = ?', [$request->email]);
```

### 10.2 Minimum Privilege untuk DB User

Cipta user MySQL khusus untuk aplikasi dengan kebenaran minimum:

```sql
-- Dalam MySQL
CREATE USER 'mjfkbt3_app'@'localhost' IDENTIFIED BY '{KATA_LALUAN_KUKUH}';
GRANT SELECT, INSERT, UPDATE, DELETE ON mjfkbt3.* TO 'mjfkbt3_app'@'localhost';
-- JANGAN beri DROP, CREATE, ALTER — kebenaran ini hanya untuk migrations
FLUSH PRIVILEGES;
```

Untuk migrations (jalankan secara manual), gunakan user dengan kebenaran lebih tinggi.

---

## 11. Keselamatan Pengangkutan (Transport Security)

### 11.1 HTTPS Wajib

- **Semua** trafik mesti melalui HTTPS.
- Redirect automatik dari HTTP ke HTTPS (dalam Nginx/Apache atau Cloudflare).
- Sertifikat SSL: Gunakan Let's Encrypt (percuma) atau sertifikat yang disediakan oleh saasku.my.
- Konfigurasikan `APP_URL=https://mjfkbt3.saasku.my` dalam `.env`.

### 11.2 Security Headers

Tambah headers keselamatan dalam Nginx atau middleware Laravel:

```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

Dalam Laravel, tambah middleware `SecurityHeaders` atau gunakan pakej `spatie/laravel-csp`.

### 11.3 Cookie Security

Dalam `config/session.php`:
```php
'secure' => true,        // Cookie hanya dihantar melalui HTTPS
'http_only' => true,     // Cookie tidak boleh diakses oleh JavaScript
'same_site' => 'strict', // Cookie tidak dihantar dalam cross-site requests
```

---

## 12. Pemantauan Keselamatan dan Respons Insiden

### 12.1 Log yang Perlu Dipantau

| Log | Lokasi | Tanda Bahaya |
|-----|--------|-------------|
| Log masuk admin | `admin_logs` table | > 5 percubaan gagal dalam 15 minit dari IP sama |
| Error Laravel | `storage/logs/laravel.log` | Exception berulang, SQL errors |
| Nginx/Apache access log | Server log | Banyak request 404 ke path yang tidak wujud |
| PHP error log | Server error log | Fatal errors, memory exceeded |

### 12.2 Tindakan Sekiranya Dicerobohi

1. **Segera:** Ubah kata laluan admin.
2. **Segera:** Revoke semua API keys dan jana yang baru.
3. **Segera:** Padam semua sesi aktif (`php artisan session:flush` atau clear `sessions` table).
4. **Dalam 24 jam:** Semak `admin_logs` untuk aktiviti mencurigakan.
5. **Dalam 24 jam:** Semak fail yang dimuat naik baru-baru ini.
6. **Dalam 72 jam:** Jika data pengguna terdedah (Fasa 2), notify pihak berkuasa jika perlu mengikut PDPA.

### 12.3 Backup

- **Backup DB harian:** Setup cron job untuk dump MySQL setiap hari, simpan 7 hari.
- **Backup fail:** Backup `storage/app/public/uploads/` mingguan.
- **Lokasi backup:** Simpan di luar pelayan (e.g., Google Drive atau S3).

```bash
# Contoh backup command (dalam cron)
mysqldump -u mjfkbt3_app -p{KATA_LALUAN} mjfkbt3 | gzip > /backups/mjfkbt3_$(date +%Y%m%d).sql.gz
```

---

## 13. Checklist Keselamatan Sebelum Launch

### Semak setiap item sebelum laman web dilancarkan:

- [ ] `.env` ada dalam `.gitignore` dan tidak pernah di-commit
- [ ] `APP_DEBUG=false` dalam production `.env`
- [ ] `APP_ENV=production` dalam `.env`
- [ ] Semua API keys dihadkan domain dalam konsol provider
- [ ] HTTPS aktif, redirect HTTP → HTTPS berfungsi
- [ ] Admin kata laluan mematuhi keperluan minimum (12 aksara, kompleks)
- [ ] Rate limiting aktif untuk `/admin/login`
- [ ] Fail upload divalidasi dengan dua lapisan (extension + magic bytes)
- [ ] Fail disimpan dengan nama UUID (bukan nama asal)
- [ ] Halaman Dasar Privasi (`/dasar-privasi`) sudah ada
- [ ] Halaman Terma Penggunaan (`/terma-penggunaan`) sudah ada
- [ ] Security headers aktif (HSTS, X-Frame-Options, dll.)
- [ ] CSP header dikonfigurasi dengan betul
- [ ] Backup DB harian sudah disediakan
- [ ] Log rotation dikonfigurasikan (jangan biar log membesar tanpa had)
- [ ] `php artisan config:cache` dijalankan untuk production
- [ ] `php artisan route:cache` dijalankan untuk production
- [ ] Semua dependency dikemas kini (`composer audit`, `npm audit`)

---

*Dokumen ini hendaklah dibaca bersama dengan `06_Constraints/constraints.md` untuk kekangan keselamatan tambahan dan `05_Backend/architecture.md` untuk konteks teknikal penuh.*
