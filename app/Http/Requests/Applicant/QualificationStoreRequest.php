<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class QualificationStoreRequest extends FormRequest
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
            'certificate_type' => 'sometimes|required',
            'awarding_institution' => 'sometimes|required',
            'qualification_obtained' => 'sometimes|required',
            'certificate_no' => 'sometimes|required',
            'year_obtained' => 'sometimes|date|required',
            'class' => 'nullable',
            'type' => 'sometimes|required|in:school,professional',
            'expiry_date' => 'sometimes|date|required|after:'.$this->year_obtained
        ];
    }
}
