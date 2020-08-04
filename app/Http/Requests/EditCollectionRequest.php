<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCollectionRequest extends FormRequest
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
            'database' => 'required|string',
            'capped'   => 'sometimes|boolean',
            'size'     => 'sometimes|integer|min:1|max:10240000',
            'count'    => 'sometimes|integer|min:1|max:1000000'
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
            'name'     => 'A collection name must be provided',
            'database' => 'A database name is required to crrate a collection',
            'capped'   => 'Capped must be a boolean value',
            'size'     => 'Size must be an integer greater than 1',
            'count'    => 'Count must be an integer greater than 1'
        ];
    }
}
