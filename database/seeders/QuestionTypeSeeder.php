<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('❓ Creating Question Types...');

        $questionTypes = [
            ['id' => 1, 'name' => 'numeric'],
            ['id' => 2, 'name' => 'mcq'],
            ['id' => 3, 'name' => 'multiple_select'],
        ];

        foreach ($questionTypes as $type) {
            QuestionType::updateOrCreate(['id' => $type['id']], $type);
        }

        $this->command->info('   ✅ Created ' . count($questionTypes) . ' question types');
        $this->command->info('✅ Question Types seeding completed!');
    }
}
