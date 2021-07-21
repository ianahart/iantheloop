<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Messenger;
use App\Events\MessageSent;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;


class MessengerController extends Controller
{
    /*It retrieves users that are following the current user and
    * those users are followed by the current user
    * @param Request $request
    * @param string $userId
    * @return JsonResponse
    */
    #Paginate later on if users grow
    #Switch to show only users that are online

    public function show(Request $request, string $userId)
    {
        try {
            $messenger = new Messenger($userId);

            $messenger->aggregateContacts();

            $exception = $messenger->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Contacts retrieved',
                        'contacts' => $messenger->getContacts(),
                        'contacts_count' => $messenger->getContacts()->count(),
                    ]
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'No contacts available',
                        'errors' => $e->getMessage(),
                    ],
                    404
                );
        }
    }
    /*
    *It retrieves all the messages for the current user's current chat conversation
    * @param Request $request
    * @return JsonResponse
    */
    public function showChatMessages(string $recipientId)
    {

        try {

            $messenger = new Messenger(JWTAuth::user()->id);

            $chatMessages = $messenger->aggregateChatMessages($recipientId);
            $exception = $messenger->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }



            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'chat_messages' => $messenger->getChatMessages(),
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'something went wrong',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
    *It retrieves all the messages for the current user's current chat conversation
    * @param Request $request
    * @return JsonResponse
    */
    public function store(Request $request)
    {

        try {

            $messenger = new Messenger($request['sender']['sender_user_id']);
            $messenger->setNewMessage($request->all());

            $messenger->storeNewMessage();

            // $exception = $messenger->getError();
            // if (!is_null($exception)) {
            //     throw new Exception($exception);
            // }

            return response()->json(['msg' => 'broadcasting'], 200);
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Failed to send message',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }
}
