<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;




class RegisterController extends Controller
{
    /*
    /**
     * Perform validation and register user.
     *
     * @param  Request  $request
     *
     * @return Response
     **/

    public function store(RegisterRequest $request)
    {

        $isValidated = false;

        $validated = $request->validated();

        if ($validated) {

            $isValidated = true;
        }

        $userData = array_shift($validated);

        $exists = User::where('email', $userData['email'])->first();

        if ($exists) {

            return response()->json(
                [
                    'errors' => 'Email is already taken'
                ],
                409
            );
        }

        $user = User::create(
            [
                'full_name' => strtolower($userData['firstName']) . ' ' . strtolower($userData['lastName']),
                'first_name' => strtolower($userData['firstName']),
                'last_name' => strtolower($userData['lastName']),
                'email' => strtolower($userData['email']),
                'password' => Hash::make($userData['password']),
                'is_logged_in' => false,
            ]
        );

        if ($isValidated) {

            $user->save();
            $user->refresh();

            $user->slug = Str::slug($user->first_name . ' ' . $user->last_name . ' ' . $user->id, '-');
            $user->save();

            return response()->json(

                [
                    'message' => 'success',
                    'status', 201,
                    'isSubmitted' => true,
                ],
                201
            );
        }
    }
}
