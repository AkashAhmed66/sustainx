<?php

namespace Database\Seeders;

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

        $this->command->info('🔐 Creating Permissions and Roles...');

        // Create all permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // ESG Structure - Sections
            'view sections',
            'create sections',
            'edit sections',
            'delete sections',

            // ESG Structure - Subsections
            'view subsections',
            'create subsections',
            'edit subsections',
            'delete subsections',

            // ESG Structure - Items
            'view items',
            'create items',
            'edit items',
            'delete items',

            // ESG Structure - Questions
            'view questions',
            'create questions',
            'edit questions',
            'delete questions',

            // Factory Management - Countries
            'view countries',
            'create countries',
            'edit countries',
            'delete countries',

            // Factory Management - Factory Types
            'view factory-types',
            'create factory-types',
            'edit factory-types',
            'delete factory-types',

            // Factory Management - Factories
            'view factories',
            'create factories',
            'edit factories',
            'delete factories',

            // Assessment Management
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',
            'approve assessments',
            'perform assessments',

            // Dashboard & Reports
            'view dashboard',
            'view analytics',
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
        }
        $this->command->info('   ✅ Created ' . count($permissions) . ' permissions');

        // Create Admin Role - All permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());
        $this->command->info('   ✅ Admin role created with all permissions');

        // Create Manager Role - Limited permissions
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['guard_name' => 'web']
        );
        $managerRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view permissions',
            'view assessments',
            'create assessments',
            'edit assessments',
            'approve assessments',
            'view dashboard',
            'view analytics',
            'view reports',
            'export reports',
        ]);
        $this->command->info('   ✅ Manager role created with limited permissions');

        // Create User Role - Basic permissions
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'web']
        );
        $userRole->syncPermissions([
            'view assessments',
            'create assessments',
            'perform assessments',
            'view dashboard',
            'view reports',
        ]);
        $this->command->info('   ✅ User role created with basic permissions');

        $this->command->info('✅ Roles and Permissions seeding completed!');
    }
}
