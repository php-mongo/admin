<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditServerRequest extends FormRequest
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
            'host'     => 'required|string|max:200',
            'port'     => 'required|integer|min:5',
            'username' => 'required|string|min:5|max:100',
            'active'   => 'required|boolean',
            'id'       => 'sometimes|integer',
            'password' => 'sometimes|string|min:5',
            'user_id'  => 'sometimes|integer'
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
            'host'     => 'Please enter a valid db server host name',
            'port'     => 'Please enter a valid mongodb port value',
            'username' => 'Please enter a valide username',
            'password' => 'Your password must contain at least 5 characters',
            'active'   => 'A valid configuration status was not found',
            'user_id'  => 'The selected user ID is invalid'
        ];
    }
}
