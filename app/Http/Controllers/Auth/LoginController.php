<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Login;
use App\Helpers\LoginThrottle;
use Exception;

class LoginController extends Controller
{
    /**
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

            $login = new Login;

            $login->setCredentials(['email' => $email, 'password' => $password]);
            $login->loginUser();

            $exception = $login->getException();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            $token = $login->getToken();

            return response()->json(
                [
                    'formSubmitted' => true,
                    'userLoggedIn' => true,
                    'jwt' => $token,
                ],
                200
            );
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
}
