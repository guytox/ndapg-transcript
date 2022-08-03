<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(
            [
                RoleSeeder::class,
                AdminUserSeeder::class,
                StudyLevelsSeeder::class,
                FacultySeeder::class,
                DepartmentSeeder::class,
                ProgramSeeder::class,
                GradingSystemSeeder::class,
                PaymentConfigSeeder::class,
                AcademicSessionSeeder::class,
                SemesterSeeder::class,
                MatricConfigSeeder::class,
                BillingItemSeeder::class,
                StatesSeeder::class,
                LocalGovernmentSeeder::class,
                ErrorCodesSeeder::class
            ]

        );
    }
}
