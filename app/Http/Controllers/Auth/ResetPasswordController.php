<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class ResetPasswordController extends Controller
{

    /*
     * Reset password
     * @param Request $request
     * @return JsonResponse
     */

    public function store(Request $request)
    {

        try {

            $userId = $this->getUser($request->resetToken);

            $rules = [
                'password' => [
                    'required',
                    'confirmed',
                    'min:6',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'
                ],
                'password_confirmation' => 'required|min:6',
            ];
            $validator = Validator::make($request->formData, $rules);

            if ($validator->fails()) {

                return response()->json(
                    [
                        'formSubmitted' => false,
                        'errors' => $validator->errors(),
                        'form' => $request->formName,
                    ],
                    422
                );
            }

            $passwordReset = $this->getPasswordReset($request->resetToken);

            if (is_null($passwordReset)) {

                return response()->json(
                    [
                        'error' => 'Please try again. Something went wrong, click the link below.'
                    ],
                    404
                );
            }

            $isValid = $this->checkTokenTLL($request->resetToken);

            if (!$isValid) {

                $passwordReset->delete();

                return response()->json(
                    [
                        'error' => 'The link has expired please return to forgot password page'
                    ],
                    404
                );
            }

            $userId = $this->getUser($request->resetToken);

            $status = $this->updatePassword($userId, $request->formData['password']);

            if ($status !== 200) {

                return response()->json(
                    [
                        'error' => 'New password cannot be the same as old password'
                    ],
                    400
                );
            }

            $passwordReset->delete();

            return response()->json(
                [
                    'formSubmitted' => true,
                ],
                200
            );
        } catch (\Exception $e) {

            error_log(print_r($e->getMessage()));
        }
    }

    /*
     * get user out of token
     * @param token string;
     * @return int
     */

    private function getUser(string $token)

    {

        $tokenParts = explode(".", $token);

        $tokenHeader = base64_decode($tokenParts[0]);

        $tokenPayload = base64_decode($tokenParts[1]);

        $jwtHeader = json_decode($tokenHeader);

        $jwtPayload = json_decode($tokenPayload);

        return $jwtPayload->user_id;
    }
    /*
     * check if token has expired
     * @param token string;
     * @return boolean
     */
    private function checkTokenTLL(string $token)
    {

        $isValid = true;

        $TLL = 86400;

        $passwordReset  = PasswordReset::where('token', '=', $token)->first();
        error_log(print_r($passwordReset, true));
        $timestamp = date_timestamp_get($passwordReset->created_at);

        $timeElapsed = time() - $timestamp;

        if ($timeElapsed > $TLL) {

            $isValid = false;
        }
        error_log(print_r($isValid, true));
        return $isValid;
    }

    /*
     * get password reset
     * @param token string;
     * @return object
     */
    private function getPasswordReset(string $token)
    {
        $passwordReset = PasswordReset::where('token', '=', $token)->first();

        return $passwordReset;
    }

    /*
     * update password
     * @param int $userId
     * @param string $newPassword
     * @return int
     */
    private function  updatePassword(int $userId, string $newPassword)
    {

        $user = User::find($userId);

        if (Hash::check($newPassword, $user->password)) {

            return 400;
        }

        $user->password = Hash::make($newPassword);

        $user->save();

        return 200;
    }
}
