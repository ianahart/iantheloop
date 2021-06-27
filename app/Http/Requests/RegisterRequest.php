<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class RegisterRequest extends FormRequest
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

            'formData.firstName' => ['required', 'min:2', 'max:40', 'regex:/^[a-zA-Z .]+$/'],
            'formData.lastName' => ['required', 'min:2', 'max:80', 'regex:/^[a-zA-Z .]+$/'],
            'formData.email' => 'required|email',
            'formData.password' => ['required', 'confirmed', 'min:6', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'],
            'formData.password_confirmation' => 'required|min:6',

        ];
    }

    public function messages()
    {

        return [

            'formData.firstName.required' => 'First name is required',
            'formData.firstName.min' => 'First name must be at least 2 characters',
            'formData.firstName.max' => 'First name cannot exceed 40 characters',
            'formData.firstName.regex' => 'First name can only include letters and spaces',

            'formData.lastName.required' => 'Last name is required',
            'formData.lastName.min' => 'Last name must be at least 2 characters',
            'formData.lastName.max' => 'Last name cannot exceed 80 characters',
            'formData.lastName.regex' => 'Last name can only include letters and spaces',

            'formData.email.required' => 'Email is required',
            'formData.email.email' => 'Email is not a valid email address',

            'formData.password.required' => 'Password is required',
            'formData.password.confirmed' => 'Password must match your confirm password',
            'formData.password.min' => 'Password must be at least 6 characters',
            'formData.password.regex' => 'Password must include 1 uppercase letter, 1 lowercase letter, 1 digit, and 1 special character',

            'formData.password_confirmation.required' => 'Confirming password is required',
            'formData.password_confirmation.min' => 'Confirmed password must be at least 6 characters',
        ];
    }

    public function filters()
    {

        return [

            'firstName' => 'trim|lowercase',
            'lastName' => 'trim|lowercase',
            'email' => 'trim',
            'password' => 'trim',
            'password_confirmation' => 'trim',
        ];
    }
}
