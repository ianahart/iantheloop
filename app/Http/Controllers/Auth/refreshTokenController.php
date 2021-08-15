<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Profile;
use Illuminate\Http\Request;

use App\Helpers\Status;
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

            if (!array_key_exists('token', $request->all())) {
                throw new JWTException('Token Expired');
            }
            $token = $request->all()['token'];

            JWTAuth::setToken($token)->toUser();

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(
                    [
                        'status' => false,
                        'data' => null,
                        'message' => 'User not found'
                    ],
                    400
                );
            }
        } catch (TokenExpiredException $e) {

            try {

                $TLL = 20160;
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();

                $refreshed = $this->respondWithRefreshToken($refreshed, $TLL);

                return response()->json(
                    [
                        'access_token' => $refreshed,
                        'message' => 'Token refreshed',
                    ],
                    200
                )->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            } catch (JWTException $e) {

                return response()->json(
                    [
                        'message' => 'Token Expired'
                    ],
                    403
                );
            }
        } catch (JWTException $e) {

            return response()->json(
                [
                    'message' => 'Token Expired'
                ],
                403
            );
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

        $userStatus = new Status(JWTAuth::user()->id);

        $userStatus->updateStatus(true, 'online');

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
                'is_logged_in' => true,
                'status' => 'online',
                'slug' => JWTAuth::user()->slug,
            ]
        );
    }
}
