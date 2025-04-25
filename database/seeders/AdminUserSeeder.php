<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@proofit.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'super@proofit.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superAdmin'),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );
    }
}
