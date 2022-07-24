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
        $faculties = ['Social and Management Sciences', 'Food Science and Techology', 'Science and Education','Arts','Office of the Vice Chancellor','Office of the Registrar','Office of the Bursar','Librarian'];

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
