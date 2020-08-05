<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      EditCollectionRequest.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      EditCollectionRequest.php
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See COPYRIGHT.php for copyright notices and further details.
 */

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
