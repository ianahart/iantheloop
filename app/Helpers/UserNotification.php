<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

use Exception;
use DateTime;
use DateTimeZone;

class UserNotification
{

  private string $type;
  private int $user;
  private string $error;
  private array|object $notifications;


  public function __construct(int $user)
  {
    $this->user = $user;
  }

  public function setType($type)
  {
    $this->type = implode('', str_replace('/', '\\', str_split($type)));
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getNotifications()
  {
    return $this->notifications;
  }

  public function retrieveByType()
  {
    try {
      // if type is App\Notifications\UnreadMessage use distinct()
      $currentUser = User::find($this->user);

      $this->notifications = $currentUser
        ->notifications()

        ->distinct('data->sender_user_id')
        ->where('data->recipient_user_id', '=', $this->user)
        ->where('type', '=', $this->type)
        ->select(
          [
            'data->sender_user_id as sender_user_id',
            'data->sender_name as sender_name',
            'data->recipient_user_id as recipient_user_id',
            'data->profile_picture as profile_picture',

          ],
        )
        ->get();

      foreach ($this->notifications as $notification) {

        $notification->notification_id = bin2hex(random_bytes(16));

        $total = $currentUser
          ->notifications()
          ->where('data->sender_user_id', '=', $notification->sender_user_id)
          ->whereNull('read_at')
          ->count();


        $latestReadNotification = $currentUser->notifications()
          ->orderBy('read_at', 'DESC')
          ->where('data->sender_user_id', '=', $notification->sender_user_id)
          ->whereNotNull('read_at')
          ->limit(1)
          ->latest()
          ->get();
        error_log(print_r($latestReadNotification->read_at, true));
        // if (count($latestReadNotification->read_at) > 0) {
        //   $notification->latest_read_at =  'date';
        //   // error_log(print_r($notification->latest_read_at->read_at, true));
        // }



        // $notification->new_notifications = isset($notification->latest_read_at) ? false : true;


        $notification->new_notifications = $total > 0 ? true : false;
      }



      $this->notifications = count($this->notifications->toArray()) > 0 ? $this->notifications->toArray() : [];
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function markAsRead(string $senderId)
  {
    try {

      $currentUser = User::find($this->user);

      // if ($currentUser) {
      //   $currentUser
      //     ->unreadNotifications()
      //     ->where('data->sender_user_id', '=', $senderId)
      //     ->update(
      //       [
      //         'read_at' => now()
      //       ]
      //     );
      // }
      $this->makeReadAt();





      // return read at date
      // if new_notifications is true turn to false
      // when false, show read_at date in component
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  private function makeReadAt()
  {
    // $date = new DateTime();
    // $timezone = new DateTimeZone('America/New_York');
    // $date->setTimezone($timezone);
    // $readAt = $date->format('h:i:a m/d/Y');
    // error_log(print_r($readAt, true));

    $epochs = [
      ['name' => 'year', 'value' => 31536000],
      ['name' => 'month', 'value' => 2592000],
      ['name' => 'day', 'value' => 86400],
      ['name' => 'hour', 'value' => 3600],
      ['name' => 'minute', 'value' => 60],
      ['name' => 'second', 'value' => 1],
    ];


    // error_log(print_r($epochs, true));
  }
}











#Steps

#1.) render data to messages popup -- DONE
#2.) Show Mark Read Button And Delete Button -- DONE
#3. ) If Mark Read button is clicked send update request to update all unread --DONE
#messages for that sender_user_id to read_at and hide the Mark Read button and Keep the Delete Button Visible --DONE
#4. ) If Delete button is clicked send a delete request to delete all unread or read_at messages from the sender_user_id
# Hide the delete message
#5. )For either Mark as Read and Delete buttons, if the messenger component
#is open remove unread_message_count from that sender_user_id in the contacts state in messenger.js
#6. ) if notifications in pop up are over 2 months old delete

//today
// yesterday
// 2 days ago
// 3 days ago

// if 30 days ago -- a month ago