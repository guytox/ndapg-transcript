<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Exception;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faculties = ['Social and Management Sciences', 'Food Science and Techology', 'Science and Education','Arts','Office of the Vice Chancellor','Office of the Registrar','Office of the Bursar','Librarian']; // mark the faculty with their indexes e.g science is $faculties[0]

        $departments = [

            [
                'Academic Planning',
                $faculties[4]
            ],
            [
                'Directorate of ICT',
                $faculties[4]
            ],
            [
                'Bursary',
                $faculties[6]
            ],
            [
                'Registry',
                $faculties[5]
            ],
            [
                'Student Affairs',
                $faculties[4]
            ],
            [
                'Security',
                $faculties[4]
            ],
            [
                'General Studies',
                $faculties[4]
            ],
            [
                'Food Science and Technology',
                $faculties[1]
            ],
            [
                'Philosophy',
                $faculties[3]
            ],
            [
                'English and Other Languages',
                $faculties[3]
            ],
            [
                'Religious Studies',
                $faculties[3]
            ],
            [
                'Chemical Science',
                $faculties[2]
            ],
            [
                'Mathematics and Computer Science',
                $faculties[2]
            ],
            [
                'Physical Science',
                $faculties[2]
            ],
            [
                'Biological Science',
                $faculties[2]
            ],
            [
                'Education',
                $faculties[2]
            ],
            [
                'Accounting',
                $faculties[0]
            ],
            [
                'Business Management',
                $faculties[0]
            ],
            [
                'Economics',
                $faculties[0]
            ],
            [
                'Mass Communication',
                $faculties[0]
            ],
            [
                'Political Science',
                $faculties[0]
            ],
            [
                'Sociology',
                $faculties[0]
            ],
            [
                'Psychology',
                $faculties[0]
            ]
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department[0]], [
                'name' => $department[0],
                'uid' => uniqid('dp_'),
                'faculty_id' => $this->getFacultyIdByName($department[1]),
                'academic'=>1
            ]);
        }
    }

    public function getFacultyIdByName($name)
    {
        $faculty = Faculty::where('name', $name)->first();

        if ($faculty) {
            return $faculty->id;
        }

        throw new Exception('Faculty with that name does not exist');
        return 0;
    }
}
