<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Http\Requests\GeneralDetailsRequest;
use App\Http\Requests\AboutDetailsRequest;
use App\Http\Requests\WorkDetailsRequest;


class StoreMultipleForm extends FormRequest
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

        $formRequests = [
            'generalDetails' =>
            GeneralDetailsRequest::class,
            'aboutDetails' => AboutDetailsRequest::class,
            'workDetails' => WorkDetailsRequest::class,

        ];
        $rules = [];

        foreach ($formRequests as $key => $source) {


            $rules = array_merge(
                $rules,
                (new $source($this->input($key)))->rules()
            );
        }

        return $rules;
    }

    /**
     * Return custom validation messages.
     *
     * @return array
     */
    public function messages()
    {

        $formRequests = [
            'generalDetails' =>
            GeneralDetailsRequest::class,
            'aboutDetails' => AboutDetailsRequest::class,
            'workDetails' => WorkDetailsRequest::class,

        ];

        $messages = [];

        foreach ($formRequests as $key => $source) {

            $messages = array_merge(
                $messages,

                (new $source($this->input($key)))->messages()
            );
        }

        return $messages;
    }
}
