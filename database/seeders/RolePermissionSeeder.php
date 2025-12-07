<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $permissions = [
            // Video permissions
            'manage videos',
            'view videos',
            'create videos',
            'edit videos',
            'delete videos',

            // Access request permissions
            'manage access requests',
            'view access requests',
            'approve access requests',
            'reject access requests',

            // Customer permissions
            'request video access',
            'watch videos',

            // Customer management permissions
            'manage customers',
            'create customers',
            'edit customers',
            'delete customers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to admin role
        $adminRole->givePermissionTo([
            'manage videos',
            'view videos',
            'create videos',
            'edit videos',
            'delete videos',
            'manage access requests',
            'view access requests',
            'approve access requests',
            'reject access requests',
            'manage customers',
            'create customers',
            'edit customers',
            'delete customers',
        ]);

        // Assign permissions to customer role
        $customerRole->givePermissionTo([
            'view videos',
            'request video access',
            'watch videos',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
