<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    public function store(Request $request)
    {

        $header = $request->header('Authorization');

        $token = str_replace('Bearer ', '', $header);

        JWTAuth::setToken($token)->invalidate();

        return response()->json(['logout' => true], 200);
    }
}
