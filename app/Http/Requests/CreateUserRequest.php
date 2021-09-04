<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      EditUserRequest.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   EditUserRequest.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'active' => 'required|boolean',
            'database' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users',
            'isAdmin' => 'sometimes|boolean',
            'name' => 'required_if:type,login|required_if:type,both|string|max:200',
            'password' => 'required|string|min:5',
            'password2' => 'required|same:password|string|min:5',
            'profile_visibility' => 'sometimes|boolean',
            'roles' => 'required_if:type,database|required_if:type,both|array',
            'type' => 'required|string',
            'user' => 'required|string|unique:users|min:3|max:100',
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
            'active' => 'Please ensure users Active state has been selected',
            'email' => 'Please enter a valid email address',
            'isAdmin' => 'The Is Admin value is invalid',
            'name' => 'Please enter a valid name',
            'password' => 'Your password must contain at least 5 characters',
            'password2' => 'Your password did not match',
            'profile_visibility.boolean' => 'The profile visibility flag needs to be a boolean',
            'roles' => 'Roles must be provided for database users',
            'type' => 'User type is not valid',
            'user' => 'Please provide a valid user name',
            'user.unique' => 'The user-name provided already exists as an application user',
        ];
    }
}
