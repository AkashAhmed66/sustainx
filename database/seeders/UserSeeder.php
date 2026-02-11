<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Creating Users...');

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sustainx.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
        $this->command->info('   ✅ Admin user created (admin@sustainx.com)');

        // Create Manager Users
        $managers = [
            [
                'name' => 'John Manager',
                'email' => 'manager@sustainx.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($managers as $managerData) {
            $manager = User::firstOrCreate(
                ['email' => $managerData['email']],
                $managerData
            );
            
            if (!$manager->hasRole('manager')) {
                $manager->assignRole('manager');
            }
        }
        $this->command->info('   ✅ ' . count($managers) . ' manager user(s) created');

        // Create Regular Users
        $users = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@sustainx.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@sustainx.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fatima Ahmed',
                'email' => 'fatima@sustainx.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Raj Patel',
                'email' => 'raj@sustainx.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }
        }
        $this->command->info('   ✅ ' . count($users) . ' regular user(s) created');

        $totalUsers = 1 + count($managers) + count($users);
        $this->command->info('✅ Users seeding completed! Total users: ' . $totalUsers);
        $this->command->line('');
        $this->command->info('🔑 Login Credentials (Password: 12345678):');
        $this->command->info('   • Admin: admin@sustainx.com');
        $this->command->info('   • Manager: manager@sustainx.com');
        $this->command->info('   • Users: sarah@sustainx.com, michael@sustainx.com, etc.');
    }
}
