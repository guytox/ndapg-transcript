<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class StoreOlevelCardRequest extends FormRequest
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
            'exam_year' => 'required|integer|min:1952|max:'.date("Y"),
            'sitting'=> 'required|in:first,second',
            'card_pin' => 'required',
            'card_serial_no' => 'required',
            'exam_type' => 'required'
        ];
    }
}
