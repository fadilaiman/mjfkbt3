<?php

namespace App\Services;

use App\Models\PrayerTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JakimPrayerService
{
    /**
     * Fetch monthly prayer times from JAKIM e-Solat API.
     */
    public function fetchMonthlyPrayerTimes(?string $zone = null): array
    {
        $zone = $zone ?? config('mjfkbt3.jakim.zone', 'SGR01');
        $apiUrl = config('mjfkbt3.jakim.api_url', 'https://www.e-solat.gov.my/index.php');

        try {
            $response = Http::timeout(15)->get($apiUrl, [
                'r' => 'esolatApi/takwimsolat',
                'period' => 'month',
                'zone' => $zone,
            ]);

            if ($response->failed()) {
                Log::error('JAKIM API request failed', [
                    'status' => $response->status(),
                    'zone' => $zone,
                ]);
                return [];
            }

            $data = $response->json();

            if (!isset($data['prayerTime']) || !is_array($data['prayerTime'])) {
                Log::error('JAKIM API response missing prayerTime data', [
                    'zone' => $zone,
                    'status' => $data['status'] ?? 'unknown',
                ]);
                return [];
            }

            return $data['prayerTime'];
        } catch (\Exception $e) {
            Log::error('JAKIM API call failed', [
                'zone' => $zone,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Sync prayer times to database via upsert.
     */
    public function syncToDatabase(array $prayerTimes): int
    {
        $count = 0;
        $zone = config('mjfkbt3.jakim.zone', 'SGR01');

        foreach ($prayerTimes as $prayer) {
            try {
                // API returns date as "01-Mar-2026" format
                $date = Carbon::createFromFormat('d-M-Y', $prayer['date'])->format('Y-m-d');

                // API field names: fajr→subuh, dhuhr→zohor, asr→asar, isha→isyak
                // Times include seconds (e.g. "06:17:00") — substr to H:i
                PrayerTime::updateOrCreate(
                    [
                        'date' => $date,
                        'zone_code' => $zone,
                    ],
                    [
                        'subuh' => substr($prayer['fajr'], 0, 5),
                        'syuruk' => substr($prayer['syuruk'], 0, 5),
                        'zohor' => substr($prayer['dhuhr'], 0, 5),
                        'asar' => substr($prayer['asr'], 0, 5),
                        'maghrib' => substr($prayer['maghrib'], 0, 5),
                        'isyak' => substr($prayer['isha'], 0, 5),
                        'hijri_date' => $prayer['hijri'] ?? null,
                        'fetched_at' => now(),
                    ]
                );

                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to sync prayer time record', [
                    'date' => $prayer['date'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Prayer times synced to database', ['count' => $count]);

        return $count;
    }

    /**
     * Get today's prayer times from DB.
     */
    public function getTodayPrayerTimes(?string $zone = null): ?PrayerTime
    {
        $zone = $zone ?? config('mjfkbt3.jakim.zone', 'SGR01');
        $today = Carbon::now('Asia/Kuala_Lumpur')->format('Y-m-d');

        return PrayerTime::where('date', $today)
            ->where('zone_code', $zone)
            ->first();
    }

    /**
     * Determine which prayer is currently active or upcoming.
     *
     * Returns array with name, time, status (upcoming|active), and minutes_until.
     */
    public function getActivePrayer(): ?array
    {
        $prayerTime = $this->getTodayPrayerTimes();

        if (!$prayerTime) {
            return null;
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $today = $now->format('Y-m-d');

        $prayers = [
            'subuh' => $prayerTime->subuh,
            'syuruk' => $prayerTime->syuruk,
            'zohor' => $prayerTime->zohor,
            'asar' => $prayerTime->asar,
            'maghrib' => $prayerTime->maghrib,
            'isyak' => $prayerTime->isyak,
        ];

        $prayerNames = array_keys($prayers);
        $activePrayer = null;

        for ($i = count($prayerNames) - 1; $i >= 0; $i--) {
            $name = $prayerNames[$i];
            $timeString = $prayers[$name];
            $prayerCarbon = Carbon::parse($today . ' ' . $timeString, 'Asia/Kuala_Lumpur');

            if ($now->gte($prayerCarbon)) {
                // Current time is past this prayer — this prayer is active
                // Next prayer is upcoming
                $nextIndex = $i + 1;

                if ($nextIndex < count($prayerNames)) {
                    $nextName = $prayerNames[$nextIndex];
                    $nextTimeString = $prayers[$nextName];
                    $nextPrayerCarbon = Carbon::parse($today . ' ' . $nextTimeString, 'Asia/Kuala_Lumpur');
                    $minutesUntil = (int) $now->diffInMinutes($nextPrayerCarbon, false);

                    $activePrayer = [
                        'name' => $nextName,
                        'time' => $nextTimeString,
                        'status' => 'upcoming',
                        'minutes_until' => max(0, $minutesUntil),
                    ];
                } else {
                    // After Isyak — Isyak is the active prayer
                    $activePrayer = [
                        'name' => 'isyak',
                        'time' => $prayers['isyak'],
                        'status' => 'active',
                        'minutes_until' => 0,
                    ];
                }

                break;
            }
        }

        // If no prayer has passed yet (before Subuh), Subuh is upcoming
        if ($activePrayer === null) {
            $subuhCarbon = Carbon::parse($today . ' ' . $prayers['subuh'], 'Asia/Kuala_Lumpur');
            $minutesUntil = (int) $now->diffInMinutes($subuhCarbon, false);

            $activePrayer = [
                'name' => 'subuh',
                'time' => $prayers['subuh'],
                'status' => 'upcoming',
                'minutes_until' => max(0, $minutesUntil),
            ];
        }

        return $activePrayer;
    }
}
