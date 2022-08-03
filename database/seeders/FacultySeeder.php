<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faculties = ['Arts And Social Sciences', 'Engineering Technology', 'Management Sciences','Military Science And Interdisciplinary Studies','Science','Directorate Of Linkages And Collaboration'];

        foreach ($faculties as $faculty) {
            // create role when the seeder is called with 3 basic roles.
            Faculty::firstOrCreate(['name' => $faculty], [
                'name' => $faculty,
                'uid' => uniqid('fc_'),
                'academic'=>1,
            ]);
        }
    }
}
