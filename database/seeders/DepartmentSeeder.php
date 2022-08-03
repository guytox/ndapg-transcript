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

            ['Defence And Security Studies','Arts And Social Sciences'],
            ['Geography','Arts And Social Sciences'],
            ['History And War Studies','Arts And Social Sciences'],
            ['Languages','Arts And Social Sciences'],
            ['Political Science','Arts And Social Sciences'],
            ['Psychology','Arts And Social Sciences'],
            ['Directorate Of Linkages And Collaboration','Directorate Of Linkages And Collaboration'],
            ['Civil Engineering','Engineering Technology'],
            ['Electrical Electronics Engineering','Engineering Technology'],
            ['Mechanical Engineering','Engineering Technology'],
            ['Mechatronic Engineering','Engineering Technology'],
            ['Accounting','Management Sciences'],
            ['Economics','Management Sciences'],
            ['Logistics And Supply Chain Management','Management Sciences'],
            ['Management','Management Sciences'],
            ['Computer Science','Military Science And Interdisciplinary Studies'],
            ['Cyber Security','Military Science And Interdisciplinary Studies'],
            ['Intelligence And Security Science','Military Science And Interdisciplinary Studies'],
            ['Biology','Science'],
            ['Biotechnology','Science'],
            ['Chemistry','Science'],
            ['Mathematical Sciences','Science'],
            ['Physics','Science'],
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
