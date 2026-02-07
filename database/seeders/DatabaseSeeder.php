<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Starting database seeding...');

        // Step 1: Create all permissions
        $this->command->info('📝 Creating permissions...');
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
        $this->command->info('✅ Created ' . count($permissions) . ' permissions');

        // Step 2: Create roles
        $this->command->info('👥 Creating roles...');
        
        // Admin Role - All permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());
        $this->command->info('✅ Admin role created with all permissions');

        // Manager Role - Limited permissions
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
            'view dashboard',
            'view analytics',
            'view reports',
            'export reports',
        ]);
        $this->command->info('✅ Manager role created with limited permissions');

        // User Role - Basic permissions
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'web']
        );
        $userRole->syncPermissions([
            'view dashboard',
            'view reports',
        ]);
        $this->command->info('✅ User role created with basic permissions');

        // Step 3: Create admin user
        $this->command->info('👤 Creating admin user...');
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sustainx.com'],
            [
                'name' => 'Admin User',
                'password' => '$2y$12$20QNal.hdi8wNNKdHAmxpeYBY1yUe6BITp.Jm7vY5zkEs5GpgZm4K',
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $this->command->info('✅ Admin user created successfully');
        $this->command->line('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->line('');
        $this->command->info('📊 Summary:');
        $this->command->info('   • Permissions: ' . Permission::count());
        $this->command->info('   • Roles: ' . Role::count());
        $this->command->info('   • Users: ' . User::count());
        $this->command->line('');
        $this->command->info('🔑 Admin Login Credentials:');
        $this->command->info('   Email: admin@sustainx.com');
        $this->command->info('   Password: [Your provided password]');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
