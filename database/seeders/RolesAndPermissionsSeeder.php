<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Define roles
         $admin = Role::create(['name' => 'admin']);
         $superAdmin = Role::create(['name' => 'super_admin']);
 
         // Define permissions
         $permissions = [
             'view uploads',
             'search logs',
             'approve flagged',
             'download reports',
             'manage users',
             'reset passwords',
             'configure settings',
             'modify templates',
             'access blockchain logs',
         ];
 
         foreach ($permissions as $perm) {
             $permission = Permission::create(['name' => $perm]);
             // Assign permissions to the roles
             if (in_array($perm, ['view uploads', 'search logs', 'approve flagged', 'download reports'])) {
                 $admin->givePermissionTo($permission);
             }
             $superAdmin->givePermissionTo($permission);
         }
    }
}
