<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Creating Countries...');

        $countries = [
            ['name' => 'Bangladesh', 'iso_code' => 'BGD'],
            ['name' => 'India', 'iso_code' => 'IND'],
            ['name' => 'China', 'iso_code' => 'CHN'],
            ['name' => 'Vietnam', 'iso_code' => 'VNM'],
            ['name' => 'Pakistan', 'iso_code' => 'PAK'],
            ['name' => 'Indonesia', 'iso_code' => 'IDN'],
            ['name' => 'Turkey', 'iso_code' => 'TUR'],
            ['name' => 'United States', 'iso_code' => 'USA'],
            ['name' => 'United Kingdom', 'iso_code' => 'GBR'],
            ['name' => 'Germany', 'iso_code' => 'DEU'],
            ['name' => 'France', 'iso_code' => 'FRA'],
            ['name' => 'Japan', 'iso_code' => 'JPN'],
            ['name' => 'South Korea', 'iso_code' => 'KOR'],
            ['name' => 'Italy', 'iso_code' => 'ITA'],
            ['name' => 'Spain', 'iso_code' => 'ESP'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['iso_code' => $country['iso_code']], $country);
        }

        $this->command->info('   ✅ Created ' . count($countries) . ' countries');
        $this->command->info('✅ Countries seeding completed!');
    }
}
