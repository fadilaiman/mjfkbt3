# PRD 02 — Pola Penamaan (Naming Patterns)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Prinsip Umum

1. **Konsisten** — Semua penamaan mengikut konvensyen yang sama di seluruh projek.
2. **Deskriptif** — Nama mesti jelas tanpa perlu konteks tambahan.
3. **Bahasa Inggeris untuk kod** — Nama kelas, function, variable dalam Bahasa Inggeris.
4. **Bahasa Melayu untuk URL dan UI** — Route paths, label, dan kandungan dalam Bahasa Melayu.

---

## 2. PHP / Laravel

### 2.1 Kelas (Classes)

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Model | PascalCase, singular | `Announcement`, `PrayerTime`, `YoutubeVideo` |
| Controller | PascalCase + `Controller` | `AnnouncementController`, `HomeController` |
| Form Request | PascalCase + `Request` | `StoreAnnouncementRequest`, `AdminLoginRequest` |
| Service | PascalCase + `Service` | `YouTubeService`, `JakimPrayerService` |
| Job | PascalCase (verb phrase) | `SyncYoutubeVideos`, `CheckYoutubeLiveStream` |
| Middleware | PascalCase | `AdminActive` |
| Seeder | PascalCase + `Seeder` | `AdminUserSeeder`, `WhatsappContactSeeder` |

### 2.2 Method

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Controller action | camelCase | `index()`, `store()`, `showLogin()` |
| Service method | camelCase (verb + noun) | `fetchLiveStatus()`, `syncVideos()`, `getPrayerTimes()` |
| Model scope | camelCase, prefix `scope` | `scopePublished()`, `scopeActive()`, `scopeUpcoming()` |
| Model accessor | camelCase, prefix `get` | `getFormattedDateAttribute()` |
| Model relationship | camelCase | `announcements()`, `createdBy()` |

### 2.3 Variable & Property

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Variable | camelCase | `$prayerTimes`, `$liveStatus`, `$videoList` |
| Constant | UPPER_SNAKE_CASE | `MAX_UPLOAD_SIZE`, `JAKIM_ZONE_DEFAULT` |
| Config key | snake_case | `youtube.api_key`, `upload.max_size_mb` |
| .env key | UPPER_SNAKE_CASE | `YOUTUBE_API_KEY`, `JAKIM_ZONE` |

### 2.4 Namespace

```
App\Http\Controllers\Admin\     — Controller admin
App\Http\Controllers\Public\    — Controller awam
App\Http\Requests\Admin\        — Form request admin
App\Http\Requests\Auth\         — Form request auth
App\Models\                     — Semua model
App\Services\                   — Semua service
App\Jobs\                       — Semua scheduled jobs
```

---

## 3. Pangkalan Data (Database)

### 3.1 Jadual (Tables)

| Konvensyen | Contoh |
|-----------|--------|
| snake_case, plural | `prayer_times`, `youtube_videos`, `fb_posts` |
| Pivot table: singular + singular, alphabetical | `event_media` (jika perlu) |

### 3.2 Lajur (Columns)

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Primary key | `id` | `id` |
| Foreign key | singular_table + `_id` | `admin_user_id`, `fb_event_id` |
| Boolean | prefix `is_` | `is_active`, `is_hidden`, `is_published`, `is_live` |
| Datetime/Timestamp | suffix `_at` | `published_at`, `fetched_at`, `expires_at` |
| URL | suffix `_url` | `thumbnail_url`, `permalink_url`, `fb_url` |
| File path | suffix `_path` | `attachment_path`, `cover_image_path` |
| Count | suffix `_count` | `view_count`, `like_count`, `contributor_count` |
| Amount (RM) | prefix descriptif | `target_amount`, `collected_amount` |
| Sort | `sort_order` | `sort_order` |
| Enum/Type | descriptif | `category`, `post_type`, `source`, `type` |

### 3.3 Migration File

Format: `{YYYY_MM_DD}_{sequence}_create_{table_name}_table.php`

```
2026_03_01_000001_create_prayer_times_table.php
2026_03_01_000002_create_youtube_videos_table.php
2026_03_01_000007_create_announcements_table.php
```

### 3.4 Index

| Konvensyen | Contoh |
|-----------|--------|
| Unique | `uq_{column}` or `uq_{col1}_{col2}` | `uq_date_zone` |
| Index | `idx_{column}` | `idx_published`, `idx_is_live` |

---

## 4. Vue 3 / JavaScript

### 4.1 Fail Komponen

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Page | PascalCase `.vue` | `Home.vue`, `Kuliah.vue`, `Dashboard.vue` |
| Component | PascalCase `.vue` | `PrayerTimesWidget.vue`, `VideoCard.vue` |
| Layout | PascalCase + `Layout` | `PublicLayout.vue`, `AdminLayout.vue` |
| CRUD pages | `Index.vue`, `Create.vue`, `Edit.vue` | `Pengumuman/Index.vue` |

### 4.2 Nama Komponen (Component Name)

Gunakan multi-word PascalCase dalam template:

```vue
<PrayerTimesWidget />
<VideoCard :video="video" />
<DonationWidget :donation="activeDonation" />
```

### 4.3 Props

| Konvensyen | Contoh |
|-----------|--------|
| camelCase | `videoId`, `prayerTimes`, `isLive`, `donationData` |

### 4.4 Emits

| Konvensyen | Contoh |
|-----------|--------|
| kebab-case (verb phrase) | `@video-selected`, `@form-submitted`, `@file-uploaded` |

### 4.5 Composables

Jika perlu composable, prefix `use`:

```
composables/
├── usePrayerTimes.js
├── useLiveStatus.js
└── useFileUpload.js
```

### 4.6 Variable JavaScript

| Jenis | Konvensyen | Contoh |
|-------|-----------|--------|
| Variable | camelCase | `prayerTimes`, `currentVideo`, `isMenuOpen` |
| Constant | UPPER_SNAKE_CASE | `API_BASE_URL`, `MAX_FILE_SIZE` |
| Function | camelCase (verb) | `fetchVideos()`, `toggleMenu()`, `formatDuration()` |
| Boolean | prefix `is/has/can` | `isLive`, `hasAttachment`, `canUpload` |
| Array | plural | `videos`, `announcements`, `events` |
| Ref (Vue) | descriptif, no prefix | `const searchQuery = ref('')` |

---

## 5. CSS / Tailwind

### 5.1 Custom Classes

Custom CSS classes (jika perlu) mengikut BEM-like naming:

```css
.prayer-widget { }
.prayer-widget__time { }
.prayer-widget__time--active { }
```

### 5.2 Warna Khas Tailwind Config

| Nama | Hex | Kegunaan |
|------|-----|----------|
| `primary` | `#13ec49` | Warna utama hijau masjid |
| `background-light` | `#f6f8f6` | Latar belakang terang |
| `background-dark` | `#102215` | Latar belakang gelap |

### 5.3 Breakpoint Mobile-First

Gunakan Tailwind responsive prefixes secara mobile-first:
- Default = mobile
- `sm:` = ≥640px
- `md:` = ≥768px
- `lg:` = ≥1024px

---

## 6. URL Routes (Bahasa Melayu)

### 6.1 Public Routes

| Route | Peraturan |
|-------|-----------|
| `/` | Homepage |
| `/kuliah` | Video library |
| `/aktiviti` | Event calendar |
| `/pengumuman` | Announcements |
| `/derma` | Donations |
| `/hubungi` | Contact / WhatsApp |
| `/dasar-privasi` | Privacy policy |
| `/terma-penggunaan` | Terms of use |

**Konvensyen:** Huruf kecil, tanda sempang, Bahasa Melayu.

### 6.2 Admin Routes

| Route | Peraturan |
|-------|-----------|
| `/admin/dashboard` | Dashboard |
| `/admin/pengumuman` | Manage announcements |
| `/admin/pengumuman/cipta` | Create (bukan "create") |
| `/admin/pengumuman/{id}` | Edit |
| `/admin/aktiviti` | Manage events |
| `/admin/derma` | Manage donations |
| `/admin/kandungan/youtube` | Content moderation |
| `/admin/fail` | File management |
| `/admin/log` | Audit log |
| `/admin/tetapan` | Settings |

**Konvensyen:** Huruf kecil, Bahasa Melayu, RESTful-inspired.

---

## 7. Git

### 7.1 Branch

| Jenis | Format | Contoh |
|-------|--------|--------|
| Feature | `feature/{short-description}` | `feature/prayer-times-widget` |
| Bug fix | `fix/{short-description}` | `fix/youtube-live-detection` |
| Hotfix | `hotfix/{short-description}` | `hotfix/facebook-token-expired` |
| Release | `release/{version}` | `release/1.0.0` |

### 7.2 Commit Message

Format: `{type}: {description}`

```
feat: add prayer times widget with JAKIM API
fix: handle youtube api quota exceeded gracefully
refactor: extract facebook sync logic to service class
style: update homepage hero section layout
docs: add integration setup guide
chore: update npm dependencies
```

Types: `feat`, `fix`, `refactor`, `style`, `docs`, `chore`, `test`

---

## 8. Fail Konfigurasi

### 8.1 `.env` Keys

Grouped by service, UPPER_SNAKE_CASE:

```
# App
APP_NAME=
APP_ENV=
APP_URL=

# Database
DB_CONNECTION=
DB_HOST=
DB_DATABASE=

# YouTube
YOUTUBE_API_KEY=
YOUTUBE_CHANNEL_ID=
YOUTUBE_UPLOADS_PLAYLIST_ID=

# Facebook
FACEBOOK_PAGE_ID=
FACEBOOK_PAGE_ACCESS_TOKEN=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=

# JAKIM
JAKIM_ZONE=

# Upload
MAX_UPLOAD_SIZE_MB=
ALLOWED_UPLOAD_TYPES=

# Admin
ADMIN_LOGIN_MAX_ATTEMPTS=
ADMIN_LOGIN_DECAY_MINUTES=
```

### 8.2 Config File Naming

| Fail | Kegunaan |
|------|----------|
| `config/services.php` | Third-party service credentials (standard Laravel) |
| `config/mjfkbt3.php` | Custom project config |

---

## 9. Ringkasan Pola Penamaan

| Domain | Format | Bahasa |
|--------|--------|--------|
| PHP classes | PascalCase | English |
| PHP methods | camelCase | English |
| PHP variables | camelCase | English |
| DB tables | snake_case, plural | English |
| DB columns | snake_case | English |
| Vue files | PascalCase.vue | English |
| Vue props | camelCase | English |
| CSS custom classes | kebab-case | English |
| URL routes (public) | kebab-case | **Malay** |
| URL routes (admin) | kebab-case | **Malay** |
| UI labels & text | — | **Malay** |
| Git branches | kebab-case | English |
| Git commits | lowercase | English |
| .env keys | UPPER_SNAKE_CASE | English |

---

*Dokumen ini hendaklah dibaca bersama dengan `01_File_Structure/file-structure.md`.*
