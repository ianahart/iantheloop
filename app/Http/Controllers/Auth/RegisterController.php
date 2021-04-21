<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


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
        error_log(print_r($exists, true));
        if ($exists) {

            return response()->json(
                [
                    'errors' => 'Email is already taken'
                ],
                409
            );
        }



        // $user = new User();

        // $user->full_name = strtolower($userData['firstName']) . ' ' . strtolower($userData['lastName']);
        // $user->first_name = strtolower($userData['firstName']);
        // $user->last_name = strtolower($userData['lastName']);
        // $user->email = strtolower($userData['email']);
        // $user->password = Hash::make($userData['password']);
        $dood = 'POSDFPSDOFPSDFOSDPFOSDPFSDOFPSDFOSD';
        error_log(print_r($dood, true));
        $user = User::create(
            [
                'first_name' => strtolower($userData['firstName']) . ' ' . strtolower($userData['lastName']),
                'last_name' => strtolower($userData['firstName']),
                'full_name' => strtolower($userData['lastName']),
                'email' => strtolower($userData['email']),
                'password' => Hash::make($userData['password']),
            ]
        );


        if ($isValidated) {

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
