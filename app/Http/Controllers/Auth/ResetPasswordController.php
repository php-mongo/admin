<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ResetPasswordController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   ResetPasswordController.php
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
use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Exception\DateTimeException;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * This offers moderate protection against obvious forgeries
     * Also lock the process down for the moment
     * ToDo:  rewrite when/if we need to allow a broader range of users to reset passwords
     *
     * @param   string $email
     * @param   string $token
     * @return  bool
     * @throws  \Exception
     */
    private function checkTokenIsValid(string $email, string $token)
    {
        if ($email && $token) {
            // ensure there is a token for the given email address
            $result = DB::select('select * from password_resets where email = :e', ['e' => $email]);
            if (empty($result)) {
                return false;
            }

            // ToDo: for now we are limiting this action to the control user
            $user = User::where('email', $email)->get();
            if (empty($user)) {
                return false;
            }

            $user = $user[0]->getAttributes();
            // make sure the user is active
            if ($user['active'] !== "1") {
                return false;
            }

            // check for control user
            if ($user['control_user'] !== "1") {
                return false;
            }

            try {
                $expire = (new DateTime($result[0]->created_at))->add(new \DateInterval('PT60M'));
            } catch (DateTimeException $d) {
                Log::debug($d->getMessage());
                $expire = new DateTime();
            }

            // time now
            $now = new DateTime('now');

            // check expiry
            if ($expire >= $now) {
                // gtg
                return true;
            }
        }
        return false;
    }

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->get('email', null);
        return view(
            'auth.passwords.reset',
            [
                'token' => $token,
                'email' => $email,
                'validToken' => $this->checkTokenIsValid($email, $token)
            ]
        );
    }
}
