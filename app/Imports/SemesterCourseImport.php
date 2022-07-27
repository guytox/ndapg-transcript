<?php

namespace App\Imports;

use App\Models\SemesterCourse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SemesterCourseImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    private $department_id;

    public function __construct($department_id)
    {
        $this->department_id = $department_id;
    }

    public function model(array $row)
    {
        $newCourse = SemesterCourse::updateOrCreate(['department_id' => $this->department_id, 'courseCode' => $row['coursecode'],'creditUnits' => $row['creditunits']],[
            'courseCode' => $row['coursecode'],
            'courseTitle'=> $row['coursetitle'],
            'creditUnits' => $row['creditunits'],
            'courseDescription' => $row['description'],
            'department_id' => $this->department_id
        ]);


        return $newCourse;
    }
}
