<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\FollowRequest;



class FollowRequestController extends Controller
{

    /*
    * Find all the follow requests for the current user
    * @param Request $request
    * @return JSONResponse
    */

    public function index(Request $request)
    {

        try {

            $currentUserId = $request->query('userId');

            $followRequest = new FollowRequest;
            $followRequest->setReceiverUserId($currentUserId);


            $followRequests = $followRequest->findFollowRequests();

            return response()
                ->json(
                    [
                        'msg' => 'follow requests loaded',
                        'follow_requests' => $followRequests,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'No follow requests found',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }


    /*
    * Store a follow request
    * @param Request $request
    * @return JSONResponse
    */

    public function store(Request $request)
    {

        try {

            $followRequest = new FollowRequest;

            $followRequest->setReceiverUserId($request->all()['receiver_user_id']);
            $followRequest->setRequesterUserId($request->all()['requester_user_id']);

            $followRequest->addFollowRequest();

            $error = $followRequest->getError();

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }

            $followStatus = $followRequest->getFollowStatus();

            return response()
                ->json(
                    [
                        'msg' => 'Follow request sent.',
                        'follow_status' => $followStatus,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Follow request failed to send.',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }


    /*
    * remove pending request if the current user is the receiver
    * @param Request $request
    /*@param String $requestId
    * @return JSONResponse
    */

    public function delete(Request $request, string $requestId)
    {

        try {

            $followRequest = new FollowRequest();

            $followRequest->setReceiverUserId(intval($request->query('userId')));

            $followRequest->removeFollowRequest(intval($requestId));

            $error = $followRequest->getError();

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }

            return response()
                ->json(
                    ['msg' => 'Follow request removed'],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Action not allowed by this user',
                        'intercept' => false,
                        'error' => $e->getMessage()
                    ],
                    403
                );
        }
    }
}
