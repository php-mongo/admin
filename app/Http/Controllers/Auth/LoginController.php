<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      LoginController.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      LoginController.php
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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/#/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return 'user';
    }

    /**
     * Hanldes our loging via API request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        // get the creds
        $credentials = $request->only([$this->getUser(), 'password']);

        // check the creds
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        // get the user
        $user = auth()->user();

        // generate the user's token from Passport
        $token = $user->createToken('PHPMongoAdmin')->accessToken;

        // login the user
        Auth::login( $user );

        // check to see if they still have their 'app-member' cookie
        $appMember = $request->cookie('phpmongoapp-member');
        if (empty($appMember)) {
            // set the account member cookie
            $llct     = time()+60*60*24*180; // 180 days should be enough !!
            Cookie::queue('phpmongoapp-member', $user->id, $llct, false, false, false, false);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Handle logout requests
     *
     * @param Request $request
     * @param $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request, $uid)
    {
        $user = auth()->user();// Auth::user();
        if ($uid == $user->id) {
            auth()->logout();
            return response()->json(['success' => true, 'message'=>'success']);
        }
        return response()->json(['success' => false, 'error' => 'failed'], 200);
    }

    /**
     * Returns an authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * Generates the response with the Passport Token
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // ToDo: setup a mechanism to set this expiry duration via an admin panel and use a dynamic value
        $days = 60 * 60 * 27 * 28; // 28 days
        return response()->json([
            'success' => true,
            'uid' => Auth::id(),
            'token' => $token,
            'time' => time(),
            'token_type' => 'bearer',
            'expires_in' => time() + $days
        ]);
//        return response()->json([
//            'success' => true,
//            'access_token' => $token,
//            'token_type' => 'bearer',
//            'expires_in' => auth()->factory()->getTTL() * 60
//        ]);
    }
}
