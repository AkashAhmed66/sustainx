<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Factory;
use App\Models\Question;
use App\Models\Item;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Creating Assessments with Answers for multiple years...');

        $factories = Factory::all();
        $questions = Question::with(['questionType', 'options', 'item'])->get();

        // Define years to seed (2021-2025)
        $years = [2021, 2022, 2023, 2024, 2025];
        
        $assessmentCount = 0;
        $answerCount = 0;

        foreach ($factories as $factory) {
            // Each factory gets assessments for multiple years
            $factoryYearCount = rand(3, 5); // Each factory will have 3-5 years of data
            $selectedYears = array_slice($years, -$factoryYearCount); // Get the most recent years

            foreach ($selectedYears as $year) {
                // Create assessment with varying statuses
                $status = $year === 2025 ? 'draft' : 'approved'; // 2025 is draft, others approved
                
                $assessment = Assessment::create([
                    'factory_id' => $factory->id,
                    'year' => $year,
                    'period' => 'annual',
                    'status' => $status,
                    'submitted_at' => $status === 'approved' ? now()->subDays(rand(1, 365)) : null,
                ]);
                $assessmentCount++;

                // Create answers for all questions
                foreach ($questions as $question) {
                    $answerData = [
                        'assessment_id' => $assessment->id,
                        'question_id' => $question->id,
                        'item_id' => $question->item_id,
                    ];

                    // Generate appropriate answer based on question type
                    if ($question->question_type_id == 1) {
                        // Numeric type
                        $answerData['numeric_value'] = $this->generateNumericAnswer($question, $year, $factory);
                        $answerData['actual_answer'] = $answerData['numeric_value'];
                        $answerData['option_id'] = null;
                        $answerData['text_value'] = null;
                        $answerData['selected_options'] = null;
                    } elseif ($question->question_type_id == 2) {
                        // MCQ type
                        $option = $question->options->random();
                        $answerData['option_id'] = $option->id;
                        $answerData['numeric_value'] = null;
                        $answerData['actual_answer'] = null;
                        $answerData['text_value'] = null;
                        $answerData['selected_options'] = null;
                    } elseif ($question->question_type_id == 3) {
                        // Multiple select type
                        $selectedCount = rand(1, min(3, $question->options->count()));
                        $selectedOptions = $question->options->random($selectedCount)->pluck('id')->toArray();
                        $answerData['selected_options'] = $selectedOptions;
                        $answerData['option_id'] = null;
                        $answerData['numeric_value'] = null;
                        $answerData['actual_answer'] = null;
                        $answerData['text_value'] = null;
                    }

                    Answer::create($answerData);
                    $answerCount++;
                }

                $this->command->info("   ✅ Created assessment for {$factory->name} - Year {$year} with " . $questions->count() . " answers");
            }
        }

        $this->command->info('');
        $this->command->info('   📈 Total Assessments: ' . $assessmentCount);
        $this->command->info('   📝 Total Answers: ' . $answerCount);
        $this->command->info('✅ Assessment seeding completed!');
    }

    /**
     * Generate realistic numeric answers based on question context and year
     */
    private function generateNumericAnswer(Question $question, int $year, Factory $factory): float
    {
        $itemName = $question->item->name;
        $unit = $question->input_unit;

        // Base values that increase/decrease over years to show trends
        $baseYear = 2021;
        $yearDiff = $year - $baseYear;

        // Generate values based on item type with realistic variations
        $value = match($itemName) {
            // Energy & Emissions
            'Total Energy Consumption' => rand(800, 2000) + ($yearDiff * rand(20, 50)), // Increasing trend
            'Renewable Energy Usage' => min(100, rand(5, 30) + ($yearDiff * rand(3, 8))), // Increasing percentage
            'GHG Emissions' => rand(500, 1500) + ($yearDiff * rand(10, 30)), // Increasing trend
            'Energy Efficiency' => rand(10, 50) - ($yearDiff * rand(1, 3)), // Decreasing is better

            // Water Management
            'Water Consumption' => rand(10000, 50000) + ($yearDiff * rand(500, 1500)),
            'Wastewater Treatment' => min(100, rand(60, 90) + ($yearDiff * rand(2, 5))), // Increasing percentage
            'Water Recycling' => min(100, rand(10, 40) + ($yearDiff * rand(2, 7))), // Increasing percentage
            'Water Quality Testing' => rand(1, 10), // Number of tests

            // Waste Management
            'Total Waste Generated' => rand(200, 800) + ($yearDiff * rand(10, 30)),
            'Waste Recycled' => min(100, rand(20, 60) + ($yearDiff * rand(3, 8))), // Increasing percentage
            'Hazardous Waste' => max(0, rand(50, 300) - ($yearDiff * rand(5, 15))), // Decreasing trend
            'Waste Reduction Initiatives' => rand(1, 5),

            // Chemical Management
            'Chemical Inventory' => rand(20, 100),
            'Hazardous Chemicals' => max(5, rand(10, 50) - ($yearDiff * rand(1, 3))), // Decreasing trend
            'Chemical Safety Training' => min(100, rand(70, 95) + ($yearDiff * rand(1, 3))), // Increasing percentage
            'ZDHC Compliance' => min(100, rand(40, 80) + ($yearDiff * rand(3, 7))), // Increasing

            // Labor Practices
            'Employee Count' => rand(200, 1000) + ($yearDiff * rand(20, 50)),
            'Fair Wages' => min(100, rand(60, 85) + ($yearDiff * rand(2, 5))),
            'Working Hours' => 45 + rand(-5, 5), // Around 45 hours per week
            'Employee Benefits' => rand(50, 90),

            // Health & Safety
            'Safety Training' => min(100, rand(75, 98) + ($yearDiff * rand(1, 2))), // Increasing percentage
            'Accident Rate' => max(0, rand(5, 20) - ($yearDiff * rand(1, 3))), // Decreasing trend
            'Safety Equipment' => min(100, rand(80, 100)),
            'Emergency Procedures' => rand(2, 12), // Number of drills

            // Training & Development
            'Training Hours' => rand(10, 80) + ($yearDiff * rand(2, 10)), // Increasing trend
            'Skill Development Programs' => rand(30, 90),
            'Career Advancement' => rand(20, 60),

            // Diversity & Inclusion
            'Gender Diversity' => min(70, rand(20, 50) + ($yearDiff * rand(2, 5))), // Increasing percentage
            'Equal Opportunity' => rand(60, 95),
            'Discrimination Prevention' => rand(70, 100),

            // Community Engagement
            'Community Programs' => rand(40, 90),
            'Local Employment' => rand(40, 90), // Percentage
            'Stakeholder Engagement' => rand(1, 12), // Times per year

            // Governance - Supply Chain
            'Supplier Assessment' => min(100, rand(30, 80) + ($yearDiff * rand(3, 7))), // Increasing percentage
            'Supply Chain Transparency' => rand(1, 3), // Number of tiers tracked
            'Supplier Code of Conduct' => rand(70, 100),
            'Conflict Minerals' => rand(60, 100),

            // Governance - Compliance
            'Certifications' => rand(2, 8), // Number of certifications
            'Regulatory Compliance' => max(0, rand(0, 5) - $yearDiff), // Decreasing violations
            'Audit Results' => rand(2, 8), // Number of audits
            'Legal Violations' => max(0, rand(0, 3) - $yearDiff), // Decreasing violations

            // Governance - Ethics & Data
            'Ethics Training' => min(100, rand(70, 95) + ($yearDiff * rand(1, 3))),
            'Data Protection Policy' => rand(60, 100),
            'Cybersecurity Measures' => rand(60, 100),
            'Data Breach Protocol' => rand(70, 100),

            // Default for any other items
            default => rand(10, 100) + ($yearDiff * rand(1, 10)),
        };

        // Add some random variation (±10%) to make data more realistic
        $variation = $value * (rand(-10, 10) / 100);
        $finalValue = max(0, $value + $variation);

        // Handle percentage values
        if ($unit === '%') {
            $finalValue = min(100, max(0, $finalValue));
        }

        return round($finalValue, 2);
    }
}
