<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      web.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      web.php
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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\VerifiesEmails;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
| For testing we can always load the welcome blade template
*/
Route::get('/welcome', function () {
    return view('public/welcome');
});

/*
| Localization - customised to provide language files data to Vue
*/
Route::get('js/lang.js', function() {
    $strings = Cache::rememberForever( 'lang.js', function() {

        // output container
        $strings = [];

        // defined the languages we need to load
        $languages = array('en', 'zh');
        //$lang = config('app.locale');

        foreach ($languages as $lang) {
            // fetch the files for this language set
            $files = glob( resource_path('lang/' . $lang) . '/*.php');
            foreach ($files as $file) {
                $name  = basename( $file, '.php');
                $strings[ $lang ][ $name ] = require $file;
            }
        }

        // output the combined language set
        return $strings;
    });

    header('Content-Type: text/javascript');
    // this needs to support chinese chars
    echo('window.i18n = ' . json_encode( $strings, JSON_UNESCAPED_UNICODE ) . ';');
    exit();

})->name('assets-lang');

/*
| Localisation assistance - getting the accept language value from the browser
*/
Route::get('js/acceptLang.js', function() {
    header('Content-Type: text/javascript');
    $string = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    echo explode(";", explode(",", $string)[1])[0];
    exit();
});

/*
| Default routes - always require setup to be completed - default public page with notification
*/
Route::get('/', 'Web\AppController@getApp')->name('public')
    ->middleware('setup');


Route::get('/admin', 'Web\AppController@getApp')
    ->middleware('setup');

/*
| Public, Login and logout
*/
Route::get('/logout', 'Web\AppController@getLogout')
    ->name('logout');

/*
| Initial setup for the control user - public access
*/
Route::get('/setup', 'Web\SetupController@getSetup')
    ->name('setup');

Route::post('/setup', 'Web\SetupController@saveSetup')
    ->name('save-setup');

/*
| Auth routes
*/
//Auth::routes(['verify' => true]);
Auth::routes();

/*
 * Laravel 8
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
*/
