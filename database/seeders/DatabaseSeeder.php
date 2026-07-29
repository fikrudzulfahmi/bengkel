<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@smekisa.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        User::create([
            'name' => 'Mekanik Handal',
            'email' => 'mekanik@smekisa.com',
            'password' => Hash::make('password'),
            'role' => 'mekanik',
        ]);

        User::create([
            'name' => 'Bendahara Utama',
            'email' => 'bendahara@smekisa.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);
    }
}
