<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

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
     * @param $users
     * @return bool
     */
    private function checkUsers($users)
    {
        foreach ($users as $user) {
            if ($user['control_user'] == 1 && $user['active'] == 1) {
        //        $user               = User::where('id', $user['id'])->get();

             //   echo '<pre>'; var_dump($user); echo '</pre>'; die;

                //$user->password     = Hash::make('9aa7969b');
               // $user->save();

        //        $password     = Hash::make('9aa7969b');
         //       $user->toQuery()->update([ 'password' => $password]);

                return true;
            }
        }
        return false;
    }
}
