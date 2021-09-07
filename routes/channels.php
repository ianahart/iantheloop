<?php

use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\ChatChannel;
use App\Broadcasting\UserStatusChannel;
use App\Broadcasting\NotificationChannel;
use App\Broadcasting\InteractionChannel;
use App\Broadcasting\StoryChannel;




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
Broadcast::channel('notifications.{userId}', NotificationChannel::class);
Broadcast::channel('stories.{userId}', StoryChannel::class);
