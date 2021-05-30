<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Status;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /*
  * update the users status to either "online" or "offline"
  * @param Request $request
  * @param string $userId
  * @return void
  */

    public function updateUserStatus(Request $request, string $userId)
    {
        try {


            if (JWTAuth::user()->id !== intval($userId)) {

                throw new Exception('User is unauthorized to make this request');
            }

            $userStatus = new Status($userId);

            $userStatus->updateStatus(true, $request->all()['status']);

            $exception = $userStatus->getException();

            if ($exception) {

                throw new Exception($exception);
            }

            $newUserStatus = $userStatus->getUserStatus();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'new_user_status' => $newUserStatus,
                        'status_updated' => true,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'conflict',
                        'error' => $e->getMessage(),
                        'intercept' => false,
                        'status_updated' => false,
                    ],
                    401
                );
        }
    }
}
