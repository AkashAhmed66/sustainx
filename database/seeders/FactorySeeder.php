<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\FactoryType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Creating Factories and connecting users...');

        // Get factory types and countries
        $textileType = FactoryType::where('name', 'Textile Manufacturing')->first();
        $garmentType = FactoryType::where('name', 'Garment Manufacturing')->first();
        $dyeingType = FactoryType::where('name', 'Dyeing & Printing')->first();
        $knittingType = FactoryType::where('name', 'Knitting')->first();
        $weavingType = FactoryType::where('name', 'Weaving')->first();

        $bangladesh = Country::where('iso_code', 'BGD')->first();
        $india = Country::where('iso_code', 'IND')->first();
        $china = Country::where('iso_code', 'CHN')->first();
        $vietnam = Country::where('iso_code', 'VNM')->first();
        $pakistan = Country::where('iso_code', 'PAK')->first();

        // Get users (excluding admin)
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['user', 'manager']);
        })->get();

        // Define factories
        $factoriesData = [
            [
                'name' => 'SustainTex Dhaka',
                'factory_type_id' => $textileType->id,
                'country_id' => $bangladesh->id,
                'address' => 'House 45, Road 12, Tejgaon Industrial Area, Dhaka-1208, Bangladesh',
                'users' => [$users[0]->id, $users[1]->id], // Sarah and Michael
            ],
            [
                'name' => 'GreenGarments Mumbai',
                'factory_type_id' => $garmentType->id,
                'country_id' => $india->id,
                'address' => 'Plot 23, MIDC Industrial Estate, Andheri East, Mumbai-400093, India',
                'users' => [$users[2]->id], // Fatima
            ],
            [
                'name' => 'EcoTextile Shanghai',
                'factory_type_id' => $textileType->id,
                'country_id' => $china->id,
                'address' => 'No. 789, Huqingping Highway, Pudong District, Shanghai-201206, China',
                'users' => [$users[0]->id], // Sarah
            ],
            [
                'name' => 'Eco Dyeing & Printing Chittagong',
                'factory_type_id' => $dyeingType->id,
                'country_id' => $bangladesh->id,
                'address' => 'Bay Industrial Park, Patenga, Chittagong-4200, Bangladesh',
                'users' => [$users[1]->id, $users[3]->id], // Michael and Raj
            ],
            [
                'name' => 'Prime Knitting Hanoi',
                'factory_type_id' => $knittingType->id,
                'country_id' => $vietnam->id,
                'address' => 'Lot A2, Thang Long Industrial Park, Dong Anh District, Hanoi, Vietnam',
                'users' => [$users[2]->id], // Fatima
            ],
            [
                'name' => 'Royal Weaving Karachi',
                'factory_type_id' => $weavingType->id,
                'country_id' => $pakistan->id,
                'address' => 'S.I.T.E Industrial Area, Main Manghopir Road, Karachi-75700, Pakistan',
                'users' => [$users[3]->id], // Raj
            ],
            [
                'name' => 'Ethical Textiles Bangalore',
                'factory_type_id' => $textileType->id,
                'country_id' => $india->id,
                'address' => 'No. 156, Bommasandra Industrial Area, Bangalore-560099, India',
                'users' => [$users[0]->id, $users[2]->id], // Sarah and Fatima
            ],
            [
                'name' => 'Sustainable Garments Dhaka',
                'factory_type_id' => $garmentType->id,
                'country_id' => $bangladesh->id,
                'address' => 'Sector 7, Uttara EPZ, Dhaka-1230, Bangladesh',
                'users' => [$users[1]->id], // Michael
            ],
        ];

        $factoryModels = [];
        foreach ($factoriesData as $factoryData) {
            $userIds = $factoryData['users'];
            unset($factoryData['users']);
            
            $factory = Factory::create(array_merge($factoryData, ['is_active' => true]));
            
            // Connect users to factory
            foreach ($userIds as $userId) {
                $factory->users()->attach($userId, [
                    'role' => 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $factoryModels[] = $factory;
        }

        $this->command->info('   ✅ Created ' . count($factoryModels) . ' factories');
        $this->command->info('   ✅ Connected factories to users');
        $this->command->info('✅ Factory seeding completed!');
    }
}
