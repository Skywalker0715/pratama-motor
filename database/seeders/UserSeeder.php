<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    

    public function run(): void
    {
        // User admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pratama.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // User biasa
        User::create([
            'name' => 'Roy',
            'email' => 'roy@pratama.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
