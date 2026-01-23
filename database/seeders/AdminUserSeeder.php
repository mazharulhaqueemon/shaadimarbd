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
            ['email' => 'admin@example.com'], // ensure idempotency
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // 🔑 change in production!
                'account_created_by' => 'system',
                'is_admin' => true,
            ]
        );
    }
}
