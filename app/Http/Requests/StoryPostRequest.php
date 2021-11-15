<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoryPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('sanctum')->user()->id === intval($this->user_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['nullable', 'mimes:jpg,bmp,png', 'max:2300'],
            'text' => ['max:150', 'regex:/^[\.a-zA-Z0-9,!?\' ]*$/'],
        ];
    }

    /**
     * Return custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return
            [
                'file.mimes' => 'Your story photo must be an image of type (jpg, png).',
                'file.max' => 'Your story photo must be under 2MB.',
                'text.max' => 'Your story text must be under 150 characters.',
                'text.regex' => 'Please use only letters, numbers, and [ . ? ! ]'
            ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = json_decode($this->all()['data'], true);
        $this->merge(
            $data,
        );
    }
}
