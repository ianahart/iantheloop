<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Profile;
use App\Models\User;
use App\Helpers\Status;
use App\Helpers\Setting;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Exceptions\JWTException;
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

                $refreshed = JWTAuth::refresh();
                $cat = JWTAuth::getToken();

                JWTAuth::setToken($refreshed)->toUser();


                $refreshed = $this->respondWithRefreshToken($refreshed, $TLL);


                return response()->json(
                    [
                        'access_token' => $refreshed,
                        'message' => 'Token refreshed',
                    ],
                    200
                )->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            } catch (JWTException $e) {


                if (Cookie::has('remember_me')) {

                    $setting = new Setting;
                    $validRememberMe = $setting->validateRememberMe($request->userAgent());

                    if ($validRememberMe['validated']) {

                        $currentUser = User::find($validRememberMe['user_id']);

                        $userStatus = new Status($currentUser->id);
                        $userStatus->updateStatus(true, 'online');

                        $token = JWTAuth::fromUser($currentUser, $currentUser->getJWTCustomClaims());

                        $data = json_encode([
                            'access_token' => $token,
                            'profile_pic' => $currentUser->profile->profile_picture ?? '',
                            'profile_created' => $currentUser->profile_created,
                            'status' => $currentUser->status,
                        ]);


                        return response()->json(
                            [
                                'access_token' => $data,
                                'message' => 'Token refreshed'
                            ],
                            200
                        )->header('Cache-Control', 'no-cache, no-store, must-revalidate');;
                    }
                }

                $token = $request->all()['token'];


                $cookie = Cookie::forget('remember_me');

                return response()->json(
                    [
                        'message' => 'Token Expired'
                    ],
                    403
                )->withCookie($cookie);
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
        return json_encode([
            'access_token' => $token,
            'profile_pic' => $profile_pic ?? '',
            'profile_created' => JWTAuth::user()->profile_created,
            'status' => 'online',
        ]);
    }
}
