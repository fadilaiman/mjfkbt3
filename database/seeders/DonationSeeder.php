<?php

namespace Database\Seeders;

use App\Models\Donation;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        Donation::updateOrCreate(
            ['name' => 'Tabung Pembangunan Masjid'],
            [
                'description' => 'Memperluas pusat komuniti dan sayap pendidikan',
                'target_amount' => 100000.00,
                'collected_amount' => 32450.00,
                'contributor_count' => 1245,
                'whatsapp_number' => '60123412459',
                'is_active' => true,
            ]
        );
    }
}
