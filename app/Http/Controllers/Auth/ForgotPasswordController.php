<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Models\PasswordReset;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;



class ForgotPasswordController extends Controller
{
    /*
     * validate email address
     * check if it exists
     * send email
     * add token to password_resets_table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {

            $rules =
                [
                    'email' => 'required|email',
                ];

            $validator = Validator::make(
                $request->formData,
                $rules,
            );

            if ($validator->fails()) {

                return response()->json(
                    [
                        'errors' => $validator->errors(),
                        'formSubmitted' => false,
                        'form' => $request->formName,

                    ],
                    422
                );
            }
            $user = User::where('email', '=', $request->formData['email'])->first();

            if (is_null($user)) {

                throw new Exception('Email does not exist');
            }

            $this->SendEmail($request->formData['email'], $user, $request->server('HTTP_USER_AGENT'));

            return response()->json(
                [
                    'status' => 'Email sent',
                    'formSubmitted' => true
                ],
                200
            );
        } catch (\Exception $e) {

            return response()->json(
                [
                    'errors' => [
                        'email' => [$e->getMessage()]
                    ],
                    'formSubmitted' => false,
                    'form' => $request->formName,
                ],
                404
            );
        }
    }


    /*
     * Send Reset Password Email
     *
     * @param string $emailAddress
     * @param object $user
     * @param string $userAgent
     * @return void
     */
    private function SendEmail(string $emailAddress, object $user, string $userAgent)
    {

        try {

            $token = $this->generateToken($user->id);

            /**Change $URL later for Production**/
            $URL = '/reset-password/create?token=' . $token;

            $details = [

                'name' => ucwords($user->first_name),
                'email' => $user->email,
                'URL' => $URL,
                'userAgent' => $userAgent,
            ];

            Mail::to($emailAddress)->send(new ResetPasswordMail($details));

            $this->storeResetToken($token, $user->email);
        } catch (\Exception $e) {

            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                424
            );
        }
    }

    /*
     * generate a password reset token
     *
     * @param id int;
     * @return string $token
     */
    private function generateToken(int $id)
    {

        $data = [
            'user_id' => $id,

        ];
        $customClaims = JWTFactory::customClaims($data);

        $payload = JWTFactory::make($data);

        $token = JWTAuth::encode($payload);

        return $token;
    }

    /*
     * add token to password_resets_table
     *
     * @param string $token
     * @param string $email
     * @return void
     */

    private function storeResetToken(string $token, string $email)
    {

        try {

            $passwordReset =  new PasswordReset();

            $passwordReset->email = $email;
            $passwordReset->token = $token;

            $passwordReset->save();
        } catch (\Exception $e) {

            error_log(print_r($e->getMessage(), true));
        }
    }
}
