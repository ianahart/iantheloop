<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


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

        $this->urlLinks = $this->all();

        $this->urlLinks = array_filter(
            $this->urlLinks,
            function ($field, $key) {

                return str_contains($key, 'url-') ? $field : '';
            },
            ARRAY_FILTER_USE_BOTH
        );
        $rules = [];

        foreach ($this->urlLinks as $key => $val) {

            $rules['generalDetails.' . $key] = ['nullable', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/', 'max:70'];
        }

        return array_merge(
            [

                'generalDetails.town' => ['nullable', 'regex:/^[\pL\s\-]+$/u'],
                'generalDetails.displayname' => ['nullable', 'alpha_num', 'max:50'],
                'generalDetails.phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            ],
            $rules
        );
    }

    /**
     * Return custom validation messages.
     *
     * @return array
     */

    public function messages()
    {

        $linkMessages = [];

        $this->linkKeys = array_keys($this->all());

        $this->linkKeys = array_map(
            function ($linkKey) {

                if (str_contains($linkKey, 'url-')) {

                    return explode('-', $linkKey)[1];
                }
            },
            $this->linkKeys
        );

        $this->linkKeys = array_values(array_filter(
            $this->linkKeys,
            function ($item) {

                return isset($item) ? $item : '';
            }
        ));

        for ($i = 0; $i < count($this->linkKeys); $i++) {

            $linkMessages['generalDetails.url-' . $this->linkKeys[$i] . '.max'] = 'Maximum allowed characters is 50';

            $linkMessages['generalDetails.url-' . $this->linkKeys[$i] . '.regex'] = 'The URL format is invalid';
        }

        return array_merge(
            [

                'generalDetails.town.regex' => 'Letters, spaces, and hyphens allowed',
                'generalDetails.displayname.alpha_num' => 'Letters and numbers allowed',
                'generalDetails.displayname.max' => 'Maximum of 50 characters',
                'generalDetails.phone.regex' => 'Please provide a valid phone number',
                'generalDetails.phone.min' => 'Phone number must be at least 10 characters',
            ],
            $linkMessages
        );
    }
}
