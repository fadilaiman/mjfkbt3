# PRD 05 — Seni Bina Backend (Backend Architecture)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Gambaran Keseluruhan Seni Bina Sistem

```
┌─────────────────────────────────────────────────────────┐
│                    PELAYAR PENGGUNA                     │
│              (Mobile-first, Vue 3 + Inertia)            │
└───────────────────────┬─────────────────────────────────┘
                        │ HTTPS
                        v
┌─────────────────────────────────────────────────────────┐
│                  NGINX / Apache                         │
│            (saasku.my VPS / cPanel)                     │
└───────────────────────┬─────────────────────────────────┘
                        │
                        v
┌─────────────────────────────────────────────────────────┐
│              LARAVEL 12 APPLICATION                     │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │   Routes    │  │ Controllers  │  │   Middleware  │  │
│  │ (web.php /  │  │ (API calls,  │  │ (Auth, CSRF,  │  │
│  │  inertia)   │  │  data serve) │  │  Rate Limit)  │  │
│  └─────────────┘  └──────────────┘  └───────────────┘  │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │  Services   │  │ Scheduled    │  │    Storage    │  │
│  │ (YouTube,   │  │  Jobs (Cron) │  │  (uploads/    │  │
│  │  Facebook,  │  │              │  │   public/)    │  │
│  │  JAKIM etc) │  └──────────────┘  └───────────────┘  │
│  └─────────────┘                                        │
└───────────────────────┬─────────────────────────────────┘
                        │
               ┌────────┴────────┐
               v                 v
┌──────────────────────┐  ┌──────────────────────────────┐
│     MySQL Database   │  │     External APIs            │
│                      │  │                              │
│  - prayer_times      │  │  - YouTube Data API v3       │
│  - youtube_videos    │  │  - Facebook Graph API        │
│  - fb_posts          │  │  - TikTok oEmbed             │
│  - fb_events         │  │  - JAKIM e-Solat API         │
│  - tiktok_videos     │  │  - Instagram oEmbed (P2)     │
│  - announcements     │  │  - Payment Gateway (P2)      │
│  - events            │  └──────────────────────────────┘
│  - donations         │
│  - admin_users       │
│  - admin_logs        │
│  - media_files       │
└──────────────────────┘
```

### Komponen Utama

| Komponen | Teknologi | Peranan |
|----------|-----------|---------|
| Backend Framework | Laravel 12 | Routing, business logic, scheduled jobs |
| Frontend | Vue 3 + Inertia.js | SPA-like experience tanpa API boilerplate |
| Styling | Tailwind CSS v3 | Utility-first CSS |
| Build Tool | Vite 7 | Asset bundling |
| Database | MySQL 8.x | Penyimpanan data utama |
| Cache | Laravel Cache (file/Redis) | Cache API responses |
| Queue | Laravel Queue (database driver) | Background jobs untuk API scraping |
| Hosting | saasku.my VPS/cPanel | Pelayan produksi |

---

## 2. Skema Pangkalan Data (Database Schema)

### 2.1 `prayer_times`

Menyimpan data waktu solat dari JAKIM, cached sebulan ke hadapan.

```sql
CREATE TABLE prayer_times (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date        DATE NOT NULL,
    zone_code   VARCHAR(10) NOT NULL DEFAULT 'SGR01',
    subuh       TIME NOT NULL,
    syuruk      TIME NOT NULL,
    zohor       TIME NOT NULL,
    asar        TIME NOT NULL,
    maghrib     TIME NOT NULL,
    isyak       TIME NOT NULL,
    hijri_date  VARCHAR(50) NULL,
    fetched_at  TIMESTAMP NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_date_zone (date, zone_code)
);
```

---

### 2.2 `youtube_videos`

Cache data video dari YouTube channel.

```sql
CREATE TABLE youtube_videos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id        VARCHAR(20) NOT NULL UNIQUE,
    title           VARCHAR(500) NOT NULL,
    description     TEXT NULL,
    thumbnail_url   VARCHAR(500) NULL,
    published_at    DATETIME NOT NULL,
    duration        VARCHAR(20) NULL,      -- ISO 8601 format: PT45M21S
    view_count      BIGINT UNSIGNED DEFAULT 0,
    like_count      BIGINT UNSIGNED DEFAULT 0,
    is_live         BOOLEAN DEFAULT FALSE,
    is_hidden       BOOLEAN DEFAULT FALSE, -- Admin boleh sembunyikan
    fetched_at      TIMESTAMP NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (published_at DESC),
    INDEX idx_is_live (is_live)
);
```

---

### 2.3 `youtube_live_status`

Track status siaran langsung semasa.

```sql
CREATE TABLE youtube_live_status (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    is_live         BOOLEAN DEFAULT FALSE,
    video_id        VARCHAR(20) NULL,
    title           VARCHAR(500) NULL,
    concurrent_viewers INT UNSIGNED DEFAULT 0,
    checked_at      TIMESTAMP NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 2.4 `fb_posts`

Cache post dari Facebook Page.

```sql
CREATE TABLE fb_posts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fb_post_id      VARCHAR(100) NOT NULL UNIQUE,
    message         TEXT NULL,
    story           VARCHAR(500) NULL,
    full_picture    VARCHAR(1000) NULL,
    permalink_url   VARCHAR(1000) NULL,
    post_type       ENUM('status','photo','video','link','event') DEFAULT 'status',
    published_at    DATETIME NOT NULL,
    is_hidden       BOOLEAN DEFAULT FALSE,
    fetched_at      TIMESTAMP NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (published_at DESC)
);
```

---

### 2.5 `fb_events`

Cache acara dari Facebook Page Events.

```sql
CREATE TABLE fb_events (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fb_event_id     VARCHAR(100) NOT NULL UNIQUE,
    name            VARCHAR(500) NOT NULL,
    description     TEXT NULL,
    start_time      DATETIME NOT NULL,
    end_time        DATETIME NULL,
    place           VARCHAR(500) NULL,
    cover_url       VARCHAR(1000) NULL,
    attending_count INT UNSIGNED DEFAULT 0,
    is_hidden       BOOLEAN DEFAULT FALSE,
    fb_url          VARCHAR(1000) NULL,
    fetched_at      TIMESTAMP NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_start_time (start_time)
);
```

---

### 2.6 `tiktok_videos`

URL video TikTok yang diurus oleh admin.

```sql
CREATE TABLE tiktok_videos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tiktok_url      VARCHAR(1000) NOT NULL,
    title           VARCHAR(500) NULL,
    thumbnail_url   VARCHAR(1000) NULL,
    oembed_html     TEXT NULL,           -- HTML embed dari oEmbed response
    author_name     VARCHAR(200) NULL,
    sort_order      INT UNSIGNED DEFAULT 0,
    is_hidden       BOOLEAN DEFAULT FALSE,
    oembed_fetched_at TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 2.7 `announcements`

Pengumuman yang dicipta oleh admin.

```sql
CREATE TABLE announcements (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(500) NOT NULL,
    body            TEXT NOT NULL,
    category        ENUM('aktiviti','pengumuman','kebajikan','am') DEFAULT 'am',
    is_pinned       BOOLEAN DEFAULT FALSE,
    is_published    BOOLEAN DEFAULT TRUE,
    attachment_path VARCHAR(1000) NULL,   -- Path ke fail PDF/gambar
    attachment_type ENUM('pdf','image') NULL,
    published_at    DATETIME NOT NULL,
    expires_at      DATETIME NULL,
    created_by      BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (published_at DESC),
    INDEX idx_pinned (is_pinned, published_at DESC)
);
```

---

### 2.8 `events`

Acara manual yang ditambah oleh admin (bukan dari Facebook).

```sql
CREATE TABLE events (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(500) NOT NULL,
    description     TEXT NULL,
    start_datetime  DATETIME NOT NULL,
    end_datetime    DATETIME NULL,
    location        VARCHAR(500) NULL,
    cover_image_path VARCHAR(1000) NULL,
    source          ENUM('manual','facebook') DEFAULT 'manual',
    fb_event_id     VARCHAR(100) NULL,   -- Jika dari Facebook (cross-ref)
    is_published    BOOLEAN DEFAULT TRUE,
    is_featured     BOOLEAN DEFAULT FALSE,
    created_by      BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_start (start_datetime),
    INDEX idx_source (source)
);
```

---

### 2.9 `donations`

Tabung derma yang diurus admin.

```sql
CREATE TABLE donations (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(300) NOT NULL,          -- Nama tabung
    description     TEXT NULL,
    target_amount   DECIMAL(12,2) NOT NULL DEFAULT 0,
    collected_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    contributor_count INT UNSIGNED DEFAULT 0,
    whatsapp_number VARCHAR(30) NULL,               -- Fallback WA untuk derma
    is_active       BOOLEAN DEFAULT TRUE,
    sort_order      INT UNSIGNED DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 2.10 `whatsapp_contacts`

Direktori kenalan WhatsApp.

```sql
CREATE TABLE whatsapp_contacts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(200) NOT NULL,
    role            VARCHAR(200) NOT NULL,
    wa_number       VARCHAR(50) NULL,              -- Format: 60123412459
    wa_qr_id        VARCHAR(200) NULL,             -- Untuk wa.me/qr/ format
    category        ENUM('kewangan','am','pendidikan','kebajikan') DEFAULT 'am',
    sort_order      INT UNSIGNED DEFAULT 0,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 2.11 `admin_users`

Pengguna admin (satu pengguna sahaja untuk fasa 1).

```sql
CREATE TABLE admin_users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(200) NOT NULL,
    email           VARCHAR(200) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,          -- bcrypt
    remember_token  VARCHAR(100) NULL,
    last_login_at   TIMESTAMP NULL,
    last_login_ip   VARCHAR(45) NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 2.12 `admin_logs`

Audit trail tindakan admin.

```sql
CREATE TABLE admin_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id   BIGINT UNSIGNED NOT NULL,
    action          VARCHAR(200) NOT NULL,          -- e.g. 'created_announcement'
    model_type      VARCHAR(100) NULL,              -- e.g. 'Announcement'
    model_id        BIGINT UNSIGNED NULL,
    description     TEXT NULL,
    ip_address      VARCHAR(45) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_user_id, created_at DESC)
);
```

---

### 2.13 `media_files`

Fail yang dimuat naik oleh admin (PDF, gambar).

```sql
CREATE TABLE media_files (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    original_name   VARCHAR(500) NOT NULL,
    stored_name     VARCHAR(500) NOT NULL,          -- UUID-based filename
    path            VARCHAR(1000) NOT NULL,         -- storage/app/public/...
    disk            VARCHAR(50) DEFAULT 'public',
    mime_type       VARCHAR(100) NOT NULL,
    file_size       BIGINT UNSIGNED NOT NULL,       -- bytes
    type            ENUM('pdf','image') NOT NULL,
    uploaded_by     BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 3. Integrasi API

### 3.1 YouTube Data API v3

**Endpoint yang digunakan:**

```
GET https://www.googleapis.com/youtube/v3/search
  ?part=snippet
  &channelId={CHANNEL_ID}
  &eventType=live
  &type=video
  &key={API_KEY}
→ Detect live stream

GET https://www.googleapis.com/youtube/v3/playlistItems
  ?part=snippet,contentDetails
  &playlistId={UPLOADS_PLAYLIST_ID}
  &maxResults=50
  &key={API_KEY}
→ Ambil senarai video terbaru

GET https://www.googleapis.com/youtube/v3/videos
  ?part=snippet,contentDetails,statistics
  &id={comma_separated_video_ids}
  &key={API_KEY}
→ Ambil detail video (tontonan, tempoh)
```

**Konfigurasi `.env`:**
```
YOUTUBE_API_KEY=
YOUTUBE_CHANNEL_ID=UCxxxxxxxxxx
YOUTUBE_UPLOADS_PLAYLIST_ID=UUxxxxxxxxxx
```

**Service class:** `App\Services\YouTubeService`

**Scheduled jobs:**
- `CheckYoutubeLiveStream` — tiap 5 minit
- `SyncYoutubeVideos` — tiap 6 jam

---

### 3.2 Facebook Graph API

**Endpoint yang digunakan:**

```
GET https://graph.facebook.com/v19.0/{PAGE_ID}/posts
  ?fields=id,message,story,full_picture,permalink_url,created_time,attachments
  &access_token={PAGE_ACCESS_TOKEN}
→ Ambil post terbaru

GET https://graph.facebook.com/v19.0/{PAGE_ID}/events
  ?fields=id,name,description,start_time,end_time,place,cover,attending_count
  &access_token={PAGE_ACCESS_TOKEN}
→ Ambil acara

GET https://graph.facebook.com/v19.0/debug_token
  ?input_token={ACCESS_TOKEN}
  &access_token={APP_ID}|{APP_SECRET}
→ Semak token expiry
```

**Konfigurasi `.env`:**
```
FACEBOOK_PAGE_ID=
FACEBOOK_PAGE_ACCESS_TOKEN=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
```

**Nota penting:** Long-lived Page Access Token boleh dilanjutkan menjadi "never expire" menggunakan System User dalam Facebook Business Manager. Ini adalah pendekatan yang disyorkan untuk production.

**Service class:** `App\Services\FacebookService`

**Scheduled jobs:**
- `SyncFacebookPosts` — tiap 30 minit
- `SyncFacebookEvents` — tiap 1 jam

---

### 3.3 TikTok oEmbed

**Endpoint:**
```
GET https://www.tiktok.com/oembed?url={TIKTOK_VIDEO_URL}
→ Kembalikan HTML embed + metadata
```

**Tiada authentication diperlukan untuk oEmbed.**

**Service class:** `App\Services\TikTokService`

**Scheduled jobs:**
- `RefreshTikTokOEmbed` — tiap 24 jam (untuk video yang sudah ada dalam DB)

---

### 3.4 JAKIM e-Solat API

**Endpoint:**
```
GET https://www.e-solat.gov.my/index.php
  ?r=esolatApi/takwimsolat
  &period=month
  &zone=SGR01
→ Kembalikan waktu solat sebulan
```

**Respons format:**
```json
{
  "status": "OK!",
  "serverTime": "...",
  "periodType": "month",
  "lang": "ms",
  "zone": "SGR01",
  "bearing": "...",
  "prayerTime": [
    {
      "hijri": "...",
      "date": "01-03-2026",
      "day": "Ahad",
      "imsak": "05:43",
      "subuh": "05:53",
      "syuruk": "07:07",
      "zohor": "13:14",
      "asar": "16:30",
      "maghrib": "19:17",
      "isyak": "20:29"
    }
  ]
}
```

**Service class:** `App\Services\JakimPrayerService`

**Scheduled jobs:**
- `SyncPrayerTimes` — sekali sehari pada 00:01 MYT (ambil sebulan sekaligus)

---

## 4. Strategi Cache dan Scraping

### 4.1 Lapisan Cache

```
Permintaan Pengguna
        |
        v
  [Laravel Cache] ← Cache dalam memori (5 minit untuk data kritikal)
        |
     Miss ↓
  [Database Cache] ← Data yang sudah di-scrape & disimpan dalam DB
        |
     Stale ↓
  [API External] ← Cuma dipanggil mengikut jadual (bukan per request)
```

**Prinsip utama:** API luaran **tidak pernah dipanggil secara synchronous** semasa pengguna membuat permintaan. Semua API dipanggil melalui scheduled jobs dan hasilnya disimpan dalam DB. Laman web sentiasa serve dari DB.

### 4.2 Jadual Refresh

| Sumber | Kekerapan | Driver |
|--------|-----------|--------|
| JAKIM Prayer Times | Sekali sehari (00:01 MYT) | Laravel Scheduler |
| YouTube Live Status | Setiap 5 minit | Laravel Scheduler |
| YouTube Video Library | Setiap 6 jam | Laravel Scheduler |
| Facebook Posts | Setiap 30 minit | Laravel Scheduler |
| Facebook Events | Setiap 1 jam | Laravel Scheduler |
| TikTok oEmbed | Setiap 24 jam | Laravel Scheduler |

### 4.3 Pengendalian Kegagalan API

Setiap service mengikuti pola ini:

```php
// Pseudocode
try {
    $data = $this->callExternalApi();
    $this->saveToDatabase($data);
    Log::info('Sync berjaya: ' . get_class($this));
} catch (ApiRateLimitException $e) {
    Log::warning('Rate limit: ' . get_class($this) . ' - guna cache lama');
    // Teruskan guna data lama dalam DB
} catch (ApiAuthException $e) {
    Log::error('Auth gagal: ' . get_class($this));
    // Notify admin melalui log/email
} catch (\Exception $e) {
    Log::error('API gagal: ' . $e->getMessage());
    // Teruskan guna data lama
}
```

---

## 5. Struktur Panel Admin

### 5.1 Routing Admin

```
/admin                  → Redirect ke /admin/dashboard (atau login)
/admin/login            → Halaman log masuk
/admin/dashboard        → Dashboard utama
/admin/pengumuman       → Senarai pengumuman (index)
/admin/pengumuman/cipta → Borang tambah pengumuman
/admin/pengumuman/{id}  → Edit pengumuman
/admin/aktiviti         → Senarai acara
/admin/aktiviti/cipta   → Tambah acara manual
/admin/aktiviti/{id}    → Edit acara
/admin/derma            → Urus tabung derma
/admin/kandungan        → Semak kandungan auto (YouTube, FB, TikTok)
/admin/fail             → Muat naik & urus fail
/admin/tetapan          → Tetapan am (WhatsApp contacts, nama masjid etc)
/admin/log              → Log aktiviti admin
/admin/logout           → Log keluar
```

### 5.2 Middleware

```php
// routes/web.php
Route::middleware(['auth:admin', 'admin.active'])->prefix('admin')->group(function () {
    // Semua route admin
});
```

Custom middleware `admin.active` — semak `admin_users.is_active = true`.

### 5.3 Controller Utama

| Controller | Tanggungjawab |
|------------|---------------|
| `AdminAuthController` | Login, logout, remember me |
| `AnnouncementController` | CRUD pengumuman + attach fail |
| `EventController` | CRUD acara manual |
| `DonationController` | CRUD tabung derma |
| `ContentModerationController` | Toggle `is_hidden` untuk YouTube/FB/TikTok |
| `MediaController` | Upload, list, delete fail |
| `WhatsappContactController` | CRUD direktori WA |
| `SettingsController` | Tetapan am sistem |
| `AdminLogController` | Papar log aktiviti |

---

## 6. Struktur Fail Laravel (Folder Structure)

```
app/
├── Console/
│   └── Commands/              ← Artisan commands untuk manual sync
├── Http/
│   ├── Controllers/
│   │   ├── Admin/             ← Semua admin controllers
│   │   └── Public/            ← Inertia page controllers (homepage, kuliah, etc)
│   └── Middleware/
│       └── AdminActive.php
├── Models/
│   ├── PrayerTime.php
│   ├── YoutubeVideo.php
│   ├── YoutubeLiveStatus.php
│   ├── FbPost.php
│   ├── FbEvent.php
│   ├── TiktokVideo.php
│   ├── Announcement.php
│   ├── Event.php
│   ├── Donation.php
│   ├── WhatsappContact.php
│   ├── AdminUser.php
│   ├── AdminLog.php
│   └── MediaFile.php
├── Services/
│   ├── YouTubeService.php
│   ├── FacebookService.php
│   ├── TikTokService.php
│   ├── JakimPrayerService.php
│   └── MediaUploadService.php
└── Jobs/
    ├── CheckYoutubeLiveStream.php
    ├── SyncYoutubeVideos.php
    ├── SyncFacebookPosts.php
    ├── SyncFacebookEvents.php
    ├── RefreshTikTokOEmbed.php
    └── SyncPrayerTimes.php

resources/
├── js/
│   ├── Pages/
│   │   ├── Public/
│   │   │   ├── Home.vue
│   │   │   ├── Kuliah.vue
│   │   │   ├── Aktiviti.vue
│   │   │   ├── Pengumuman.vue
│   │   │   ├── Derma.vue
│   │   │   └── Hubungi.vue
│   │   └── Admin/
│   │       ├── Dashboard.vue
│   │       ├── Login.vue
│   │       ├── Pengumuman/
│   │       ├── Aktiviti/
│   │       ├── Derma/
│   │       ├── Kandungan/
│   │       └── Fail/
│   └── Components/
│       ├── PrayerTimesWidget.vue
│       ├── YoutubeHero.vue
│       ├── VideoCard.vue
│       ├── TikTokStrip.vue
│       ├── EventCard.vue
│       ├── AnnouncementCard.vue
│       ├── DonationWidget.vue
│       └── WhatsappContact.vue
```

---

## 7. Pematuhan PDPA Malaysia

### 7.1 Data yang Dikumpul (Fasa 1)

Dalam fasa 1, **tiada data peribadi pengguna awam dikumpul**. Pengunjung laman web boleh melayari kandungan tanpa mendaftar atau memberikan sebarang maklumat.

Data yang diproses:
- **Admin logs:** IP address admin dan tindakan mereka — untuk audit dalaman sahaja.
- **Server logs:** IP address pengunjung dalam log pelayan — standard server operation, bukan dikumpul dalam DB aplikasi.

### 7.2 Prinsip PDPA yang Dipatuhi

| Prinsip PDPA | Pelaksanaan |
|--------------|-------------|
| **Notis & Pilihan** | Halaman `/dasar-privasi` menerangkan data apa yang diproses |
| **Pendedahan** | Tiada data dijual atau dikongsi dengan pihak ketiga |
| **Keselamatan** | Kata laluan di-hash dengan bcrypt, HTTPS wajib |
| **Integriti Data** | Hanya data minimum yang diperlukan dikumpul |
| **Akses** | Admin boleh padam data dari sistem |
| **Pengekalan** | Log admin dipadamkan selepas 90 hari (soft-delete atau purge) |

### 7.3 Perancangan Fasa 2 (Jika Data Pengguna Dikumpul)

Jika laman web mula mengumpul data pengguna (e.g., pendaftaran, borang permohonan):
- Tambah `consent_given`, `consent_timestamp`, `consent_ip` dalam setiap jadual pengguna.
- Borang pendaftaran MESTI ada checkbox persetujuan yang jelas.
- Sediakan mekanisme "hak untuk dipadamkan" (right to erasure).
- Dasar Privasi mesti dikemas kini untuk mencerminkan pengumpulan data baru.
- Dapatkan nasihat dari peguam atau pakar PDPA jika perlu.

### 7.4 Penyimpanan Data Third-Party

Data yang di-scrape dari Facebook, YouTube, TikTok disimpan untuk tujuan paparan sahaja. Data ini adalah milik awam (public posts) dari platform rasmi MJFK Batu 3. Jika mana-mana pihak meminta data dipadamkan, admin boleh toggle `is_hidden = true` atau padam dari DB.

---

## 8. Konfigurasi Cron (Laravel Scheduler)

Tambah dalam crontab VPS:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Dalam `app/Console/Kernel.php`:
```php
$schedule->job(new SyncPrayerTimes())->dailyAt('00:01')->timezone('Asia/Kuala_Lumpur');
$schedule->job(new CheckYoutubeLiveStream())->everyFiveMinutes();
$schedule->job(new SyncYoutubeVideos())->everySixHours();
$schedule->job(new SyncFacebookPosts())->everyThirtyMinutes();
$schedule->job(new SyncFacebookEvents())->hourly();
$schedule->job(new RefreshTikTokOEmbed())->daily();
```

---

## 9. Keperluan Persekitaran (.env)

```env
APP_NAME="Masjid Jamek Fastabiqul Khayrat Batu 3"
APP_ENV=production
APP_URL=https://mjfkbt3.saasku.my

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mjfkbt3
DB_USERNAME=
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# YouTube
YOUTUBE_API_KEY=
YOUTUBE_CHANNEL_ID=
YOUTUBE_UPLOADS_PLAYLIST_ID=

# Facebook
FACEBOOK_PAGE_ID=
FACEBOOK_PAGE_ACCESS_TOKEN=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=

# JAKIM Prayer Times
JAKIM_ZONE=SGR01

# File Upload
MAX_UPLOAD_SIZE_MB=10
ALLOWED_UPLOAD_TYPES=pdf,jpg,jpeg,png,webp

# Admin Security
ADMIN_LOGIN_MAX_ATTEMPTS=5
ADMIN_LOGIN_DECAY_MINUTES=15
```

---

*Dokumen ini hendaklah dibaca bersama dengan `08_Integrations/integrations.md` untuk butiran teknikal setiap API.*
