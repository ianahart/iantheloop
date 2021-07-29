<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Messenger;
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
    * @param string $recipientId
    * @return JsonResponse
    */
    public function showChatMessages(Request $request, string $recipientId)
    {

        try {

            $messenger = new Messenger(JWTAuth::user()->id);

            $messenger->setMetaData(
                [
                    'last_message' => $request->query('id'),
                    'created_at' => $request->query('createdAt')
                ]
            );

            $messenger->aggregateChatMessages($recipientId);

            $conversationId = $messenger->getConversationId();


            $exception = $messenger->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'chat_messages' => $messenger->getChatMessages()['chat_messages'],
                        'total' => $messenger->getChatMessages()['total'],
                        'notifications_read' => $messenger->getChatMessages()['notifications_read'],
                        'conversation_id' => $conversationId,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'something went wrong',
                        'error' => $e->getMessage(),
                        'conversation_id' => $messenger->getConversationId()
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

            $messenger = new Messenger($request->all()['chat_message']['sender']['sender_user_id']);

            $messenger->setNewMessage($request->all()['chat_message']);

            $messenger->storeNewMessage(intval($request->all()['conversation_id']));


            $exception = $messenger->getError();
            if (!is_null($exception)) {
                throw new Exception($exception);
            }
            $conversationId = $messenger->getConversationId();
            return response()
                ->json(
                    [
                        'msg' => 'broadcasting',
                        'conversation_id' => $conversationId,
                    ],
                    200
                );
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
