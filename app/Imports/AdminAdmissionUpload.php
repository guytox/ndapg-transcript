<?php

namespace App\Imports;

use App\Jobs\SubmitAdminAdmissionJob;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdminAdmissionUpload implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $staff;

    public function __construct($staff)
    {
        $this->staff = $staff;
    }

    public function model(array $row)
    {
        $name = $row['fullname'];
        $progId = $row['programmeid'];
        $progchoice = $row['programchoice'];
        $gender = $row['gender'];
        $maritalstatus = $row['maritalstatus'];
        $dob = $row['dob'];
        $email = $row['email'];
        $nationality = $row['nationality'];
        $stateorigin = $row['stateorigin'];
        $stateid = $row['stateid'];
        $lga = $row['lga'];
        $gsm = $row['gsm'];
        $occupation = $row['occupation'];
        $staff = $this->staff;

        $scTime = Carbon::now()->addSeconds(10);
        SubmitAdminAdmissionJob::dispatch($name, $progId, $progchoice, $gender, $maritalstatus, $dob, $email, $nationality, $stateorigin, $stateid, $lga, $gsm, $occupation, $staff)->delay($scTime);
    }
}
