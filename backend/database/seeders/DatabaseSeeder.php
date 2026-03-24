<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'email' => 'admin@metagoon.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'gender' => 'vīrietis',
                'birth_date' => '1990-01-01',
                'company_name' => null,
                'company_number' => null,
                'company_address' => null,
            ]
        );
    }
}
