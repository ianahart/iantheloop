<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Status;
use App\Helpers\LoginThrottle;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;



class LoginController extends Controller
{


    public bool $authStatus;
    public string $userStatus;

    /*
     * Login user and return a token
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $email = $request->form['email'];
            $password = $request->form['password'];


            $loginThrottle = new LoginThrottle;

            $loginThrottle->setClientIp($request->ip());
            $loginThrottle->setUserAgent($request->header('user_agent'));
            $loginThrottle->setCreatedSeconds(time());

            $timePassed = $loginThrottle->checkTimePassed();

            if ($timePassed) {
                $loginThrottle->clearLoginAttempts();
            }

            $maxLoginAttempts = $loginThrottle->countLoginAttempts();

            if ($maxLoginAttempts) {
                throw new Exception('You have been locked out for 15 minutes for too many login attempts');
            }


            $user = User::where('email', '=', $email)->first();

            if (!$user) {
                throw new Exception('Sorry, we couldn\'t find an account with that email.');
            }

            if (Hash::check($password, $user->password)) {

                $TLL = 60;
                $payload = JWTAuth::attempt($request->form);


                $this->updateStatus();

                $jwt = $this->createNewToken($payload, $TLL, $user);


                return response()->json(
                    [
                        'formSubmitted' => true,
                        'userLoggedIn' => true,
                        'jwt' => $jwt,
                    ],
                    200
                );
            } else {
                throw new Exception('The provided credentials are invalid.');
            }
        } catch (Exception $e) {

            if (!$maxLoginAttempts) {

                $loginThrottle->recordLoginAttempt();
            }

            return response()->json(
                [
                    'password' =>
                    [
                        'errors' => $e->getMessage(),
                    ],
                    'formSubmitted' => false,
                ],
                400
            );
        }
    }

    // /*
    //  * update authentication column
    //  * @param void
    //  * @return void
    //  */

    private function updateStatus()
    {

        $userStatus = new Status(JWTAuth::user()->id);

        $userStatus->updateStatus(true, 'online');

        $exception = $userStatus->getException();

        if (!$exception) {

            $this->userStatus = 'online';
            $this->authStatus = true;
        }
    }

    /*
     * retrieve user's profile picture
     * @param void
     * @return string
     */

    private function getProfilePic()
    {
        if (JWTAuth::user()->profile_created) {

            $profile = Profile::where('user_id', '=', JWTAuth::user()->id)->first();

            return $profile->profile_picture;
        }
    }


    /*
     * create new token with payload
     * @param String
     * @param int
     * @param object
     * @return string
     */
    protected function createNewToken(string $payload, int $TLL, object $user)
    {

        $profile_pic = $this->getProfilePic();

        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $payload,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' => time() + $TLL * 60,
                'user_id' => JWTAuth::user()->id,
                'profile_created' => JWTAuth::user()->profile_created,
                'profile_pic' => $profile_pic ?? '',
                'name' => $user->full_name,
                'status' => $this->userStatus,
                'is_logged_in' => $this->authStatus,
            ]
        );
    }
}
