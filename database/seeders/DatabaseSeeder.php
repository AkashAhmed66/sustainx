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
use App\Models\Answer;
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
        $this->command->line('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🚀 SUSTAINX ESG PLATFORM - DATABASE SEEDING');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');

        // Display seeding sequence
        $this->command->info('📌 SEEDING SEQUENCE:');
        $this->command->info('   1. Permissions & Roles');
        $this->command->info('   2. Question Types');
        $this->command->info('   3. Countries');
        $this->command->info('   4. Factory Types');
        $this->command->info('   5. Users');
        $this->command->info('   6. ESG Structure (Sections, Subsections, Items)');
        $this->command->info('   7. Questions & Options');
        $this->command->info('   8. Factories');
        $this->command->info('   9. Assessments & Answers');
        $this->command->line('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');

        // Call all seeders in proper sequence
        // Step 1: Roles and Permissions
        $this->call(RolePermissionSeeder::class);
        $this->command->line('');

        // Step 2: Question Types
        $this->call(QuestionTypeSeeder::class);
        $this->command->line('');

        // Step 3: Countries
        $this->call(CountrySeeder::class);
        $this->command->line('');

        // Step 4: Factory Types
        $this->call(FactoryTypeSeeder::class);
        $this->command->line('');

        // Step 5: Users
        $this->call(UserSeeder::class);
        $this->command->line('');

        // Step 6: ESG Structure
        $this->call(SectionSubsectionSeeder::class);
        $this->command->line('');

        // Step 7: Questions
        $this->call(QuestionSeeder::class);
        $this->command->line('');

        // Step 8: Factories
        $this->call(FactorySeeder::class);
        $this->command->line('');

        // Step 9: Assessments
        $this->call(AssessmentSeeder::class);
        $this->command->line('');

        // Display final statistics
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🎉 DATABASE SEEDING COMPLETED SUCCESSFULLY!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');
        $this->command->info('📊 FINAL STATISTICS:');
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
        $this->command->info('   • Answers: ' . Answer::count());
        $this->command->line('');
        $this->command->info('🔑 LOGIN CREDENTIALS:');
        $this->command->info('   📧 Email: admin@sustainx.com');
        $this->command->info('   🔒 Password: 12345678');
        $this->command->line('');
        $this->command->info('📝 OTHER TEST USERS:');
        $this->command->info('   • manager@sustainx.com (Manager Role)');
        $this->command->info('   • sarah@sustainx.com (User Role)');
        $this->command->info('   • michael@sustainx.com (User Role)');
        $this->command->info('   • fatima@sustainx.com (User Role)');
        $this->command->info('   • raj@sustainx.com (User Role)');
        $this->command->info('   (All passwords: 12345678)');
        $this->command->line('');
        $this->command->info('📅 DATA COVERAGE:');
        $this->command->info('   • Years: 2021-2025 (Multiple assessments per factory)');
        $this->command->info('   • All factories have 3-5 years of historical data');
        $this->command->info('   • All questions answered with realistic values');
        $this->command->info('   • Year-over-year trends visible in dashboard');
        $this->command->line('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');
    }
}
