<?php

namespace App\Services\Profile;

use App\Mail\RefereeRequestMail;
use App\Models\OlevelCard;
use App\Models\OlevelResult;
use App\Models\UserProfile;
use App\Models\UserReferee;
use App\Services\BaseService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreOlevelService extends BaseService implements ServiceInterface
{

    public $data;
    public $user;
    public $action;

    public function __construct(array $data, $user, $action)
    {
        $this->data = $data;
        $this->user = $user;
        $this->action = $action;
    }

    public function run()
    {
        $data = $this->data;
        if($this->action === 'card'){
           $olevelCard = OlevelCard::updateOrCreate(['user_id' => $this->user->id, 'sitting' => $this->data['sitting']], [
               'exam_year' => $data['exam_year'],
               'exam_body' => $data['exam_body'],
               'exam_type' => $data['exam_type'],
               'sitting' => $data['sitting'],
               'card_pin' => $data['card_pin'],
               'card_serial_no' => $data['card_serial_no'],
           ]);

        }else {

            $examDetails = [];
            $examDetails['Exam_type'] = $data['exam_type'];
            $examDetails['Exam_body'] = $data['exam_body'];
            $examDetails['Exam_year'] = $data['exam_year'];
            $examDetails['Mathematics'] = $data['mathematics'];
            $examDetails['English'] = $data['english'];
            $examDetails['subject_3'] =  ["subject_name" => $data['subject_3'], "grade" => $data['subject_3_grade']];
            $examDetails['subject_4'] =  ["subject_name" => $data['subject_4'], "grade" => $data['subject_4_grade']];
            $examDetails['subject_5'] =  ["subject_name" => $data['subject_5'], "grade" => $data['subject_5_grade']];

            $oLevel = OlevelResult::updateOrCreate(['user_id' => $this->user->id, 'sitting' => $data['sitting']], [
                'user_id' => $this->user->id,
                'exam_details' => $examDetails,
                'sitting' => $data['sitting'],
            ]);
        }

    }
}


/**
 * Is The Candidate Morally Upright?
Is The Candidate Emotionally Stable?
Is The Candidate Physically Fit?
Assuming You are a Researcher, would
you accept the Candidate as a Research Student?
If No is selected for the last question, please briefly
 */


