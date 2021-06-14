<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'input' => 'min:1|max:255',
        ];
    }

    /**
     * Create custom messages for the rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'input.min' => 'The comment cannot be empty',
            'input.max' => 'Please keep comment under 255 characters',
        ];
    }
}
