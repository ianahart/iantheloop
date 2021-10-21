<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;


class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return JWTAuth::user()->id === intval($this->current_user_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currentUser = JWTAuth::user();

        return [
            'form.old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($currentUser) {
                    if (!Hash::check($value, $currentUser->password)) {
                        $fail('The password you provided does not match your current password');
                    }
                }
            ],
            'form.password' => [
                'required',
                'confirmed',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
                'different:form.old_password'
            ],
            'form.password_confirmation' => 'required|min:6',
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
            'form.old_password.required' => 'Your current password is required',
            'form.password.required' => 'A new password is required',
            'form.password.confirmed' => 'New password and confirmed password must match',
            'form.password.min' => 'New password must be at least 6 characters',
            'form.password.regex' => 'Password must include 1 uppercase letter, 1 lowercase letter, 1 digit, and 1 special character',
            'form.password.different' => 'Your new password has to be different from your current password',
            'form.password_confirmation.required' => 'New password confirmation is required',
            'form.password_confirmation.min' => 'Password confirmation must be at least 6 characters',
        ];
    }
}
