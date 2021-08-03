<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Helpers\UserNotification;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    *Show the current user's unread notifications by type
    * @param string $userId
    * @param Request $request
    * @return JsonResponse
    */

    public function showMessageNotifications(Request $request, string $userId)
    {
        try {
            $notification = new UserNotification($userId);
            $notification->setType($request->query('type'));

            $notification->messageNotifications();

            if (!is_null($notification->getError())) {
                throw new Exception($notification->getError());
            }
            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'notifications' => $notification->getNotifications(),
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg', 'No new notifications',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
    *Mark notifications from the sender as read
    * @param Request $request
    * @param string $userId
    * @return JsonResponse
    */
    public function updateMessageNotifications(Request $request, $userId)
    {
        try {

            $notification = new UserNotification($userId);
            $notification->setType($request->all()['type']);

            $notification->markAsRead($request->all()['sender']);

            if (!is_null($notification->getError())) {
                throw new Exception($notification->getError());
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                    ],
                    204

                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to update notifications',
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }

    /*
    *Delete notifications from the sender
    * @param Request $request
    * @param string $userId
    * @return JsonResponse
    */
    public function deleteMessageNotifications(Request $request, string $userId)
    {
        try {

            $notification = new UserNotification($userId);
            $notification->setType($request->all()['type']);

            $notification->deleteMessageNotifications($request->all()['sender']);

            if (!is_null($notification->getError())) {
                throw new Exception($notification->getError());
            }

            return response()->json(
                [
                    'msg' => 'success'
                ],
                200
            );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Unable to delete notifications',
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }
}
