<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreControlUser extends FormRequest
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
            'name'     => 'required|string|max:200',
            'user'     => 'required|string|min:5|max:50',
            'password' => 'required|string|min:8',
            'email'    => 'required|email:rfc,dns'
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
            'name'     => 'Please enter a valid name',
            'user'     => 'Please enter a valid control user name with at least 5 characters',
            'password' => 'You password must contain at least 8 characters',
            'email'    => 'Please enter a valid email address'
        ];
    }
}
