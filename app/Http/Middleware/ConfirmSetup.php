<?php

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
