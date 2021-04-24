<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use JTWAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class refreshTokenController extends Controller
{
    /*
     * Refresh Token
     * @param Request $request
     * @return JsonResponse
     */

    public function store(Request $request)
    {

        try {


            if (!$authenticated = JWTAuth::parseToken()->authenticate()) {


                return response()->json(
                    [
                        'status' => false,
                        'data' => null,
                        'message' => 'User not found'
                    ]
                );
            }
        } catch (TokenExpiredException $e) {


            try {
                $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

                $user = JWTAuth::setToken($refreshToken)->toUser();


                $accessToken = $this->respondWithRefreshToken($refreshToken);


                return response()->json(
                    [
                        'access_token' => $accessToken,
                        'message' => 'Token refreshed',
                    ],
                    200
                );
            } catch (JWTException $e) {

                return response()->json(
                    [
                        'message' => 'Token Expired'
                    ],
                    403
                );
            }
        }
    }

    protected function respondWithRefreshToken($token)
    {

        $TLL = time() + 60;

        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $token,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' =>  $TLL,
                'user_id' => JWTAuth::user()->id,
            ]
        );
    }
}
