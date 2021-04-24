<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;


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

                $payload = Auth::attempt($request->form);

                $jwt = $this->createNewToken($payload);

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

    protected function createNewToken($payload)
    {

        // $TLL = time() + 60 * 60 * 60 * 24;
        $TLL = time() + 60;
        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $payload,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' =>  $TLL,
                'user_id' => Auth::user()->id,
            ]
        );
    }
}
