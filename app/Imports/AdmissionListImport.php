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
            'email' => $row['email'],
            'password' => Hash::make($row['matricno']),
            'email_verified_at' => now(),
            'username' => $row['matricno'],
            'phone_number'=> formatPhoneNumber($row['gsm']),
            'current_level' => getProgrammeDetailById($this->program_id, 'level'),
        ];

        $studentUser = User::upsert($data, $uniqueBy =['email'],[
            'email_verified_at',
        ]);

        //$studentUser = User::updateOrCreate(['email'=>$row['email']],$data);
        // Assign Student Role to user
        $newuser = User::where('username', $row['matricno'])->first();
        if ($newuser) {
            $newuser->assignRole('student');
        }


        //get studentRecordData

        $studentData = [
            'user_id'=>$newuser->id,
            'matric'=>$newuser->username,
            'program_id' => $this->program_id,
            'admission_session' => activeSession()->id,
        ];

        //create student record
        // $studentUserRecord = StudentRecord::upsert($studentData, $uniqueBy =['user_id'],[
        //     'admission_session'
        // ]);

        $studentUserRecord = StudentRecord::updateOrCreate(['user_id'=>$newuser->id, 'matric'=>$newuser->username],$studentData);

        return $studentUserRecord;


    }
}
