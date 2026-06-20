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

        User::factory()->create([
            'username' => 'admin',
            'name' => 'admin',
            'nip' => '123',
            'email' => 'admin@petaru.com',
            'is_admin' => true,
            'is_active' => true,
            'phone' => '081275888421',
            'title' => '',
            'password' => Hash::make('admin123'),
        ]);

         User::factory()->create([
            'username' => 'abdi',
            'name' => 'abdi maulana',
            'nip' => '1234',
            'email' => 'abdi@petaru.com',
            'is_admin' => true,
            'is_active' => true,
            'phone' => '',
            'title' => '',
            'password' => Hash::make('admin123'),
        ]);
    }
}
