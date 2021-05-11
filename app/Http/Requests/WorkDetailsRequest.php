<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkDetailsRequest extends FormRequest
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
            'workDetails.company' => ['nullable', 'max:100', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
            'workDetails.position' => ['nullable', 'max:50', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
            'workDetails.work_city' => ['nullable', 'max:100', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
            'workDetails.description' => ['nullable', 'max:300', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
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
            'workDetails.company.max' => 'The maximum number of characters for the company is (100) characters',
            'workDetails.company.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'workDetails.position.max' => 'The maximum number of characters for the position is (50) characters',
            'workDetails.position.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'workDetails.work_city.max' => 'The maximum number of characters for the city/town is (100) characters',
            'workDetails.work_city.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'workDetails.description.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'workDetails.description.max' => 'The maximum number of characters for the description is (300)',

        ];
    }
}
