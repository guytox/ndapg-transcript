<?php

namespace App\Imports;

use App\Models\RegClearance;
use App\Models\StudentRecord;
use App\Models\UploadedPayment;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentPaymentUploadImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    private $semester;
    private $uploaded_by;

    public function __construct($semester, $uploaded_by)
    {
        $this->semester = $semester;
        $this->uploaded_by = $uploaded_by;
    }

    public function model(array $row)
    {
        //find the student
        $student = StudentRecord::where('matric', $row['matricno'])->first();
        if ($student) {
            //insert the payment record uploaded_payments
            $paymentData = [
                'student_id' => $student->id,
                'session_id' => activeSession()->id,
                'amount_paid' => convertToKobo(intval($row['amountpaid'])),
                'uploaded_by' => $this->uploaded_by
            ];

            $studentPayment = UploadedPayment::updateOrCreate(['student_id' => $student->id,
            'session_id' => activeSession()->id,], $paymentData);

            if ($studentPayment) {
                $data = [
                    'student_id' => $student->id,
                    'school_session_id' => activeSession()->id,

                ];

                //insert the course_reg monitor entry based on selection

                $regClearance = RegClearance::updateOrCreate(['student_id' => $student->id,
                'school_session_id' => activeSession()->id,],$data);

                if ($regClearance) {
                    if ($this->semester==0) {
                        $regClearance->first_semester = 1;
                        $regClearance->second_semester = 1;
                        $regClearance->save();
                    }elseif ($this->semester == 1) {
                        $regClearance->first_semester = 1;
                        $regClearance->save();
                    }elseif ($this->semester ==2) {
                        $regClearance->second_semester = 1;
                        $regClearance->save();
                    }
                }
            }

            return $regClearance;
        }

        return $this->uploaded_by;

    }
}
