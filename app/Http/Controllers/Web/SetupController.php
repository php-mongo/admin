<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      SetupController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   SetupController.php
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

namespace App\Http\Controllers\Web;

/**
 * Base Controllers
 */
use App\Http\Controllers\Controller;

/**
 * Requests
 */
use Illuminate\Http\Request;
use App\Http\Requests\StoreControlUser;

/**
 * Response
 */
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

/**
 * Models
 */
use App\Models\Server;
use App\Models\User;

/**
 * Helpers
 */
use App\Helpers\UserHelper;
use App\Helpers\MongoConnectionHelper;

/**
 * Facades
 */
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

/**
 * Exception
 */
use Throwable;

/**
 * Class SetupController
 * @package App\Http\Controllers\Web
 */
class SetupController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/#/admin';

    /**
     * @param Collection|null $users
     * @return bool
     */
    private function checkStatus(?Collection $users): bool
    {
        $result = false;
        foreach ($users as $user) {
            if ($user->exists && $user->isControlUser()) {
                /** @var Server $servers */
                $servers = $user->servers()->where('active', 1)->get();
                foreach ($servers as $server) {
                    $attrs = $server->getAttributes();
                    $result = MongoConnectionHelper::checkConnectionConfig(
                        $attrs['port'],
                        $attrs['host'],
                        $attrs['username'],
                        $attrs['password']
                    );
                }
            }
        }

        return $result;
    }

    private function getMessage(Request $request)
    {
        $string = $request->session()->get('status', null);
        return strpos($string, 'Your password reset request was successful') !== false ?
            $string : null;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Loads the initial setup layout
     *
     * @return Factory|View
     */
    public function getSetup(Request $request)
    {
        $success = $this->getMessage($request);
        return view(
            'public.setup',
            [
                'completed' => $this->checkStatus(User::all()),
                'success' => $success
            ]
        );
    }

    /**
     * Saves the Control User
     *
     * @param StoreControlUser $request
     * @return RedirectResponse|Redirector
     * @throws Throwable
     */
    public function saveSetup(StoreControlUser $request)
    {
        try {
            $data = $request->validated();
            UserHelper::generateControlUser($data);
            return redirect('/admin');

        } catch (Throwable $t) {
            throw $t;
        }
    }
}
