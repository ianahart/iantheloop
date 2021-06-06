<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomizeRequest extends FormRequest
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
        $rules = [];


        return [
            'backgroundfile' => ['nullable', 'mimes:jpg,bmp,png', 'max:2001'],
            'profilefile' => ['nullable', 'mimes:jpg,bmp,png', 'max: 2001'],
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
            'backgroundfile.mimes' => 'Background picture must be an image(jpg, png)',
            'backgroundfile.max' => 'Background picture must be under 2MB',
            'profilefile.mimes' => 'Profile picture must be an image(jpg, png)',
            'profilefile.max' => 'Profile picture must be under 2MB',
        ];
    }
}
