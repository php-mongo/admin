<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      UsersController.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      UsersController.php
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

/*
  Defines a namespace for the controller.
*/
namespace App\Http\Controllers\Api;

/*
  Uses the controller interface.
*/
use App\Http\Controllers\Auth\RegisterController;

/*
  Defines the requests used by the controller.
*/
use Illuminate\Http\Request;
use App\Http\Requests\EditUserRequest;
//use App\Http\Requests\RegisterUserRequest;

/*
  Defines the models used by the controller.
*/
use App\Models\User;
use App\Models\State;
use App\Models\Postcode;

/*
  Defines the facades etc used by the controller.
*/
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use Illuminate\Validation\ValidationException;
use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;
use Mockery\Exception;

/**
 * The user controller handles all API requests that manage user data.
 */
class UsersController extends RegisterController
{
    /**
     * @param Request $request
     * @return bool|\ipinfo\ipinfo\Details|mixed
     */
    private function getIPInfo(Request $request)
    {
        try {
            $ipInfo = Session::get('ipInfo');
            if ($ipInfo) {
                return $ipInfo;
            }
            $ip = config('ipinfo_address');
            $access_token = 'e5f368ed86097c';
            $client       = new IPinfo($access_token);
            $ipInfo       = $client->getDetails($ip);
            if ($ipInfo) {
                Session::put('ipInfo', $ipInfo);
                $country  = $ipInfo->country_name;
                $llct     = time()+60*60*24*180;
                Cookie::queue('my-country', $country, $llct, false, false, false, false);
                return $ipInfo;
            }
            return false;

        } catch(IPinfoException $e) {
            throw new $e;
        }
    }

    /*
    |------------------------------------------------- ------------------------------
    | Get User
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Method:         GET
    | Description:    Gets the authenticated user
    */
    public function getUser(Request $request)
    {
     //   dd($request);
        $user = auth('api')->user();
        $attributes = $user->getAttributes();
     //   dd($attributes);
        if (isset($attributes['id'])) {
            return response()->json($attributes);
        }
        return response()->json(array());

    }

    /*
    |-------------------------------------------------------------------------------
    | Get Users
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/users
    | Method:         GET
    | Description:    Gets the users searched by the authenticated user.
    */
    public function getUsers(Request $request)
    {
        dd($request);
        $query = $request->get('search');

        $users = User::where('name', 'LIKE', '%'.$query.'%')
         ->orWhere('email', 'LIKE', '%'.$query.'%')
         ->get();

        return response()->json( $users );
    }

    /*
    |-------------------------------------------------------------------------------
    | Registers a User's Profile
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Method:         POST
    | Description:    Updates the authenticated user's profile
    */
    public function postRegisterUser( Request $request )
    {
        try {
            // validate
            $result = $this->validate( $request, array(
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ));

        //    dd($result);

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
            dd($e);
            throw new $e;

        }
        catch( Exception $e) {
            dd($e);
        }
    }

    /*
    |-------------------------------------------------------------------------------
    | Updates a User's Profile
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Method:         PUT
    | Description:    Updates the authenticated user's profile
    */
    public function putUpdateUser( EditUserRequest $request )
    {
        dd($request);
        $user = Auth::user();

        $user->save();

        /*
        *   Return a response that the user was updated successfully.
        */
        return response()->json( ['user_updated' => true], 201 );
    }

    /**
     * @param Request $request
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request, $email)
    {
        if ($email) {
        //    $query = $request->get('search');

            $users = User::where('username', 'LIKE', '%'.$email.'%')
                ->orWhere('email', 'LIKE', '%'.$email.'%')
                ->get();

            if (count($users) > 0) {
                $result = array("success" => true, "record" => array("email" => array("notFound" => 0, "valid" => $this->validEmail($email))));

            } else {
                $result = array("success" => true, "record" => array("email" => array("notFound" => 1, "valid" => $this->validEmail($email))));
            }
            return response()->json( $result );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLocation(Request $request)
    {
        $location = $this->getIPInfo($request);

        // dd($location->all);

        return response()->json( $location->all );

    }

    /**
     * @param Request $request
     * @param $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStates(Request $request, $country)
    {
        // $country = $request->get('country');

        /*$states = State::where('country_code', '=', $country)
        ->get();

        if (count($states)) {
            $result = array("success" => true, "states" => $states);

        } else {
            $result = array("success" => true, "states" => array());
        }*/

        // ToDo: !! temporary measure !!
        $result = array();
        $states = array(
            "AU" => array(
                array("id" => 0, "code" => "ACT", "name" => "Australia Capitol Territory"),
                array("id" => 1, "code" => "NSW", "name" => "New South Wales"),
                array("id" => 3, "code" => "NT", "name" => "Northern Territory"),
                array("id" => 3, "code" => "QLD", "name" => "Queensland"),
                array("id" => 4, "code" => "SA", "name" => "South Australia"),
                array("id" => 5 , "code"=> "TAS", "name" => "Tasmania"),
                array("id" => 6, "code" => "VIC", "name" => "Victoria"),
                array("id" => 7, "code" => "WA", "name" => "Western Australia")
            )
        );

        if (isset($states[strtoupper($country)])) {
            $result = $states[strtoupper($country)];
        }

        return response()->json( $result );
    }

    /**
     * @param Request $request
     * @param $country
     * @param $state
     * @param $suburb
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPostcode(Request $request, $country, $state, $suburb)
    {
        // $country = $request->get('country');

        $postcode = Postcode::where('country_code', '=', $country)
            ->where('state_code', '=', $state)
            ->where('suburb', '=', $suburb)
            ->get();

        if (count($postcode)) {
            $result = array("success" => true, "postcode" => $postcode);

        } else {
            $result = array("success" => true, "postcode" => false);
        }
        return response()->json( $result );
    }

    /**
     * @param Request $request
     * @param $country
     * @param $state
     * @param $postcode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserSuburb(Request $request, $country, $state, $postcode)
    {
        // $country = $request->get('country');

        $suburb = Postcode::where('country_code', '=', $country)
            ->where('state_code', '=', $state)
            ->where('postcode', '=', $postcode)
            ->get();

        if (count($suburb)) {
            $result = array("success" => true, "suburb" => $suburb);

        } else {
            $result = array("success" => true, "suburb" => false);
        }
        return response()->json( $result );
    }

    /**
     * @param Request $request
     * @param $country
     * @param $state
     * @param $value
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserArea(Request $request, $country, $state, $value)
    {
        if (is_numeric($value)) {
            // use postcode as reference //  LEFT JOIN states s ON (s.code = p.state_code)
            if ($state) {
                // use state
                $result = Postcode::where('country_code', '=', $country)
                    ->where('state_code', '=', $state)
                    ->where('postcode', 'LIKE', $value .'%')
                    ->orderBy('suburb')
                    ->offset(0)
                    ->limit(25)
                    ->get();

            } else {
                // no state
                $result = Postcode::where('country_code', '=', $country)
                    ->where('postcode', 'LIKE', $value .'%')
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

                return response()->json( array('success' => true, 'records' => $output) );

            } else {
                // no previous result
                if ($state) {
                    // try without the state reference
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('postcode', 'LIKE', $value .'%')
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
                        return response()->json( array('success' => true, 'records' => $output) );

                    } else {
                        return response()->json( array('success' => true, 'records' => false) );
                    }

                } else {
                    return response()->json( array('success' => true, 'records' => false) );
                }
            }

        } else {
            // use suburb as reference
            if ($state) {
                // use state
                $result = Postcode::where('country_code', '=', $country)
                    ->where('state_code', '=', $state)
                    ->where('suburb', 'LIKE', $value .'%')
                    ->orderBy('suburb')
                    ->offset(0)
                    ->limit(25)
                    ->get();

            } else {
                // no state
                $result = Postcode::where('country_code', '=', $country)
                    ->where('suburb', 'LIKE', $value .'%')
                    ->orderBy('suburb')
                    ->offset(0)
                    ->limit(25)
                    ->get();
            }

         ///   dd($result);

            if (isset($result)) {

                $output = array();
                foreach ($result as $row) {
                    $arr = array(
                        'postcode'  => $row->postcode,
                        'suburb'    => ucwords(strtolower($row->suburb))
                    );
                    $output[] = $arr;
                }
                return response()->json( array('success' => true, 'records' => $output) );

            } else {
                if ($state) {
                    // try without the state reference
                    //$query = "SELECT * FROM `postcode` WHERE `suburb` LIKE ? AND `country_code` = ? ORDER BY `suburb` ASC LIMIT 25";
                    //$result = DB::select( $query, [$value.'%', $cid] );
                    $result = Postcode::where('country_code', '=', $country)
                        ->where('suburb', 'LIKE', $value .'%')
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
                        return response()->json( array('success' => true, 'records' => $output) );

                    } else {
                        return response()->json( array('success' => true, 'records' => false) );
                    }

                } else {
                    return response()->json( array('success' => true, 'records' => false) );
                }
            }
        }
    }

    /**
     * @param $email
     * @return bool
     */
    private function validEmail( $email) {
        return 1;
    }
}
