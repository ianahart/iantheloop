<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Http\Requests\GeneralDetailsRequest;
use App\Http\Requests\AboutDetailsRequest;
use App\Http\Requests\WorkDetailsRequest;
use App\Http\Requests\CustomizeRequest;


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
        $customize = ['customize' => [
            'backgroundfile' => request()->file('backgroundfile'),
            'profilefile' => request()->file('profilefile'),
            'backgroundsrc' => request()->input('backgroundsrc'),
            'profilesrc' => request()->input('profilesrc'),
        ]];

        $formRequests = [
            'generalDetails' =>
            GeneralDetailsRequest::class,
            'aboutDetails' => AboutDetailsRequest::class,
            'workDetails' => WorkDetailsRequest::class,
            'customize' => CustomizeRequest::class,

        ];

        $rules = [];
        $instance = null;
        foreach ($formRequests as $key => $source) {

            $data =  $key === 'customize' ? $customize : $this->input($key);

            $instance = (new $source($data))->rules();

            $rules = array_merge(
                $rules,
                $instance,
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

        $customize = ['customize' => [
            'backgroundfile' => request()->file('backgroundfile'),
            'profilefile' => request()->file('profilefile'),
            'backgroundsrc' => request()->input('backgroundsrc'),
            'profilesrc' => request()->input('profilesrc'),
        ]];

        $formRequests = [
            'generalDetails' =>
            GeneralDetailsRequest::class,
            'aboutDetails' => AboutDetailsRequest::class,
            'workDetails' => WorkDetailsRequest::class,
            'customize' => CustomizeRequest::class,

        ];

        $messages = [];
        $instance = null;
        foreach ($formRequests as $key => $source) {

            $data =  $key === 'customize' ? $customize : $this->input($key);

            $instance = (new $source($data))->messages();


            $messages = array_merge(
                $messages,
                $instance,
            );
        }
        return $messages;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {

        $this->merge(
            json_decode(request()->input('data'), true),
        );
    }
}
