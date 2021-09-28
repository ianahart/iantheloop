<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return JWTAuth::user()->id === intval($this->user_id);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search_value' => ['max:200', 'regex:/^[A-Za-z\s.-]+$/'],
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
            'search_value.max' => 'Please shorten your search max:(200 characters)',
            'search_value.regex' => 'Letters, spaces, periods, and hyphens are allowed.',
        ];
    }
}
