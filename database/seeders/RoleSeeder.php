<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'admin',
            'consultant', 
            'applicant'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $this->command->info('Roles created successfully!');

        // Optional: Create some permissions if needed
        $permissions = [
            'view applications',
            'create applications',
            'edit applications',
            'delete applications',
            'approve applications',
            'conduct inspections',
            'view all applications',
            'manage users',
            'manage consultants',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Optional: Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $consultantRole = Role::findByName('consultant');
        $consultantRole->givePermissionTo([
            'view applications',
            'edit applications',
            'approve applications',
            'conduct inspections',
        ]);

        $applicantRole = Role::findByName('applicant');
        $applicantRole->givePermissionTo([
            'view applications',
            'create applications',
        ]);

        $this->command->info('Permissions assigned successfully!');
    }
}