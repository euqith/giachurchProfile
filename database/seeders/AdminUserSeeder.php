<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'chris@mail.com'],
            [
                'name'     => 'Chris',
                'password' => Hash::make('12345678'),
                'role'     => 'admin', // Sesuaikan jika ada kolom role di tabel users
            ]
        );
    }
}