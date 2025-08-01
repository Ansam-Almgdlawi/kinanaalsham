<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // إنشاء أو تحديث حساب الأدمن
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin12345'),
                'role_id' => 1,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // إنشاء أو تحديث حساب Project Manager
        User::updateOrCreate(
            ['email' => 'pm@gmail.com'],
            [
                'name' => 'Project Manager',
                'password' => Hash::make('pm12345'),
                'role_id' => 2,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }

}
