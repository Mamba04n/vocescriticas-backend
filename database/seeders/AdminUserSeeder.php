<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['carnet' => 'CLARA-0001'],
            [
                'name' => 'Clara Wilford',
                'carnet' => 'CLARA-0001',
                'email' => 'clara.wilford@vocescriticas.com',
                'password' => Hash::make(env('CLARA_SUPERUSER_PASSWORD', 'Clara2026!')),
                'role' => 'teacher',
                'is_admin' => true,
            ]
        );
    }
}
