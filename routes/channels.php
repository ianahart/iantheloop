<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Message;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {

    $message = Message::where('conversation_id', '=', $conversationId)->first();

    return $user->id === $message->sender_user_id || $message->recipient_user_id;
});
