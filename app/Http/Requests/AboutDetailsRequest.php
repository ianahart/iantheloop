<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutDetailsRequest extends FormRequest
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
            'aboutDetails.bio' => ['nullable', 'max:300', 'regex:/(^[A-Za-z0-9 ]+$)+/'],
            'aboutDetails.interests.*.name' => ['nullable', 'max:75', 'min:2', 'regex:/(^[A-Za-z0-9 ]+$)+/'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'aboutDetails.bio.max' => 'The maximum number of characters for the bio is (300)',
            'aboutDetails.bio.regex' => 'Please use only letters, numbers, and spaces',
            'aboutDetails.interests.*.name.min' => 'The minimum number of characters for an interest is (2)',
            'aboutDetails.interests.*.name.max' => 'The maximum number of characters for an interest is (75)',
            'aboutDetails.interests.*.name.regex' => 'Please use only letters, numbers, and spaces',
        ];
    }
}
