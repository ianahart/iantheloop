<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\CustomValidator;

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

        $customValidator = new CustomValidator($this->all());
        $customValidator->setFormName('');
        $customValidator->generateLinkRules();

        return array_merge(
            [
                // general
                'town' => ['nullable'],
                'display_name' => ['required', 'alpha_num', 'max:50'],
                'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
                //general
                //about
                'bio' => ['nullable', 'max:500', 'regex:/^[\.a-zA-Z0-9,!?\' ]*$/'],
                'interests.*.name' => ['nullable', 'max:75', 'min:2', 'regex:/(^[A-Za-z0-9 ]+$)+/'],
                //about
                //work
                'company' => ['nullable', 'max:100'],
                'position' => ['nullable', 'max:50'],
                'work_city' => ['nullable', 'max:100'],
                'description' => ['nullable', 'max:300', 'regex:/^[\.a-zA-Z0-9,!? ]*$/'],
                //work
                //pictures
                'background_picture' => ['nullable', 'mimes:jpg,bmp,png', 'max:2001'],
                'profile_picture' => ['nullable', 'mimes:jpg,bmp,png', 'max: 2001'],
                //pictures
            ],
            $customValidator->getRules(),
        );
    }

    /**
     * Return custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        $customValidator = new CustomValidator($this->all());
        $customValidator->setFormName('');
        $customValidator->generateLinkMessages();

        return array_merge(
            [
                //general
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
                'position.max' => 'The maximum number of characters for the position is (50) characters',
                'work_city.max' => 'The maximum number of characters for the city/town is (100) characters',
                'description.regex' => 'Please use only letters, numbers, spaces, and punctuation',
                'description.max' => 'The maximum number of characters for the description is (300)',
                //work
                //pictures
                'background_picture.mimes' => 'Background picture must be an image(jpg, png)',
                'background_picture.max' => 'Background picture must be under 2MB',
                'profile_picture.mimes' => 'Profile picture must be an image(jpg, png)',
                'profile_picture.max' => 'Profile picture must be under 2MB',
                // pictures
            ],
            $customValidator->getMessages(),
        );
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
