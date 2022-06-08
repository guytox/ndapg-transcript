<?php

namespace App\Http\Requests;

use App\Models\State;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileStoreRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->merge([
        'state_of_origin' => $this->nationality !== 'Nigeria' ? 38 : $this->state_of_origin
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $acceptableAge = Carbon::parse(now())->subYears(15)->toDateString();
        return [
            'marital_status' => 'sometimes|required|in:single,married,divorced',
            'dob' => 'sometimes|required|date|before:' . $acceptableAge,
            'nationality' => 'sometimes|required',
            'town' => 'sometimes|required',
            'extra_curricular' => 'sometimes|required',
            'state_of_origin' => [
                'sometimes',
                'required_if:nationality,Nigeria',
                'exists:states,id'
            ],
            'local_government' => ['sometimes', 'required_if:nationality,Nigeria'],
            'gender' => 'sometimes|required',
            'contact_address' => 'sometimes|required',
            'permanent_home_address' => 'sometimes|required',
        ];
    }
}
