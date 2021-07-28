<?php

use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\ChatChannel;
use App\Broadcasting\UserStatusChannel;
use App\Broadcasting\NotificationChannel;




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


Broadcast::channel('chat.{conversation}', ChatChannel::class);
Broadcast::channel('userstatus', UserStatusChannel::class);
Broadcast::channel('unreadmessage.{userId}', NotificationChannel::class);
