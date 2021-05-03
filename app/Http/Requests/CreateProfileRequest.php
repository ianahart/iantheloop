<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'formData.fieldname' => ['required', 'min:2', 'max:40', 'regex:/^[a-zA-Z\s]+$/'],

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
            'formData.fieldname.required' => 'First name is required',

        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        error_log(print_r($this->all(), true));

        // get single input value
        // need to figure out how to get all the inputs that are name="url"
        // and apply the same validation to all of them
        error_log(print_r($this->input('formData.firstName'), true));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            // 'slug' => Str::slug($this->slug),
        ]);
    }
}
