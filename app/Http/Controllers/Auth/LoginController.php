<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class LoginController extends Controller
{
    /*
     * Login user and return a token
     * @param Request $request
     * @return JsonResponse
     */


    public function store(Request $request)
    {

        $email = $request->form['email'];
        $password = $request->form['password'];

        $user = User::where('email', '=', $email)->first();

        if ($user) {

            if (Hash::check($password, $user->password)) {

                $TLL =  60;

                JWTAuth::factory()->setTTL($TLL);

                $payload = JWTAuth::attempt($request->form);

                $jwt = $this->createNewToken($payload, $TLL, $user);

                $this->updateAuthStatus($jwt);

                return response()->json(
                    [
                        'formSubmitted' => true,
                        'userLoggedIn' => true,
                        'jwt' => $jwt,
                    ],
                    200
                );
            } else {

                $errorPassword = 'Sorry, that password isn\'t right.';

                return response()->json(
                    [
                        'password' =>
                        [
                            'errors' => $errorPassword,
                        ],
                        'formSubmitted' => false,
                    ],
                    400
                );
            }
        } else {

            $errorEmail = 'Sorry, we couldn\'t find an account with that email.';

            return response()->json(
                [
                    'email' =>
                    [
                        'errors' => $errorEmail
                    ],
                    'formSubmitted' => false,
                ],
                400
            );
        }
    }

    /*
     * update authentication column
     * @param String
     * @return void
     */

    private function updateAuthStatus(string $token)
    {

        $contents = json_decode($token, true);

        User::where('id', '=', $contents['user_id'])
            ->update(
                [
                    'is_logged_in' => true
                ]
            );
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

        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $payload,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' => time() + $TLL,
                'user_id' => JWTAuth::user()->id,
                'name' => $user->full_name,
            ]
        );
    }
}
