<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      MongoHelper.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   MongoHelper.php
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

/**
 * Every good name deserves a space
 */
namespace App\Helpers;

/**
 * @use
 */
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use ipinfo\ipinfo\Details;
use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;

/**
 * Always be prepared to accept failure !!
 */
use App\Exceptions\UnableToDeleteUserException;
use Exception;

/**
 * Class    UserHelper
 * @package App\Helpers
 */
class UserHelper
{
    /**
     * Returns true if user is not a control user role
     * Prevents accidental deletion of the control user
     *
     * @param   int $id
     * @return  bool
     */
    private static function notControlUser(int $id): bool
    {
        return (User::where('id', $id)->get()[0]->getAttributes()['control_user'] !== '1');
    }

    /**
     * ToDo:  move this to a reusable service class
     *
     * @param   string $host
     * @return  bool|false
     */
    private static function pingHost(string $host): bool
    {
        try {
            $response = Http::timeout(1)->get($host);
            // basic test on this response
            if ($response->successful()) {
                return true;
            }
        } catch (Exception $e) {
            Log::debug($e->getMessage() . " fetching: " . $host);
            return false;
        }
        return false;
    }

    /**
     * Generate a new Login user
     *
     * @param   array $data
     * @return  User
     */
    public static function generateLoginUser(array $data): User
    {
        // generate new login user
        $user               = new User();
        $user->setAttribute('name', $data['name']);
        $user->setAttribute('user', $data['user']);
        $user->setAttribute('email', $data['email']);
        $user->setAttribute('password', Hash::make($data['password']));
        $user->setAttribute('control_user', 0); // only one control user is allowed
        $user->setAttribute('admin_user', $data['isAdmin']);
        $user->setAttribute('active', $data['active']);
        $user->setAttribute('encrypted_password', Crypt::encryptString($data['password']));
        $user->setAttribute('message', ''); // this needs to be an empty string for the FE
        if ($data['type'] === 'both') {
            $user->setAttribute('has_both', "1");
        }
        $user->save();

        if ($user->admin_user === "1") {
            dispatch('Illuminate\Auth\Events\Registered');
        }

        return $user;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function updateLoginUser(array $data): array
    {
        /** @var User $user */
        $user = User::where('id', '=', $data['id'])->get()[0];

        // return the users 'has_both' flag
        $hasBoth = $user->getAttribute('has_both');

        // update based on changed values only
        $keys = [];
        foreach ($data['updated'] as $arr) {
            $user->setAttribute($arr['key'], $data[$arr['key']]);
            $keys[] = $arr['key'];
        }
        $user->save();
        return array('keys' => $keys, 'user' => $user->toArray(), 'hasBoth' => $hasBoth);
    }

    /**
     * Return one or more users
     *
     * @param   string|null $search
     * @return  User[]|Collection
     */
    public static function getLoginUsers(?string $search = null)
    {
        /** @var User $users */
        $users = User::all();
        foreach ($users as $user) {
            $user->message = '';
            $user->save();
        }
        return $search ? User::where('name LIKE %?% OR email LIKE %?%', [$search])->get() : User::all();
    }

    /**
     * Delete a single User profile
     *
     * @param   int $id
     * @return  bool
     * @throws  UnableToDeleteUserException
     */
    public static function deleteLoginUser(int $id): bool
    {
        if ($id > 0) {
            if (self::notControlUser($id)) {
                return User::where('id', $id)->delete();
            }
            throw new UnableToDeleteUserException('Unable to delete the application control user account');
        }
        return false;
    }

    /**
     * Generate a new Login user
     *
     * @param array $data
     * @return  User
     */
    public static function generateControlUser(array $data): User
    {
        // generate new login user
        $password = $data['password'];
        $user = new User();
        $user->setAttribute('name', $data['name']);
        $user->setAttribute('user', $data['user']);
        $user->setAttribute('email', $data['email']);
        $user->setAttribute('password', Hash::make($password));
        $user->setAttribute('control_user', "1"); // only one control user is allowed
        $user->setAttribute('admin_user', "1");
        $user->setAttribute('active', "1");
        $user->setAttribute('encrypted_password', Crypt::encryptString($password));
        $user->setAttribute('message', ''); // this needs to be an empty string for the FE
        // the control user should have a local (or remote) MongoDb account to add to the Server table
        $user->save();

        dispatch('Illuminate\Auth\Events\Registered');

        return $user;
    }

    /**
     * @param   Request $request
     * @return  bool|Details|mixed
     * @throws  Exception
     */
    public static function getIPInfo(Request $request)
    {
        try {
            if (config('ipinfo_enabled') && self::pingHost(IPinfo::API_URL)) {
                $ipInfo = Session::get('ipInfo');
                if ($ipInfo) {
                    return $ipInfo;
                }
                $ip = config('ipinfo_address'); // from env() or $_SERVER
                $access_token = 'e5f368ed86097c';
                /** @var IPinfo $client */
                $client = new IPinfo($access_token);
                /** @var Object $ipInfo */
                $ipInfo = $client->getDetails($ip);
                if ($ipInfo) {
                    Session::put('ipInfo', $ipInfo);
                    $country = $ipInfo->country_name;
                    $llct = time() + 60 * 60 * 24 * 180;
                    Cookie::queue('my-country', $country, $llct, false, false, false, false);
                    return $ipInfo;
                }
            }
            return false;

        } catch (IPinfoException $e) {
            Log::debug($e->getMessage());
            return false;
        }
    }

    /**
     * @param   string $country
     * @return  array
     */
    public static function getUserStates(string $country)
    {
        // $country = $request->get('country');

        /*$states = State::where('country_code', '=', $country)
        ->get();

        if (count($states)) {
            $result = array("success" => true, "states" => $states);

        } else {
            $result = array("success" => true, "states" => array());
        }*/

        try {
            // ToDo: !! temporary measure !!
            $states = array(
                "AU" => array(
                    array("id" => 0, "code" => "ACT", "name" => "Australia Capitol Territory"),
                    array("id" => 1, "code" => "NSW", "name" => "New South Wales"),
                    array("id" => 3, "code" => "NT", "name" => "Northern Territory"),
                    array("id" => 3, "code" => "QLD", "name" => "Queensland"),
                    array("id" => 4, "code" => "SA", "name" => "South Australia"),
                    array("id" => 5, "code" => "TAS", "name" => "Tasmania"),
                    array("id" => 6, "code" => "VIC", "name" => "Victoria"),
                    array("id" => 7, "code" => "WA", "name" => "Western Australia")
                )
            );

            if (isset($states[strtoupper($country)])) {
                return $states[strtoupper($country)];
            }
            return [];

        } catch (\Exception $e) {
            throw new \RuntimeException(printf('Unable to fetch states: %s', $e->getMessage()));
        }
    }

    /**
     * @param   Request $request
     * @param   $country
     * @param   $state
     * @param   $suburb
     * @return  array
     */
    public static function getUserPostcode(Request $request, $country, $state, $suburb): array
    {
        try {
            $postcode = Postcode::where('country_code', '=', $country)
                ->where('state_code', '=', $state)
                ->where('suburb', '=', $suburb)
                ->get();

            if (count($postcode)) {
                $result = array("success" => true, "postcode" => $postcode);

            } else {
                $result = array("success" => true, "postcode" => false);
            }
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param   Request $request
     * @param   $country
     * @param   $state
     * @param   $postcode
     * @return  array
     */
    public static function getUserSuburb(Request $request, $country, $state, $postcode): array
    {
        try {
            $suburb = Postcode::where('country_code', '=', $country)
                ->where('state_code', '=', $state)
                ->where('postcode', '=', $postcode)
                ->get();

            if (count($suburb)) {
                return $suburb;

            } else {
                return array();
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param Request $request
     * @param $country
     * @param $state
     * @param $value
     *
     * @return JsonResponse
     */
    public static function getUserArea(Request $request, $country, $state, $value): JsonResponse
    {
        try {
            if (is_numeric($value)) {
                // use postcode as reference //  LEFT JOIN states s ON (s.code = p.state_code)
                if ($state) {
                    // use state
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('state_code', '=', $state)
                        ->where('postcode', 'LIKE', $value . '%')
                        ->orderBy('suburb')
                        ->offset(0)
                        ->limit(25)
                        ->get();

                } else {
                    // no state
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('postcode', 'LIKE', $value . '%')
                        ->orderBy('suburb')
                        ->offset(0)
                        ->limit(25)
                        ->get();
                }

                if (isset($result)) {
                    // result found
                    $output = array();
                    foreach ($result as $row) {
                        $arr = array(
                            'postcode'  => $row->postcode,
                            'suburb'    => ucwords(strtolower($row->suburb))
                        );
                        $output[] = $arr;
                    }

                    return response()->json(array('success' => true, 'records' => $output));

                } else {
                    // no previous result
                    if ($state) {
                        // try without the state reference
                        $result = Postcode::where('country_code', '=', $country)
                            ->where('postcode', 'LIKE', $value . '%')
                            ->orderBy('suburb')
                            ->offset(0)
                            ->limit(25)
                            ->get();

                        if (isset($result)) {
                            $output = array();
                            foreach ($result as $row) {
                                $arr = array(
                                    'postcode'  => $row->postcode,
                                    'suburb'    => ucwords(strtolower($row->suburb))
                                );
                                $output[] = $arr;
                            }
                            return response()->json(array('success' => true, 'records' => $output));

                        } else {
                            return response()->json(array('success' => true, 'records' => false));
                        }

                    } else {
                        return response()->json(array('success' => true, 'records' => false));
                    }
                }

            } else {
                // use suburb as reference
                if ($state) {
                    // use state
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('state_code', '=', $state)
                        ->where('suburb', 'LIKE', $value . '%')
                        ->orderBy('suburb')
                        ->offset(0)
                        ->limit(25)
                        ->get();

                } else {
                    // no state
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('suburb', 'LIKE', $value . '%')
                        ->orderBy('suburb')
                        ->offset(0)
                        ->limit(25)
                        ->get();
                }

                if (isset($result)) {
                    $output = array();
                    foreach ($result as $row) {
                        $arr = array(
                            'postcode'  => $row->postcode,
                            'suburb'    => ucwords(strtolower($row->suburb))
                        );
                        $output[] = $arr;
                    }
                    return response()->json(array('success' => true, 'records' => $output));

                } else {
                    if ($state) {
                        // try without the state reference
                        // $query = "SELECT * FROM `postcode` WHERE `suburb` LIKE ? AND `country_code` = ? ORDER BY `suburb` ASC LIMIT 25";
                        //$result = DB::select($query, [$value.'%', $cid] );
                        $result = Postcode::where('country_code', '=', $country)
                            ->where('suburb', 'LIKE', $value . '%')
                            ->orderBy('suburb')
                            ->offset(0)
                            ->limit(25)
                            ->get();

                        if (isset($result)) {
                            $output = array();
                            foreach ($result as $row) {
                                $arr = array(
                                    'postcode'  => $row->postcode,
                                    'suburb'    => ucwords(strtolower($row->suburb))
                                );
                                $output[] = $arr;

                            }
                            return response()->json(array('success' => true, 'records' => $output));

                        } else {
                            return response()->json(array('success' => true, 'records' => false));
                        }

                    } else {
                        return response()->json(array('success' => true, 'records' => false));
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(array());
        }
    }

    /**
     * @param   $email
     * @return  bool
     */
    public static function validEmail($email)
    {
        return 1;
    }
}
