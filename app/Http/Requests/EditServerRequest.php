<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      EditServerRequest.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      EditServerRequest.php
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
