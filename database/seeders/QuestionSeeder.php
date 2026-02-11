<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Option;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('❔ Creating Questions and Options...');

        $numericType = QuestionType::where('name', 'numeric')->first();
        $mcqType = QuestionType::where('name', 'mcq')->first();
        $multipleSelectType = QuestionType::where('name', 'multiple_select')->first();

        // Define questions for each item
        $questionsData = [
            // ENVIRONMENTAL - Energy & Emissions
            'Total Energy Consumption' => [
                ['text' => 'What is your total annual energy consumption?', 'type' => $numericType->id, 'input_unit' => 'MWh', 'output_unit' => 'MWh', 'required' => true],
                ['text' => 'What are your primary energy sources?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Grid Electricity', 'value' => 5],
                    ['text' => 'Natural Gas', 'value' => 4],
                    ['text' => 'Solar Power', 'value' => 3],
                    ['text' => 'Wind Power', 'value' => 2],
                    ['text' => 'Diesel Generator', 'value' => 1],
                    ['text' => 'Biogas', 'value' => 2],
                ]],
            ],
            'Renewable Energy Usage' => [
                ['text' => 'What percentage of your energy comes from renewable sources?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
                ['text' => 'Do you have renewable energy generation facilities on-site?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, operational', 'value' => 10],
                    ['text' => 'Yes, under construction', 'value' => 7],
                    ['text' => 'Planned for future', 'value' => 5],
                    ['text' => 'No plans', 'value' => 0],
                ]],
            ],
            'GHG Emissions' => [
                ['text' => 'What are your total annual GHG emissions (Scope 1 & 2)?', 'type' => $numericType->id, 'input_unit' => 'tCO2e', 'output_unit' => 'tCO2e', 'required' => true],
                ['text' => 'Do you track Scope 3 emissions?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Yes, comprehensively', 'value' => 10],
                    ['text' => 'Yes, partially', 'value' => 6],
                    ['text' => 'No, but planning to', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Energy Efficiency' => [
                ['text' => 'What is your energy intensity (MWh per unit of production)?', 'type' => $numericType->id, 'input_unit' => 'MWh/unit', 'output_unit' => 'MWh/unit', 'required' => false],
                ['text' => 'Which energy efficiency measures have you implemented?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'LED Lighting', 'value' => 3],
                    ['text' => 'Energy-efficient motors', 'value' => 4],
                    ['text' => 'Heat recovery systems', 'value' => 5],
                    ['text' => 'Building insulation', 'value' => 3],
                    ['text' => 'Smart energy management system', 'value' => 5],
                ]],
            ],

            // ENVIRONMENTAL - Water Management
            'Water Consumption' => [
                ['text' => 'What is your total annual water consumption?', 'type' => $numericType->id, 'input_unit' => 'm³', 'output_unit' => 'm³', 'required' => true],
                ['text' => 'What are your primary water sources?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Municipal water supply', 'value' => 3],
                    ['text' => 'Groundwater', 'value' => 2],
                    ['text' => 'Surface water (river/lake)', 'value' => 2],
                    ['text' => 'Rainwater harvesting', 'value' => 5],
                    ['text' => 'Recycled water', 'value' => 5],
                ]],
            ],
            'Wastewater Treatment' => [
                ['text' => 'Do you have an on-site wastewater treatment facility?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, with advanced treatment (ETP)', 'value' => 10],
                    ['text' => 'Yes, basic treatment', 'value' => 6],
                    ['text' => 'No, discharge to municipal system', 'value' => 3],
                    ['text' => 'No treatment', 'value' => 0],
                ]],
                ['text' => 'What percentage of wastewater is treated before discharge?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
            ],
            'Water Recycling' => [
                ['text' => 'What percentage of water is recycled and reused?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
                ['text' => 'Which water recycling methods do you use?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Reverse osmosis', 'value' => 5],
                    ['text' => 'Cooling tower blowdown recovery', 'value' => 4],
                    ['text' => 'Rinse water reuse', 'value' => 3],
                    ['text' => 'Rainwater collection', 'value' => 4],
                ]],
            ],
            'Water Quality Testing' => [
                ['text' => 'How frequently do you test water quality?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Daily', 'value' => 10],
                    ['text' => 'Weekly', 'value' => 8],
                    ['text' => 'Monthly', 'value' => 6],
                    ['text' => 'Quarterly', 'value' => 4],
                    ['text' => 'Annually', 'value' => 2],
                    ['text' => 'Never', 'value' => 0],
                ]],
            ],

            // ENVIRONMENTAL - Waste Management
            'Total Waste Generated' => [
                ['text' => 'What is your total annual waste generation?', 'type' => $numericType->id, 'input_unit' => 'tonnes', 'output_unit' => 'tonnes', 'required' => true],
                ['text' => 'What types of waste do you generate?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Fabric scraps', 'value' => 3],
                    ['text' => 'Plastic packaging', 'value' => 2],
                    ['text' => 'Paper/Cardboard', 'value' => 2],
                    ['text' => 'Chemical containers', 'value' => 4],
                    ['text' => 'Metal waste', 'value' => 3],
                    ['text' => 'Organic waste', 'value' => 1],
                ]],
            ],
            'Waste Recycled' => [
                ['text' => 'What percentage of total waste is recycled?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
                ['text' => 'Do you have a waste segregation system?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, comprehensive system', 'value' => 10],
                    ['text' => 'Yes, basic segregation', 'value' => 6],
                    ['text' => 'No, but planning', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Hazardous Waste' => [
                ['text' => 'What is your annual hazardous waste generation?', 'type' => $numericType->id, 'input_unit' => 'kg', 'output_unit' => 'kg', 'required' => true],
                ['text' => 'How is hazardous waste disposed of?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Licensed disposal facility', 'value' => 10],
                    ['text' => 'On-site treatment', 'value' => 8],
                    ['text' => 'Municipal waste system', 'value' => 3],
                    ['text' => 'Other', 'value' => 2],
                ]],
            ],
            'Waste Reduction Initiatives' => [
                ['text' => 'Which waste reduction initiatives have you implemented?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Fabric cutting optimization', 'value' => 5],
                    ['text' => 'Reusable packaging', 'value' => 4],
                    ['text' => 'Composting program', 'value' => 3],
                    ['text' => 'Zero waste to landfill goal', 'value' => 5],
                    ['text' => 'Employee awareness programs', 'value' => 3],
                ]],
            ],

            // ENVIRONMENTAL - Chemical Management
            'Chemical Inventory' => [
                ['text' => 'How many different chemicals do you use in production?', 'type' => $numericType->id, 'input_unit' => 'types', 'output_unit' => 'types', 'required' => true],
                ['text' => 'Do you maintain a chemical inventory management system?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, digital system', 'value' => 10],
                    ['text' => 'Yes, manual system', 'value' => 6],
                    ['text' => 'Partially maintained', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Hazardous Chemicals' => [
                ['text' => 'How many hazardous chemicals do you use?', 'type' => $numericType->id, 'input_unit' => 'types', 'output_unit' => 'types', 'required' => true],
                ['text' => 'Are all hazardous chemicals stored in designated areas?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, with proper containment', 'value' => 10],
                    ['text' => 'Yes, basic storage', 'value' => 6],
                    ['text' => 'Partially', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Chemical Safety Training' => [
                ['text' => 'What percentage of employees received chemical safety training?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
                ['text' => 'How frequently is chemical safety training conducted?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Quarterly', 'value' => 10],
                    ['text' => 'Bi-annually', 'value' => 8],
                    ['text' => 'Annually', 'value' => 6],
                    ['text' => 'During onboarding only', 'value' => 3],
                    ['text' => 'Never', 'value' => 0],
                ]],
            ],
            'ZDHC Compliance' => [
                ['text' => 'Are you ZDHC (Zero Discharge of Hazardous Chemicals) compliant?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, fully compliant', 'value' => 10],
                    ['text' => 'Working towards compliance', 'value' => 6],
                    ['text' => 'Planning to implement', 'value' => 3],
                    ['text' => 'Not applicable', 'value' => 0],
                ]],
            ],

            // SOCIAL - Labor Practices
            'Employee Count' => [
                ['text' => 'What is your total number of employees?', 'type' => $numericType->id, 'input_unit' => 'employees', 'output_unit' => 'employees', 'required' => true],
                ['text' => 'What is your employee gender distribution?', 'type' => $numericType->id, 'input_unit' => '% female', 'output_unit' => '% female', 'required' => false],
            ],
            'Fair Wages' => [
                ['text' => 'Do you pay living wages to all employees?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, above living wage', 'value' => 10],
                    ['text' => 'Yes, meets living wage', 'value' => 8],
                    ['text' => 'Above minimum wage', 'value' => 5],
                    ['text' => 'Meets minimum wage only', 'value' => 2],
                ]],
                ['text' => 'How often are wages paid?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Monthly', 'value' => 10],
                    ['text' => 'Bi-weekly', 'value' => 9],
                    ['text' => 'Weekly', 'value' => 10],
                    ['text' => 'Daily', 'value' => 7],
                ]],
            ],
            'Working Hours' => [
                ['text' => 'What is the average working hours per week (excluding overtime)?', 'type' => $numericType->id, 'input_unit' => 'hours', 'output_unit' => 'hours', 'required' => true],
                ['text' => 'What is the average overtime hours per week?', 'type' => $numericType->id, 'input_unit' => 'hours', 'output_unit' => 'hours', 'required' => false],
            ],
            'Employee Benefits' => [
                ['text' => 'Which benefits do you provide to employees?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Health insurance', 'value' => 5],
                    ['text' => 'Paid leave', 'value' => 4],
                    ['text' => 'Maternity/Paternity leave', 'value' => 5],
                    ['text' => 'Retirement benefits', 'value' => 4],
                    ['text' => 'Transportation', 'value' => 3],
                    ['text' => 'Meals/Subsidies', 'value' => 2],
                    ['text' => 'Housing', 'value' => 4],
                ]],
            ],
            'Child Labor Policy' => [
                ['text' => 'Do you have a child labor prevention policy?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, strictly enforced with age verification', 'value' => 10],
                    ['text' => 'Yes, policy exists', 'value' => 7],
                    ['text' => 'Informal policy', 'value' => 3],
                    ['text' => 'No formal policy', 'value' => 0],
                ]],
            ],

            // SOCIAL - Health & Safety
            'Safety Training' => [
                ['text' => 'What percentage of employees completed safety training?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
                ['text' => 'How frequently is safety training conducted?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Monthly', 'value' => 10],
                    ['text' => 'Quarterly', 'value' => 8],
                    ['text' => 'Bi-annually', 'value' => 6],
                    ['text' => 'Annually', 'value' => 4],
                    ['text' => 'During onboarding only', 'value' => 2],
                ]],
            ],
            'Accident Rate' => [
                ['text' => 'How many workplace accidents occurred in the last year?', 'type' => $numericType->id, 'input_unit' => 'incidents', 'output_unit' => 'incidents', 'required' => true],
                ['text' => 'How would you rate your workplace safety?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Excellent - Zero accidents', 'value' => 10],
                    ['text' => 'Good - Minor incidents only', 'value' => 7],
                    ['text' => 'Average - Some incidents', 'value' => 5],
                    ['text' => 'Poor - Multiple incidents', 'value' => 2],
                    ['text' => 'Critical - Major accidents', 'value' => 0],
                ]],
            ],
            'Safety Equipment' => [
                ['text' => 'What percentage of required PPE is available to employees?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => true],
                ['text' => 'Which safety equipment do you provide?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Safety helmets/Hard hats', 'value' => 3],
                    ['text' => 'Safety goggles', 'value' => 3],
                    ['text' => 'Gloves', 'value' => 2],
                    ['text' => 'Masks/Respirators', 'value' => 4],
                    ['text' => 'Safety boots', 'value' => 3],
                    ['text' => 'Ear protection', 'value' => 2],
                    ['text' => 'Protective clothing', 'value' => 3],
                ]],
            ],
            'Emergency Procedures' => [
                ['text' => 'Do you have documented emergency procedures?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, regularly updated and practiced', 'value' => 10],
                    ['text' => 'Yes, documented but not practiced', 'value' => 6],
                    ['text' => 'Informal procedures', 'value' => 3],
                    ['text' => 'No formal procedures', 'value' => 0],
                ]],
                ['text' => 'How many fire drills were conducted in the last year?', 'type' => $numericType->id, 'input_unit' => 'drills', 'output_unit' => 'drills', 'required' => false],
            ],
            'Health Programs' => [
                ['text' => 'Which health and wellness programs do you offer?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Annual health checkups', 'value' => 5],
                    ['text' => 'On-site medical facility', 'value' => 5],
                    ['text' => 'Mental health support', 'value' => 4],
                    ['text' => 'Wellness programs', 'value' => 3],
                    ['text' => 'Health insurance', 'value' => 5],
                ]],
            ],

            // SOCIAL - Training & Development
            'Training Hours' => [
                ['text' => 'Average annual training hours per employee?', 'type' => $numericType->id, 'input_unit' => 'hours', 'output_unit' => 'hours', 'required' => false],
                ['text' => 'What is your annual training budget percentage?', 'type' => $numericType->id, 'input_unit' => '% of payroll', 'output_unit' => '% of payroll', 'required' => false],
            ],
            'Skill Development Programs' => [
                ['text' => 'Which skill development programs do you offer?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Technical skills training', 'value' => 5],
                    ['text' => 'Soft skills training', 'value' => 4],
                    ['text' => 'Leadership development', 'value' => 5],
                    ['text' => 'Language training', 'value' => 3],
                    ['text' => 'Computer literacy', 'value' => 4],
                ]],
            ],
            'Career Advancement' => [
                ['text' => 'What percentage of positions are filled through internal promotion?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
                ['text' => 'Do you have a formal career development program?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Yes, comprehensive program', 'value' => 10],
                    ['text' => 'Yes, basic program', 'value' => 6],
                    ['text' => 'Informal mentoring', 'value' => 3],
                    ['text' => 'No formal program', 'value' => 0],
                ]],
            ],

            // SOCIAL - Diversity & Inclusion
            'Gender Diversity' => [
                ['text' => 'What percentage of leadership positions are held by women?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
                ['text' => 'Do you have gender diversity targets?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Yes, with measurable targets', 'value' => 10],
                    ['text' => 'Yes, aspirational targets', 'value' => 6],
                    ['text' => 'Under consideration', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Equal Opportunity' => [
                ['text' => 'Do you have an equal opportunity policy?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, actively implemented', 'value' => 10],
                    ['text' => 'Yes, policy exists', 'value' => 7],
                    ['text' => 'Informal practice', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Discrimination Prevention' => [
                ['text' => 'Which anti-discrimination measures do you have?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Written policy', 'value' => 5],
                    ['text' => 'Training programs', 'value' => 4],
                    ['text' => 'Complaint mechanism', 'value' => 5],
                    ['text' => 'Regular audits', 'value' => 4],
                    ['text' => 'Diversity committee', 'value' => 3],
                ]],
            ],

            // SOCIAL - Community Engagement
            'Community Programs' => [
                ['text' => 'Which community development programs do you support?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Education programs', 'value' => 5],
                    ['text' => 'Healthcare initiatives', 'value' => 5],
                    ['text' => 'Infrastructure development', 'value' => 4],
                    ['text' => 'Environmental programs', 'value' => 4],
                    ['text' => 'Skills training', 'value' => 4],
                ]],
            ],
            'Local Employment' => [
                ['text' => 'What percentage of workforce is from local community?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
            ],
            'Stakeholder Engagement' => [
                ['text' => 'How frequently do you engage with local stakeholders?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Monthly', 'value' => 10],
                    ['text' => 'Quarterly', 'value' => 8],
                    ['text' => 'Bi-annually', 'value' => 6],
                    ['text' => 'Annually', 'value' => 4],
                    ['text' => 'Rarely', 'value' => 1],
                ]],
            ],

            // GOVERNANCE - Corporate Ethics
            'Code of Conduct' => [
                ['text' => 'Do you have a formal code of conduct?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, regularly updated and communicated', 'value' => 10],
                    ['text' => 'Yes, exists but not regularly updated', 'value' => 6],
                    ['text' => 'Under development', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Anti-Corruption Policy' => [
                ['text' => 'Do you have an anti-corruption policy?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, with regular training', 'value' => 10],
                    ['text' => 'Yes, policy exists', 'value' => 7],
                    ['text' => 'Under development', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Whistleblower Mechanism' => [
                ['text' => 'Do you have a confidential whistleblower mechanism?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, anonymous and external', 'value' => 10],
                    ['text' => 'Yes, internal mechanism', 'value' => 7],
                    ['text' => 'Informal reporting', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Ethics Training' => [
                ['text' => 'What percentage of employees received ethics training?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
                ['text' => 'Which ethics topics are covered in training?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'Anti-corruption', 'value' => 5],
                    ['text' => 'Conflict of interest', 'value' => 4],
                    ['text' => 'Fair business practices', 'value' => 4],
                    ['text' => 'Data privacy', 'value' => 4],
                    ['text' => 'Harassment prevention', 'value' => 5],
                ]],
            ],

            // GOVERNANCE - Supply Chain
            'Supplier Assessment' => [
                ['text' => 'What percentage of suppliers undergo ESG assessment?', 'type' => $numericType->id, 'input_unit' => '%', 'output_unit' => '%', 'required' => false],
                ['text' => 'How frequently do you assess suppliers?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Annually', 'value' => 10],
                    ['text' => 'Every 2 years', 'value' => 7],
                    ['text' => 'Every 3 years', 'value' => 5],
                    ['text' => 'Only during onboarding', 'value' => 3],
                    ['text' => 'Never', 'value' => 0],
                ]],
            ],
            'Supply Chain Transparency' => [
                ['text' => 'How many tiers of supply chain do you track?', 'type' => $numericType->id, 'input_unit' => 'tiers', 'output_unit' => 'tiers', 'required' => false],
                ['text' => 'Do you publish your supplier list?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Yes, complete list publicly available', 'value' => 10],
                    ['text' => 'Yes, partial list', 'value' => 6],
                    ['text' => 'Available on request', 'value' => 3],
                    ['text' => 'Confidential', 'value' => 0],
                ]],
            ],
            'Supplier Code of Conduct' => [
                ['text' => 'Do you require suppliers to sign a code of conduct?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, mandatory with audits', 'value' => 10],
                    ['text' => 'Yes, mandatory', 'value' => 7],
                    ['text' => 'Recommended', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Conflict Minerals' => [
                ['text' => 'Do you have a conflict minerals policy?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Yes, with due diligence process', 'value' => 10],
                    ['text' => 'Yes, policy exists', 'value' => 6],
                    ['text' => 'Under development', 'value' => 3],
                    ['text' => 'Not applicable', 'value' => 0],
                ]],
            ],

            // GOVERNANCE - Compliance
            'Certifications' => [
                ['text' => 'Which certifications does your facility hold?', 'type' => $multipleSelectType->id, 'required' => false, 'options' => [
                    ['text' => 'ISO 9001 (Quality)', 'value' => 4],
                    ['text' => 'ISO 14001 (Environmental)', 'value' => 5],
                    ['text' => 'ISO 45001 (Safety)', 'value' => 5],
                    ['text' => 'WRAP', 'value' => 5],
                    ['text' => 'BSCI', 'value' => 5],
                    ['text' => 'Oeko-Tex', 'value' => 4],
                    ['text' => 'GOTS', 'value' => 5],
                    ['text' => 'LEED', 'value' => 4],
                ]],
            ],
            'Regulatory Compliance' => [
                ['text' => 'Have you received any regulatory violations in the past year?', 'type' => $numericType->id, 'input_unit' => 'violations', 'output_unit' => 'violations', 'required' => true],
                ['text' => 'Do you have a compliance management system?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, comprehensive system', 'value' => 10],
                    ['text' => 'Yes, basic system', 'value' => 6],
                    ['text' => 'Informal tracking', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Audit Results' => [
                ['text' => 'How many audits were conducted in the past year?', 'type' => $numericType->id, 'input_unit' => 'audits', 'output_unit' => 'audits', 'required' => false],
                ['text' => 'What was the outcome of your latest external audit?', 'type' => $mcqType->id, 'required' => false, 'options' => [
                    ['text' => 'Passed with no findings', 'value' => 10],
                    ['text' => 'Passed with minor findings', 'value' => 7],
                    ['text' => 'Conditional pass', 'value' => 4],
                    ['text' => 'Failed', 'value' => 0],
                    ['text' => 'Not audited yet', 'value' => 0],
                ]],
            ],
            'Legal Violations' => [
                ['text' => 'Have you received any legal penalties in the past year?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'No', 'value' => 10],
                    ['text' => 'Yes, minor violations', 'value' => 3],
                    ['text' => 'Yes, major violations', 'value' => 0],
                ]],
            ],

            // GOVERNANCE - Data Privacy & Security
            'Data Protection Policy' => [
                ['text' => 'Do you have a data protection policy?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, GDPR/equivalent compliant', 'value' => 10],
                    ['text' => 'Yes, basic policy', 'value' => 6],
                    ['text' => 'Under development', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
            'Cybersecurity Measures' => [
                ['text' => 'Which cybersecurity measures do you have in place?', 'type' => $multipleSelectType->id, 'required' => true, 'options' => [
                    ['text' => 'Firewall', 'value' => 3],
                    ['text' => 'Antivirus software', 'value' => 3],
                    ['text' => 'Data encryption', 'value' => 5],
                    ['text' => 'Regular backups', 'value' => 4],
                    ['text' => 'Access controls', 'value' => 4],
                    ['text' => 'Security training', 'value' => 4],
                    ['text' => 'Incident response plan', 'value' => 5],
                ]],
            ],
            'Data Breach Protocol' => [
                ['text' => 'Do you have a data breach response protocol?', 'type' => $mcqType->id, 'required' => true, 'options' => [
                    ['text' => 'Yes, tested and documented', 'value' => 10],
                    ['text' => 'Yes, documented', 'value' => 7],
                    ['text' => 'Under development', 'value' => 3],
                    ['text' => 'No', 'value' => 0],
                ]],
            ],
        ];

        $questionCount = 0;
        $optionCount = 0;

        foreach ($questionsData as $itemName => $questions) {
            $item = Item::where('name', $itemName)->first();
            
            if (!$item) {
                $this->command->warn("   ⚠️  Item not found: {$itemName}");
                continue;
            }

            foreach ($questions as $questionData) {
                $question = Question::create([
                    'item_id' => $item->id,
                    'question_text' => $questionData['text'],
                    'question_type_id' => $questionData['type'],
                    'input_unit' => $questionData['input_unit'] ?? null,
                    'output_unit' => $questionData['output_unit'] ?? null,
                    'is_required' => $questionData['required'],
                    'is_active' => true,
                ]);
                $questionCount++;

                // Create options if they exist
                if (isset($questionData['options'])) {
                    foreach ($questionData['options'] as $index => $optionData) {
                        Option::create([
                            'question_id' => $question->id,
                            'option_text' => $optionData['text'],
                            'option_value' => $optionData['value'],
                            'order_no' => $index + 1,
                        ]);
                        $optionCount++;
                    }
                }
            }
        }

        $this->command->info('   ✅ Created ' . $questionCount . ' questions');
        $this->command->info('   ✅ Created ' . $optionCount . ' options');
        $this->command->info('✅ Questions and Options seeding completed!');
    }
}
