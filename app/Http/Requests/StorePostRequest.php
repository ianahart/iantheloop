<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'photofile' => ['nullable', 'mimes:jpg,bmp,png', 'max:2001'],
            'videofile' => ['nullable', 'mimes:mp4,x-flv,x-mpegURL,MP2T,3gpp,qt,x-msvideo,x-ms-wmv', 'max:4100'],
            'post_text' => ['max:500'],
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
            'photofile.mimes' => 'Photo must be an image(jpg, png)',
            'photofile.max' => 'Photo must not exceed 2MB (megabytes)',
            'videofile.mimes' => 'video format is unsupported, please try a different format',
            'videofile.max' => 'Video must not exceed 4MB (megabytes)',
            'post_text.max' => 'Post body must not exceed 500 characters',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {

        $data = json_decode($this->request->all()['data'], true);

        error_log(print_r($this->request->all(), true));
        $this->merge(
            $data,
        );
    }
}
