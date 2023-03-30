<?php

namespace App\Exports;

use App\Models\ApplicantAdmissionRequest;
use App\Models\OlevelResult;
use App\Models\UserQualification;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicantExport implements FromCollection, WithHeadings
{

    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    private $sessionId;
    private $programId;

    public function __construct($sessionId, $programId)
    {
        $this->sessionId = $sessionId;
        $this->programId = $programId;
    }


    public function collection()
    {
        $appList =  ApplicantAdmissionRequest::where('program_id', $this->programId)
                                            ->where('session_id', $this->sessionId)
                                            ->get();

        $newList = new Collection();
        $sno=1;
        foreach ($appList as $k) {

            $apUser = getUserById($k->user_id);
            $oLevel = OlevelResult::where('user_id', $apUser->id)->get();
            $qualf = UserQualification::where('user_id', $apUser->id)->get();
            #get the result details
            $result = '';
            foreach ($oLevel as $o) {
                //return $o->exam_details;
                $result = $result.$o->sitting."->".$o->exam_details['Exam_body']."-".$o->exam_details['Exam_type']."(".$o->exam_details['Exam_year'].")"."-[English-".$o->exam_details['English'].": ".$o->exam_details['subject_3']['subject_name']."-".$o->exam_details['subject_3']['grade'].": ".$o->exam_details['subject_4']['subject_name']."-".$o->exam_details['subject_4']['grade'].": ".$o->exam_details['subject_5']['subject_name']."-".$o->exam_details['subject_5']['grade']."] ";
            }
            #next sort out the qualifications
            $qualifications = '';
            foreach ($qualf as $q) {
                #return the qualification details
                $qualifications = $qualifications. $q->certificate_type."->".$q->qualification_obtained."(".$q->year_obtained."): [GRADE:-".$q->class."] ";
            }


            $newList->push((object)[
                'sno' => $sno,
                'formnumber' => $k->form_number,
                'name' => $apUser->name,
                'matricno' => $k->form_number,
                'gender' => $apUser->profile->gender,
                'state' => getStateNameById($apUser->profile->state_id),
                'program' => getProgramNameById($k->program_id),
                'Dept' => getProgrammeDetailById($k->program_id, 'all')->department->name,
                'country' => $apUser->profile->nationality,
                'olevel' => $result,
                'qualification' => $qualifications
            ]);

            $sno++;
        }

        return $newList;


    }


    public function headings(): array{

        return ['sno', 'formnumber','name','matricno','gender','state','program','Dept','country','olevel','qualification'];

    }


}
