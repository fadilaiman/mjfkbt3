# PRD 01 — Struktur Fail Projek (File Structure)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Gambaran Keseluruhan

Projek ini menggunakan **Laravel 12 + Vue 3 + Inertia.js** dengan Tailwind CSS v3. Struktur fail mengikut konvensyen Laravel standard dengan penambahan khusus untuk ciri-ciri agregasi kandungan.

---

## 2. Struktur Direktori Penuh

```
mjfkbt3/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── CreateAdminUser.php          # Artisan command cipta admin
│   │       ├── SyncPrayerTimesCommand.php   # Manual trigger sync JAKIM
│   │       ├── SyncYoutubeCommand.php       # Manual trigger sync YouTube
│   │       ├── SyncFacebookCommand.php      # Manual trigger sync Facebook
│   │       └── RefreshTiktokCommand.php     # Manual trigger refresh TikTok
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── AnnouncementController.php
│   │   │   │   ├── EventController.php
│   │   │   │   ├── DonationController.php
│   │   │   │   ├── ContentModerationController.php
│   │   │   │   ├── MediaController.php
│   │   │   │   ├── WhatsappContactController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   └── AdminLogController.php
│   │   │   │
│   │   │   └── Public/
│   │   │       ├── HomeController.php
│   │   │       ├── KuliahController.php
│   │   │       ├── AktivitiController.php
│   │   │       ├── PengumumanController.php
│   │   │       ├── DermaController.php
│   │   │       ├── HubungiController.php
│   │   │       └── StaticPageController.php  # dasar-privasi, terma
│   │   │
│   │   ├── Middleware/
│   │   │   └── AdminActive.php
│   │   │
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreAnnouncementRequest.php
│   │       │   ├── UpdateAnnouncementRequest.php
│   │       │   ├── StoreEventRequest.php
│   │       │   ├── UpdateEventRequest.php
│   │       │   ├── StoreDonationRequest.php
│   │       │   ├── UpdateDonationRequest.php
│   │       │   ├── StoreTiktokVideoRequest.php
│   │       │   ├── StoreWhatsappContactRequest.php
│   │       │   └── UploadMediaRequest.php
│   │       └── Auth/
│   │           └── AdminLoginRequest.php
│   │
│   ├── Models/
│   │   ├── AdminLog.php
│   │   ├── AdminUser.php
│   │   ├── Announcement.php
│   │   ├── Donation.php
│   │   ├── Event.php
│   │   ├── FbEvent.php
│   │   ├── FbPost.php
│   │   ├── MediaFile.php
│   │   ├── PrayerTime.php
│   │   ├── TiktokVideo.php
│   │   ├── WhatsappContact.php
│   │   ├── YoutubeLiveStatus.php
│   │   └── YoutubeVideo.php
│   │
│   ├── Services/
│   │   ├── FacebookService.php
│   │   ├── JakimPrayerService.php
│   │   ├── MediaUploadService.php
│   │   ├── TikTokService.php
│   │   └── YouTubeService.php
│   │
│   ├── Jobs/
│   │   ├── CheckYoutubeLiveStream.php
│   │   ├── SyncYoutubeVideos.php
│   │   ├── SyncFacebookPosts.php
│   │   ├── SyncFacebookEvents.php
│   │   ├── RefreshTikTokOEmbed.php
│   │   └── SyncPrayerTimes.php
│   │
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── app.php
│   ├── auth.php                # Custom admin guard
│   ├── database.php
│   ├── filesystems.php         # Public disk config
│   ├── services.php            # YouTube, Facebook, JAKIM config
│   └── mjfkbt3.php             # Custom config: upload limits, API toggles
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000001_create_admin_users_table.php
│   │   ├── 0001_01_01_000002_create_admin_logs_table.php
│   │   ├── 2026_03_01_000001_create_prayer_times_table.php
│   │   ├── 2026_03_01_000002_create_youtube_videos_table.php
│   │   ├── 2026_03_01_000003_create_youtube_live_status_table.php
│   │   ├── 2026_03_01_000004_create_fb_posts_table.php
│   │   ├── 2026_03_01_000005_create_fb_events_table.php
│   │   ├── 2026_03_01_000006_create_tiktok_videos_table.php
│   │   ├── 2026_03_01_000007_create_announcements_table.php
│   │   ├── 2026_03_01_000008_create_events_table.php
│   │   ├── 2026_03_01_000009_create_donations_table.php
│   │   ├── 2026_03_01_000010_create_whatsapp_contacts_table.php
│   │   └── 2026_03_01_000011_create_media_files_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php
│       ├── WhatsappContactSeeder.php   # Seed 3 default contacts
│       └── DonationSeeder.php          # Seed default tabung
│
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   ├── ssr.js
│   │   │
│   │   ├── Layouts/
│   │   │   ├── PublicLayout.vue        # Header + footer untuk halaman awam
│   │   │   └── AdminLayout.vue         # Sidebar + topbar untuk admin panel
│   │   │
│   │   ├── Pages/
│   │   │   ├── Public/
│   │   │   │   ├── Home.vue
│   │   │   │   ├── Kuliah.vue
│   │   │   │   ├── Aktiviti.vue
│   │   │   │   ├── Pengumuman.vue
│   │   │   │   ├── Derma.vue
│   │   │   │   ├── Hubungi.vue
│   │   │   │   ├── DasarPrivasi.vue
│   │   │   │   └── TermaPenggunaan.vue
│   │   │   │
│   │   │   └── Admin/
│   │   │       ├── Login.vue
│   │   │       ├── Dashboard.vue
│   │   │       ├── Pengumuman/
│   │   │       │   ├── Index.vue
│   │   │       │   ├── Create.vue
│   │   │       │   └── Edit.vue
│   │   │       ├── Aktiviti/
│   │   │       │   ├── Index.vue
│   │   │       │   ├── Create.vue
│   │   │       │   └── Edit.vue
│   │   │       ├── Derma/
│   │   │       │   ├── Index.vue
│   │   │       │   └── Edit.vue
│   │   │       ├── Kandungan/
│   │   │       │   ├── Youtube.vue
│   │   │       │   ├── Facebook.vue
│   │   │       │   └── Tiktok.vue
│   │   │       ├── Fail/
│   │   │       │   └── Index.vue
│   │   │       ├── WhatsappKenalan/
│   │   │       │   └── Index.vue
│   │   │       └── Log/
│   │   │           └── Index.vue
│   │   │
│   │   └── Components/
│   │       ├── Public/
│   │       │   ├── YoutubeHero.vue          # Live stream / featured video
│   │       │   ├── PrayerTimesWidget.vue    # Widget waktu solat
│   │       │   ├── VideoCard.vue            # Kad video kuliah
│   │       │   ├── VideoCarousel.vue        # Carousel horizontal kuliah
│   │       │   ├── TikTokStrip.vue          # Scroll horizontal TikTok
│   │       │   ├── TikTokCard.vue           # Kad video TikTok 9:16
│   │       │   ├── EventCard.vue            # Kad acara
│   │       │   ├── AnnouncementCard.vue     # Kad pengumuman
│   │       │   ├── DonationWidget.vue       # Progress bar derma
│   │       │   ├── WhatsappDirectory.vue    # Direktori kenalan WA
│   │       │   ├── WhatsappCard.vue         # Kad kenalan WA
│   │       │   ├── QuickLinks.vue           # Butang pantas homepage
│   │       │   ├── GoogleMapEmbed.vue       # Peta lokasi masjid
│   │       │   ├── SocialLinks.vue          # Ikon media sosial
│   │       │   ├── MobileNav.vue            # Navigasi hamburger mobile
│   │       │   └── LiveBadge.vue            # Badge "SEDANG BERSIARAN"
│   │       │
│   │       ├── Admin/
│   │       │   ├── Sidebar.vue
│   │       │   ├── Topbar.vue
│   │       │   ├── StatsCard.vue
│   │       │   ├── DataTable.vue
│   │       │   ├── FileUploader.vue
│   │       │   ├── ConfirmModal.vue
│   │       │   └── StatusBadge.vue
│   │       │
│   │       └── Shared/
│   │           ├── AppHead.vue              # SEO meta tags
│   │           ├── LoadingSpinner.vue
│   │           ├── EmptyState.vue
│   │           ├── Pagination.vue
│   │           └── FlashMessage.vue
│   │
│   ├── css/
│   │   └── app.css                          # Tailwind directives
│   │
│   └── views/
│       └── app.blade.php                    # Inertia root template
│
├── routes/
│   ├── web.php                              # Public + Admin routes
│   └── console.php                          # Artisan command registration
│
├── storage/
│   └── app/
│       └── public/
│           ├── uploads/
│           │   ├── images/                  # Gambar muat naik admin
│           │   └── documents/               # PDF muat naik admin
│           └── cache/
│               └── thumbnails/              # Cached thumbnails (jika perlu)
│
├── public/
│   ├── index.php
│   ├── favicon.ico
│   ├── robots.txt
│   └── build/                               # Vite build output
│
├── tests/
│   ├── Feature/
│   │   ├── Admin/
│   │   │   ├── AuthTest.php
│   │   │   ├── AnnouncementTest.php
│   │   │   ├── EventTest.php
│   │   │   └── DonationTest.php
│   │   ├── Public/
│   │   │   ├── HomePageTest.php
│   │   │   ├── KuliahPageTest.php
│   │   │   └── PrayerTimesTest.php
│   │   └── Services/
│   │       ├── YouTubeServiceTest.php
│   │       ├── FacebookServiceTest.php
│   │       ├── TikTokServiceTest.php
│   │       └── JakimPrayerServiceTest.php
│   └── Unit/
│       └── Models/
│           ├── AnnouncementTest.php
│           └── PrayerTimeTest.php
│
├── docs/
│   └── PRD/
│       ├── 01_File_Structure/
│       │   └── file-structure.md            # Dokumen ini
│       ├── 02_Naming_Patterns/
│       │   └── naming-patterns.md
│       ├── 03_UI_Design/
│       │   ├── wireframes/
│       │   │   └── mainpage.html
│       │   ├── mockups/
│       │   └── prototypes/
│       ├── 04_Key_Features_and_Userflow/
│       │   └── features.md
│       ├── 05_Backend/
│       │   └── architecture.md
│       ├── 06_Constraints/
│       │   └── constraints.md
│       ├── 07_Security/
│       │   └── security.md
│       └── 08_Integrations/
│           └── integrations.md
│
├── .env                                     # Persekitaran (JANGAN commit)
├── .env.example                             # Template .env
├── .gitignore
├── composer.json
├── package.json
├── postcss.config.js
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## 3. Penerangan Direktori Utama

### 3.1 `app/Console/Commands/`
Artisan commands untuk trigger manual sync API. Berguna untuk debugging dan setup awal.

```bash
php artisan admin:create              # Cipta admin user
php artisan sync:prayer-times         # Fetch waktu solat JAKIM
php artisan sync:youtube              # Fetch video YouTube
php artisan sync:facebook             # Fetch post & event Facebook
php artisan refresh:tiktok            # Refresh oEmbed TikTok
```

### 3.2 `app/Http/Controllers/`
Dipisahkan kepada dua namespace:
- **`Admin/`** — Semua controller untuk panel pentadbir. Protected oleh middleware `auth:admin`.
- **`Public/`** — Semua controller untuk halaman awam. Serve data dari DB cache ke Vue via Inertia.

### 3.3 `app/Services/`
Kelas servis yang mengendalikan komunikasi dengan API luaran. Setiap servis bertanggungjawab untuk:
1. Panggil API
2. Parse response
3. Simpan/kemaskini DB
4. Handle errors gracefully

### 3.4 `app/Jobs/`
Background jobs yang dijalankan mengikut jadual oleh Laravel Scheduler. Tidak pernah dipanggil secara synchronous oleh request pengguna.

### 3.5 `resources/js/Pages/`
Halaman Vue yang di-render oleh Inertia.js:
- **`Public/`** — Halaman awam (Home, Kuliah, Aktiviti, dll.)
- **`Admin/`** — Halaman admin (Dashboard, CRUD, dll.)

Setiap subdirektori admin mengikut pattern `Index.vue`, `Create.vue`, `Edit.vue`.

### 3.6 `resources/js/Components/`
Komponen Vue yang boleh diguna semula:
- **`Public/`** — Widget dan kad untuk halaman awam
- **`Admin/`** — Komponen UI panel admin
- **`Shared/`** — Komponen guna semula merentas kedua-dua bahagian

### 3.7 `resources/js/Layouts/`
Layout wrapper untuk Inertia:
- **`PublicLayout.vue`** — Header (nav, logo, butang derma) + Footer (social links, maps, kenalan)
- **`AdminLayout.vue`** — Sidebar nav + Topbar dengan nama admin + logout

### 3.8 `storage/app/public/uploads/`
Fail yang dimuat naik oleh admin:
- **`images/`** — Gambar (JPG, PNG, WebP). Nama fail: UUID-based.
- **`documents/`** — PDF flyers. Nama fail: UUID-based.

Symlink: `php artisan storage:link` → `public/storage/`

### 3.9 `config/mjfkbt3.php`
Custom config file khusus projek:

```php
return [
    'upload' => [
        'max_size_mb' => env('MAX_UPLOAD_SIZE_MB', 10),
        'allowed_types' => explode(',', env('ALLOWED_UPLOAD_TYPES', 'pdf,jpg,jpeg,png,webp')),
    ],
    'jakim' => [
        'zone' => env('JAKIM_ZONE', 'SGR01'),
    ],
    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
        'channel_id' => env('YOUTUBE_CHANNEL_ID'),
        'uploads_playlist_id' => env('YOUTUBE_UPLOADS_PLAYLIST_ID'),
    ],
    'facebook' => [
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'page_access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
    ],
];
```

---

## 4. Route Groups

### 4.1 Public Routes (`routes/web.php`)

```
GET  /                    → Public\HomeController@index
GET  /kuliah              → Public\KuliahController@index
GET  /aktiviti            → Public\AktivitiController@index
GET  /pengumuman          → Public\PengumumanController@index
GET  /derma               → Public\DermaController@index
GET  /hubungi             → Public\HubungiController@index
GET  /dasar-privasi       → Public\StaticPageController@dasarPrivasi
GET  /terma-penggunaan    → Public\StaticPageController@termaPenggunaan
```

### 4.2 Admin Routes (middleware: `auth:admin`, `admin.active`)

```
GET   /admin              → redirect to /admin/dashboard
GET   /admin/login        → Admin\AdminAuthController@showLogin
POST  /admin/login        → Admin\AdminAuthController@login
POST  /admin/logout       → Admin\AdminAuthController@logout

GET   /admin/dashboard    → Admin\DashboardController@index

GET   /admin/pengumuman          → Admin\AnnouncementController@index
GET   /admin/pengumuman/cipta    → Admin\AnnouncementController@create
POST  /admin/pengumuman          → Admin\AnnouncementController@store
GET   /admin/pengumuman/{id}     → Admin\AnnouncementController@edit
PUT   /admin/pengumuman/{id}     → Admin\AnnouncementController@update
DELETE /admin/pengumuman/{id}    → Admin\AnnouncementController@destroy

GET   /admin/aktiviti            → Admin\EventController@index
GET   /admin/aktiviti/cipta      → Admin\EventController@create
POST  /admin/aktiviti            → Admin\EventController@store
GET   /admin/aktiviti/{id}       → Admin\EventController@edit
PUT   /admin/aktiviti/{id}       → Admin\EventController@update
DELETE /admin/aktiviti/{id}      → Admin\EventController@destroy

GET   /admin/derma               → Admin\DonationController@index
GET   /admin/derma/{id}          → Admin\DonationController@edit
PUT   /admin/derma/{id}          → Admin\DonationController@update

GET   /admin/kandungan/youtube   → Admin\ContentModerationController@youtube
GET   /admin/kandungan/facebook  → Admin\ContentModerationController@facebook
GET   /admin/kandungan/tiktok    → Admin\ContentModerationController@tiktok
PUT   /admin/kandungan/{type}/{id}/toggle → Admin\ContentModerationController@toggle

GET   /admin/fail                → Admin\MediaController@index
POST  /admin/fail                → Admin\MediaController@store
DELETE /admin/fail/{id}          → Admin\MediaController@destroy

GET   /admin/whatsapp            → Admin\WhatsappContactController@index
PUT   /admin/whatsapp/{id}       → Admin\WhatsappContactController@update

GET   /admin/log                 → Admin\AdminLogController@index

GET   /admin/tetapan             → Admin\SettingsController@index
PUT   /admin/tetapan             → Admin\SettingsController@update
```

---

## 5. Nota Penting

1. **Symlink storage:** Jalankan `php artisan storage:link` selepas deploy.
2. **Queue worker:** Jalankan `php artisan queue:work` atau setup Supervisor pada VPS.
3. **Scheduler:** Tambah `* * * * * php artisan schedule:run` dalam crontab.
4. **Permissions:** `storage/` dan `bootstrap/cache/` mesti writable oleh web server.
5. **`.env` TIDAK boleh di-commit** — guna `.env.example` sebagai template.

---

*Dokumen ini hendaklah dibaca bersama dengan `02_Naming_Patterns/naming-patterns.md`.*
