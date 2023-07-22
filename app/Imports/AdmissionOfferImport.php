<?php

namespace App\Imports;

use App\Models\Admission;
use App\Models\Program;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionOfferImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */


    private $program_id;

    public function __construct($program_id)
    {
        $this->program_id = $program_id;
    }


    public function model(array $row)
    {

        $programs = Program::where('name','=',$row['programme'])->first();


            $data = [
                'faculty' => $row['faculty'],
                'category' => ucfirst($row['category']),
                'form_number' => $row['formnumber'],
                'matric_number' => $row['matricnumber'],
                'payment_code' => $row['studentpaycode'],
                'surname' => ucfirst($row['surname']),
                'other_names' => ucfirst($row['othernames']),
                'state' => ucfirst($row['state']),
                'programme' => ucfirst($row['programme']),
                'programme_id' => $programs->id,
                'department' => ucfirst($row['department']),
                'country' => ucfirst($row['country']),
                'gender' => ucfirst($row['gender']),
                'qualifications' => ucfirst($row['qualifications']),
                'remarks' => ucfirst($row['remarks']),
                'session_id' => activeSession()->id,
            ];

            $admitted = Admission::updateOrCreate(['form_number' => $row['formnumber']],$data); 
            return $admitted;


        return false;

    }


}
