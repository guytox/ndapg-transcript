<?php

namespace App\Imports;

use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionListImport implements ToModel, WithHeadingRow
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


        // Excel import format name// matricno // email// gsm // programme // department // faculty

        $data = [
            'name' => $row['name'],
            'email' => $row['name'],
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
            'username' => $row['matricno'],
            'phone_number'=> $row['name'],
            'current_level' => getProgrammeDetailById($this->program_id, 'level'),
        ];

        $studentUser = User::updateOrCreate(['email'=>$row['email']],$data);
        // Assign Student Role to user
        $studentUser->assignRole('student');

        //get studentRecordData

        $studentData = [
            'user_id'=>$studentUser->id,
            'matric'=>$studentUser->username,
            'program_id' => $this->program_id,
            'admission_session' => activeSession()->id,
        ];

        //create student record
        $studentUserRecord = StudentRecord::updateOrCreate(['user_id'=>$studentUser->id, 'matric'=>$studentUser->username],$studentData);

        return $studentUserRecord;


    }
}
