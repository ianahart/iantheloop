<?php

namespace App\Broadcasting;

use App\Models\User;

class NotificationChannel
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
     * @return array|bool
     */
    public function join(User $user, $userId)
    {
        $userToBeNotified = User::find($userId);

        return $user->id === $userToBeNotified->id;
    }
}
