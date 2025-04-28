<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::findByName('admin');
        $superAdminRole = Role::findByName('super_admin');

        // Create admin user and assign role
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@proofit.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin'),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create super admin user and assign role
        $superAdminUser = User::updateOrCreate(
            ['email' => 'super@proofit.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superAdmin'),
            ]
        );
        $superAdminUser->assignRole($superAdminRole);
    }
}
