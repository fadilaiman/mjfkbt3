<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'admin@mjfkbt3.my'],
            [
                'name' => 'Admin MJFKBT3',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
    }
}
