<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      EditServerRequest.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   EditServerRequest.php
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
            'active'        => 'required|boolean',
            'host'          => 'required|string|max:200',
            'id'            => 'sometimes|integer',
            'mongo_cloud'   => 'required|boolean',
            'mongo_cloud_database'   => 'required_if:mongo_cloud,true|string|nullable',
            'password'      => 'sometimes|string|min:5',
            'port'          => 'required|integer|min:5',
            'username'      => 'required|string|min:5|max:100',
            'user_id'       => 'sometimes|integer'
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
            'username' => 'Please enter a valid username',
            'password' => 'Your password must contain at least 5 characters',
            'active'   => 'A valid configuration status was not found',
            'user_id'  => 'The selected user ID is invalid'
        ];
    }
}
