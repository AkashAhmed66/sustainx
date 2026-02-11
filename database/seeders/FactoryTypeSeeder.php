<?php

namespace Database\Seeders;

use App\Models\FactoryType;
use Illuminate\Database\Seeder;

class FactoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏭 Creating Factory Types...');

        $factoryTypes = [
            ['name' => 'Textile Manufacturing'],
            ['name' => 'Garment Manufacturing'],
            ['name' => 'Dyeing & Printing'],
            ['name' => 'Knitting'],
            ['name' => 'Weaving'],
            ['name' => 'Spinning'],
            ['name' => 'Embroidery'],
            ['name' => 'Packaging'],
            ['name' => 'Washing & Finishing'],
            ['name' => 'Integrated Textile Mill'],
        ];

        foreach ($factoryTypes as $type) {
            FactoryType::firstOrCreate($type);
        }

        $this->command->info('   ✅ Created ' . count($factoryTypes) . ' factory types');
        $this->command->info('✅ Factory Types seeding completed!');
    }
}
