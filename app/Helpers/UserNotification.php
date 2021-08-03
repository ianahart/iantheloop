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

  public function messageNotifications()
  {
    try {

      $currentUser = User::find($this->user);


      $this->notifications = $currentUser
        ->notifications()
        ->orderBy('read_at', 'DESC')
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
        )->limit(5)
        ->get();

      foreach ($this->notifications as $notification) {

        $notification->notification_id = bin2hex(random_bytes(16));

        $latestReadNotification = $currentUser->notifications()
          ->where('data->sender_user_id', '=', $notification->sender_user_id)
          ->latest('created_at')
          ->first();

        if (isset($latestReadNotification->data) && !is_null($latestReadNotification->read_at)) {
          if (intval($notification->sender_user_id) === intval($latestReadNotification->data['sender_user_id'])) {

            $dateTime = new DateTime($latestReadNotification->read_at);
            $notification->latest_read_at = $dateTime->format('U');


            $notification->latest_read_at = $this->makeReadAt($notification->latest_read_at);
          }
        } else {
          $notification->latest_read_at = NULL;
        }
        $notification->new_notifications = !is_null($notification->latest_read_at) ? false : true;
      }

      $this->notifications = count($this->notifications->toArray()) > 0 ? $this->notifications->toArray() : [];
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function deleteMessageNotifications(string $senderId)
  {
    try {

      $currentUser = User::find($this->user);

      if ($currentUser) {

        $currentUser
          ->notifications()
          ->where('type', '=', $this->type)
          ->where('data->sender_user_id', '=', $senderId)
          ->delete();
      }
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function markAsRead(string $senderId)
  {
    try {

      $currentUser = User::find($this->user);

      if ($currentUser) {
        $currentUser
          ->unreadNotifications()
          ->where('data->sender_user_id', '=', $senderId)
          ->update(
            [
              'read_at' => now()
            ]
          );
      }
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  private function makeReadAt(string $timestamp)
  {

    $epochs = [
      ['name' => 'second', 'value' => 1],
      ['name' => 'minute', 'value' => 60],
      ['name' => 'hour', 'value' => 60 * 60],
      ['name' => 'day', 'value' => 60 * 60 * 24],
      ['name' => 'month', 'value' => date("t") === '31' ? 86400 * 31 : 86400 * 30],
      ['name' => 'year', 'value' => 31104000],
    ];
    $message = '';
    $threshold = NULL;
    $group = NULL;
    $ago = time() - $timestamp;

    foreach ($epochs as $key => $epoch) {

      if ($ago >= $epoch['value'] && $epoch['name'] === 'year') {
        $group = $epoch;
        break;
      }

      if (
        $ago >= $epoch['value'] && $ago < $epochs[$key + 1]['value']
      ) {
        $group = $epoch;
        $threshold = $epochs[$key + 1];
        break;
      }
    }

    if ($group['name'] === 'year') {

      $time = round($ago / $group['value']);
    } else {

      $elapsed = round(($threshold['value'] - $ago) / $group['value']);
      $time = round($threshold['value'] / $group['value']) - $elapsed;
    }

    $isPlural = $time === floatval(1) ? '' : 's';
    $message = 'Read ' . $time . ' ' . $group['name'] . $isPlural . ' ' . 'ago';

    return $message;
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