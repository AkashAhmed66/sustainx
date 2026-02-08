<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\QuestionType;
use App\Models\Country;
use App\Models\FactoryType;
use App\Models\Section;
use App\Models\Subsection;
use App\Models\Item;
use App\Models\Question;
use App\Models\Option;
use App\Models\Factory;
use App\Models\Assessment;
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

        // Step 4: Seed Question Types
        $this->command->info('❓ Creating question types...');
        $questionTypes = [
            ['id' => 1, 'name' => 'numeric'],
            ['id' => 2, 'name' => 'mcq'],
        ];
        foreach ($questionTypes as $type) {
            QuestionType::updateOrCreate(['id' => $type['id']], $type);
        }
        $this->command->info('✅ Created ' . count($questionTypes) . ' question types');

        // Step 5: Seed Countries
        $this->command->info('🌍 Creating countries...');
        $countries = [
            ['name' => 'United States', 'iso_code' => 'USA'],
            ['name' => 'United Kingdom', 'iso_code' => 'GBR'],
            ['name' => 'Germany', 'iso_code' => 'DEU'],
            ['name' => 'France', 'iso_code' => 'FRA'],
            ['name' => 'China', 'iso_code' => 'CHN'],
            ['name' => 'India', 'iso_code' => 'IND'],
            ['name' => 'Japan', 'iso_code' => 'JPN'],
            ['name' => 'Bangladesh', 'iso_code' => 'BGD'],
            ['name' => 'Vietnam', 'iso_code' => 'VNM'],
            ['name' => 'Turkey', 'iso_code' => 'TUR'],
        ];
        foreach ($countries as $country) {
            Country::firstOrCreate(['iso_code' => $country['iso_code']], $country);
        }
        $this->command->info('✅ Created ' . count($countries) . ' countries');

        // Step 6: Seed Factory Types
        $this->command->info('🏭 Creating factory types...');
        $factoryTypes = [
            ['name' => 'Textile Manufacturing'],
            ['name' => 'Garment Manufacturing'],
            ['name' => 'Dyeing & Printing'],
            ['name' => 'Knitting'],
            ['name' => 'Weaving'],
            ['name' => 'Spinning'],
            ['name' => 'Packaging'],
        ];
        foreach ($factoryTypes as $type) {
            FactoryType::firstOrCreate($type);
        }
        $this->command->info('✅ Created ' . count($factoryTypes) . ' factory types');

        // Step 7: Seed Sections
        $this->command->info('📋 Creating ESG sections...');
        $sections = [
            ['name' => 'Environmental', 'description' => 'Environmental sustainability metrics and indicators', 'order_no' => 1, 'is_active' => true],
            ['name' => 'Social', 'description' => 'Social responsibility and employee welfare metrics', 'order_no' => 2, 'is_active' => true],
            ['name' => 'Governance', 'description' => 'Corporate governance and ethical practices', 'order_no' => 3, 'is_active' => true],
        ];
        $sectionModels = [];
        foreach ($sections as $section) {
            $sectionModels[$section['name']] = Section::firstOrCreate(
                ['name' => $section['name']],
                $section
            );
        }
        $this->command->info('✅ Created ' . count($sections) . ' sections');

        // Step 8: Seed Subsections
        $this->command->info('📑 Creating subsections...');
        $subsections = [
            // Environmental subsections
            ['section' => 'Environmental', 'name' => 'Energy & Emissions', 'description' => 'Energy consumption and greenhouse gas emissions', 'order_no' => 1],
            ['section' => 'Environmental', 'name' => 'Water Management', 'description' => 'Water usage and wastewater treatment', 'order_no' => 2],
            ['section' => 'Environmental', 'name' => 'Waste Management', 'description' => 'Solid waste and recycling practices', 'order_no' => 3],
            ['section' => 'Environmental', 'name' => 'Chemical Management', 'description' => 'Chemical usage and hazardous materials', 'order_no' => 4],

            // Social subsections
            ['section' => 'Social', 'name' => 'Labor Practices', 'description' => 'Employee rights and working conditions', 'order_no' => 1],
            ['section' => 'Social', 'name' => 'Health & Safety', 'description' => 'Occupational health and safety measures', 'order_no' => 2],
            ['section' => 'Social', 'name' => 'Training & Development', 'description' => 'Employee training and skill development', 'order_no' => 3],
            ['section' => 'Social', 'name' => 'Community Engagement', 'description' => 'Community relations and social impact', 'order_no' => 4],

            // Governance subsections
            ['section' => 'Governance', 'name' => 'Corporate Ethics', 'description' => 'Business ethics and anti-corruption', 'order_no' => 1],
            ['section' => 'Governance', 'name' => 'Supply Chain', 'description' => 'Supply chain transparency and responsibility', 'order_no' => 2],
            ['section' => 'Governance', 'name' => 'Compliance', 'description' => 'Regulatory compliance and certifications', 'order_no' => 3],
        ];
        $subsectionModels = [];
        foreach ($subsections as $subsection) {
            $sectionName = $subsection['section'];
            unset($subsection['section']);
            $subsection['section_id'] = $sectionModels[$sectionName]->id;
            $subsection['is_active'] = true;
            $model = Subsection::firstOrCreate(
                ['section_id' => $subsection['section_id'], 'name' => $subsection['name']],
                $subsection
            );
            $subsectionModels[$subsection['name']] = $model;
        }
        $this->command->info('✅ Created ' . count($subsections) . ' subsections');

        // Step 9: Seed Items
        $this->command->info('📝 Creating items...');
        $items = [
            // Energy & Emissions items
            ['subsection' => 'Energy & Emissions', 'name' => 'Total Energy Consumption', 'description' => 'Annual total energy consumption', 'order_no' => 1],
            ['subsection' => 'Energy & Emissions', 'name' => 'Renewable Energy Usage', 'description' => 'Percentage of renewable energy used', 'order_no' => 2],
            ['subsection' => 'Energy & Emissions', 'name' => 'GHG Emissions', 'description' => 'Total greenhouse gas emissions', 'order_no' => 3],

            // Water Management items
            ['subsection' => 'Water Management', 'name' => 'Water Consumption', 'description' => 'Total annual water consumption', 'order_no' => 1],
            ['subsection' => 'Water Management', 'name' => 'Wastewater Treatment', 'description' => 'Wastewater treatment percentage', 'order_no' => 2],
            ['subsection' => 'Water Management', 'name' => 'Water Recycling', 'description' => 'Water recycling and reuse rate', 'order_no' => 3],

            // Waste Management items
            ['subsection' => 'Waste Management', 'name' => 'Total Waste Generated', 'description' => 'Annual total waste generation', 'order_no' => 1],
            ['subsection' => 'Waste Management', 'name' => 'Waste Recycled', 'description' => 'Percentage of waste recycled', 'order_no' => 2],
            ['subsection' => 'Waste Management', 'name' => 'Hazardous Waste', 'description' => 'Hazardous waste management', 'order_no' => 3],

            // Labor Practices items
            ['subsection' => 'Labor Practices', 'name' => 'Employee Count', 'description' => 'Total number of employees', 'order_no' => 1],
            ['subsection' => 'Labor Practices', 'name' => 'Fair Wages', 'description' => 'Living wage compliance', 'order_no' => 2],
            ['subsection' => 'Labor Practices', 'name' => 'Working Hours', 'description' => 'Average working hours per week', 'order_no' => 3],

            // Health & Safety items
            ['subsection' => 'Health & Safety', 'name' => 'Safety Training', 'description' => 'Employee safety training completion', 'order_no' => 1],
            ['subsection' => 'Health & Safety', 'name' => 'Accident Rate', 'description' => 'Workplace accident frequency', 'order_no' => 2],
            ['subsection' => 'Health & Safety', 'name' => 'Safety Equipment', 'description' => 'PPE availability and usage', 'order_no' => 3],
        ];
        $itemModels = [];
        foreach ($items as $item) {
            $subsectionName = $item['subsection'];
            unset($item['subsection']);
            $item['subsection_id'] = $subsectionModels[$subsectionName]->id;
            $item['is_active'] = true;
            $model = Item::firstOrCreate(
                ['subsection_id' => $item['subsection_id'], 'name' => $item['name']],
                $item
            );
            $itemModels[$item['name']] = $model;
        }
        $this->command->info('✅ Created ' . count($items) . ' items');

        // Step 10: Seed Questions
        $this->command->info('❔ Creating questions...');
        $numericType = QuestionType::where('name', 'numeric')->first();
        $mcqType = QuestionType::where('name', 'mcq')->first();

        $questions = [
            ['item' => 'Total Energy Consumption', 'question_text' => 'What is your total annual energy consumption?', 'type' => $numericType->id, 'unit' => 'MWh', 'is_required' => true],
            ['item' => 'Renewable Energy Usage', 'question_text' => 'What percentage of your energy comes from renewable sources?', 'type' => $numericType->id, 'unit' => '%', 'is_required' => true],
            ['item' => 'GHG Emissions', 'question_text' => 'What are your total GHG emissions?', 'type' => $numericType->id, 'unit' => 'tCO2e', 'is_required' => true],
            ['item' => 'Water Consumption', 'question_text' => 'What is your total annual water consumption?', 'type' => $numericType->id, 'unit' => 'm³', 'is_required' => true],
            ['item' => 'Wastewater Treatment', 'question_text' => 'Do you have a wastewater treatment facility?', 'type' => $mcqType->id, 'unit' => null, 'is_required' => true],
            ['item' => 'Water Recycling', 'question_text' => 'What percentage of water is recycled?', 'type' => $numericType->id, 'unit' => '%', 'is_required' => false],
            ['item' => 'Total Waste Generated', 'question_text' => 'What is your total annual waste generation?', 'type' => $numericType->id, 'unit' => 'tonnes', 'is_required' => true],
            ['item' => 'Waste Recycled', 'question_text' => 'What percentage of waste is recycled?', 'type' => $numericType->id, 'unit' => '%', 'is_required' => true],
            ['item' => 'Employee Count', 'question_text' => 'How many employees do you have?', 'type' => $numericType->id, 'unit' => 'employees', 'is_required' => true],
            ['item' => 'Fair Wages', 'question_text' => 'Do you pay living wages to all employees?', 'type' => $mcqType->id, 'unit' => null, 'is_required' => true],
            ['item' => 'Working Hours', 'question_text' => 'What is the average working hours per week?', 'type' => $numericType->id, 'unit' => 'hours', 'is_required' => true],
            ['item' => 'Safety Training', 'question_text' => 'What percentage of employees completed safety training?', 'type' => $numericType->id, 'unit' => '%', 'is_required' => true],
            ['item' => 'Accident Rate', 'question_text' => 'How would you rate your workplace safety?', 'type' => $mcqType->id, 'unit' => null, 'is_required' => true],
        ];

        $questionModels = [];
        foreach ($questions as $question) {
            $itemName = $question['item'];
            unset($question['item']);
            $question['item_id'] = $itemModels[$itemName]->id;
            $question['question_type_id'] = $question['type'];
            unset($question['type']);
            $question['is_active'] = true;
            $model = Question::create($question);
            $questionModels[$question['question_text']] = $model;
        }
        $this->command->info('✅ Created ' . count($questions) . ' questions');

        // Step 11: Seed Options for MCQ questions
        $this->command->info('📊 Creating MCQ options...');
        
        // Options for workplace safety rating
        $mcqQuestion = $questionModels['How would you rate your workplace safety?'];
        $safetyOptions = [
            ['question_id' => $mcqQuestion->id, 'option_text' => 'Excellent', 'option_value' => 5, 'order_no' => 1],
            ['question_id' => $mcqQuestion->id, 'option_text' => 'Good', 'option_value' => 4, 'order_no' => 2],
            ['question_id' => $mcqQuestion->id, 'option_text' => 'Average', 'option_value' => 3, 'order_no' => 3],
            ['question_id' => $mcqQuestion->id, 'option_text' => 'Poor', 'option_value' => 2, 'order_no' => 4],
            ['question_id' => $mcqQuestion->id, 'option_text' => 'Very Poor', 'option_value' => 1, 'order_no' => 5],
        ];
        foreach ($safetyOptions as $option) {
            Option::create($option);
        }

        // Options for wastewater treatment (Yes/No)
        $wastewaterQuestion = $questionModels['Do you have a wastewater treatment facility?'];
        $yesNoOptions1 = [
            ['question_id' => $wastewaterQuestion->id, 'option_text' => 'Yes', 'option_value' => 1, 'order_no' => 1],
            ['question_id' => $wastewaterQuestion->id, 'option_text' => 'No', 'option_value' => 0, 'order_no' => 2],
        ];
        foreach ($yesNoOptions1 as $option) {
            Option::create($option);
        }

        // Options for fair wages (Yes/No)
        $fairWagesQuestion = $questionModels['Do you pay living wages to all employees?'];
        $yesNoOptions2 = [
            ['question_id' => $fairWagesQuestion->id, 'option_text' => 'Yes', 'option_value' => 1, 'order_no' => 1],
            ['question_id' => $fairWagesQuestion->id, 'option_text' => 'No', 'option_value' => 0, 'order_no' => 2],
        ];
        foreach ($yesNoOptions2 as $option) {
            Option::create($option);
        }

        $totalOptions = count($safetyOptions) + count($yesNoOptions1) + count($yesNoOptions2);
        $this->command->info('✅ Created ' . $totalOptions . ' options');

        // Step 12: Seed Factories
        $this->command->info('🏢 Creating sample factories...');
        $textileType = FactoryType::where('name', 'Textile Manufacturing')->first();
        $garmentType = FactoryType::where('name', 'Garment Manufacturing')->first();
        $bangladesh = Country::where('iso_code', 'BGD')->first();
        $india = Country::where('iso_code', 'IND')->first();
        $china = Country::where('iso_code', 'CHN')->first();

        $factories = [
            ['name' => 'SustainTex Dhaka', 'factory_type_id' => $textileType->id, 'country_id' => $bangladesh->id, 'address' => 'Tejgaon Industrial Area, Dhaka, Bangladesh', 'is_active' => true],
            ['name' => 'GreenGarments Mumbai', 'factory_type_id' => $garmentType->id, 'country_id' => $india->id, 'address' => 'Andheri East, Mumbai, India', 'is_active' => true],
            ['name' => 'EcoTextile Shanghai', 'factory_type_id' => $textileType->id, 'country_id' => $china->id, 'address' => 'Pudong District, Shanghai, China', 'is_active' => true],
        ];
        $factoryModels = [];
        foreach ($factories as $factory) {
            $factoryModels[] = Factory::create($factory);
        }
        $this->command->info('✅ Created ' . count($factories) . ' factories');

        // Step 13: Seed Assessments
        $this->command->info('📋 Creating sample assessments...');
        foreach ($factoryModels as $factory) {
            Assessment::create([
                'factory_id' => $factory->id,
                'year' => 2025,
                'period' => 'annual',
                'status' => 'draft',
            ]);
        }
        $this->command->info('✅ Created ' . count($factoryModels) . ' assessments');

        $this->command->line('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->line('');
        $this->command->info('📊 Summary:');
        $this->command->info('   • Permissions: ' . Permission::count());
        $this->command->info('   • Roles: ' . Role::count());
        $this->command->info('   • Users: ' . User::count());
        $this->command->info('   • Question Types: ' . QuestionType::count());
        $this->command->info('   • Countries: ' . Country::count());
        $this->command->info('   • Factory Types: ' . FactoryType::count());
        $this->command->info('   • Sections: ' . Section::count());
        $this->command->info('   • Subsections: ' . Subsection::count());
        $this->command->info('   • Items: ' . Item::count());
        $this->command->info('   • Questions: ' . Question::count());
        $this->command->info('   • Options: ' . Option::count());
        $this->command->info('   • Factories: ' . Factory::count());
        $this->command->info('   • Assessments: ' . Assessment::count());
        $this->command->line('');
        $this->command->info('🔑 Admin Login Credentials:');
        $this->command->info('   Email: admin@sustainx.com');
        $this->command->info('   Password: 12345678');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
