<?php

return [
    'upload' => [
        'max_size_mb' => (int) env('MAX_UPLOAD_SIZE_MB', 10),
        'allowed_types' => explode(',', env('ALLOWED_UPLOAD_TYPES', 'pdf,jpg,jpeg,png,webp')),
    ],

    'jakim' => [
        'zone' => env('JAKIM_ZONE', 'SGR01'),
        'api_url' => 'https://www.e-solat.gov.my/index.php',
    ],

    // YouTube: no API key needed — uses RSS feed + public page scraping
    'youtube' => [
        'channel_handle' => env('YOUTUBE_CHANNEL_HANDLE', 'masjidjamekfastabiqulkhairat'),
        'channel_id' => env('YOUTUBE_CHANNEL_ID'), // auto-resolved if blank
    ],

    // Facebook: embeds always work without auth.
    // Set FACEBOOK_APP_ID + FACEBOOK_APP_SECRET to enable auto-sync via Graph API.
    // App Token (app_id|app_secret) can read any public Page — no page owner login needed.
    'facebook' => [
        'page_url'   => env('FACEBOOK_PAGE_URL', 'https://www.facebook.com/mjfkbt3'),
        'page_slug'  => env('FACEBOOK_PAGE_SLUG', 'mjfkbt3'),
        'app_id'     => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    // TikTok: Playwright headless browser scrapes the profile page
    'tiktok' => [
        'profile_url' => env('TIKTOK_PROFILE_URL', 'https://www.tiktok.com/@mjfk.batu03'),
    ],

    'admin' => [
        'max_login_attempts' => (int) env('ADMIN_LOGIN_MAX_ATTEMPTS', 5),
        'login_decay_minutes' => (int) env('ADMIN_LOGIN_DECAY_MINUTES', 15),
    ],

    'mosque' => [
        'name' => 'Masjid Jamek Fastabiqul Khayrat Batu 3',
        'short_name' => 'MJFKBT3',
        'address' => 'Persiaran Jubli Perak Batu 3, Shah Alam, Petaling Jaya, Malaysia',
        'phone' => '011-3734 7675',
        'email' => 'mjfkbt3@gmail.com',
        'linktree' => 'https://linktr.ee/mjfkbt3',
        'google_maps_url' => 'https://maps.app.goo.gl/QMDfe4jAAAgUZEp37',
        'google_maps_embed' => 'https://maps.google.com/maps?q=3.059286,101.557224&z=17&output=embed',
        'social' => [
            'youtube' => 'https://youtube.com/@masjidjamekfastabiqulkhairat',
            'facebook' => 'https://www.facebook.com/mjfkbt3/',
            'tiktok' => 'https://www.tiktok.com/@mjfk.batu03',
            'instagram' => 'https://www.instagram.com/mjfk.bt3',
        ],
        'whatsapp' => [
            'infaq' => [
                'name' => 'Bendahari',
                'number' => '60123412459',
                'message' => 'Saya Nak Infaq',
            ],
            'am' => [
                'name' => 'Imam Ashroff (Pegawai Tadbir)',
                'number' => '60182757817',
            ],
            'pendidikan' => [
                'name' => 'Imam Asim',
                'qr_id' => 'BOUHAIA55UUHP1',
            ],
        ],
    ],
];
