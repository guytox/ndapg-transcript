<?php

namespace App\Imports;

use App\Models\CourseAllocationItems;
use App\Models\GradingSystemItems;
use App\Models\RegMonitorItems;
use App\Models\ResultAuditTrail;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
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
    private $staffId;


    public function __construct($id, $grading, $courseId, $sessionId, $semesterId, $staffId)
    {
        $this->id = $id;
        $this->grading = $grading;
        $this->courseId = $courseId;
        $this->semesterId = $semesterId;
        $this->semesterId = $semesterId;
        $this->staffId = $staffId;
        $this->sessionId = $sessionId;
    }


    public function model(array $row)
    {
        //get details of the regmonitor item and proceed with upload

        //get student
        $student = StudentRecord::where('matric', $row['matricno'])->first();
        $studentId = $student->id;

        $tograde = RegMonitorItems::where([
                                        'student_id' => $studentId,
                                        'session_id' => $this->sessionId,
                                        'course_id' => $this->courseId,
                                        'semester_id' => $this->semesterId,
                                        'status' => 'approved'
                                    ])
                                    ->first();
        $semesterCourse = SemesterCourse::find($this->courseId);

        if ($tograde->student_id !='') {
            // Entry found begin checks and final entry

            if ($this->grading =='ca1' && $row['ca1']!='' && number_format(floatval($row['ca1']),2)!= convertToBoolean($tograde->ca1) && $tograde->cfm_ca1==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['ca1']))+$tograde->ca2 + $tograde->ca3 + $tograde->ca4 <= covertToInt($semesterCourse->max_ca) ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[CA1]",
                        'old_values' => '['.convertToBoolean($tograde->ca1).']',
                        'new_values' => '['.number_format($row['ca1'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->ca1 = covertToInt($row['ca1']);
                        $tograde->ltotal = covertToInt($row['ca1']) + $tograde->ca2 + $tograde->ca3 + $tograde->ca4 + $tograde->exam;
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }
            elseif ($this->grading =='ca2' && $row['ca2']!='' && number_format(floatval($row['ca2']),2)!= convertToBoolean($tograde->ca2) && $tograde->cfm_ca2==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['ca2']))+$tograde->ca1 + $tograde->ca3 + $tograde->ca4 <= covertToInt($semesterCourse->max_ca) ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[CA2]",
                        'old_values' => '['.convertToBoolean($tograde->ca2).']',
                        'new_values' => '['.number_format($row['ca2'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->ca2 = covertToInt($row['ca2']);
                        $tograde->ltotal = covertToInt($row['ca2']) + $tograde->ca1 + $tograde->ca3 + $tograde->ca4 + $tograde->exam;
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }
            elseif ($this->grading =='ca3' && $row['ca3']!='' && number_format(floatval($row['ca3']),2)!= convertToBoolean($tograde->ca3) && $tograde->cfm_ca3==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['ca3']))+$tograde->ca1 + $tograde->ca2 + $tograde->ca4 <= covertToInt($semesterCourse->max_ca) ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[CA3]",
                        'old_values' => '['.convertToBoolean($tograde->ca3).']',
                        'new_values' => '['.number_format($row['ca3'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->ca3 = covertToInt($row['ca3']);
                        $tograde->ltotal = covertToInt($row['ca3']) + $tograde->ca1 + $tograde->ca2 + $tograde->ca4 + $tograde->exam;
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }
            elseif ($this->grading =='ca4' && $row['ca4']!='' && number_format(floatval($row['ca4']),2)!= convertToBoolean($tograde->ca4) && $tograde->cfm_ca4==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['ca4']))+$tograde->ca1 + $tograde->ca2 + $tograde->ca3 <= covertToInt($semesterCourse->max_ca) ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[CA4]",
                        'old_values' => '['.convertToBoolean($tograde->ca4).']',
                        'new_values' => '['.number_format($row['ca4'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->ca4 = covertToInt($row['ca4']);
                        $tograde->ltotal = covertToInt($row['ca4']) + $tograde->ca1 + $tograde->ca2 + $tograde->ca3 + $tograde->exam;
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }
            elseif ($this->grading =='exam' && $row['exam']!='' && number_format(floatval($row['exam']),2)!= convertToBoolean($tograde->exam) && $tograde->cfm_exam==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if (covertToInt(floatval($row['exam'])) <= covertToInt($semesterCourse->max_exam) ) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[EXAM]",
                        'old_values' => '['.convertToBoolean($tograde->exam).']',
                        'new_values' => '['.number_format($row['exam'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->exam = covertToInt($row['exam']);
                        $tograde->ltotal = covertToInt($row['exam']) + $tograde->ca1 + $tograde->ca2 + $tograde->ca3 + $tograde->ca4;
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }
            elseif ($this->grading =='all' && $row['ca1']!='' && number_format(floatval($row['ca1']),2)!= convertToBoolean($tograde->ca1) && $tograde->cfm_ca1==='0' && $row['ca2']!='' && number_format(floatval($row['ca2']),2)!= convertToBoolean($tograde->ca2) && $tograde->cfm_ca2==='0' && $row['ca3']!='' && number_format(floatval($row['ca3']),2)!= convertToBoolean($tograde->ca3) && $tograde->cfm_ca3==='0' && $row['ca4']!='' && number_format(floatval($row['ca4']),2)!= convertToBoolean($tograde->ca4) && $tograde->cfm_ca4==='0' && $row['exam']!='' && number_format(floatval($row['exam']),2)!= convertToBoolean($tograde->exam) && $tograde->cfm_exam==='0') {
                //difference found, you may proceed
                //check for limit extension then allow or disallow
                if ( covertToInt($row['ca1'] + $row['ca2'] +$row['ca3'] +$row['ca4'])<= covertToInt($semesterCourse->max_ca) && covertToInt(floatval($row['exam'])) <= covertToInt($semesterCourse->max_exam)) {
                    // limits not exceeded you may proceed with upload of scores
                    // enter log into table
                    //prepare for logging
                    $data =[
                        'user_id' => $this->staffId,
                        'changes' => "[ALL]",
                        'old_values' => '['.convertToBoolean($tograde->ca1).']['.convertToBoolean($tograde->ca2).']['.convertToBoolean($tograde->ca3).']['.convertToBoolean($tograde->ca4).']['.convertToBoolean($tograde->exam).']',
                        'new_values' => '['.number_format($row['ca1'],2).']['.number_format($row['ca2'],2).']['.number_format($row['ca3'],2).']['.number_format($row['ca4'],2).']['.number_format($row['exam'],2).']',
                        'course_id' => $this->courseId,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'student_id' => $studentId,
                    ];

                    $logEntry = ResultAuditTrail::create($data);

                    if ($logEntry) {
                        //all clear, you may write and proceed
                        $tograde->ca1 = covertToInt($row['ca1']);
                        $tograde->ca2 = covertToInt($row['ca2']);
                        $tograde->ca3 = covertToInt($row['ca3']);
                        $tograde->ca4 = covertToInt($row['ca4']);
                        $tograde->exam = covertToInt($row['exam']);
                        $tograde->ltotal = covertToInt($row['ca1']) + covertToInt($row['ca2']) + covertToInt($row['ca3']) + covertToInt($row['ca4']) + covertToInt($row['exam']);
                        $tograde->save();

                        //next update the student grade from the grading system table


                    }


                }

            }


            $grade = GradingSystemItems::join('grading_systems as g','g.id','=','grading_system_items.grading_system_id')
                                                                            ->join('student_records as r','r.grading_system_id','=','g.id')
                                                                            ->join('reg_monitor_items as m','m.student_id','=','r.id')
                                                                            ->where('m.id',$tograde->id)
                                                                            //->whereBetween('m.ltotal',['grading_system_items.lower_boundary', 'grading_system_items.upper_boundary'])
                                                                            ->select('m.ltotal','grading_system_items.*')
                                                                            ->get();
                                                foreach ($grade as $key => $v) {
                                                    if ($v->ltotal >= $v->lower_boundary && $v->ltotal <= $v->upper_boundary) {

                                                        $gradeLetter = $v->grade_letter;

                                                        $tograde->lgrade = $gradeLetter;
                                                        $tograde->save();
                                                    }
                                                }


        }





    }
}
