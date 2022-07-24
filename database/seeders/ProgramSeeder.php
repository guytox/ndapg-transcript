<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Exception;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = ['Food Science and Technology', 'Accounting', 'Business Management', 'Economics', 'Mass Communication', 'Political Science',
        'Sociology', 'Psychology', 'Chemical Science', 'Mathematics and Computer Science', 'Physical Science', 'Biological Science',
        'Education', 'Philosophy', 'English and Other Languages', 'Religious Studies'];

        $programs = [

            [
                'M.Sc. Food Science and Technology',
                'M.Sc.',
                $departments[0]
            ],

            [
                'M.Sc. Accounting',
                'M.Sc.',
                $departments[1]
            ],

            [
                'M.Sc. Business Management',
                'M.Sc.',
                $departments[2]
            ],

            [
                'M.Sc. Entrepreneurship',
                'M.Sc.',
                $departments[2]
            ],

            [
                'M.Sc. Economics',
                'M.Sc.',
                $departments[3]
            ],

            [
                'M.Sc. Mass Communication',
                'M.Sc.',
                $departments[4]
            ],

            [
                'M.Sc. Political Science',
                'M.Sc.',
                $departments[5]
            ],

            [
                'M.Sc. International Relations',
                'M.Sc.',
                $departments[5]
            ],

            [
                'M.Sc. Sociology',
                'M.Sc.',
                $departments[6]
            ],

            [
                'M.Sc. Psychology',
                'M.Sc.',
                $departments[7]
            ],

            [
                'M.Sc. Biochemistry',
                'M.Sc.',
                $departments[8]
            ],

            [
                'M.Sc. Industrial Chemistry',
                'M.Sc.',
                $departments[8]
            ],

            [
                'M.Sc. Computer Science',
                'M.Sc.',
                $departments[9]
            ],

            [
                'M.Sc. Mathematics',
                'M.Sc.',
                $departments[9]
            ],

            [
                'M.Sc. Statistics',
                'M.Sc.',
                $departments[9]
            ],


            [
                'M.Sc. Physics',
                'M.Sc.',
                $departments[10]
            ],

            [
                'M.Sc. Industrial Physics',
                'M.Sc.',
                $departments[10]
            ],

            [
                'M.Sc. Microbiology',
                'M.Sc.',
                $departments[11]
            ],

            [
                'M.Sc. (Ed) Biology',
                'M.Sc. (Ed)',
                $departments[12]
            ],

            [
                'M.Sc. (Ed) Physics',
                'M.Sc. (Ed)',
                $departments[12]
            ],

            [
                'M.Sc. (Ed) Chemistry',
                'M.Sc. (Ed)',
                $departments[12]
            ],


            [
                'M.Sc.(Ed) Mathematics',
                'M.Sc. (Ed)',
                $departments[12]
            ],

            [
                'M.A. Philosophy',
                'M.A.',
                $departments[13]
            ],

            [
                'M.A. Religious Studies',
                'M.A.',
                $departments[15]
            ],

            [
                'M.A. English and Other Languages',
                'M.A.',
                $departments[14]
            ]
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(['name' => $program[0]], [
                'name' => $program[0],
                'degree_title' => $program[1],
                'level_id' => '2',
                'department_id' => $this->getDepartmentIdByName($program[2]),
                'uid' => uniqid('pr_')
            ]);
        }
    }

    public function getDepartmentIdByName($name)
    {
        $department = Department::where('name', $name)->first();

        if ($department) {
            return $department->id;
        }

        throw new Exception('Department with that name does not exist');
        return 0;
    }
}
