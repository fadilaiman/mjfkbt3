# PRD 08 — Integrasi Sistem (Integrations)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08

---

## Gambaran Keseluruhan

Laman web ini mengintegrasikan **enam platform luaran** dalam fasa 1, dengan satu platform tambahan dirancang untuk fasa 2. Semua integrasi mengikut prinsip **cache-first** — data diambil mengikut jadual dan disimpan dalam DB, bukan dipanggil secara langsung semasa pengguna melayari laman web.

| # | Integrasi | Keutamaan | Kaedah |
|---|-----------|-----------|--------|
| 1 | YouTube Data API v3 | P1 | REST API dengan API Key |
| 2 | Facebook Graph API | P1 | REST API dengan Page Access Token |
| 3 | TikTok oEmbed | P1 | oEmbed (tanpa auth) |
| 4 | JAKIM e-Solat API | P1 | REST API (tanpa auth) |
| 5 | WhatsApp Direct Links | P1 | wa.me URL scheme (tiada API) |
| 6 | Google Maps Embed | P1 | Iframe embed |
| 7 | Instagram oEmbed | P2 | oEmbed atau Basic Display API |
| 8 | Payment Gateway | P2 | API disediakan kemudian |

---

## 1. YouTube Data API v3

### 1.1 Maklumat Akaun

- **Channel:** `@masjidjamekfastabiqulkhairat`
- **Channel URL:** `https://youtube.com/@masjidjamekfastabiqulkhairat`
- **Channel ID:** Perlu diambil dari YouTube Studio atau API (`https://www.googleapis.com/youtube/v3/channels?part=id&forHandle=masjidjamekfastabiqulkhairat&key={KEY}`)
- **Uploads Playlist ID:** Tambah `UU` sebagai prefix kepada Channel ID (contoh: jika Channel ID = `UCxxxxxxx`, maka Playlist ID = `UUxxxxxxx`)

### 1.2 Persediaan

1. Cipta projek dalam [Google Cloud Console](https://console.cloud.google.com/).
2. Aktifkan **YouTube Data API v3**.
3. Cipta **API Key** (bukan OAuth — kita hanya baca data awam).
4. Hadkan API Key: Hadkan kepada domain `mjfkbt3.saasku.my` dan kepada YouTube Data API v3 sahaja.
5. Simpan dalam `.env` sebagai `YOUTUBE_API_KEY`.

### 1.3 Penggunaan Quota

Quota harian lalai: **10,000 unit/hari**.

| Operasi | Kos Unit |
|---------|----------|
| `search.list` (detect live) | 100 unit |
| `playlistItems.list` (video library) | 1 unit |
| `videos.list` (detail video) | 1 unit |

**Anggaran penggunaan harian:**
- Detect live: 100 unit × 288 panggilan (tiap 5 min) = **28,800 unit** ⚠️

**Penyelesaian untuk live detection:** Guna `search.list` sekali untuk detect, kemudian guna `videos.list` (1 unit) untuk semak status. Atau implement logik: jika `is_live = false` lebih dari 1 jam, kurangkan kekerapan semakan kepada tiap 15 minit.

**Anggaran penggunaan harian yang dioptimumkan:**
- Live detection (tiap 15 min waktu off-peak, 5 min waktu peak): ~50 × 100 + 100 × 1 = ~5,100 unit/hari
- Video library sync (4x sehari): 4 × 1 = 4 unit
- Video detail (50 video): 1 unit
- **Jumlah anggaran: ~5,200 unit/hari** ✓

### 1.4 Butiran API

#### Detect Live Stream

```http
GET https://www.googleapis.com/youtube/v3/search
  ?part=id,snippet
  &channelId={YOUTUBE_CHANNEL_ID}
  &eventType=live
  &type=video
  &key={YOUTUBE_API_KEY}
```

Respons jika ada live:
```json
{
  "items": [{
    "id": { "videoId": "abc123" },
    "snippet": {
      "title": "Kuliah Maghrib Khamis",
      "liveBroadcastContent": "live"
    }
  }]
}
```

Respons jika tiada live: `"items": []`

#### Ambil Statistik Live (Concurrent Viewers)

```http
GET https://www.googleapis.com/youtube/v3/videos
  ?part=snippet,liveStreamingDetails,statistics
  &id={VIDEO_ID}
  &key={YOUTUBE_API_KEY}
```

Gunakan `liveStreamingDetails.concurrentViewers`.

#### Ambil Video Library

```http
GET https://www.googleapis.com/youtube/v3/playlistItems
  ?part=snippet,contentDetails
  &playlistId={YOUTUBE_UPLOADS_PLAYLIST_ID}
  &maxResults=50
  &pageToken={NEXT_PAGE_TOKEN}
  &key={YOUTUBE_API_KEY}
```

#### Ambil Detail Video (Tontonan, Tempoh)

```http
GET https://www.googleapis.com/youtube/v3/videos
  ?part=snippet,contentDetails,statistics
  &id=VIDEO_ID_1,VIDEO_ID_2,...
  &key={YOUTUBE_API_KEY}
```

Ambil maksimum 50 video ID setiap panggilan.

### 1.5 Embed Video

Gunakan **iframe embed standard** — tiada YouTube JS SDK diperlukan untuk fungsi asas:

```html
<iframe
  src="https://www.youtube.com/embed/{VIDEO_ID}?rel=0&modestbranding=1"
  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen
  class="w-full aspect-video rounded-xl"
></iframe>
```

Untuk live stream:
```html
<iframe
  src="https://www.youtube.com/embed/{VIDEO_ID}?autoplay=1"
  ...
></iframe>
```

### 1.6 Service Class: `YouTubeService`

```php
namespace App\Services;

class YouTubeService
{
    public function checkLiveStream(): ?array
    public function getLiveStreamDetails(string $videoId): array
    public function syncVideoLibrary(): void
    public function getVideoDetails(array $videoIds): array
}
```

---

## 2. Facebook Graph API

### 2.1 Maklumat Akaun

- **Facebook Page:** `https://www.facebook.com/mjfkbt3/`
- **Page ID:** Ambil dari URL Facebook Page atau Graph API Explorer
- **API Version:** v19.0 (atau versi stabil terkini)

### 2.2 Persediaan

1. Cipta **Facebook App** dalam [Meta for Developers](https://developers.facebook.com/).
2. Jenis App: **Business**.
3. Tambah produk: **Facebook Login** dan **Pages API**.
4. Minta kebenaran (permissions):
   - `pages_read_engagement` — baca posts
   - `pages_read_user_content` — baca kandungan pengguna pada page
   - `pages_show_list` — senarai pages
   - Untuk events: `pages_manage_events` (atau `public_profile` cukup untuk events awam)
5. Hasilkan **Page Access Token** melalui Graph API Explorer.
6. Panjangkan token: Tukar short-lived token kepada long-lived token (60 hari).

**Kaedah terbaik untuk production:** Gunakan **System User** dalam Meta Business Suite untuk token yang tidak luput. Langkah:
- Meta Business Suite → Tetapan → Pengguna Sistem
- Cipta System User, berikan akses ke Facebook Page
- Jana token System User (pilih tidak luput)

### 2.3 Pembaharuan Token Automatik

Jika menggunakan long-lived token (60 hari), perlu implement:
- Cron job semak token expiry 7 hari sebelum luput
- Notify admin melalui log atau e-mel

### 2.4 Butiran API

#### Ambil Posts Terbaru

```http
GET https://graph.facebook.com/v19.0/{PAGE_ID}/posts
  ?fields=id,message,story,full_picture,permalink_url,created_time,status_type,
          attachments{media_type,media,url}
  &limit=20
  &access_token={PAGE_ACCESS_TOKEN}
```

Respons:
```json
{
  "data": [{
    "id": "123456789_987654321",
    "message": "Teks post...",
    "full_picture": "https://...",
    "permalink_url": "https://facebook.com/...",
    "created_time": "2026-03-08T10:00:00+0000"
  }],
  "paging": {
    "cursors": { "after": "...", "before": "..." },
    "next": "https://graph.facebook.com/..."
  }
}
```

#### Ambil Events

```http
GET https://graph.facebook.com/v19.0/{PAGE_ID}/events
  ?fields=id,name,description,start_time,end_time,place,cover,attending_count,
          event_times,type
  &time_filter=upcoming
  &access_token={PAGE_ACCESS_TOKEN}
```

#### Semak Status Token

```http
GET https://graph.facebook.com/v19.0/debug_token
  ?input_token={PAGE_ACCESS_TOKEN}
  &access_token={APP_ID}|{APP_SECRET}
```

Respons mengandungi `data.expires_at` (Unix timestamp).

### 2.5 Pengendalian Error

| Error Code | Maksud | Tindakan |
|------------|--------|----------|
| 190 | Token invalid/luput | Log error, notify admin, guna cache lama |
| 32 | API rate limit | Tunggu, cuba semula 30 minit kemudian |
| 4 | App-level rate limit | Kurangkan kekerapan panggilan |
| 100 | Invalid parameter | Semak format request |

### 2.6 Service Class: `FacebookService`

```php
namespace App\Services;

class FacebookService
{
    public function syncPosts(int $limit = 20): void
    public function syncEvents(): void
    public function checkTokenExpiry(): array  // returns ['expires_at' => ..., 'is_valid' => bool]
    public function refreshToken(): string
}
```

---

## 3. TikTok oEmbed

### 3.1 Maklumat Akaun

- **TikTok:** `https://www.tiktok.com/@mjfk.batu03`
- **Nota:** TikTok tidak ada API awam untuk listing video dari channel. Pendekatan yang digunakan adalah **admin simpan URL video secara manual**.

### 3.2 Bagaimana ia Berfungsi

TikTok oEmbed adalah endpoint HTTP mudah yang mengembalikan HTML embed untuk URL video TikTok yang diberikan. **Tiada API key diperlukan.**

```http
GET https://www.tiktok.com/oembed?url=https://www.tiktok.com/@mjfk.batu03/video/1234567890
```

Respons:
```json
{
  "version": "1.0",
  "type": "video",
  "title": "Tajuk Video",
  "author_name": "mjfk.batu03",
  "author_url": "https://www.tiktok.com/@mjfk.batu03",
  "thumbnail_url": "https://...",
  "thumbnail_width": 576,
  "thumbnail_height": 1024,
  "html": "<blockquote class=\"tiktok-embed\" ...>...</blockquote>",
  "width": 325,
  "height": 575
}
```

### 3.3 Cara Admin Tambah Video TikTok

1. Admin buka TikTok dalam browser.
2. Pergi ke video yang ingin dipapar di laman web.
3. Salin URL video (contoh: `https://www.tiktok.com/@mjfk.batu03/video/1234567890`).
4. Masuk ke panel admin → Kandungan → TikTok Videos.
5. Paste URL dan simpan.
6. Sistem auto-fetch oEmbed data dan simpan dalam DB.

### 3.4 Rendering di Frontend

Gunakan HTML dari field `oembed_html` + tambah script TikTok:

```html
<!-- Dalam Vue component -->
<div v-html="video.oembed_html"></div>

<!-- Di bahagian bawah halaman (sekali sahaja) -->
<script async src="https://www.tiktok.com/embed.js"></script>
```

**Alternatif lebih ringan:** Papar thumbnail dari `thumbnail_url` dan link ke TikTok apabila diklik, untuk elak load TikTok embed JS yang berat.

### 3.5 Service Class: `TikTokService`

```php
namespace App\Services;

class TikTokService
{
    public function fetchOEmbed(string $videoUrl): array
    public function refreshAllOEmbeds(): void  // Untuk cron job harian
}
```

---

## 4. JAKIM e-Solat API

### 4.1 Maklumat API

- **URL Asas:** `https://www.e-solat.gov.my/index.php`
- **Endpoint:** `?r=esolatApi/takwimsolat`
- **Pengesahan:** Tiada (API awam kerajaan Malaysia)
- **Format:** JSON

### 4.2 Kod Zon

| Zon | Negeri | Kawasan |
|-----|--------|---------|
| `SGR01` | Selangor | Gombak, Petaling, Sepang, Hulu Langat, Hulu Selangor, S. Alam |

MJFK Batu 3 berada di **Persiaran Jubli Perak Batu 3, Shah Alam** — zon `SGR01` adalah tepat.

### 4.3 Butiran API

#### Ambil Waktu Solat Sebulan

```http
GET https://www.e-solat.gov.my/index.php
  ?r=esolatApi/takwimsolat
  &period=month
  &zone=SGR01
```

#### Ambil Waktu Solat Sehari

```http
GET https://www.e-solat.gov.my/index.php
  ?r=esolatApi/takwimsolat
  &period=today
  &zone=SGR01
```

#### Ambil Waktu Solat Setahun

```http
GET https://www.e-solat.gov.my/index.php
  ?r=esolatApi/takwimsolat
  &period=year
  &zone=SGR01
```

**Cadangan:** Gunakan `period=month` setiap bulan untuk elak request besar.

### 4.4 Format Respons

```json
{
  "status": "OK!",
  "serverTime": "08-03-2026 07:00:00",
  "periodType": "month",
  "lang": "ms",
  "zone": "SGR01",
  "bearing": "292° 39' 2.39\"",
  "prayerTime": [
    {
      "hijri": "08-Rejab-1447",
      "date": "08-03-2026",
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

### 4.5 Strategi Cache

- Ambil data sebulan sekali pada 1 haribulan.
- Simpan semua hari dalam DB table `prayer_times`.
- Jika request pada 1 Julai, sistem check adakah data Julai sudah ada. Jika ada, skip.
- Jika API tidak tersedia, guna data hari sebelum dan papar notis kecil.
- Pre-fetch bulan depan pada 25 haribulan.

### 4.6 Paparan Waktu Solat Aktif

Logik untuk highlight waktu solat yang sedang berlangsung:

```javascript
// Dalam Vue component
function getActivePrayer(prayerTimes, currentTime) {
  const order = ['subuh', 'syuruk', 'zohor', 'asar', 'maghrib', 'isyak']
  const times = order.map(name => ({
    name,
    time: parseTime(prayerTimes[name])
  }))

  // Cari waktu solat yang sedang aktif (waktu sekarang >= waktu solat ini,
  // dan waktu sekarang < waktu solat berikutnya)
  for (let i = times.length - 1; i >= 0; i--) {
    if (currentTime >= times[i].time) {
      return times[i].name
    }
  }
  return 'isyak' // Selepas Isyak hingga Subuh
}
```

### 4.7 Service Class: `JakimPrayerService`

```php
namespace App\Services;

class JakimPrayerService
{
    public function syncMonth(int $year, int $month): void
    public function getTodayPrayerTimes(): array
    public function getMonthPrayerTimes(int $year, int $month): array
    public function getActivePrayer(): string  // Nama waktu solat semasa
    public function getNextPrayer(): array     // ['name' => ..., 'time' => ..., 'minutes_until' => ...]
}
```

---

## 5. WhatsApp Direct Links

### 5.1 Maklumat Kenalan

| Peranan | Nombor/QR | Format URL |
|---------|-----------|------------|
| Bendahari (Infaq) | 60123412459 | `https://wa.me/60123412459` |
| Pegawai Tadbir (Imam Ashroff) | +60182757817 | `https://wa.me/60182757817` |
| Imam Asim (Belajar Mengaji) | QR Code | `https://wa.me/qr/BOUHAIA55UUHP1` |

### 5.2 Format URL wa.me

```
https://wa.me/{NOMBOR_TELEFON}
  - Nombor dalam format antarabangsa tanpa + atau simbol lain
  - Contoh: 60123412459 (bukan +6012-341 2459)

https://wa.me/qr/{QR_ID}
  - Untuk akaun yang menggunakan WhatsApp QR link
  - ID QR: BOUHAIA55UUHP1

Dengan mesej pre-filled:
https://wa.me/60123412459?text=Salam%2C%20saya%20ingin%20membuat%20infaq
```

### 5.3 Pelaksanaan

Tiada API atau backend diperlukan. Pautan terus dalam HTML/Vue:

```html
<a
  href="https://wa.me/60123412459?text=Salam%2C%20saya%20ingin%20membuat%20infaq"
  target="_blank"
  rel="noopener noreferrer"
  class="..."
>
  Hubungi Bendahari
</a>
```

**Perhatian:** `rel="noopener noreferrer"` wajib untuk keselamatan apabila menggunakan `target="_blank"`.

### 5.4 Konfigurasi dalam DB

Nombor WhatsApp disimpan dalam table `whatsapp_contacts` dan boleh dikemaskini oleh admin tanpa perlu edit kod. Frontend ambil data ini dari backend via Inertia props.

---

## 6. Google Maps Embed

### 6.1 Maklumat Lokasi

- **Google Maps Link:** `https://maps.app.goo.gl/QMDfe4jAAAgUZEp37`
- **Alamat:** Persiaran Jubli Perak Batu 3, Shah Alam, Petaling Jaya, Malaysia

### 6.2 Cara Embed

Gunakan iframe embed dari Google Maps tanpa API key (untuk embed asas):

1. Buka Google Maps.
2. Cari lokasi masjid.
3. Klik "Kongsi" → "Embed peta".
4. Salin kod iframe.

Atau gunakan URL Places embed:
```html
<iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!...{PLACE_DATA}..."
  width="100%"
  height="300"
  style="border:0;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade"
  title="Lokasi Masjid Jamek Fastabiqul Khayrat Batu 3"
></iframe>
```

**Nota:** Embed asas Maps tidak memerlukan API key. Hanya perlukan API key jika menggunakan Maps JavaScript API atau Places API untuk ciri lanjutan.

### 6.3 Pautan Fallback

```html
<a
  href="https://maps.app.goo.gl/QMDfe4jAAAgUZEp37"
  target="_blank"
  rel="noopener noreferrer"
>
  Buka dalam Google Maps
</a>
```

---

## 7. Instagram oEmbed (P2)

### 7.1 Status

Integrasi Instagram dijadualkan untuk **Fasa 2 (Post-Launch)**.

### 7.2 Maklumat Akaun

- **Instagram:** `@mjfk.bt3`
- **URL:** `https://www.instagram.com/mjfk.bt3/`

### 7.3 Pilihan Pendekatan

**Pilihan A: Instagram oEmbed (lebih mudah)**

```http
GET https://graph.facebook.com/v19.0/instagram_oembed
  ?url=https://www.instagram.com/p/{SHORTCODE}/
  &access_token={FACEBOOK_APP_ID}|{FACEBOOK_CLIENT_TOKEN}
```

- Memerlukan Facebook App dengan kebenaran asas.
- Hanya boleh embed post individual (bukan listing semua post).
- Admin perlu simpan URL post secara manual (sama seperti TikTok).

**Pilihan B: Instagram Basic Display API**

- Memerlukan persetujuan Meta App Review.
- Boleh ambil senarai post dari akaun.
- Proses kelulusan lebih lama.

**Pilihan C: EmbedSocial / Curator.io (Third-party)**

- Lebih mudah setup tapi ada kos.
- Sesuai jika Meta API terlalu rumit.

**Cadangan:** Mulakan dengan Pilihan A (oEmbed manual) untuk Fasa 2, kemudian beralih ke Pilihan B jika diperlukan.

---

## 8. Payment Gateway (P2 — Placeholder)

### 8.1 Status

Payment gateway **belum ditentukan**. UI placeholder sudah ada dalam Fasa 1 (butang "Derma Sekarang" redirect ke WhatsApp Bendahari).

### 8.2 Perancangan Fasa 2

Apabila API payment disediakan oleh pihak masjid, integrasi akan merangkumi:

- **Endpoint callback/webhook** untuk terima notifikasi pembayaran.
- **Kemaskini `donations.collected_amount`** secara automatik selepas pembayaran berjaya.
- **Rekod transaksi** dalam table baru `donation_transactions`:

```sql
CREATE TABLE donation_transactions (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donation_id         BIGINT UNSIGNED NOT NULL,
    transaction_ref     VARCHAR(200) UNIQUE NOT NULL,
    amount              DECIMAL(12,2) NOT NULL,
    currency            VARCHAR(3) DEFAULT 'MYR',
    status              ENUM('pending','success','failed','refunded') DEFAULT 'pending',
    payment_method      VARCHAR(100) NULL,
    payer_name          VARCHAR(200) NULL,   -- Jika pengguna berikan
    payer_email         VARCHAR(200) NULL,   -- Jika pengguna berikan
    payer_phone         VARCHAR(50) NULL,    -- Jika pengguna berikan
    consent_given       BOOLEAN DEFAULT FALSE, -- PDPA consent
    consent_timestamp   TIMESTAMP NULL,
    gateway_response    JSON NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 8.3 Gateway yang Mungkin Digunakan (Malaysia)

| Gateway | Nota |
|---------|------|
| Billplz | Popular untuk NGO/masjid, ada FPX |
| iPay88 | Established, sokongan FPX/card |
| ToyyibPay | Direka untuk masjid/surau, mudah setup |
| Stripe | Antarabangsa, perlu USD settlement |

**Cadangan:** **ToyyibPay** — direka khusus untuk pengumpulan dana masjid dan pertubuhan Islam, proses pendaftaran lebih mudah, fee kompetitif.

---

## 9. Pengurusan API Keys

### 9.1 Simpan dalam `.env`

**Jangan sesekali commit API keys ke dalam Git.** Semua keys mesti disimpan dalam `.env` dan `.env` mesti ada dalam `.gitignore`.

### 9.2 Rotasi Key

| Key | Kekerapan Rotasi | Cara |
|-----|-----------------|------|
| YouTube API Key | Tahunan atau jika bocor | Google Cloud Console |
| Facebook Page Access Token | 60 hari (atau guna System User yang tidak luput) | Meta Developer Portal |
| Facebook App Secret | Tahunan atau jika bocor | Meta Developer Portal |
| Payment Gateway Key | Ikut arahan gateway | Portal gateway |

### 9.3 Pemantauan

- Setup **alert quota** dalam Google Cloud Console untuk YouTube API (alert pada 80% penggunaan).
- Log semua panggilan API ke dalam Laravel Log dengan status (berjaya/gagal).
- Semak log mingguan atau setup alert e-mel untuk error berulang.

---

## 10. Ringkasan Dependency dan Timeline

### Pra-syarat Sebelum Build

Sebelum pembangun boleh mulakan kerja integrasi, pihak masjid perlu:

| Tugasan | Siapa | Anggaran Masa |
|---------|-------|---------------|
| Cipta Google Cloud Project & aktifkan YouTube API | Pembangun | 1 jam |
| Dapatkan YouTube Channel ID | Admin/Pembangun | 15 minit |
| Cipta Facebook App dalam Meta for Developers | Pembangun | 2 jam |
| Jana Facebook Page Access Token | Pembangun | 30 minit |
| Setup System User Facebook (untuk token tidak luput) | Pembangun | 1 jam |
| Simpan URL video TikTok yang ingin dipapar (10-20 video) | Admin Masjid | 30 minit |
| Dapatkan kode embed Google Maps | Pembangun | 15 minit |
| Semak dan sahkan zon JAKIM (SGR01) | Pembangun | 15 minit |

---

*Dokumen ini hendaklah dibaca bersama dengan `05_Backend/architecture.md` untuk skema DB dan service class details.*
