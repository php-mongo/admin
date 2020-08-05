<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      AppController.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      AppController.php
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
namespace App\Http\Controllers\Web;

/**
 * @uses
 */
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function getApp()
    {
        return view('layouts.app');
    }

    public function getLogin()
    {
        return view('public.login');
    }

    /*public function getLoginForm() {
        return view('public.login');
    }*/

    public function getLogout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
