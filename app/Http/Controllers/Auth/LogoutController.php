<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\Status;

class LogoutController extends Controller
{

    /*
     * Logout user and invalidate token
     * @param Request $request
     * @return JsonResponse
     */


    public function store(Request $request)
    {


        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        $currentUser = User::find(JWTAuth::user()->id);

        if (Cookie::has('remember_me')) {

            $currentUser->setting->remember_me = 0;
            $currentUser->setting->lookup = NULL;
            $currentUser->setting->validator = NULL;
            $currentUser->setting->ip_address = NULL;
            $currentUser->setting->user_agent = NULL;
            $currentUser->setting->expire_in = NULL;

            $currentUser->setting->save();
        }

        $this->updateIsLoggedIn($currentUser->id);

        JWTAuth::setToken($token)->invalidate();

        $cookie = Cookie::forget('remember_me');

        return response()->json(
            [
                'logout' => true
            ],
            200
        )->withCookie($cookie);
    }

    /**
     * update is_logged_in column to false
     * @param Int
     * @return void
     */

    private function updateIsLoggedIn(Int $userId)
    {

        $userStatus = new Status($userId);

        $userStatus->updateStatus(false, 'offline');
    }
}
