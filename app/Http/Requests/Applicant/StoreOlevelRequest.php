<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class StoreOlevelRequest extends FormRequest
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
        $availableGrades = ['A1', 'B2', 'B3', 'C4', 'C5', 'C6', 'D7', 'E8', 'F9', 'AR'];
        return [
            'exam_year' => 'required|integer|min:1952|max:'.date("Y"),
            'exam_type' => 'required',
            'english' => 'required|in:'. implode(',', $availableGrades),
            'mathematics' => 'required|in:'. implode(',', $availableGrades),
            'subject_3' => 'required',
            'subject_4' => 'required',
            'subject_5' => 'required',
            'subject_3_grade' => 'required|in:'. implode(',', $availableGrades),
            'subject_4_grade' => 'required|in:'. implode(',', $availableGrades),
            'subject_5_grade' => 'required|in:'. implode(',', $availableGrades),
            'sitting' => 'required|in:first,second',
        ];
    }

    public function messages()
    {
        return [
            'exam_year.min' => 'The exam year must start from atleast 1952',
            'exam_year.max' => 'The maximum date for olevel result is '.date("Y"),

        ];
    }
}
