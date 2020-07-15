<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/js/lang.js', function() {
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
Route::get('/js/acceptLang.js', function() {
    //dd($_SERVER);
    header('Content-Type: text/javascript');
    $string = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    echo explode(";", explode(",", $string)[1])[0];
    exit();
});

/*
| Default route - always require authentication - no public pages
*/
Route::get('/', 'Web\AppController@getApp')->name('public')
    ->middleware('setup');
    //->middleware('auth');

Route::get('/admin', 'Web\AppController@getApp')
    ->middleware('setup');
    //->middleware('auth');

/*
| Public, Logins and outs
*/
/*Route::get('/public', function () {
    return view('public/login');
})->name('public');*/

/*Route::post('/public/login', 'Auth\LoginController@login')->name('login')
    ->middleware('guest');*/

/*Route::get('/login', 'Web\AppController@getLogin')->name('login')
    ->middleware('guest');*/

Route::get('/logout', 'Web\AppController@getLogout')
    ->name('logout');

/*
| Initial setup for the control user
*/
Route::get('/setup', 'Web\SetupController@getSetup')
    ->name('setup');

Route::post('/setup', 'Web\SetupController@saveSetup')
    ->name('save-setup');

/*
| Auth routes
*/
Auth::routes();


