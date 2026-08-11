<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'justin.leoni@gmail.com'],
            [
                'name' => 'Justin Leoni',
                'email' => 'justin.leoni@gmail.com',
                'password' => Hash::make('P@$$w0rd123!'),
                'security_group' => 9, // super-admin
                'force_update' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}