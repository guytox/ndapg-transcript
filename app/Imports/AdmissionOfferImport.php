<?php

namespace App\Imports;

use App\Models\Admission;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionOfferImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            'faculty' => $row['faculty'],
            'category' => ucfirst($row['category']),
            'form_number' => $row['formnumber'],
            'surname' => ucfirst($row['surname']),
            'other_names' => ucfirst($row['othernames']),
            'state' => ucfirst($row['state']),
            'programme' => ucfirst($row['programme']),
            'programme_id' => getProgrammeDetailByName($row['programme'])->id,
            'department' => ucfirst($row['department']),
            'country' => ucfirst($row['country']),
            'gender' => ucfirst($row['gender']),
            'qualifications' => ucfirst($row['qualifications']),
            'remarks' => ucfirst($row['remarks']),
            'session_id' => activeSession()->id,
        ];

        $admitted = Admission::updateOrCreate(['form_number' => $row['formnumber']],$data);

        return $admitted;
    }
}
