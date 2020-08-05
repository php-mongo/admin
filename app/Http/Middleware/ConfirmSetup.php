<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ConfirmSetup.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      ConfirmSetup.php
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

namespace App\Http\Middleware;

/**
 * @uses
 */
use Closure;
use App\Models\User;

/**
 * Class ConfirmSetup
 * @package App\Http\Middleware
 */
class ConfirmSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $users = User::all();
        if (!$this->checkUsers( $users )) {
            return redirect()->route('setup');
        }
        return $next($request);
    }

    /**
     * This method simply checks that we have a Control User already setup
     * At least one control user must be created during the initial setup
     *
     * @param $users
     * @return bool
     */
    private function checkUsers($users)
    {
        foreach ($users as $user) {
            if ($user['control_user'] == 1 && $user['active'] == 1) {
                return true;
            }
        }
        return false;
    }
}
