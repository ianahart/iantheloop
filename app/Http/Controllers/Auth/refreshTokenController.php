<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Profile;
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


    protected function respondWithRefreshToken($token, $TLL)
    {

        $profile_pic = $this->getProfilePic();

        return json_encode(
            [
                'iss' => 'jwt-auth',
                'access_token' => $token,
                'token_type' => 'bearer',
                'iat' => time(),
                'exp' => time() + $TLL * 60,
                'user_id' => JWTAuth::user()->id,
                'profile_created' => JWTAuth::user()->profile_created,
                'profile_pic' => $profile_pic ?? '',
                'name' => JWTAuth::user()->full_name,
            ]
        );
    }
}
