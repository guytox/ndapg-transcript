<?php

namespace App\Services\Profile;

use App\Mail\RefereeRequestMail;
use App\Models\UserProfile;
use App\Models\UserReferee;
use App\Services\BaseService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class StoreRefereeService extends BaseService implements ServiceInterface
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
        if($this->action === 'applicant'){

            $refereeExist = UserReferee::where(['user_id' => $this->user->id, 'email' => $data['referee_email'], 'is_filled' => false])->first();
            if($refereeExist === null) {
               $referee = UserReferee::create([
                   'user_id' => $this->user->id,
                   'email' => $data['referee_email'],
                   'name' => $data['referee_name'],
                   'is_filled' => false
               ]);

               $link = route('referee.view_details', $referee->uid);

               Log::info($link);

               $name = $data['referee_name'];

               // send email to referee with unique id

             

               try {
                Mail::to($data['referee_email'])->send(new RefereeRequestMail($this->user, $link, $name));

                return redirect()->route('applicant.referee')->with(['success' => 'refree added successfully']);

               }catch(\Throwable $e) {
                return redirect()->route('applicant.referee')->with(['error' => 'an error occured adding referee']);  
               }
            }else{
                return redirect()->back()->with(['success', 'refree added successfully']);
            }
        }else {
            UserReferee::where('uid', $data['uid'])->update([
                'name' => $data['referee_name'],
                'phone' => $data['phone'],
                'candidate_referee_relationship_years' => $data['candidate_referee_relationship_years'],
                'candidate_relationship' => $data['candidate_relationship'],
                'intellectual_ability' => $data['intellectual_ability'],
                'capacity_for_persistent_academic_study' => $data['capacity_for_persistent_academic_study'],
                'capacity_for_independent_academic_study' => $data['capacity_for_independent_academic_study'],
                'ability_for_imaginative_thought' => $data['ability_for_imaginative_thought'],
                'ability_for_oral_expression_in_english' => $data['ability_for_oral_expression_in_english'],
                'ability_for_written_expression_in_english' => $data['ability_for_written_expression_in_english'],
                'candidate_rank_academically_among_students_in_last_five_years' => $data['candidate_rank_academically_among_students_in_last_five_years'],
                'candidate_morally_upright' => $data['candidate_morally_upright'],
                'candidate_emotionally_stable' => $data['candidate_emotionally_stable'],
                'candidate_physically_fit' => $data['candidate_physically_fit'],
                'accept_candidate_for_research' => $data['accept_candidate_for_research'],
                'reason_for_rejecting_candidate_for_research' => $data['reason_for_rejecting_candidate_for_research'],
                'general_comment' => $data['reason_for_rejecting_candidate_for_research'],
                'is_filled' => true,
            ]);

            return UserReferee::where('uid', $data['uid'])->first();
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


