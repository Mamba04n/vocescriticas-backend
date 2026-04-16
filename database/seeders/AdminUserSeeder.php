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
            ['carnet' => 'ADMIN-0000'],
            [
                'name' => 'Superadmin',
                'carnet' => 'ADMIN-0000',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin1234'),
                'is_admin' => true,
            ]
        );
    }
}
