<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefreeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'referee_email' => 'sometimes|required|email',
            'referee_name' => 'required',
            'phone' => 'sometimes|required|digits:11',
            'candidate_referee_relationship_years' => 'sometimes|required|integer|min:1952|max:'.date("Y"),
            'candidate_relationship' => 'sometimes|required',
            'intellectual_ability' => 'sometimes|required',
            'capacity_for_persistent_academic_study' => 'sometimes|required',
            'capacity_for_independent_academic_study' => 'sometimes|required',
            'ability_for_imaginative_thought' => 'sometimes|required',
            'ability_for_oral_expression_in_english' => 'sometimes|required',
            'ability_for_written_expression_in_english' => 'sometimes|required',
            'candidate_rank_academically_among_students_in_last_five_years' => 'sometimes|required|integer',
            'candidate_morally_upright' => 'sometimes|required|in:yes,no',
            'candidate_emotionally_stable' => 'sometimes|required|in:yes,no',
            'candidate_physically_fit' => 'sometimes|required|in:yes,no',
            'accept_candidate_for_research' => 'sometimes|required|in:yes,no',
            'reason_for_rejecting_candidate_for_research' => 'required_if:accept_candidate_for_research,no',
            'general_comment' => 'nullable',
        ];
    }
}
