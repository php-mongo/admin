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

/**
 *  Defines the controllers used by controller.
 */

use App\Exceptions\NoServerConfigurationException;
use App\Http\Controllers\Controller;

/**
 *  Defines the requests used by the controller.
 */
use Illuminate\Http\Request;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\CreateUserRequest;

/**
 *  Mongo DB
 */

use Illuminate\Support\Facades\Lang;
use MongoDB;

/**
 *  Defines the models used by the controller.
 */
use App\Models\User;

/**
 *  Defines the facades used by the controller.
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 *  Defined classes used by controller
 */
use App\Http\Classes\MongoConnection as Mongo;
use App\Helpers\UserHelper;
use App\Helpers\MongoHelper;
use Illuminate\Validation\ValidationException;
use App\Exceptions\UnableToDeleteUserException;

/**
 *  Vendor
 */
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * The user controller handles all API requests that manage user data.
 */
class UsersController extends Controller
{
    /**
     * @var MongoDB\Client
     */
    private $client;

    /**
     * @var Mongo
     */
    private $mongo;

    /**
     * @var User
     */
    private $user;

    /**
     * @param array $data
     */
    private function isAdminSetup(array &$data)
    {
        if (isset($data['isAdmin']) && $data['isAdmin'] === true) {
            // set thr roles
            $data['roles'] = array(
                0 => 'dbAdminAnyDatabase',
                1 => 'userAdminAnyDatabase'
            );
            // reset the type
            $data['type'] = 'both';
        }
    }

    /**
     * Used to evaluate restrictions on creating user accounts
     * @return bool
     */
    private function isUserCreateAllowed(): bool
    {
        $env = config('app.env');
        $environments = explode(",", config('app.deny_env_add_users'));
        return empty($environments) || !in_array($env, $environments);
    }

    /**
     * DatabasesController constructor.
     */
    public function __construct()
    {
        try {
            /** @var User $user */
            $this->user = auth()->guard('api')->user();
            $this->mongo = new Mongo($this->user);
            $this->client = $this->mongo->connectAndGetClient();
        } catch (\Throwable $t) {
            Log::debug($t->getMessage());
        }

        if ($this->user && $this->user->exists) {
            parent::__construct($this->user, $this->mongo);
        }
    }

    /**
     * Get current Authenticated User
     *
     * URL:            /api/v1/user
     * Method:         GET
     * Description:    Gets the authenticated user public data
     *
     * @param   Request $request
     * @param   string|null $fetch
     * @param   string|null $type
     * @param   int|null $id
     * @return  Response
     */
    public function read(Request $request, ?string $fetch = null, ?string $type = null, ?int $id = 0): Response
    {
        try {
            if ($this->user && empty($fetch)) {
                $attributes = $this->user->toArray();
                if (isset($attributes['id'])) {
                    $attributes['user_role'] = $this->mongo->getUserRoles();
                    return response()->success('success', $attributes);
                }
            }
            if ($this->user && $this->canAdministerUsers() && isset($fetch, $type, $id)) {
                // todo: set this up to fetch users for editing
            }
            return response()->error('failed', array( 'error' => 'Unauthorized'), 401);

        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /**
     * Get all Users
     * ToDo: currently only available to admin users
     *
     * URL:            /api/v1/user/all
     * Method:         GET
     * Description:    Gets all the users visible by the current authenticated user.
     *
     * @param Request $request
     * @return Response
     * @throws NoServerConfigurationException
     */
    public function index(Request $request): Response
    {
        try {
            /**
             * The current use must be an Admin
             * ToDo: create a reusable AUTHORIZATION service to handle these tasks
             */
            if ($this->canAdministerUsers()) {
                return response()->success(
                    'success',
                    array('users' => array(
                        'loginUsers' => UserHelper::getLoginUsers($request->input('search')),
                        'databaseUsers' => MongoHelper::getMongoDbUsers($this->mongo)
                        )
                    )
                );
            }
            return response()->error('failed', array('error' => 'Forbidden'), 403);

        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /**
     *----------------------------------------------------------------------------
     * Create a User Profile
     *----------------------------------------------------------------------------
     * URL:              /api/v1/user
     * Method:           POST
     * Description:      Create a new login||Database or both user profile
     *
     * @param   CreateUserRequest $request
     * @return  Response
     */
    public function create(CreateUserRequest $request): Response
    {
        try {
            // validate
            $data = $request->validated();

            if ($this->canAdministerUsers($data['database'])) {
                if ($this->isUserCreateAllowed()) {
                    // handle login account creation
                    $loginUser = '';
                    if (in_array($data['type'], ['login','both'])) {
                        $loginUser = UserHelper::generateLoginUser($data);
                    }

                    // this adds the roles for an AdminUser
                    $this->isAdminSetup($data);

                    // handle login account creation
                    $databaseUser = '';
                    if (in_array($data['type'], ['database','both'])) {
                        $databaseUser = MongoHelper::generateMongoDbUser($this->mongo, $data);
                    }

                    // Return the new user profiles.
                    return response()->success(
                        'success',
                        array(
                            "users" => array(
                                'loginUsers' => $loginUser,
                                'databaseUsers' => $databaseUser
                            )
                        )
                    );
                }
                return response()->error('failed', array('error' => trans('users.userCreateDenied')));
            }
            return response()->error('failed', array('error' => 'Forbidden'), 403);

        } catch (ValidationException $e) {
            return response()->error('failed', array('error' => $e->getMessage()));

        } catch (\Throwable $t) {
            return response()->error('failed', array('error' => $t->getMessage()));
        }
    }

    /**
     *-------------------------------------------------------------------------------
     * Updates a User's Profile
     *-------------------------------------------------------------------------------
     * URL:            /api/v1/user
     * Method:         PUT
     * Description:    Updates a users login or mongodb account
     *
     * @param  EditUserRequest   $request
     * @return Response
     */
    public function update(EditUserRequest $request): Response
    {
        try {
            // validate
            $data = $request->validated();
            if ($this->canAdministerUsers() || $data['id'] == $this->user->toArray()['id']) {
                $arr = array('keys' => null);
                // logins
                if ($data['type'] === 'login') {
                    $arr = UserHelper::updateLoginUser($data);
                }

                // mongostos
                if ($data['type'] === 'database') {
                    $arr = MongoHelper::updateMongodbUser($this->mongo, $data);
                }

                /*
                *   Return success with string of parameter keys.
                */
                return response()->success(
                    'success',
                    array(
                        'user' => array(
                            'updated' => implode(",", $arr['keys']),
                            'user' => $arr['user']
                        )
                    )
                );
            }
            return response()->error('failed', array('error' => 'Forbidden'), 403);

        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /**
     *-------------------------------------------------------------------------------
     * Delete a User's Profile - application or database
     *-------------------------------------------------------------------------------
     * URL:            /api/v1/user
     * Method:         DELETE
     * Description:    Delete the provided user's profile
     *
     * @param   Request $request
     * @param   string  $id
     * @param   string  $type
     * @param   string  $userName
     * @return  Response
     */
    public function destroy(Request $request, string $id, string $type, string $userName): Response
    {
        try {
            if (
                $this->canAdministerUsers() &&
                $request->query('uid') === $id &&
                $this->user->toArray()['id'] != $id // !! cannot delete your own login account
            ) {
                $result = false;
                if ($type === 'login') {
                    $result = UserHelper::deleteLoginUser($id);
                }
                if ($type === 'database') {
                    $result = MongoHelper::deleteMongoDbUser($this->mongo, $id);
                }
                if ($result) {
                    return response()->success(
                        'success',
                        array(
                            'user' => array('deleted' => $id)
                        )
                    );
                }
                return response()->error('success', array('error' => 'Unable to delete user due to unhandled error'));
            }
            return response()->error('failed', array('error' => 'Unauthorized'), 401);

        } catch (UnableToDeleteUserException $e) {
            return response()->error('failed', array('error' => $e->getMessage()));

        } catch (\Throwable $t) {
            return response()->error('failed', array('error' => $t->getMessage()));
        }
    }

    /**
     * @param   Request $request
     * @param   string  $email
     *
     * @return  Response
     */
    public function checkEmail(Request $request, string $email): Response
    {
        try {
            if ($email) {
                $users = User::where('username', 'LIKE', '%' . $email . '%')
                    ->orWhere('email', 'LIKE', '%' . $email . '%')
                    ->get();

                if (count($users) > 0) {
                    $result = array("success" => true,
                        "record" => array("email" => array("notFound" => 0, "valid" => $this->validEmail($email))));

                } else {
                    $result = array("success" => true,
                        "record" => array("email" => array("notFound" => 1, "valid" => $this->validEmail($email))));
                }
                return response()->success('success', array('result' => $result));
            }
            return response()->error('failed', array('error' => 'required parameter missing'));

        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /**
     * @param   Request $request
     * @return  Response
     * @throws  \ipinfo\ipinfo\IPinfoException|\Exception
     */
    public function getUserLocation(Request $request): Response
    {
        try {
            $location = UserHelper::getIPInfo($request);
            if (empty($location)) {
                return response()->error('failed', array('error' => 'Unable to fetch IpInfo data'));
            }
            return response()->success('success', array('location' => $location->all));
        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }

    /**
     * @param   string $country
     * @return  Response
     */
    public function getUserStates(string $country): Response
    {
        try {
            $states = UserHelper::getUserStates($country);
            if ($states) {
                return response()->success('success', array('states' => $states[strtoupper($country)]));
            }
            return response()->error('failed', array('error' => 'Unable to find user states'));
        } catch (Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }
}
