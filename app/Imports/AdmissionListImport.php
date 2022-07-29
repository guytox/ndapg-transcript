<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionListImport implements ToModel, WithHeadingRow
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
        //create user
        //create student record
        // assign role as student
        // name , username , phone_number , current_level , email
    }
}
