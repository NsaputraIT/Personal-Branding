<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('test123'),
                'email_verified_at' => now(),
            ],
        );
    }
}
