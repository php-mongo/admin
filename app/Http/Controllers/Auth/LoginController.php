<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      LoginController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   LoginController.php
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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    //use AuthenticatesUsers;
    use ThrottlesLogins;

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
     * @param Request $request
     * @return array
     */
    public function credentials(Request $request): array
    {
        return [
            'user'      => $request->user,
            'password'  => $request->password,
            'active'    => '1'
        ];
    }

    /**
     * Handles our logging via API request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'user' => 'required|string',
            'password' => 'required|string|min:5',
            'active' => 'required|numeric|size:1'
        ]);

        //dd(new DateTime('now'));

        // get the creds
        $credentials = $request->only([$this->getUser(), 'password', 'active']);

        // ensure the active value has not been forged
        if ($credentials['active'] !== "1") {
            Log::channel('auth')->info('Bad login attempted: ', ['request' => json_encode($request)]);
            return response()->json(['success' => false, 'error' => 'Bad-Request'], 400);
        }

        // check the creds
        if (!$token = auth()->attempt($credentials)) {
            $user = User::where('user', $credentials['user'])->get();
            $user = isset($user[0]) > 0 ? $user[0]->getAttributes() : array('active' => null);
            if ($user['active'] === "0") {
                Log::channel('auth')->info('Login attempted on inactive account: ', ['user' => $credentials['user']]);
                return response()->json(['success' => false, 'error' => 'Inactive'], 401);
            }
            Log::channel('auth')->alert('Failed login attempt: ', ['user' => $credentials['user']]);
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        // get the user
        $user = auth()->user();

        // generate the user's token from Passport
        $token = $user->createToken('PHPMongoAdmin')->accessToken;

        // login the user
        Auth::login($user);

        // check to see if they still have their 'app-member' cookie
        $appMember = $request->cookie('phpmongoapp-member');
        if (empty($appMember)) {
            // set the account member cookie
            $llct     = time() + 60 * 60 * 24 * 180; // 180 days should be enough !!
            Cookie::queue('phpmongoapp-member', $user->id, $llct, false, false, false, false);
        }

        // check the 'remember' flag
        if ($request->input('remember', null) === true) {
            $llct     = time() + 60 * 60 * 24 * 365; // 365 days should be enough !!
            Cookie::queue('pma-member', $credentials['user'], $llct, false, false, false, false);
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
        $user = auth()->user();
        if ($uid == $user->id) {
            auth('web')->logout();
            return response()->json(['success' => true, 'message'=>'success']);
        }
        return response()->json(['success' => false, 'error' => 'failed'], 200);
    }

    public function showLoginForm()
    {
        return view('public.welcome');
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
    }
}
