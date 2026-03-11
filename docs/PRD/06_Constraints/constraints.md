# PRD 06 — Kekangan Sistem (Constraints)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Had Kadar API dan Keperluan Cache

### 1.1 YouTube Data API v3

**Had quota harian:** 10,000 unit setiap 24 jam (per API key, boleh minta peningkatan dari Google).

| Operasi | Kos Unit | Kekerapan Maksimum Selamat |
|---------|----------|---------------------------|
| `search.list` (detect live) | 100 unit/panggilan | 100 panggilan/hari = 1 panggilan setiap 14 minit |
| `playlistItems.list` | 1 unit/panggilan | 1,000 panggilan/hari |
| `videos.list` | 1 unit/panggilan | 1,000 panggilan/hari |

**Kekangan kritikal:** Pengesanan live stream menggunakan `search.list` yang mahal (100 unit). Dengan quota 10,000 unit, kita hanya boleh panggil maksimum 100 kali/hari.

**Strategi mematuhi had:**

1. Bagi hari biasa: Semak live setiap 15 minit (96 panggilan × 100 unit = 9,600 unit) — hampir mencapai had.
2. **Penyelesaian lebih baik:** Implement "masa peak" vs "masa off-peak":
   - Off-peak (11PM–5AM MYT): Semak setiap 60 minit — 6 panggilan × 100 = 600 unit
   - Peak (5AM–11PM MYT): Semak setiap 15 minit — 72 panggilan × 100 = 7,200 unit
   - Video library sync: 4 × 1 = 4 unit
   - **Jumlah: ~7,804 unit/hari** — dalam had selamat
3. Jika `is_live = true`, kurangkan ke 5 minit untuk statistics viewer count (guna `videos.list` yang murah — 1 unit).

**Caching wajib:**
- Cache keputusan live detection selama 5 minit (atau 15 minit off-peak) dalam Redis/file cache.
- Cache senarai video selama 6 jam dalam DB.
- **Jangan sekali-kali panggil YouTube API dalam synchronous request pengguna.**

---

### 1.2 Facebook Graph API

**Had kadar (Rate Limits):**

| Jenis Had | Nilai |
|-----------|-------|
| Application-level | 200 × jumlah pengguna aktif per jam |
| Page API | 4,800 panggilan per 24 jam per Page token |

Bagi projek ini (bukan platform besar), had ini sangat longgar. Kekangan sebenar ialah:

**Token Expiry:**
- Short-lived token: Luput dalam 1-2 jam.
- Long-lived token: Luput dalam 60 hari.
- System User token: Tidak luput — **ini yang harus digunakan untuk production**.

**Risiko:** Jika token luput tanpa diperbaharui, seluruh integrasi Facebook (posts, events) berhenti berfungsi. Laman web akan papar data terakhir yang dicache sahaja.

**Mitigasi:**
- Implement pemantauan token expiry dalam cron job.
- Hantar alert (email atau log) 7 hari sebelum token luput.
- Gunakan System User token untuk production.

**Perubahan API:**
- Meta kerap mengubah Graph API. Gunakan versi yang tepat (v19.0) dalam semua URL.
- Semak [Meta Developer Changelog](https://developers.facebook.com/docs/changelog/) setiap suku tahun.

---

### 1.3 TikTok oEmbed

**Had:**
- TikTok oEmbed adalah endpoint awam — tiada had kadar yang didokumenkan.
- Walau bagaimanapun, **jangan panggil tiap kali halaman dimuatkan.** Cache HTML oEmbed dalam DB.
- Cache oEmbed data selama **24 jam**.
- TikTok boleh menukar format oEmbed tanpa notis — perlukan penyelenggaraan berkala.

**Kekangan besar:**
- **Tiada API untuk listing video dari channel TikTok secara awam.**
- Admin MESTI menambah URL video secara manual.
- Ini adalah proses berterusan: Setiap kali masjid upload video TikTok baru yang ingin dipapar, admin perlu tambah URL secara manual.

---

### 1.4 JAKIM e-Solat API

**Had:**
- Endpoint kerajaan Malaysia — tiada dokumentasi rasmi tentang had kadar.
- Guna `period=month` dan cache semua data sebulan dalam DB.
- Panggil API sekali sebulan (pada awal bulan) — hampir tiada risiko rate limit.

**Kekangan:**
- API tidak ada SLA (Service Level Agreement) — boleh down tanpa amaran.
- Jika API down, laman web mesti papar data cache hari sebelum dengan notis.
- Pre-cache bulan depan pada 25 haribulan sebagai langkah pencegahan.

---

## 2. Had Scraping Facebook dan TikTok

### 2.1 Facebook — Larangan Scraping HTML

**Facebook melarang keras HTML scraping.** Gunakan hanya Graph API yang telah dibenarkan:

- **Dibenarkan:** Panggilan kepada Graph API dengan token yang sah.
- **Dilarang:** HTTP requests ke `facebook.com/mjfkbt3` untuk ekstrak HTML.
- **Dilarang:** Menggunakan browser automation (Puppeteer, Selenium) untuk login Facebook dan ekstrak kandungan.
- **Risiko:** Akaun Facebook masjid boleh disekat atau digantung.

**Batasan kandungan yang boleh diakses:**
- Hanya post dan event yang **awam (public)** boleh diakses tanpa masalah.
- Post yang dikongsi dengan "Kawan Sahaja" atau "Hanya Saya" tidak boleh diakses melalui API.
- Pastikan privacy setting Facebook Page MJFK Batu 3 adalah **Public** untuk posts.

### 2.2 TikTok — Tiada API Awam untuk Channel Listing

TikTok Content API memerlukan permohonan khas dan proses kelulusan yang rumit. Untuk projek ini:

- **Tidak boleh:** Auto-scrape senarai video dari channel `@mjfk.batu03`.
- **Boleh:** Gunakan oEmbed untuk embed video yang URL-nya telah diketahui.
- **Implikasi pada kerja admin:** Admin perlu copy-paste URL video TikTok baru secara manual. Ini adalah proses berterusan yang mesti dijelaskan kepada admin semasa onboarding.

### 2.3 Instagram — API Terhad

- Instagram Basic Display API memerlukan **kelulusan Meta App Review** yang boleh mengambil masa beberapa minggu.
- Selagi kelulusan belum diterima, embedding post Instagram memerlukan URL manual.
- Instagram secara aktif menyekat scraping HTML.

---

## 3. Keperluan Pematuhan PDPA Malaysia

### 3.1 Akta yang Berkaitan

**Personal Data Protection Act 2010 (PDPA)** — Akta 709, Malaysia.

Prinsip-prinsip yang mesti dipatuhi:

| Prinsip | Penerangan | Pelaksanaan dalam Projek |
|---------|-----------|------------------------|
| **Prinsip Am** | Data peribadi hanya diproses untuk tujuan yang sah | Hanya data yang diperlukan untuk operasi dikumpul |
| **Prinsip Notis dan Pilihan** | Individu mesti diberitahu dan bersetuju | Halaman Dasar Privasi wajib, consent form untuk fasa 2 |
| **Prinsip Pendedahan** | Data tidak boleh didedahkan tanpa kebenaran | Tiada data dikongsi dengan pihak ketiga |
| **Prinsip Keselamatan** | Langkah keselamatan mesti diambil | HTTPS, enkripsi kata laluan, akses terhad |
| **Prinsip Penyimpanan** | Data tidak boleh disimpan lebih lama dari perlu | Log dipadamkan selepas 90 hari |
| **Prinsip Integriti Data** | Data mesti tepat dan terkini | Admin boleh kemaskini data |
| **Prinsip Akses** | Individu boleh akses data mereka | Mekanisme permintaan padam (untuk fasa 2) |

### 3.2 Kekangan Fasa 1

Dalam fasa 1, data peribadi yang diproses adalah minimum:
- **Log IP server:** Standard web server logging — tidak disimpan dalam DB aplikasi.
- **Cookies sesi admin:** Untuk sesi log masuk admin sahaja.
- **Admin audit log:** IP admin dan tindakan mereka — simpan 90 hari.

**Tiada data pengunjung awam dikumpul dalam DB.**

### 3.3 Keperluan Wajib Sekarang

Walaupun pengumpulan data minimum, perkara berikut WAJIB ada sebelum launch:

1. **Halaman Dasar Privasi** (`/dasar-privasi`):
   - Nyatakan siapa yang mengurus laman web.
   - Nyatakan data apa yang diproses (server logs, cookies).
   - Nyatakan tujuan pemprosesan.
   - Nyatakan bagaimana menghubungi untuk pertanyaan privasi.
   - Nyatakan penggunaan platform pihak ketiga (YouTube, Facebook, TikTok, Google Maps) dan dasar privasi mereka.

2. **Halaman Terma Penggunaan** (`/terma-penggunaan`).

3. **Cookie consent notice** (jika menggunakan analytics atau iklan — untuk fasa 2).

### 3.4 Kekangan Fasa 2 (Jika Ada Pengumpulan Data)

Jika laman web mula mengumpul data pengguna:
- **Wajib ada checkbox persetujuan** yang tidak pre-checked.
- **Wajib rekod consent** (tarikh, masa, IP) dalam DB.
- **Wajib ada mekanisme opt-out** dan "hak untuk dipadamkan".
- **Wajib maklumkan** jika ada data breach dalam masa 72 jam kepada pihak berkuasa (jika berkenaan).
- **Pendaftaran dengan PDPD Commissioner** mungkin diperlukan jika memproses data sensitif.

---

## 4. Kekangan Prestasi Mobile-First

### 4.1 Sasaran Prestasi

Laman web mesti mencapai sasaran berikut pada **rangkaian 4G Malaysia (anggaran 10Mbps)**:

| Metrik | Sasaran |
|--------|---------|
| First Contentful Paint (FCP) | < 1.5 saat |
| Largest Contentful Paint (LCP) | < 2.5 saat |
| Time to Interactive (TTI) | < 3.5 saat |
| Total Blocking Time (TBT) | < 200ms |
| Cumulative Layout Shift (CLS) | < 0.1 |
| Saiz halaman penuh (compressed) | < 500KB |

### 4.2 Kekangan Teknikal

**Gambar:**
- Semua gambar yang dimuat naik admin mesti auto-resize dan di-convert ke WebP.
- Maksimum lebar gambar paparan: 800px untuk card, 1200px untuk hero.
- Lazy load semua gambar di bawah fold.

**Embed Pihak Ketiga:**
- TikTok embed JS (`tiktok.com/embed.js`) mempunyai saiz ~200KB. Load hanya apabila pengguna scroll ke bahagian TikTok (Intersection Observer).
- YouTube iframe tidak load `youtube.com` HTML penuh — gunakan `youtube-nocookie.com` untuk embed.
- Google Maps iframe: Load lazy.

**JavaScript:**
- Vue 3 bundle + Inertia perlu dioptimumkan dengan code splitting per halaman.
- Gunakan `import()` dynamic untuk komponen yang tidak kritikal.
- Total JS bundle (compressed): < 200KB untuk halaman utama.

**Fonts:**
- Font Lexend sudah dimuatkan dari Google Fonts dalam wireframe — pertimbangkan self-hosting untuk elak round-trip DNS.
- Gunakan `font-display: swap` untuk elak teks tidak kelihatan.

**Caching Browser:**
- Tetapkan cache header yang sesuai untuk aset statik (JS, CSS, gambar): `max-age=31536000, immutable`.
- Vite menggunakan content hash dalam nama fail — sesuai untuk long-term caching.

### 4.3 Kekangan Ramadan Traffic Spike

Dijangka traffic meningkat **5-10x** semasa Ramadan (terutama waktu Tarawih dan Sahur):
- Pastikan semua data diambil dari DB (bukan API luaran secara langsung).
- Aktifkan **OpCache PHP** untuk cache compiled PHP.
- Pertimbangkan **Laravel Octane** jika VPS support (untuk throughput lebih tinggi).
- Gunakan **CDN** untuk aset statik (Cloudflare Free tier mencukupi untuk < 500/hari).
- Live stream YouTube akan handle traffic sendiri — embed iframe tidak menambah load ke pelayan kita.

---

## 5. Kekangan Kebolehgunaan Admin Bukan Teknikal

### 5.1 Profil Admin

Admin adalah seorang **individu bukan teknikal** yang menguruskan laman web sebagai tanggungjawab sampingan (bukan sepenuh masa). Sistem mesti direka supaya:

- Boleh digunakan **tanpa sebarang latihan teknikal**.
- Semua ralat dipaparkan dalam **Bahasa Melayu** yang mudah difahami.
- Tindakan berbahaya (padam, tutup penerbitan) mesti ada **pengesahan dua langkah** (confirm dialog).

### 5.2 Kekangan UI/UX Admin

| Kekangan | Keperluan Rekaan |
|----------|-----------------|
| Admin tidak tahu HTML/CSS | WYSIWYG text editor (bukan markdown) untuk badan pengumuman |
| Admin tidak tahu saiz gambar | Auto-resize semua gambar yang dimuat naik |
| Admin mungkin guna telefon | Panel admin mesti fully responsive (mobile-friendly) |
| Admin tidak tahu format URL | URL video TikTok — sediakan arahan langkah demi langkah dalam panel |
| Admin mungkin lupa kata laluan | Perlu ada fungsi "Lupa Kata Laluan" via e-mel |
| Admin tidak faham quota API | Dashboard papar status setiap integrasi (hijau/merah) dengan mesej mudah |

### 5.3 Kekangan Bagi Upload Fail

- Format yang dibenarkan: PDF, JPG, JPEG, PNG, WebP sahaja.
- Saiz maksimum: 10MB setiap fail.
- Nama fail tidak boleh ada aksara khas atau ruang — sistem auto-sanitize nama fail.
- Fail disimpan dengan nama UUID dalam storage, bukan nama asal (keselamatan).
- Admin boleh lihat preview PDF dan gambar sebelum publish.

### 5.4 Mesej Ralat yang Mesra Pengguna

Jangan papar mesej ralat teknikal kepada admin. Contoh:

| Ralat Teknikal | Mesej kepada Admin |
|----------------|-------------------|
| `SQLSTATE[23000]: Integrity constraint violation` | "Rekod ini tidak dapat disimpan. Sila semak medan yang bertanda merah." |
| `GuzzleHttp\Exception\ConnectException` | "Tidak dapat sambung ke YouTube buat masa ini. Data lama akan dipapar." |
| `Facebook\Exceptions\FacebookResponseException: (#190)` | "Token Facebook telah luput. Sila hubungi pembangun untuk membaharui." |
| `419 Page Expired` | "Sesi anda telah tamat. Sila log masuk semula." |

---

## 6. Kekangan Bajet dan Infrastruktur

### 6.1 Realiti Masjid Kecil

MJFK Batu 3 adalah masjid komuniti kecil dengan sumber bajet yang terhad. Semua keputusan infrastruktur mesti mengambil kira **kos penyelenggaraan berterusan yang minimum**.

### 6.2 Kos Infrastruktur Dijangka

| Komponen | Anggaran Kos/Bulan |
|----------|-------------------|
| VPS saasku.my (sudah ada) | RM 0 (sedia ada) |
| Domain saasku.my (sudah ada) | RM 0 (sedia ada) |
| YouTube Data API v3 | RM 0 (Free tier 10K unit/hari) |
| Facebook Graph API | RM 0 (Free) |
| TikTok oEmbed | RM 0 (Free) |
| JAKIM e-Solat API | RM 0 (Kerajaan Malaysia) |
| Google Maps Embed (asas) | RM 0 (Free tanpa JavaScript API) |
| Cloudflare (CDN + SSL) | RM 0 (Free tier) |
| **Jumlah Bulanan Fasa 1** | **RM 0** |

### 6.3 Kos Berpotensi

| Senario | Kos Berpotensi |
|---------|----------------|
| YouTube API quota melebihi 10K/hari | Perlu minta peningkatan (free) atau bayar $0.006/1000 unit |
| Google Maps JavaScript API (ciri lanjutan) | $7/1000 request selepas $200 kredit/bulan percuma |
| Payment gateway (Fasa 2) — ToyyibPay | ~1.5% + RM0.50 per transaksi (tiada monthly fee) |
| SMS notifications (Fasa 2) | ~RM0.05 per SMS |

### 6.4 Kekangan Hosting VPS saasku.my

Berdasarkan cPanel hosting biasa pada VPS yang dikongsikan:

| Sumber | Anggaran Had | Implikasi |
|--------|-------------|-----------|
| RAM | 512MB–2GB (bergantung pelan) | PHP workers terhad; guna FPM dengan proses yang sesuai |
| CPU | 1-2 vCPU | Jangan buat operasi CPU-heavy semasa peak; queue semua jobs |
| Disk | 10-50GB | Hadkan upload fail; bersih fail lama secara berkala |
| Bandwidth | 1-5TB/bulan | Serve gambar melalui CDN; jangan stream video dari pelayan |
| Cron jobs | 1 minit interval minimum | Sesuai dengan jadual cron Laravel |

**Peringatan:** Pastikan cPanel membolehkan cron job dengan format standard dan `php artisan schedule:run` boleh dijalankan.

### 6.5 Skalabiliti Masa Depan

Jika traffic melebihi 500/hari secara konsisten:
- Pertimbangkan upgrade VPS atau pindah ke plan dedicated.
- Aktifkan Redis untuk cache dan queue (lebih cepat dari file/database driver).
- Pertimbangkan Cloudflare Page Rules untuk cache halaman statik.

---

## 7. Ringkasan Kekangan Kritikal

Kekangan berikut adalah **blocking issues** yang mesti diselesaikan sebelum launch:

| # | Kekangan | Resolusi Wajib |
|---|----------|----------------|
| C1 | YouTube API quota untuk live detection | Implement adaptive polling (off-peak vs peak) |
| C2 | Facebook token expiry | Setup System User token yang tidak luput |
| C3 | TikTok tiada API channel listing | Implement manual URL entry dalam panel admin |
| C4 | PDPA compliance | Halaman Dasar Privasi wajib sebelum launch |
| C5 | Admin bukan teknikal | Semua mesej ralat dalam BM, WYSIWYG editor, validation yang jelas |
| C6 | Performance mobile | Lazy load images dan embeds pihak ketiga |

---

*Dokumen ini hendaklah dibaca bersama dengan `07_Security/security.md` untuk kekangan keselamatan dan `08_Integrations/integrations.md` untuk had API yang lebih terperinci.*
