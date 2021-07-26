<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Conversation;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Conversation  $conversation
     * @return array|bool
     */
    public function join(User $user, Conversation $conversation)
    {

        $conversation = Conversation::find($conversation->id);

        [$userOne, $userTwo] = explode(' ', $conversation->participants);

        return $user->id === intval($userOne) || intval($userTwo);
    }
}
