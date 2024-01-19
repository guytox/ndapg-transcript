<?php

namespace App\Imports;

use App\Jobs\OldResultUploadJob;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class OldResultImport implements  ToModel, WithHeadingRow
{

    public $sessionId;
    public $semesterId;
    public $staffId;
    public $time;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function __construct($sessionId, $semesterId, $staffId, $time)
    {
        $this->semesterId = $semesterId;
        $this->staffId = $staffId;
        $this->sessionId = $sessionId;
        $this->time = $time;
    }

    public function model(array $row)
    {
        #collect everything and forward to the job
        $matric = $row['matricno'];
        $studentId = $row['studentid'];
        $courseId = $row['courseid'];
        $courseCode = $row['coursecode'];
        $totalScore = $row['totalscore'];

        #fire the job
        OldResultUploadJob::dispatch(
            $this->staffId,
            $this->sessionId,
            $this->semesterId,
            now(),
            $matric,
            $studentId,
            $courseId,
            $courseCode,
            $totalScore
        );
    }
}
