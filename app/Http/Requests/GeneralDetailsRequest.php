<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\CustomValidator;


class GeneralDetailsRequest extends FormRequest
{

    public $urlLinks;
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
     * Return the name of the form.
     *
     * @return string
     */

    public static function getFormName()
    {
        return 'generalDetails';
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {

        $customValidator = new CustomValidator($this->all());
        $customValidator->setFormName('generalDetails');
        $customValidator->generateLinkRules();

        return array_merge(
            [

                'generalDetails.town' => ['nullable', 'regex:/^[\pL\s\-]+$/u'],
                'generalDetails.display_name' => ['required', 'alpha_num', 'max:50'],
                'generalDetails.phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            ],
            // $rules
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
        $customValidator->setFormName('generalDetails');
        $customValidator->generateLinkMessages();

        return array_merge(
            [

                'generalDetails.town.regex' => 'Letters, spaces, and hyphens allowed',
                'generalDetails.display_name.alpha_num' => 'Letters and numbers allowed',
                'generalDetails.display_name.max' => 'Maximum of 50 characters',
                'generalDetails.display_name.required' => 'Please provide a display name',
                'generalDetails.phone.regex' => 'Please provide a valid phone number',
                'generalDetails.phone.min' => 'Phone number must be at least 10 characters',
            ],
            // $linkMessages
            $customValidator->getMessages(),
        );
    }
}
