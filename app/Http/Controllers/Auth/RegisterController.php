<?php
/**
 * Defines a namespace for the controller.
 */
namespace App\Http\Controllers\Auth;

/**
 * Controller
 */
use App\Http\Controllers\Controller;

/**
 * Models
 */
use App\Models\User;

/**
 * Auth
 */
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * Facades
 */
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // some vars to assist in key/secret generation
        $browser 	= $_SERVER['HTTP_USER_AGENT'];
        $ip         = $_SERVER['REMOTE_ADDR'];
        $time 	    = time();
        $ackey      = hash('sha1', $data['name'].$ip.$data['password'].$browser.$time.$data['email']);
        $activation = base64_encode($ackey);
        unset($ackey);
        // 8 - 4 - 4 - 4 - 12 = 32 chars length + 4 dashes = 36 chars total
        $UserId = substr(hash('sha1', $data['password'].$data['name'].$browser.$ip.$time), 2, 8);
        $UserId .= "-".substr(hash('sha1', $data['email'].$ip.$data['password'].$browser.$time), 6, 4);
        $UserId .= "-".substr(hash('sha1', $browser.$data['email'].$data['password'].$ip.$data['password'].$time), 14, 4);
        $UserId .= "-".substr(hash('sha1', $ip.$browser.$data['email'].$time.$data['name']), 18, 4);
        $UserId .= "-".substr(hash('sha1', $data['email'].$ip.$time.$browser.$data['password']), 22, 12);

        // generate the secret key
        $UserKey = hash('sha384', $data['email'].$ip.$time.$browser.$data['password'].$UserId);

        // check for the user public key
        $userPublicKey = Cookie::get('AMPK', null);
        // set a flag
        $hasKey = false;
        if (empty($userPublicKey)) {
            // Generate  a new user public key
            // These keys are used when users make posts as guests
            $userPublicKey = $this->generateToken( 'user_key',32 );

        } else {
            //
            $hasKey = true;
        }

        // user note
        $note = 'Added via registration form';

        // new user
        $user               = new User();
        $user->name         = $data['name'];
        $user->username     = $data['email'];
        $user->email        = $data['email'];
        $user->password     = Hash::make($data['password']);
        $user->role         = 'member';
        $user->UserId       = $UserId;
        $user->UserSecret   = $UserKey;
        $user->userPublicKey = $userPublicKey;
        $user->active       = 1;
        $user->activation   = $activation;
        $user->note         = $note;
        $user->save();

        if ($hasKey == true) {
            $attrs = $user->getAttributes();
            // run the process that will syn any existing posts to this member
            $this->syncPosts( $userPublicKey, $attrs['id'] );
            $this->syncImageUploads($userPublicKey, $attrs['id'] );
        }

        return $user;
    }

    /**
     * @param $userPublicKey
     * @param $user
     */
    private function syncPosts( $userPublicKey, $uid )
    {
        if ($userPublicKey) {
            $publicPosts = Post::where('user_key', '=', $userPublicKey)
                ->get();

            if (count($publicPosts)) {
                foreach ($publicPosts as $post) {
                    $postPublicKey = $post->post_key;

                    $ad = Ad::where('post_public_key', '=', $postPublicKey)
                        ->first();

                    $ad->user_id = $uid;
                    $ad->save();
                }
            }
        }
    }

    /**
     * @param $userPublicKey
     * @param $user
     */
    private function syncImageUploads( $userPublicKey, $uid)
    {
        if ($userPublicKey) {
            $uploadLogs = Upload::where('user_key', '=', $userPublicKey)
                ->get();

            if (count($uploadLogs)) {
                foreach ($uploadLogs as $upload) {

                    $upload->file_user_id = $uid;
                    $upload->save();
                }
            }
        }
    }

    /**
     * Generates a token|key - suitable for password resets etc etc
     *
     * @param string $context The column that will be used to ensure uniqueness of token
     * @param int    $length The length of the token required - default is 24 chars
     *
     * @return string
     */
    public function generateToken( $context, $length = 24) : string
    {
        // generate the key|token
        $key = $this->randomString($length, true);

        // check that its unique
        $result = Post::where($context, '=', $key)
            ->get();

        if (count($result) == 0) {
            // gtg
            return $key;

        } else {
            // rerun
            return $this->generateToken($context, $length);
        }
    }

    /**
     * Generates a random string
     *
     * @param int  $length
     * @param bool $alphaOnly
     * @return string
     */
    public final function randomString($length = null, $alphaOnly = true) : string
    {
        if (null === $length || empty($length)) {
            $bits = array(
                24,
                32,
                64,
                128,
                256
            );
            shuffle($bits);
            $length = array_shift($bits);

        } elseif (is_array($length)) {
            shuffle($length);
            $length = array_shift($length);
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXQZ0123456789';
        if (!$alphaOnly) {
            $chars .= '!#$%^&*().,?~';
        }

        // simplfied version
        // return $chars[rand(0, strlen($chars)-1];

        mt_srand(10000000 * (double) microtime());

        for ($i = 0, $string = '', $lc = strlen($chars) - 1; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, $lc)];
        }
        return $string;
    }
}
