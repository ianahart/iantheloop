<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Conversator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;


class ConversatorController extends Controller
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

            $conversator = new Conversator($userId);

            $conversator->aggregateContacts();

            $exception = $conversator->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Contacts retrieved',
                        'contacts' => $conversator->getContacts(),
                        'contacts_count' => $conversator->getContacts()->count(),
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

            $conversator = new Conversator(JWTAuth::user()->id);

            $conversator->setMetaData(
                [
                    'last_message' => $request->query('id'),
                    'created_at' => $request->query('createdAt')
                ]
            );

            $conversator->aggregateChatMessages($recipientId);

            $conversationId = $conversator->getConversationId();


            $exception = $conversator->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'chat_messages' => $conversator->getChatMessages()['chat_messages'],
                        'total' => $conversator->getChatMessages()['total'],
                        'notifications_read' => $conversator->getChatMessages()['notifications_read'],
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
                        'conversation_id' => $conversator->getConversationId()
                    ],
                    404
                );
        }
    }

    /*
    *It stores a user's chat message in a conversation then broadcasts it to the other user
    * @param Request $request
    * @return JsonResponse
    */
    public function store(Request $request)
    {

        try {

            $conversator = new Conversator($request->all()['chat_message']['sender']['sender_user_id']);

            $conversator->setNewMessage($request->all()['chat_message']);

            $conversator->storeNewMessage(intval($request->all()['conversation_id']));


            $exception = $conversator->getError();
            if (!is_null($exception)) {
                throw new Exception($exception);
            }
            $conversationId = $conversator->getConversationId();
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
