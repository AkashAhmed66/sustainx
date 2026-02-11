<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Subsection;
use App\Models\Item;
use Illuminate\Database\Seeder;

class SectionSubsectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Creating ESG Structure (Sections, Subsections, Items)...');

        // Define all sections with their subsections and items
        $structure = [
            // ENVIRONMENTAL SECTION
            [
                'section' => [
                    'name' => 'Environmental',
                    'description' => 'Environmental sustainability metrics and indicators for reducing ecological footprint',
                    'order_no' => 1,
                ],
                'subsections' => [
                    [
                        'name' => 'Energy & Emissions',
                        'description' => 'Energy consumption, renewable energy usage, and greenhouse gas emissions tracking',
                        'order_no' => 1,
                        'items' => [
                            ['name' => 'Total Energy Consumption', 'description' => 'Total annual energy consumption from all sources', 'order_no' => 1],
                            ['name' => 'Renewable Energy Usage', 'description' => 'Percentage and amount of renewable energy used', 'order_no' => 2],
                            ['name' => 'GHG Emissions', 'description' => 'Total greenhouse gas emissions (Scope 1, 2, and 3)', 'order_no' => 3],
                            ['name' => 'Energy Efficiency', 'description' => 'Energy efficiency measures and improvements', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Water Management',
                        'description' => 'Water consumption, treatment, recycling, and conservation practices',
                        'order_no' => 2,
                        'items' => [
                            ['name' => 'Water Consumption', 'description' => 'Total annual water consumption and sources', 'order_no' => 1],
                            ['name' => 'Wastewater Treatment', 'description' => 'Wastewater treatment facilities and processes', 'order_no' => 2],
                            ['name' => 'Water Recycling', 'description' => 'Water recycling and reuse initiatives', 'order_no' => 3],
                            ['name' => 'Water Quality Testing', 'description' => 'Regular water quality monitoring and testing', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Waste Management',
                        'description' => 'Solid waste generation, recycling, and disposal practices',
                        'order_no' => 3,
                        'items' => [
                            ['name' => 'Total Waste Generated', 'description' => 'Total annual waste generation by type', 'order_no' => 1],
                            ['name' => 'Waste Recycled', 'description' => 'Percentage and amount of waste recycled', 'order_no' => 2],
                            ['name' => 'Hazardous Waste', 'description' => 'Hazardous waste management and disposal', 'order_no' => 3],
                            ['name' => 'Waste Reduction Initiatives', 'description' => 'Programs to reduce waste generation', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Chemical Management',
                        'description' => 'Chemical usage, storage, and hazardous materials handling',
                        'order_no' => 4,
                        'items' => [
                            ['name' => 'Chemical Inventory', 'description' => 'List and quantity of chemicals used', 'order_no' => 1],
                            ['name' => 'Hazardous Chemicals', 'description' => 'Management of hazardous chemicals and substances', 'order_no' => 2],
                            ['name' => 'Chemical Safety Training', 'description' => 'Employee training on chemical safety', 'order_no' => 3],
                            ['name' => 'ZDHC Compliance', 'description' => 'Zero Discharge of Hazardous Chemicals compliance', 'order_no' => 4],
                        ],
                    ],
                ],
            ],

            // SOCIAL SECTION
            [
                'section' => [
                    'name' => 'Social',
                    'description' => 'Social responsibility, employee welfare, and community engagement metrics',
                    'order_no' => 2,
                ],
                'subsections' => [
                    [
                        'name' => 'Labor Practices',
                        'description' => 'Employee rights, working conditions, and fair labor practices',
                        'order_no' => 1,
                        'items' => [
                            ['name' => 'Employee Count', 'description' => 'Total number of employees by category', 'order_no' => 1],
                            ['name' => 'Fair Wages', 'description' => 'Living wage compliance and payment practices', 'order_no' => 2],
                            ['name' => 'Working Hours', 'description' => 'Average working hours and overtime management', 'order_no' => 3],
                            ['name' => 'Employee Benefits', 'description' => 'Benefits provided to employees', 'order_no' => 4],
                            ['name' => 'Child Labor Policy', 'description' => 'Policies preventing child labor', 'order_no' => 5],
                        ],
                    ],
                    [
                        'name' => 'Health & Safety',
                        'description' => 'Occupational health and safety measures and protocols',
                        'order_no' => 2,
                        'items' => [
                            ['name' => 'Safety Training', 'description' => 'Employee safety training programs', 'order_no' => 1],
                            ['name' => 'Accident Rate', 'description' => 'Workplace accident frequency and severity', 'order_no' => 2],
                            ['name' => 'Safety Equipment', 'description' => 'Personal protective equipment availability', 'order_no' => 3],
                            ['name' => 'Emergency Procedures', 'description' => 'Emergency response and evacuation procedures', 'order_no' => 4],
                            ['name' => 'Health Programs', 'description' => 'Employee health and wellness programs', 'order_no' => 5],
                        ],
                    ],
                    [
                        'name' => 'Training & Development',
                        'description' => 'Employee training, skill development, and career advancement',
                        'order_no' => 3,
                        'items' => [
                            ['name' => 'Training Hours', 'description' => 'Annual training hours per employee', 'order_no' => 1],
                            ['name' => 'Skill Development Programs', 'description' => 'Programs for enhancing employee skills', 'order_no' => 2],
                            ['name' => 'Career Advancement', 'description' => 'Internal promotion and career growth opportunities', 'order_no' => 3],
                        ],
                    ],
                    [
                        'name' => 'Diversity & Inclusion',
                        'description' => 'Workplace diversity, equality, and inclusion initiatives',
                        'order_no' => 4,
                        'items' => [
                            ['name' => 'Gender Diversity', 'description' => 'Gender representation across all levels', 'order_no' => 1],
                            ['name' => 'Equal Opportunity', 'description' => 'Equal employment opportunity policies', 'order_no' => 2],
                            ['name' => 'Discrimination Prevention', 'description' => 'Policies preventing discrimination and harassment', 'order_no' => 3],
                        ],
                    ],
                    [
                        'name' => 'Community Engagement',
                        'description' => 'Community relations, social impact, and local development',
                        'order_no' => 5,
                        'items' => [
                            ['name' => 'Community Programs', 'description' => 'Community development and engagement programs', 'order_no' => 1],
                            ['name' => 'Local Employment', 'description' => 'Percentage of local workforce hired', 'order_no' => 2],
                            ['name' => 'Stakeholder Engagement', 'description' => 'Engagement with local stakeholders', 'order_no' => 3],
                        ],
                    ],
                ],
            ],

            // GOVERNANCE SECTION
            [
                'section' => [
                    'name' => 'Governance',
                    'description' => 'Corporate governance, ethics, compliance, and transparency practices',
                    'order_no' => 3,
                ],
                'subsections' => [
                    [
                        'name' => 'Corporate Ethics',
                        'description' => 'Business ethics, anti-corruption, and code of conduct',
                        'order_no' => 1,
                        'items' => [
                            ['name' => 'Code of Conduct', 'description' => 'Formal code of conduct and business ethics', 'order_no' => 1],
                            ['name' => 'Anti-Corruption Policy', 'description' => 'Policies preventing bribery and corruption', 'order_no' => 2],
                            ['name' => 'Whistleblower Mechanism', 'description' => 'Confidential reporting mechanism for violations', 'order_no' => 3],
                            ['name' => 'Ethics Training', 'description' => 'Training on ethical business practices', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Supply Chain',
                        'description' => 'Supply chain transparency, responsibility, and due diligence',
                        'order_no' => 2,
                        'items' => [
                            ['name' => 'Supplier Assessment', 'description' => 'ESG assessment of suppliers', 'order_no' => 1],
                            ['name' => 'Supply Chain Transparency', 'description' => 'Traceability and transparency in supply chain', 'order_no' => 2],
                            ['name' => 'Supplier Code of Conduct', 'description' => 'Code of conduct for suppliers', 'order_no' => 3],
                            ['name' => 'Conflict Minerals', 'description' => 'Policy on conflict minerals', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Compliance',
                        'description' => 'Regulatory compliance, certifications, and legal requirements',
                        'order_no' => 3,
                        'items' => [
                            ['name' => 'Certifications', 'description' => 'Industry certifications and standards', 'order_no' => 1],
                            ['name' => 'Regulatory Compliance', 'description' => 'Compliance with local and international regulations', 'order_no' => 2],
                            ['name' => 'Audit Results', 'description' => 'Internal and external audit findings', 'order_no' => 3],
                            ['name' => 'Legal Violations', 'description' => 'Any legal violations or penalties', 'order_no' => 4],
                        ],
                    ],
                    [
                        'name' => 'Data Privacy & Security',
                        'description' => 'Data protection, privacy, and cybersecurity measures',
                        'order_no' => 4,
                        'items' => [
                            ['name' => 'Data Protection Policy', 'description' => 'Policies for protecting personal and sensitive data', 'order_no' => 1],
                            ['name' => 'Cybersecurity Measures', 'description' => 'Information security and cybersecurity practices', 'order_no' => 2],
                            ['name' => 'Data Breach Protocol', 'description' => 'Protocol for handling data breaches', 'order_no' => 3],
                        ],
                    ],
                ],
            ],
        ];

        $sectionCount = 0;
        $subsectionCount = 0;
        $itemCount = 0;

        foreach ($structure as $structureData) {
            // Create Section
            $section = Section::firstOrCreate(
                ['name' => $structureData['section']['name']],
                array_merge($structureData['section'], ['is_active' => true])
            );
            $sectionCount++;

            // Create Subsections
            foreach ($structureData['subsections'] as $subsectionData) {
                $items = $subsectionData['items'];
                unset($subsectionData['items']);

                $subsection = Subsection::firstOrCreate(
                    ['section_id' => $section->id, 'name' => $subsectionData['name']],
                    array_merge($subsectionData, ['section_id' => $section->id, 'is_active' => true])
                );
                $subsectionCount++;

                // Create Items
                foreach ($items as $itemData) {
                    Item::firstOrCreate(
                        ['subsection_id' => $subsection->id, 'name' => $itemData['name']],
                        array_merge($itemData, ['subsection_id' => $subsection->id, 'is_active' => true])
                    );
                    $itemCount++;
                }
            }
        }

        $this->command->info('   ✅ Created ' . $sectionCount . ' sections');
        $this->command->info('   ✅ Created ' . $subsectionCount . ' subsections');
        $this->command->info('   ✅ Created ' . $itemCount . ' items');
        $this->command->info('✅ ESG Structure seeding completed!');
    }
}
