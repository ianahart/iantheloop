<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NewsFeedController extends Controller
{
    public function index(Request $request)
    {

        return response()->json(
            [
                'bill' => 'blue',
                'john' => 'red',
                'nikki' => 'yellow'
            ],
            200
        );
    }
}
