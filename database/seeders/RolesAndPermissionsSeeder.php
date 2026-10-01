<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create default roles
        $superAdmin = \App\Models\Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['is_system' => true]
        );

        $userRole = \App\Models\Role::firstOrCreate(
            ['name' => 'User'],
            ['is_system' => true]
        );

        // Assign super admin role to the first user
        $user = \App\Models\User::first();
        if ($user && !$user->hasRole('Super Admin')) {
            $user->assignRole($superAdmin);
        }
    }
}
