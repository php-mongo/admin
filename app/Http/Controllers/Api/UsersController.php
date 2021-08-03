<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      UsersController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   UsersController.php
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

/*
  Defines a namespace for the controller.
*/
namespace App\Http\Controllers\Api;

/*
  Uses the controller interface.
*/

use App\Helpers\UserHelper;

/*
  Defines the requests used by the controller.
*/
use Illuminate\Http\Request;
use App\Http\Requests\EditUserRequest;

/*
  Defines the models used by the controller.
*/
use App\Models\User;

/*
  Defines the facades etc used by the controller.
*/
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Validation\ValidationException;
use Mockery\Exception;

/**
 * The user controller handles all API requests that manage user data.
 */
class UsersController
{
    /*
    |------------------------------------------------- ------------------------------
    | Get User
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Method:         GET
    | Description:    Gets the authenticated user
    | @param  EditUserRequest   $request
    | @return \Illuminate\Http\JsonResponse
    */
    public function getUser(Request $request)
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();
            if ($user) {
                $attributes = $user->getAttributes();
                if (isset($attributes['id'])) {
                    return response()->json($attributes);
                }
            }
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }
        catch (\Exception $e) {
            return response()->json(array());
        }
    }

    /*
    |-------------------------------------------------------------------------------
    | Get Users
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/users
    | Method:         GET
    | Description:    Gets the users searched by the authenticated user.
    | @param  EditUserRequest   $request
    | @return \Illuminate\Http\JsonResponse
    */
    public function getUsers(Request $request)
    {
        try {
            /**
             * The current use must be an Admin
             * ToDo: create a reusable AUTHORIZATION service to handle these tasks
             */
            /** @var User $user */
            $user = auth('api')->user();
            $attributes = $user->getAttributes();

            //dd($user);
           // dd($attributes);s
            //dd($attributes['admin_user']);

            if (isset($attributes['admin_user']) && (int) $attributes['admin_user'] === 1) {
                $query = $request->get('search');
                $loginUsers = User::all();
                return response()->success('success', array('users' => array('loginUsers' =>  $loginUsers )));
            }
            return response()->error('failed', array('error' => 'Forbidden'), 403);
        }
        catch (\Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /*
    |-------------------------------------------------------------------------------
    | Create a User's Profile
    |-------------------------------------------------------------------------------
    | URL:              /api/v1/user
    | Method:           POST
    | Description:      Create a new login/database user's profile
    | @param  Request   $request
    | @return \Illuminate\Http\JsonResponse
    */
    public function postUser( Request $request )
    {
        try {
            $params = $request->get('params', []);
            // validate
            $result = $this->validate( $params, array(
                'name' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ));

            // create user
            $user = $this->create( $result );
           //event(new Registered($user = $this->create($request->all())));

            $token =  $user->createToken('AdultMuse')->accessToken;

            // auto login
            $this->guard()->login($user);

            // set the account member cookie
            $llct     = time()+60*60*24*180;
            Cookie::queue('app-member', $user->id, $llct, false, false, false, false);

            /*
            *   Return a response that the user was updated successfully.
            */
            return response()->json( ['user_created' => true, 'token' => $token], 201 );

        }
        catch (ValidationException $e) {
            return response()->json(array());
        }
        catch( Exception $e) {
            return response()->json(array());
        }
    }

    /*
    |-------------------------------------------------------------------------------
    | Updates a User's Profile
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Method:         PUT
    | Description:    Updates the authenticated user's profile
    | @param  EditUserRequest   $request
    | @return \Illuminate\Http\JsonResponse
    */
    public function updateUser( EditUserRequest $request )
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();
            $attributes = $user->getAttributes();
            if (isset($attributes['id'])) {
                $user = Auth::user();
                $user->save();
                /*
                *   Return a response that the user was updated successfully.
                */
                return response()->json( ['user_updated' => true], 201 );
            }
            return response()->json(['success',false, 'error' => 'Unauthorized'], 401);
        }
        catch (\Exception $e) {
            return response()->json(array());
        }
    }

    /**
     * @param Request $request
     * @param $email
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request, $email)
    {
        try {
            if ($email) {
                $users = User::where('username', 'LIKE', '%'.$email.'%')
                    ->orWhere('email', 'LIKE', '%'.$email.'%')
                    ->get();

                if (count($users) > 0) {
                    $result = array("success" => true, "record" => array("email" => array("notFound" => 0, "valid" => $this->validEmail($email))));

                } else {
                    $result = array("success" => true, "record" => array("email" => array("notFound" => 1, "valid" => $this->validEmail($email))));
                }
                return response()->success('success', array('result' => $result));
            }
            return response()->json(array());
        }
        catch (\Exception $e) {
            return response()->json(array());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLocation(Request $request)
    {
        try {
            $location = UserHelper::getIPInfo($request);
            return response()->success('success', array('location' => $location->all));
        }
        catch(\Exception $e) {
            return response()->json(array());
        }
    }

    /**
     * @param $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStates($country)
    {
        try {
            $states = UserHelper::getUserStates($country);
            if ($states) {
                return response()->success('success', array('states' => $states[strtoupper($country)]));
            }
            return response()->json( );
        }
        catch(\Exception $e) {
            //dd($e->getMessage());
            return response()->json(array());
        }
    }
}
