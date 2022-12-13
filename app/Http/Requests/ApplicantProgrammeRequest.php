<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicantProgrammeRequest extends FormRequest
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
            'programme' => ['required', 'exists:programs,id'],
            'service_status' => 'sometimes|required|in:"1","0"',
            'service_number' => 'required_if:service_status,"1"',
            'service_rank' => 'required_if:service_status,"1"',
        ];
    }
}
