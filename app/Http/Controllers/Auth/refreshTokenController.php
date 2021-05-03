<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use JTWAuth;
use Namshi\JOSE\JWT;
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
                $TLL = 20160;

                JWTAuth::factory()->setTTL($TLL);

                $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

                $user = JWTAuth::setToken($refreshToken)->toUser();


                $accessToken = $this->respondWithRefreshToken($refreshToken, $TLL);


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



    protected function respondWithRefreshToken($token, $TLL)
    {

        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $token,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' => time() + $TLL * 60,
                'user_id' => JWTAuth::user()->id,
                'profile_created' => JWTAuth::user()->profile_created,
                'name' => JWTAuth::user()->full_name,
            ]
        );
    }
}
