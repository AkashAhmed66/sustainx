# 🌱 SustainX Database Seeding Documentation

## Overview
The database seeding system has been completely restructured into **modular, sequential seeders** for better organization, maintainability, and error tracking.

## 📂 Seeder Files Structure

### 1. **RolePermissionSeeder.php**
- Creates all permissions (92 permissions)
- Creates roles: Admin, Manager, User
- Assigns permissions to roles
- **Dependencies:** None

### 2. **QuestionTypeSeeder.php**
- Creates 3 question types: numeric, mcq, multiple_select
- **Dependencies:** None

### 3. **CountrySeeder.php**
- Creates 15 countries with ISO codes
- **Dependencies:** None

### 4. **FactoryTypeSeeder.php**
- Creates 10 factory types
- **Dependencies:** None

### 5. **UserSeeder.php**
- Creates admin user (admin@sustainx.com)
- Creates manager user (manager@sustainx.com)
- Creates 4 regular users
- Assigns roles to all users
- **Dependencies:** RolePermissionSeeder

### 6. **SectionSubsectionSeeder.php**
- Creates 3 ESG sections (Environmental, Social, Governance)
- Creates 14 subsections across all sections
- Creates 64 items across all subsections
- **Dependencies:** None

### 7. **QuestionSeeder.php**
- Creates 150+ questions with different types
- Creates 700+ options for MCQ and multiple select questions
- Covers all items with comprehensive questions
- **Dependencies:** QuestionTypeSeeder, SectionSubsectionSeeder

### 8. **FactorySeeder.php**
- Creates 8 factories across different countries
- Connects factories to users (factory_user pivot table)
- **Dependencies:** CountrySeeder, FactoryTypeSeeder, UserSeeder

### 9. **AssessmentSeeder.php**
- Creates assessments for years 2021-2025
- Each factory has 3-5 years of historical data
- Creates complete answers for all questions
- Generates realistic numeric values with year-over-year trends
- **Dependencies:** FactorySeeder, QuestionSeeder

### 10. **DatabaseSeeder.php** (Main orchestrator)
- Calls all seeders in correct sequence
- Displays progress and statistics
- Shows final counts and login credentials

## 🔢 Data Statistics (After Seeding)

| Entity | Count |
|--------|-------|
| Permissions | 92 |
| Roles | 3 |
| Users | 6 |
| Question Types | 3 |
| Countries | 15 |
| Factory Types | 10 |
| Sections | 3 |
| Subsections | 14 |
| Items | 64 |
| Questions | 150+ |
| Options | 700+ |
| Factories | 8 |
| Assessments | 30+ |
| Answers | 4500+ |

## 🔑 Login Credentials

### Admin Account
- **Email:** admin@sustainx.com
- **Password:** 12345678
- **Role:** Admin (Full access)

### Manager Account
- **Email:** manager@sustainx.com
- **Password:** 12345678
- **Role:** Manager (Limited permissions)

### User Accounts
All passwords: **12345678**
- sarah@sustainx.com
- michael@sustainx.com
- fatima@sustainx.com
- raj@sustainx.com

## 📊 Data Coverage

### Year Range: 2021-2025
- Each factory has 3-5 years of assessment data
- 2021-2024: Status = "approved" (historical data)
- 2025: Status = "draft" (current year)

### Question Type Distribution
- **Numeric Questions:** ~50% (Energy consumption, emissions, employee count, etc.)
- **MCQ Questions:** ~30% (Yes/No, Rating scales, Frequency selections)
- **Multiple Select Questions:** ~20% (Benefits, Certifications, Programs)

### Realistic Data Features
- **Year-over-year trends:** Values increase/decrease naturally over time
- **Contextual variations:** Different factories have different values
- **Percentage boundaries:** All % values stay between 0-100
- **Industry-realistic ranges:** Energy in MWh, Water in m³, Emissions in tCO2e

## 🏭 Factory-User Connections

| Factory | Country | Users |
|---------|---------|-------|
| SustainTex Dhaka | Bangladesh | Sarah, Michael |
| GreenGarments Mumbai | India | Fatima |
| EcoTextile Shanghai | China | Sarah |
| Eco Dyeing & Printing Chittagong | Bangladesh | Michael, Raj |
| Prime Knitting Hanoi | Vietnam | Fatima |
| Royal Weaving Karachi | Pakistan | Raj |
| Ethical Textiles Bangalore | India | Sarah, Fatima |
| Sustainable Garments Dhaka | Bangladesh | Michael |

## 🚀 Running the Seeders

### Full Fresh Seed
```bash
php artisan migrate:fresh --seed
```

### Run Individual Seeders
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=QuestionTypeSeeder
php artisan db:seed --class=UserSeeder
# etc...
```

### Run Only Assessment Data (if structure exists)
```bash
php artisan db:seed --class=AssessmentSeeder
```

## 🎯 Purpose of Each Module

1. **Roles & Permissions:** Security and access control foundation
2. **Question Types:** Defines answer formats (numeric, mcq, multiple select)
3. **Countries & Factory Types:** Master data for factories
4. **Users:** Creates test users with different roles
5. **ESG Structure:** Complete hierarchy (Sections → Subsections → Items)
6. **Questions:** Actual assessment questions with options
7. **Factories:** Physical locations where assessments happen
8. **Assessments:** Multi-year data with complete answers

## 📈 Dashboard Features Enabled

With this seed data, the dashboard can display:
- ✅ Year-over-year comparisons (2021-2025)
- ✅ Multi-factory comparisons
- ✅ All 3 question types with appropriate visualizations
- ✅ Progress tracking across sections/subsections
- ✅ Item-grouped numeric question pie charts
- ✅ MCQ pie charts
- ✅ Multiple select bar charts
- ✅ Trend lines showing improvement over years

## 🔧 Maintenance

### Adding New Questions
1. Add question data to `QuestionSeeder.php`
2. Link to existing item by name
3. Add options if MCQ or multiple select

### Adding New Factories
1. Add factory data to `FactorySeeder.php`
2. Connect to users using `users` array
3. Run `AssessmentSeeder` to generate assessment data

### Updating Answer Values
Modify the `generateNumericAnswer()` method in `AssessmentSeeder.php` to adjust:
- Base values
- Year-over-year trends
- Industry-specific ranges

## ⚠️ Important Notes

1. **Seeding Order Matters:** The sequence defined in DatabaseSeeder.php must be maintained
2. **Factory-User Connection:** Required for dashboard to show data (users only see their factories)
3. **Question Type IDs:** 1=numeric, 2=mcq, 3=multiple_select (fixed IDs)
4. **Assessment Status:** Only "approved" assessments appear in dashboard
5. **Answer Completeness:** All questions must have answers for proper dashboard functionality

## 🧪 Testing the Seed Data

After running the seeder:

1. **Login Test:**
   - Login with admin@sustainx.com / 12345678
   - Verify full access to all modules

2. **Dashboard Test:**
   - Check main dashboard shows data
   - Test year filter (2021-2025)
   - Test factory filter
   - Verify comparison dashboard works

3. **Assessment Test:**
   - View existing assessments
   - Verify all questions have answers
   - Check all 3 question types display correctly

4. **User Connection Test:**
   - Login as sarah@sustainx.com
   - Should see only assigned factories (SustainTex Dhaka, EcoTextile Shanghai, Ethical Textiles Bangalore)

## 📝 Troubleshooting

**If migrations fail:**
```bash
php artisan migrate:fresh
```

**If seeding fails midway:**
```bash
php artisan db:seed --force
```

**To reset specific data:**
```bash
# Reset only assessments
Assessment::truncate();
Answer::truncate();
php artisan db:seed --class=AssessmentSeeder
```

## 🎉 Success Verification

After successful seeding, you should see:
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🎉 DATABASE SEEDING COMPLETED SUCCESSFULLY!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📊 FINAL STATISTICS:
   • Permissions: 92
   • Roles: 3
   • Users: 6
   ... (all counts)
```

---

**Last Updated:** February 11, 2026
**Version:** 2.0 (Modular Seeders)
