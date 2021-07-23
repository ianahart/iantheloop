<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

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

    $conversation = Conversation::find($conversationId);

    [$userOne, $userTwo] = explode(' ', $conversation->participants);

    return $user->id === intval($userOne) || intval($userTwo);
});
