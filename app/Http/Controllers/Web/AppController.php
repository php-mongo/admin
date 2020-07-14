<?php
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
