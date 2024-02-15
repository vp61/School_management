<?php

/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 7/25/2017
 * Time: 7:12 AM
 */
namespace App\Http\Requests\Transport\User;

use Illuminate\Foundation\Http\FormRequest;

class AddValidation extends FormRequest
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
            'user_type'         => 'required',
            'reg_no'            => 'required',
            'route'             => 'required',
            'vehicle_select'    => 'required',
            'duration'          => 'required',
            'from_date'         => 'required',
            'to_date'           => 'required', 
        ];
    }

   /* public function messages()
    {
        return [
            'title.unique' => 'This Time Already Register',
        ];
    }*/
}
