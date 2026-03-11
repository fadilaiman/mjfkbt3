<?php

namespace Database\Seeders;

use App\Models\WhatsappContact;
use Illuminate\Database\Seeder;

class WhatsappContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Bendahari',
                'role' => 'Bendahari',
                'wa_number' => '60123412459',
                'wa_qr_id' => null,
                'category' => 'kewangan',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Imam Ashroff',
                'role' => 'Pegawai Tadbir',
                'wa_number' => '60182757817',
                'wa_qr_id' => null,
                'category' => 'am',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Imam Asim',
                'role' => 'Imam Asim',
                'wa_number' => null,
                'wa_qr_id' => 'BOUHAIA55UUHP1',
                'category' => 'pendidikan',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($contacts as $contact) {
            WhatsappContact::updateOrCreate(
                ['name' => $contact['name'], 'category' => $contact['category']],
                $contact
            );
        }
    }
}
