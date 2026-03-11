# BUILD CHECKLIST — MJFKBT3 Mosque Digital Hub

**Started:** 2026-03-08
**Status:** PHASE 1-12 COMPLETE ✅

---

## Phase 1: Foundation ✅
- [x] 1.1 Update .env (APP_NAME, APP_URL, DB config, API keys placeholders)
- [x] 1.2 Create config/mjfkbt3.php (custom config)
- [x] 1.3 Update tailwind.config.js (brand colors, fonts, border-radius)
- [x] 1.4 Update resources/css/app.css (Lexend font, scrollbar-hide)
- [x] 1.5 Update resources/views/app.blade.php (Lexend + Material Symbols fonts)
- [x] 1.6 Update resources/js/app.js (layout resolver, progress color)
- [x] 1.7 Update config/auth.php (admin guard + provider)

## Phase 2: Database ✅
- [x] 2.1–2.13 All 13 migrations created and run
- [x] 2.14–2.26 All 13 models created with scopes
- [x] 2.27–2.30 Seeders created (AdminUser, WhatsappContact, Donation, DatabaseSeeder)
- [x] 2.31 Migrations + seeders run successfully

## Phase 3: Backend Services ✅
- [x] 3.1 JakimPrayerService
- [x] 3.2 YouTubeService
- [x] 3.3 FacebookService
- [x] 3.4 TikTokService
- [x] 3.5 MediaUploadService

## Phase 4: Jobs & Scheduler ✅
- [x] 4.1 SyncPrayerTimes job
- [x] 4.2 CheckYoutubeLiveStream job
- [x] 4.3 SyncYoutubeVideos job
- [x] 4.4 SyncFacebookPosts job
- [x] 4.5 SyncFacebookEvents job
- [x] 4.6 RefreshTikTokOEmbed job
- [x] 4.7 Scheduler registered in routes/console.php

## Phase 5: Admin Backend ✅
- [x] 5.1 Middleware: AdminActive
- [x] 5.2 Middleware registered in bootstrap/app.php
- [x] 5.3 AdminAuthController
- [x] 5.4 DashboardController
- [x] 5.5 AnnouncementController + FormRequests
- [x] 5.6 EventController + FormRequests
- [x] 5.7 DonationController + FormRequests
- [x] 5.8 ContentModerationController
- [x] 5.9 MediaController + FormRequests
- [x] 5.10 WhatsappContactController
- [x] 5.11 AdminLogController
- [x] 5.12 SettingsController
- [x] 5.13 Artisan command: admin:create

## Phase 6: Public Backend ✅
- [x] 6.1–6.7 All public controllers created
- [x] 6.8 routes/web.php updated (46 routes registered)

## Phase 7: Frontend — Shared & Layouts ✅
- [x] 7.1 PublicLayout.vue
- [x] 7.2 AdminLayout.vue
- [x] 7.3 AppHead.vue
- [x] 7.4 LoadingSpinner.vue
- [x] 7.5 EmptyState.vue
- [x] 7.6 Pagination.vue
- [x] 7.7 FlashMessage.vue

## Phase 8: Frontend — Public Components ✅
- [x] 8.1 YoutubeHero.vue
- [x] 8.2 PrayerTimesWidget.vue
- [x] 8.3 VideoCard.vue
- [x] 8.4 VideoCarousel.vue
- [x] 8.5 TikTokStrip.vue
- [x] 8.6 TikTokCard.vue
- [x] 8.7 EventCard.vue
- [x] 8.8 AnnouncementCard.vue
- [x] 8.9 DonationWidget.vue
- [x] 8.10 WhatsappDirectory.vue
- [x] 8.11 WhatsappCard.vue
- [x] 8.12 QuickLinks.vue
- [x] 8.13 GoogleMapEmbed.vue
- [x] 8.14 SocialLinks.vue
- [x] 8.15 MobileNav.vue
- [x] 8.16 LiveBadge.vue

## Phase 9: Frontend — Public Pages ✅
- [x] 9.1 Public/Home.vue
- [x] 9.2 Public/Kuliah.vue
- [x] 9.3 Public/Aktiviti.vue
- [x] 9.4 Public/Pengumuman.vue
- [x] 9.5 Public/Derma.vue
- [x] 9.6 Public/Hubungi.vue
- [x] 9.7 Public/DasarPrivasi.vue
- [x] 9.8 Public/TermaPenggunaan.vue

## Phase 10: Frontend — Admin Components ✅
- [x] 10.1 Admin/StatsCard.vue
- [x] 10.2 Admin/DataTable.vue
- [x] 10.3 Admin/FileUploader.vue
- [x] 10.4 Admin/ConfirmModal.vue
- [x] 10.5 Admin/StatusBadge.vue
(Sidebar + Topbar embedded in AdminLayout.vue)

## Phase 11: Frontend — Admin Pages ✅
- [x] 11.1 Admin/Login.vue
- [x] 11.2 Admin/Dashboard.vue
- [x] 11.3 Admin/Pengumuman/Index.vue
- [x] 11.4 Admin/Pengumuman/Create.vue
- [x] 11.5 Admin/Pengumuman/Edit.vue
- [x] 11.6 Admin/Aktiviti/Index.vue
- [x] 11.7 Admin/Aktiviti/Create.vue
- [x] 11.8 Admin/Aktiviti/Edit.vue
- [x] 11.9 Admin/Derma/Index.vue
- [x] 11.10 Admin/Derma/Edit.vue
- [x] 11.11 Admin/Kandungan/Youtube.vue
- [x] 11.12 Admin/Kandungan/Facebook.vue
- [x] 11.13 Admin/Kandungan/Tiktok.vue
- [x] 11.14 Admin/Fail/Index.vue
- [x] 11.15 Admin/WhatsappKenalan/Index.vue
- [x] 11.16 Admin/Log/Index.vue

## Phase 12: Verification ✅
- [x] 12.1 Database migrations run without errors (16 tables)
- [x] 12.2 Seeders run without errors (Admin, WhatsApp, Donation)
- [x] 12.3 npm run build succeeds (clean, no errors)
- [x] 12.4 php artisan route:list shows 46 routes
- [x] 12.5 All 8 public routes return HTTP 200

## Phase 13: Bug Fixes ✅
- [x] 13.1 Fix TiktokVideo missing `scopeVisible()` — added to Model
- [x] 13.2 Fix Dashboard.vue Vue compiler crash — removed invalid `<template #title>` in div
- [x] 13.3 Fix PHP nullable param deprecation — `?string $pageUrl = null` in FacebookService
- [x] 13.4 Fix `Route [login] not defined` — added `redirectGuestsTo()` in bootstrap/app.php
- [x] 13.5 Final npm run build — clean, 836 modules, no errors

---

## Architecture: No API Keys Required ✅
- **YouTube**: RSS feed (`feeds/videos.xml?channel_id=...`) + channel page HTML scraping for ID + `/live` page for live detection
- **Facebook**: Admin pastes post URLs → embeds generated via `plugins/post.php` iframe (no token)
- **TikTok**: oEmbed API (public, no auth)
- **Prayer Times**: JAKIM e-Solat API (public, no key)

## Next Steps (Post-Launch Setup)
1. Run: `php artisan admin:create` to create first admin user
2. Setup cron: `* * * * * php artisan schedule:run`
3. Start queue worker: `php artisan queue:work`
4. Run first manual sync: `php artisan sync:prayer-times` and `php artisan sync:youtube`
5. Add Facebook posts via Admin > Kandungan > Facebook (paste post URLs)
6. Add TikTok videos via Admin > Kandungan > TikTok (paste video URLs)
