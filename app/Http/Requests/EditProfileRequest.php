<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{

    public $urlLinks = [];
    public $linkKeys;

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

        foreach ($this->all() as $key => $val) {

            if (str_contains($key, 'url-')) {

                $this->urlLinks[$key] = ['nullable', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/', 'max:70'];
            }
        }

        return array_merge(
            [
                // general
                'town' => ['nullable', 'regex:/^[\pL\s\-]+$/u'],
                'display_name' => ['required', 'alpha_num', 'max:50'],
                'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
                //general
                //about
                'bio' => ['nullable', 'max:500', 'regex:/^[\.a-zA-Z0-9,!?\' ]*$/'],
                'interests.*.name' => ['nullable', 'max:75', 'min:2', 'regex:/(^[A-Za-z0-9 ]+$)+/'],
                //about
                //work
                'company' => ['nullable', 'max:100', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
                'position' => ['nullable', 'max:50', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
                'work_city' => ['nullable', 'max:100', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
                'description' => ['nullable', 'max:300', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
                //work
                //pictures
                'background_picture' => ['nullable', 'mimes:jpg,bmp,png', 'max:2001'],
                'profile_picture' => ['nullable', 'mimes:jpg,bmp,png', 'max: 2001'],
                //pictures
            ],
            $this->urlLinks
        );
    }

    /**
     * Return custom validation messages.
     *
     * @return array
     */
    public function messages()
    {

        foreach ($this->all() as $key => $val) {

            if (str_contains($key, 'url-')) {

                $this->linkKeys[$key . '.' . 'max'] = 'Maximum allowed characters is 50';
                $this->linkKeys[$key . '.' . 'regex'] = 'The URL format is invalid';
            }
        }
        $this->linkKeys = isset($this->linkKeys) ? $this->linkKeys : [];

        return array_merge([
            //general
            'town.regex' => 'Letters, spaces, and hyphens allowed',
            'display_name.alpha_num' => 'Letters and numbers allowed',
            'display_name.max' => 'Maximum of 50 characters',
            'display_name.required' => 'Please provide a display name',
            'phone.regex' => 'Please provide a valid phone number',
            'phone.min' => 'Phone number must be at least 10 characters',
            //general
            //about
            'bio.max' => 'The maximum number of characters for the bio is (500)',
            'bio.regex' => 'Please use only letters, numbers, spaces, apostrophes, and punctuation',
            'interests.*.name.min' => 'The minimum number of characters for an interest is (2)',
            'interests.*.name.max' => 'The maximum number of characters for an interest is (75)',
            'interests.*.name.regex' => 'Please use only letters, numbers, and spaces',
            //about
            // work
            'company.max' => 'The maximum number of characters for the company is (100) characters',
            'company.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'position.max' => 'The maximum number of characters for the position is (50) characters',
            'position.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'work_city.max' => 'The maximum number of characters for the city/town is (100) characters',
            'work_city.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'description.regex' => 'Please use only letters, numbers, spaces, and punctuation',
            'description.max' => 'The maximum number of characters for the description is (300)',
            //work
            //pictures
            'background_picture.mimes' => 'Background picture must be an image(jpg, png)',
            'background_picture.max' => 'Background picture must be under 2MB',
            'profile_picture.mimes' => 'Profile picture must be an image(jpg, png)',
            'profile_picture.max' => 'Profile picture must be under 2MB',
            // pictures
        ], $this->linkKeys);
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
