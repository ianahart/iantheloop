<?php

namespace App\Http\Controllers\General;

use App\Helpers\Network;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkController extends Controller
{

    /**
     * Get the user's following list
     * @param Request $request
     * @param String $id
     * @return JsonResponse
     */
    public function showFollowing(Request $request, string $userId)
    {

        try {

            $network = new Network(JWTAuth::user()->id, intval($userId));

            $network->aggregateUserList('following');

            $error = $network->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $users = $network->getNetwork();

            $ownerUser = $network->getOwner();

            return response()
                ->json(
                    [
                        'users' => $users,
                        'owner_user' => $ownerUser,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'error',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /**
     * Get the user's followers list
     * @param Request $request
     * @param String $id
     * @return JsonResponse
     */
    public function showFollowers(Request $request, string $userId)
    {

        try {

            $network = new Network(JWTAuth::user()->id, intval($userId));

            $network->aggregateUserList('followers');

            $error = $network->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $users = $network->getNetwork();


            $ownerUser = $network->getOwner();

            return response()
                ->json(
                    [
                        'users' => $users,
                        'owner_user' => $ownerUser,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'error',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }
}
