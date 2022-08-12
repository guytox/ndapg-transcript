<?php

namespace App\Imports;

use App\Models\CourseAllocationItems;
use App\Models\RegMonitorItems;
use App\Models\SemesterCourse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturerGradeUploadImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    private $id;
    private $grading;
    private $courseId;
    private $sessionId;
    private $semesterId;


    public function __construct($id, $grading, $courseId, $sessionId, $semesterId)
    {
        $this->id = $id;
        $this->grading = $grading;
        $this->courseId = $courseId;
        $this->semesterId = $semesterId;
        $this->semesterId = $semesterId;
    }


    public function model(array $row)
    {
        //get details of the regmonitor item and proceed with upload
        $tograde = RegMonitorItems::where([
                                        'student_id' => getStudentByMatric($row['matricno'])->id,
                                        'session_id' => $this->sessionId,
                                        'course_id' => $this->courseId,
                                        'semester_id' => $this->semesterId
                                    ])
                                    ->first();
        $semesterCourse = SemesterCourse::find($this->course_id);

        if ($tograde->student_id!='') {
            // Entry found begin checks and final entry

            if ($this->grading =='ca1' && $row['ca1']!='' && number_format(floatval($row['ca1']),2)!= convertToBoolean($tograde->ca1)) {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['ca1']))+$tograde->ca2 + $tograde->ca3 + $tograde->ca4<= $semesterCourse->max_ca ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    // update the column
                    // update the total
                    

                }

            }


        }





    }
}
