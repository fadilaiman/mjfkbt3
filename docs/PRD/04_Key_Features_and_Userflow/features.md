# PRD 04 — Ciri-ciri Utama dan Aliran Pengguna (Features & User Flows)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## 1. Gambaran Keseluruhan Produk

Laman web ini berfungsi sebagai **tetingkap agregasi tunggal** untuk semua kandungan digital MJFK Batu 3. Matlamat dwi-teras adalah:

1. **Da'wah dan Penjangkauan** — Menyebarkan kandungan ilmu, khutbah, dan rakaman kuliah kepada khalayak yang lebih luas.
2. **Perkhidmatan Komuniti** — Memudahkan jemaah tempatan mengakses maklumat masjid, waktu solat, aktiviti, dan saluran derma.

---

## 2. Senarai Ciri Lengkap dengan Keutamaan

Keutamaan:
- **P1** = Wajib untuk pelancaran (launch)
- **P2** = Selepas pelancaran (post-launch)

### 2.1 Paparan Awam (Public-Facing)

| ID | Ciri | Keutamaan | Keterangan Ringkas |
|----|------|-----------|-------------------|
| F-01 | Waktu Solat Auto (JAKIM) | P1 | Auto-fetch dari JAKIM e-Solat API, zon Shah Alam (WLY/SEL) |
| F-02 | Hero — Siaran Langsung YouTube | P1 | Detect & papar live stream aktif; fallback ke video terbaru |
| F-03 | Rakaman Kuliah (Video Library) | P1 | Grid/carousel video dari YouTube channel, papar via YouTube iframe |
| F-04 | TikTok Video Strip | P1 | Horizontal scroll strip, embed via TikTok oEmbed |
| F-05 | Suapan Facebook (Posts & Aktiviti) | P1 | Terkini dari Facebook Page via Graph API; fallback ke link luar |
| F-06 | Kalendar Aktiviti / Acara | P1 | Auto-sync Facebook Events + entri manual admin |
| F-07 | Pengumuman | P1 | Nota ringkas daripada admin; boleh attach PDF/gambar |
| F-08 | Tabung Derma (Placeholder UI) | P1 | UI butang derma dengan progress bar; API bayaran disambung fasa lanjut |
| F-09 | Direktori WhatsApp | P1 | Pautan wa.me terus untuk Bendahari, Pegawai Tadbir, Imam |
| F-10 | Embed Google Maps | P1 | Iframe Google Maps untuk lokasi masjid |
| F-11 | Pautan Media Sosial | P1 | Footer/header ikon ke YouTube, TikTok, Facebook, Instagram, Linktree |
| F-12 | Dasar Privasi & Terma | P1 | Halaman statik, wajib untuk pematuhan PDPA |
| F-13 | Navigasi Mobile-First | P1 | Hamburger menu, responsive, touch-friendly |
| F-14 | Dark Mode | P2 | Toggle gelap/terang (wireframe sudah ada class dark:) |
| F-15 | Instagram Feed | P2 | Embed post Instagram terbaru via oEmbed |
| F-16 | Carian Kandungan | P2 | Carian teks dalam kuliah dan pengumuman |
| F-17 | Pemberitahuan Web Push | P2 | Notify jemaah untuk waktu solat atau acara khas |
| F-18 | Integrasi WhatsApp Bot | P2 | Sambungan ke bot WhatsApp untuk pertanyaan auto |
| F-19 | Pendaftaran Nikah / Tempahan Ruang | P2 | Borang permohonan dalam talian |
| F-20 | Direktori Jemaah | P2 | Senarai hubungan komuniti (PDPA-compliant, opt-in) |

### 2.2 Panel Admin

| ID | Ciri | Keutamaan | Keterangan Ringkas |
|----|------|-----------|-------------------|
| A-01 | Log Masuk Admin (Single User) | P1 | E-mel + kata laluan, sesi selamat, ingat peranti |
| A-02 | Urus Pengumuman | P1 | CRUD teks, attach PDF/gambar (max 10MB) |
| A-03 | Urus Aktiviti Manual | P1 | Tambah/edit/padam acara yang tidak ada di Facebook |
| A-04 | Urus Tabung Derma | P1 | Kemaskini sasaran jumlah, nama tabung, status |
| A-05 | Semak Kandungan Auto | P1 | Lihat preview kandungan yang di-scrape; sembunyikan item tertentu |
| A-06 | Muat Naik Fail | P1 | Upload PDF/gambar, papan pemuka ringkas |
| A-07 | Log Aktiviti Admin | P1 | Rekod semua tindakan admin (audit trail mudah) |
| A-08 | Urus Direktori WhatsApp | P2 | Kemaskini nombor dan nama kenalan WhatsApp |

---

## 3. Penerangan Ciri Terperinci

### F-01: Waktu Solat Auto (JAKIM e-Solat)

**Tujuan:** Paparkan waktu solat harian terkini tanpa input manual.

**Kelakuan:**
- Ambil data dari JAKIM e-Solat API sekali sehari (cache 24 jam dalam DB).
- Zon: Shah Alam — kod zon `SGR01` (Petaling / Shah Alam).
- Papar: Subuh, Syuruk, Zuhur, Asar, Maghrib, Isyak.
- Sorot waktu solat yang sedang aktif atau akan datang dalam 30 minit (seperti wireframe).
- Jika API gagal, papar data cache hari sebelum dengan notis "Data mungkin tidak dikemas kini."

**Data yang dipapar:**
```
{ waktu_solat, tarikh_hijri, tarikh_masihi, zon }
```

---

### F-02: Hero — Siaran Langsung YouTube

**Tujuan:** Paparkan siaran langsung YouTube secara automatik sebagai elemen utama halaman.

**Kelakuan:**
1. Setiap 5 minit, sistem periksa YouTube Data API v3 untuk live stream aktif pada channel `@masjidjamekfastabiqulkhairat`.
2. Jika **ada live stream**: Papar YouTube iframe embed dengan badge "SEDANG BERSIARAN" dan kiraan penonton.
3. Jika **tiada live stream**: Papar video terbaru dari channel sebagai featured video.
4. Klik pada thumbnail membuka modal atau navigate ke halaman video.

**Peraturan Fallback:**
- Cache live detection result 5 minit.
- Jika API quota habis, sembunyikan badge LIVE dan papar thumbnail statik.

---

### F-03: Rakaman Kuliah (Video Library)

**Tujuan:** Papar arkib semua video kuliah dari YouTube.

**Kelakuan:**
- Fetch senarai video dari YouTube channel via `playlistItems` API (uploads playlist).
- Paparan: Horizontal scroll carousel di homepage (8 terbaru); halaman penuh `/kuliah` dengan grid dan pagination.
- Setiap video: thumbnail, tajuk, tarikh upload, kiraan tontonan, tempoh.
- Data di-cache 6 jam dalam DB.
- Admin boleh "sembunyikan" video tertentu dari paparan laman web.

---

### F-04: TikTok Video Strip

**Tujuan:** Aggregasi video TikTok dari `@mjfk.batu03` ke dalam laman web.

**Kelakuan:**
- Gunakan TikTok oEmbed API untuk embed video.
- Paparan: Horizontal scroll strip format potret (9:16) di homepage.
- Sebab limitasi API TikTok, gunakan pendekatan campuran:
  - Simpan URL video TikTok terpilih dalam DB (admin atau scrape manual).
  - Render embed via `https://www.tiktok.com/oembed?url={video_url}`.
- Admin boleh tambah URL video TikTok secara manual.

---

### F-05: Suapan Facebook

**Tujuan:** Papar post terkini dari Facebook Page MJFK Batu 3.

**Kelakuan:**
- Fetch posts via Facebook Graph API (`/{page_id}/posts`).
- Paparan: Grid 3 kolum di homepage (3 terbaru); halaman `/pengumuman` untuk semua.
- Jika post ada gambar, papar gambar. Jika teks sahaja, papar dengan ikon Facebook.
- Data di-cache 30 minit.
- Fallback: Jika API tidak tersedia, papar mesej "Kunjungi Facebook kami" dengan pautan.

---

### F-06: Kalendar Aktiviti

**Tujuan:** Satu sumber kebenaran untuk semua acara masjid.

**Kelakuan:**
- **Sumber 1 (Auto):** Facebook Events dari `/{page_id}/events` via Graph API.
- **Sumber 2 (Manual):** Admin tambah acara terus dalam panel admin.
- Paparan: Senarai kronologi di homepage (3 akan datang); halaman `/aktiviti` dengan calendar view dan list view.
- Setiap acara: tajuk, tarikh/masa, lokasi, penerangan, sumber (FB/Manual), gambar.
- Admin boleh override atau sembunyikan acara dari Facebook.

---

### F-07: Pengumuman

**Tujuan:** Saluran rasmi untuk maklumat segera dari pihak masjid.

**Kelakuan:**
- Admin cipta pengumuman dalam panel (teks + optional PDF/gambar).
- Paparan: Kad di homepage; halaman `/pengumuman` untuk semua.
- Kategori: `aktiviti`, `pengumuman`, `kebajikan` (dengan warna label berbeza seperti wireframe).
- PDF boleh dimuat turun oleh pengunjung.
- Pengumuman boleh ditandakan sebagai "utama" untuk pin di atas.

---

### F-08: Tabung Derma (Placeholder UI)

**Tujuan:** Sediakan UI derma yang sedia untuk disambung ke payment gateway.

**Kelakuan (Fasa 1 - Placeholder):**
- Papar nama tabung, penerangan, progress bar (jumlah terkumpul vs sasaran), kiraan penyumbang.
- Butang "Derma Sekarang" — sementara boleh redirect ke WhatsApp Bendahari (`wa.me/60123412459`).
- Admin boleh kemaskini nama tabung, sasaran jumlah, dan jumlah terkumpul secara manual.

**Kelakuan (Fasa 2 - Payment API):**
- Sambung ke payment gateway yang disediakan kemudian.
- Rekod setiap transaksi dalam DB.
- Auto-update progress bar.

---

### F-09: Direktori WhatsApp

**Tujuan:** Hubungkan jemaah terus ke pihak masjid yang relevan.

**Kenalan yang dikonfigurasi:**

| Nama | Peranan | Pautan |
|------|---------|--------|
| Bendahari | Infaq / Derma | `wa.me/60123412459` |
| Pegawai Tadbir (Imam Ashroff) | Pertanyaan Umum | `wa.me/+60182757817` |
| Imam Asim | Belajar Mengaji | `wa.me/qr/BOUHAIA55UUHP1` |

**Paparan:**
- Bahagian "Hubungi Kami" dengan kad setiap kenalan.
- Butang buka terus dalam aplikasi WhatsApp.
- Admin boleh edit nombor dan nama dari panel.

---

### F-12: Dasar Privasi & Terma Penggunaan

- Halaman `/dasar-privasi` dan `/terma-penggunaan`.
- Pematuhan PDPA Malaysia — nyatakan data apa yang dikumpul, tujuan, dan hak pengguna.
- Pautan di footer setiap halaman.

---

## 4. Aliran Pengguna (User Flows)

### 4.1 Aliran: Pengunjung Homepage

```
[Pengguna buka laman web]
        |
        v
[Homepage dimuatkan]
  - Header: Logo + Nav + Butang Derma
  - Hero: Siaran Langsung (atau video terbaru)
  - Sidebar: Waktu Solat hari ini (JAKIM auto)
        |
        v
[Pengguna scroll ke bawah]
  - Quick Links: Tonton Langsung | Derma | Waktu Solat | Aktiviti | Media Sosial
  - Tabung Pembangunan dengan progress bar
  - Rakaman Kuliah (horizontal carousel)
  - Video TikTok (horizontal scroll)
  - Kemas Kini Terkini (Facebook/Pengumuman - 3 kad)
  - Footer: Alamat | Hubungi | Google Maps | Social Links
        |
   [Pilih tindakan]
   /           |           \           \
[Tonton    [Klik Derma]  [Klik Aktiviti] [Klik Media Sosial]
 Video]         |               |               |
   |       [Halaman Derma]  [/aktiviti]    [Buka tab baru
[YouTube    (Placeholder     (Senarai +     ke platform]
 iframe     atau WhatsApp)   Calendar)]
 modal]
```

---

### 4.2 Aliran: Pengunjung Hubungi via WhatsApp

```
[Pengunjung ada soalan / nak derma / nak belajar mengaji]
        |
        v
[Skrol ke bahagian "Hubungi Kami" atau klik nav]
        |
        v
[Pilih kenalan yang sesuai]
  - Infaq/Derma → Bendahari
  - Pertanyaan Umum → Pegawai Tadbir
  - Belajar Mengaji → Imam Asim
        |
        v
[Klik butang WhatsApp]
        |
        v
[Redirect ke wa.me/{nombor}]
        |
        v
[Aplikasi WhatsApp dibuka pada peranti pengguna]
        |
        v
[Chat terus dengan pihak masjid]
```

---

### 4.3 Aliran: Admin Mengurus Kandungan

```
[Admin buka /admin/login]
        |
        v
[Masukkan e-mel + kata laluan]
        |
   [Gagal] → Mesej ralat + cuba semula (max 5 percubaan)
        |
   [Berjaya]
        v
[Dashboard Admin]
  - Ringkasan: Bil. Pengumuman | Acara Akan Datang | Status API
        |
   [Pilih tindakan]
    /        |         |          \
[Tambah   [Urus     [Urus      [Semak
Pengumuman] Aktiviti] Tabung]   Kandungan
    |          |        |       Auto-Sync]
    v          v        v           |
[Borang    [Borang  [Kemaskini  [Senarai
teks +     CRUD    sasaran +   kandungan
attach     acara]  jumlah]     dari API;
fail]                          toggle
    |                          sembunyikan]
    v
[Simpan → Kandungan dipapar di laman hadapan]
        |
        v
[Log keluar / tutup browser]
```

---

### 4.4 Aliran: Pengunjung Tonton Siaran Langsung

```
[Pengguna buka laman web semasa solat Jumaat / kuliah]
        |
        v
[Sistem detect live stream aktif]
        |
        v
[Hero papar badge "SEDANG BERSIARAN" + kiraan penonton]
        |
        v
[Pengguna klik butang Play]
        |
        v
[YouTube iframe bermain dalam laman web]
        |
   [Ingin kongsi?]
        |
        v
[Klik ikon kongsi → buka YouTube terus]
```

---

## 5. Strategi Agregasi Kandungan Per Platform

### 5.1 YouTube

- **Kaedah:** YouTube Data API v3 (API key dalam `.env`).
- **Data yang diambil:** Live streams, video library (uploads playlist), tajuk, thumbnail, kiraan tontonan, tempoh.
- **Kekerapan refresh:** Live detection tiap 5 minit; video library tiap 6 jam.
- **Penyimpanan cache:** DB table `youtube_videos` + `youtube_live_status`.
- **Paparan:** Embed iframe `https://www.youtube.com/embed/{video_id}` — tidak memerlukan JS SDK.
- **Fallback:** Jika API quota habis, papar data cache terakhir.

### 5.2 Facebook

- **Kaedah:** Facebook Graph API v19+ dengan Page Access Token (long-lived).
- **Data yang diambil:** Posts (`/{page_id}/posts`), Events (`/{page_id}/events`), gambar.
- **Kekerapan refresh:** Posts tiap 30 minit; Events tiap 1 jam.
- **Penyimpanan cache:** DB table `fb_posts`, `fb_events`.
- **Paparan:** Render data dari DB (bukan embed iframe Facebook) — lebih pantas dan privacy-friendly.
- **Fallback:** Pautan ke Facebook Page jika API gagal.
- **Had:** Page Access Token mesti diperbaharui tiap 60 hari (atau gunakan system user token yang tidak luput).

### 5.3 TikTok

- **Kaedah:** TikTok oEmbed API (`https://www.tiktok.com/oembed?url={video_url}`).
- **Pendekatan campuran:** Admin simpan URL video TikTok pilihan dalam DB; sistem ambil oEmbed data.
- **Kekerapan refresh:** oEmbed cache 24 jam.
- **Penyimpanan cache:** DB table `tiktok_videos`.
- **Paparan:** Render `<blockquote>` embed dari oEmbed response + TikTok JS widget.
- **Had:** Tiada API awam TikTok untuk listing video — memerlukan input manual URL.

### 5.4 Instagram

- **Kaedah (P2):** Instagram Basic Display API atau Instagram oEmbed.
- **Paparan:** Grid post terbaru; klik buka Instagram.
- **Had:** Memerlukan Facebook Developer App approval.

### 5.5 JAKIM e-Solat

- **Kaedah:** HTTP GET ke endpoint JAKIM.
- **Kekerapan refresh:** Sekali sehari (pagi 00:01 MYT), cron job.
- **Penyimpanan cache:** DB table `prayer_times`, cache sebulan ke hadapan.
- **Zon:** `SGR01` (Shah Alam / Petaling).

### 5.6 Pengumuman & Aktiviti Manual

- **Kaedah:** Input admin terus dalam panel (bukan scrape).
- **Penyimpanan:** DB table `announcements`, `events`.
- **Refresh:** Serta-merta selepas admin simpan.

---

## 6. Halaman-halaman Laman Web

| URL | Nama Halaman | Keutamaan |
|-----|-------------|-----------|
| `/` | Utama (Homepage) | P1 |
| `/kuliah` | Rakaman Kuliah (Video Library) | P1 |
| `/aktiviti` | Kalendar Aktiviti & Acara | P1 |
| `/pengumuman` | Semua Pengumuman | P1 |
| `/derma` | Halaman Derma | P1 |
| `/hubungi` | Hubungi Kami + Direktori WA | P1 |
| `/dasar-privasi` | Dasar Privasi (PDPA) | P1 |
| `/terma-penggunaan` | Terma Penggunaan | P1 |
| `/admin` | Dashboard Admin (redirect ke login) | P1 |
| `/admin/login` | Log Masuk Admin | P1 |
| `/admin/pengumuman` | Urus Pengumuman | P1 |
| `/admin/aktiviti` | Urus Aktiviti | P1 |
| `/admin/derma` | Urus Tabung Derma | P1 |
| `/admin/kandungan` | Semak & Toggle Kandungan Auto | P1 |
| `/admin/fail` | Muat Naik & Urus Fail | P1 |

---

*Dokumen ini hendaklah dibaca bersama dengan `05_Backend/architecture.md` dan `08_Integrations/integrations.md`.*
