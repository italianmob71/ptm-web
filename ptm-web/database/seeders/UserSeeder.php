<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
        [
            'name' => 'Justin Leoni',
            'email' => 'justin.leoni@gmail.com',
            'password' => Hash::make('P@$$w0rd123!'),
            'security_group' => 9,
            'force_update' => true,
            'email_verified_at' => now(),
        ],
        [
            'name' => 'Janice Baca',
            'email' => 'JaniceFBaca@proton.me',
            'password' => Hash::make('P@$$w0rd123!'),
            'security_group' => 5,
            'force_update' => true,
            'email_verified_at' => now(),
        ],
        [
            'name' => 'Wendy Heineck Leoni',
            'email' => 'wcheineck@gmail.com',
            'password' => Hash::make('P@$$w0rd123!'),
            'security_group' => 5,
            'force_update' => true,
            'email_verified_at' => now(),
        ],
        [
            'name' => 'Bryan Williams',
            'email' => 'bryan@projecttruthministries.org',
            'password' => Hash::make('P@$$w0rd123!'),
            'security_group' => 5,
            'force_update' => true,
            'email_verified_at' => now(),
        ],
        [
            'name' => 'Stephen Moran',
            'email' => 'contact@stephenmoran.com',
            'password' => Hash::make('P@$$w0rd123!'),
            'security_group' => 5,
            'force_update' => true,
            'email_verified_at' => now(),
        ]
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
