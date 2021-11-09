<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\User;
use App\Helpers\Status;
use App\Helpers\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;


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
            $currentUser = User::find($request->all()['user_id']);

            if (Cookie::has('remember_me')) {
                $setting = new Setting;
                $validRememberMe = $setting->validateRememberMe($request->userAgent());

                if ($validRememberMe['validated']) {

                    $userStatus = new Status($currentUser->id);
                    $userStatus->updateStatus(true, 'online');

                    $token = [
                        'access_token' => $currentUser->createToken('auth_token')->plainTextToken,
                        'token_type' => 'Bearer',
                        'user_info' => json_encode($currentUser->getUserInfo()),
                    ];

                    return response()->json(
                        [
                            'access_token' => $token,
                            'message' => 'Token refreshed',
                        ],
                        200
                    );
                }
            }

            $currentUser->tokens()
                ->whereIn('id', $currentUser->tokens->pluck('id'))
                ->where('name', '=', 'auth_token')
                ->delete();

            $cookie = Cookie::forget('remember_me');

            return response()->json(
                [
                    'message' => 'Token Expired'
                ],
                403
            )->withCookie($cookie);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Token Expired',
                    'error' => $e->getMessage(),
                ],
                403
            );
        }
    }
}
