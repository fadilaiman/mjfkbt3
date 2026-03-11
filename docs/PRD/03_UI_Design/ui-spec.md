# PRD 03 — Spesifikasi Reka Bentuk UI (UI Design Spec)

**Projek:** Mosque Digital Hub — mjfkbt3.saasku.my
**Masjid:** Masjid Jamek Fastabiqul Khayrat Batu 3, Shah Alam
**Versi:** 1.0
**Tarikh:** 2026-03-08
**Wireframe rujukan:** `wireframes/mainpage.html`

---

## 1. Identiti Jenama (Brand Identity)

| Aspek | Nilai |
|-------|-------|
| Nama penuh | Masjid Jamek Fastabiqul Khayrat Batu 3 |
| Nama singkatan | MJFKBT3 |
| Warna utama (primary) | `#13ec49` (hijau cerah) |
| Latar terang | `#f6f8f6` |
| Latar gelap | `#102215` |
| Font | Lexend (Google Fonts), weight 100–900 |
| Ikon | Material Symbols Outlined (Google Fonts) |
| Border radius | Default `0.5rem`, lg `1rem`, xl `1.5rem`, full `9999px` |
| Ikon logo | `mosque` (Material Symbols) di atas latar `primary` |

---

## 2. Pendekatan Reka Bentuk

### 2.1 Mobile-First
- Reka bentuk bermula dari saiz skrin mobile (< 640px).
- Konten disusun secara vertikal pada mobile, grid pada desktop.
- Touch target minimum 44x44px untuk semua butang dan link.

### 2.2 Prinsip Utama
1. **Mudah dibaca** — Font size minimum 14px untuk body text. Kontras tinggi.
2. **Navigasi jelas** — Hamburger menu pada mobile, horizontal nav pada desktop.
3. **Maklumat penting di atas** — Siaran langsung dan waktu solat sentiasa di bahagian atas.
4. **Interaksi ringkas** — Minimum klik untuk sampai ke kandungan.

---

## 3. Susun Atur Halaman Utama (Homepage Layout)

Berdasarkan wireframe `mainpage.html`:

```
┌─────────────────────────────────────────────┐
│  HEADER (sticky)                             │
│  [Logo + Nama Masjid]  [Nav Links]  [Derma]  │
├─────────────────────────────────────────────┤
│                                              │
│  HERO SECTION (grid: 2/3 + 1/3)             │
│  ┌──────────────────┐ ┌──────────────┐       │
│  │ Siaran Langsung  │ │ Waktu Solat  │       │
│  │ / Video Terbaru  │ │ Widget       │       │
│  │ (aspect-video)   │ │ (5 waktu)    │       │
│  │ + Badge LIVE     │ │ + highlight  │       │
│  │ + Play button    │ │   aktif      │       │
│  └──────────────────┘ └──────────────┘       │
│                                              │
│  QUICK LINKS (5 butang grid)                 │
│  [Tonton] [Derma] [Solat] [Aktiviti] [Media] │
│                                              │
│  TABUNG DERMA (banner gradient gelap)        │
│  [Nama Tabung] [Progress Bar] [Butang Derma] │
│                                              │
│  RAKAMAN KULIAH (horizontal scroll)          │
│  [Video 1] [Video 2] [Video 3] →            │
│                                              │
│  VIDEO TIKTOK (horizontal scroll, 9:16)      │
│  [TikTok 1] [TikTok 2] [TikTok 3] [4] →    │
│                                              │
│  KEMAS KINI TERKINI (3-column grid)          │
│  [Kad 1] [Kad 2] [Kad 3]                    │
│                                              │
├─────────────────────────────────────────────┤
│  FOOTER                                     │
│  [Logo+Info] [Pautan] [Hubungi] [Peta]       │
│  [© 2024] [Terma] [Privasi]                 │
└─────────────────────────────────────────────┘
```

### Mobile Layout (< 768px)
- Hero: Full width video, waktu solat di bawah video (stacked)
- Quick Links: 2 kolum grid
- Kuliah & TikTok: Single row horizontal scroll
- Kemas Kini: Single column stacked
- Nav: Hamburger menu

---

## 4. Spesifikasi Komponen

### 4.1 Header (Sticky)

```
Height: h-20 (80px)
Background: bg-white/80 backdrop-blur-md (semi-transparent)
Border: border-b border-primary/10
Position: sticky top-0 z-50
Content:
  Left:   Logo icon (h-10 w-10, rounded-lg, bg-primary) + Nama masjid
  Center: Nav links (hidden on mobile) — Utama, Siaran Langsung, Kuliah Lepas, Aktiviti, Pengumuman
  Right:  Butang Derma (hidden on mobile) + Hamburger icon (mobile only)
```

### 4.2 Hero — Siaran Langsung

```
Container: relative overflow-hidden rounded-xl bg-black aspect-video
Badge LIVE: absolute top-4 left-4, bg-red-600, rounded-full, animate-pulse
  Text: "SEDANG BERSIARAN" + dot putih
Play button: center, h-20 w-20, rounded-full bg-primary
  Hover: scale-110 transition
Info bar: absolute bottom-0, bg-gradient-to-t from-black/80
  Title: text-2xl md:text-3xl font-bold text-white
  Subtitle: text-slate-200 text-sm
Shadow: shadow-2xl
```

### 4.3 Waktu Solat Widget

```
Container: rounded-xl border border-primary/10 bg-white p-6, islamic-pattern background
Header: "Waktu Solat" + badge lokasi (bg-primary/10 rounded-full)
Rows: 5 waktu solat, each p-3 rounded-lg bg-slate-50
Active row: p-4 rounded-xl bg-primary/20 border-2 border-primary ring-4 ring-primary/5
  Icon: notifications_active (material symbol)
  Time: text-primary font-black
  Subtitle: "bermula dalam Xm" (text-[10px] uppercase)
Inactive future: opacity-60
```

### 4.4 Quick Links

```
Layout: grid grid-cols-2 md:grid-cols-5 gap-4
Each button:
  Container: flex flex-col items-center gap-3 p-6 rounded-xl bg-white border
  Icon: material-symbols-outlined text-3xl text-primary
  Label: text-sm font-bold
  Hover: border-primary/50, shadow-md, icon scale-110
Last button: col-span-2 md:col-span-1 (fill remaining space on mobile)
```

### 4.5 Tabung Derma Banner

```
Container: rounded-2xl bg-gradient-to-r from-background-dark via-[#1a3d24] to-background-dark p-8 md:p-12
Layout: grid md:grid-cols-2 gap-12 items-center
Left:
  Title: text-3xl font-bold text-white
  Description: text-slate-300
  CTA: rounded-xl bg-primary px-8 py-3 font-bold text-background-dark
  Stats: "1,245 Orang" penyumbang
Right:
  Progress: "RM 32,450 terkumpul" vs "Sasaran: RM 100,000"
  Bar: h-4 rounded-full bg-white/10 with bg-primary fill
  Labels: text-[10px] uppercase tracking-wider text-slate-400
```

### 4.6 Kad Video Kuliah

```
Container: min-w-[280px] md:min-w-[320px], group cursor-pointer
Thumbnail: aspect-video rounded-xl overflow-hidden
  Image: object-cover, group-hover:scale-105 transition
  Duration badge: absolute bottom-2 right-2, bg-black/60, text-[10px] font-bold
Title: font-bold text-sm line-clamp-2
Meta: text-xs text-slate-500 ("2 days ago • 3.4k views")
```

### 4.7 Kad TikTok

```
Container: min-w-[200px] aspect-[9/16] rounded-xl overflow-hidden
Image: object-cover opacity-80, group-hover:scale-105
Play icon: absolute center, material-symbols text-white text-4xl "play_circle"
```

### 4.8 Kad Kemas Kini

```
Container: p-4 rounded-xl bg-white border border-slate-100 shadow-sm
Image: aspect-[4/3] rounded-lg overflow-hidden
Category: text-[10px] font-black uppercase
  Aktiviti: text-primary
  Pengumuman: text-blue-500
  Kebajikan: text-green-500
Title: font-bold text-sm mt-1
Description: text-xs text-slate-500 mt-2
```

### 4.9 Footer

```
Container: mt-20 border-t bg-white pt-16 pb-8
Layout: grid grid-cols-1 md:grid-cols-4 gap-12
Sections:
  1. Logo + tagline + social icons (youtube, leaderboard, video_library, send)
  2. Pautan Pantas (Siaran Langsung, Zakat & Derma, Tempahan, Nikah)
  3. Hubungi Kami (Alamat, Telefon, E-mel, Linktree)
  4. Google Maps thumbnail with "Open in Maps" overlay
Bottom bar: border-t, flex between
  Left: © 2024 copyright text
  Right: Terma Penggunaan | Dasar Privasi
```

---

## 5. Tipografi

| Elemen | Saiz | Berat | Contoh |
|--------|------|-------|--------|
| Logo nama masjid | text-xl | font-bold | "Masjid Jamek..." |
| Nav link | text-sm | font-medium | "Utama", "Siaran Langsung" |
| Section heading | text-2xl | font-bold | "Rakaman Kuliah" |
| Donation title | text-3xl | font-bold | "Tabung Pembangunan" |
| Card title | text-sm | font-bold | "Kuliah Bulanan..." |
| Card meta | text-xs | default | "2 days ago" |
| Category label | text-[10px] | font-black uppercase | "AKTIVITI" |
| Copyright | text-[10px] | font-medium uppercase tracking-widest | "© 2024..." |
| Butang utama | text-sm | font-bold | "Derma" |

---

## 6. Ikon (Material Symbols Outlined)

| Kegunaan | Nama Ikon |
|----------|-----------|
| Logo masjid | `mosque` |
| Menu (mobile) | `menu` |
| Derma/hati | `favorite` |
| Live stream | `videocam` |
| Derma (quick link) | `volunteer_activism` |
| Waktu solat | `schedule` |
| Kalendar | `calendar_month` |
| Media sosial | `share` |
| Rakaman kuliah | `history` |
| Video TikTok | `video_library` |
| Kemas kini | `feed` |
| Solat aktif | `notifications_active` |
| Play | `play_arrow` / `play_circle` |
| Arrow forward | `arrow_forward` |
| Peta | `map` |
| Lokasi | `location_on` |
| Telefon | `phone` |
| E-mel | `mail` |
| Pautan | `link` |
| YouTube | `youtube_activity` |
| Social board | `social_leaderboard` |
| Send (Telegram) | `send` |

---

## 7. Warna Tema

### 7.1 Mode Terang (Default)

| Elemen | Warna |
|--------|-------|
| Background halaman | `#f6f8f6` (background-light) |
| Background kad | `white` |
| Teks utama | `slate-900` |
| Teks sekunder | `slate-500` |
| Primary (hijau) | `#13ec49` |
| Border | `slate-100` / `primary/10` |
| Badge LIVE | `red-600` |
| Badge Aktiviti | `primary` (hijau) |
| Badge Pengumuman | `blue-500` |
| Badge Kebajikan | `green-500` |

### 7.2 Mode Gelap (P2)

| Elemen | Warna |
|--------|-------|
| Background halaman | `#102215` (background-dark) |
| Background kad | `slate-800` |
| Teks utama | `slate-100` |
| Teks sekunder | `slate-400` |
| Border | `slate-700` |
| Primary | `#13ec49` (sama) |

---

## 8. Animasi & Interaksi

| Elemen | Kesan |
|--------|-------|
| Badge LIVE | `animate-pulse` |
| Play button hover | `scale-110 transition-transform` |
| Quick link icon hover | `scale-110 transition-transform` |
| Video thumbnail hover | `scale-105 transition-transform` |
| Nav link hover | `text-primary transition-colors` |
| Butang Derma CTA hover | `scale-105 transition-transform` |
| Kad hover | `shadow-md transition-shadow` |

---

## 9. Halaman Admin (Spesifikasi Ringkas)

Admin panel menggunakan layout yang berbeza daripada halaman awam:

```
┌──────────────────────────────────────────┐
│  TOPBAR                                   │
│  [Hamburger] [Nama Masjid] [Admin ▼]     │
├──────┬───────────────────────────────────┤
│      │                                    │
│  S   │  MAIN CONTENT                     │
│  I   │                                    │
│  D   │  Bergantung pada halaman:         │
│  E   │  - Dashboard: Stat cards + table   │
│  B   │  - CRUD: Form / DataTable          │
│  A   │  - Kandungan: Toggle switches      │
│  R   │                                    │
│      │                                    │
├──────┴───────────────────────────────────┤
│  Footer minimal                           │
└──────────────────────────────────────────┘
```

### Sidebar Items
1. Dashboard (`dashboard`)
2. Pengumuman (`campaign`)
3. Aktiviti (`event`)
4. Tabung Derma (`volunteer_activism`)
5. Kandungan Auto (`sync`)
6. Muat Naik Fail (`upload_file`)
7. WhatsApp (`chat`)
8. Log Aktiviti (`history`)
9. Tetapan (`settings`)
10. Log Keluar (`logout`)

### Style Admin
- Background: `slate-50` (terang sahaja, tiada dark mode untuk admin)
- Sidebar: `white` with primary accent for active item
- Kad statistik: Rounded, shadow-sm, icon + number + label
- Jadual: Ringkas, striped rows, sortable headers
- Borang: Tailwind Forms plugin, label di atas input, error messages in red

---

*Dokumen ini hendaklah dibaca bersama dengan wireframe di `wireframes/mainpage.html`.*
