<?php

namespace App\Helpers;

use App\Models\User;


use Exception;
use DateTime;


class UserNotification
{
  const PAG_LIMIT = 3;
  private string $type;
  private int $user;
  private string $error;
  private array|object $notifications;
  private int|string $currentPage;


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

  public function getCurrentPage()
  {
    return $this->currentPage;
  }

  public function getNotifications()
  {
    return $this->notifications;
  }

  public function interactionNotifications()
  {
    try {

      $results = User::find($this->user)
        ->notifications()
        ->orderBy('created_at', 'DESC')
        ->where('type', '=', $this->type)
        ->cursorPaginate(self::PAG_LIMIT);


      $arrayNotifications = collect([]);

      foreach ($results->items() as $key => $value) {
        $newValue = $value->getAttributes();
        $newValue['data'] = json_decode($newValue['data'], true,);

        $date = new DateTime($newValue['created_at']);
        $newValue['data']['readable_date'] = $this->makeReadableTime($date->format('U'), 'create');

        $arrayNotifications[] = $newValue;
      }

      $this->notifications = $arrayNotifications;
      $this->notifications['next_page_url'] = $results->nextPageUrl();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function messageNotifications(string|null $page)
  {
    try {

      $currentUser = User::find($this->user);

      $this->notifications = $currentUser
        ->notifications()
        ->where('data->recipient_user_id', '=', $this->user)
        ->where('type', '=', $this->type)
        ->distinct('data->sender_user_id')
        ->select(
          [
            'data->sender_user_id as sender_user_id',
            'data->sender_name as sender_name',
            'data->recipient_user_id as recipient_user_id',
            'data->profile_picture as profile_picture',
          ],
        )
        ->paginate(self::PAG_LIMIT);

      $this->currentPage = $this->notifications->currentPage();

      if ($this->currentPage > $this->notifications->lastPage()) {
        $this->currentPage = 'end';
        $this->notifications = [];
        return;
      }

      $this->notifications = $this->notifications->toArray()['data'];

      foreach ($this->notifications as &$notification) {

        $notification['notification_id'] = bin2hex(random_bytes(16));

        $latestReadNotification = $currentUser->notifications()
          ->where('data->sender_user_id', '=', $notification['sender_user_id'])
          ->latest('created_at')
          ->first();

        if (isset($latestReadNotification->data) && !is_null($latestReadNotification->read_at)) {
          if (intval($notification['sender_user_id']) === intval($latestReadNotification->data['sender_user_id'])) {

            $dateTime = new DateTime($latestReadNotification->read_at);
            $notification['latest_read_at'] = $dateTime->format('U');

            $notification['latest_read_at'] = $this->makeReadableTime($notification['latest_read_at'], 'read');
          }
        } else {
          $notification['latest_read_at'] = NULL;
        }
        $notification['new_notifications'] = !is_null($notification['latest_read_at']) ? false : true;
        $notification['created_at'] = $latestReadNotification->created_at;
      }

      $this->notifications = count($this->notifications) > 0 ? $this->notifications : [];
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

  private function makeReadableTime(string $timestamp, string $type): string
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
    $prefix = $type === 'read' ? 'Read ' : '';

    $message = $prefix . $time . ' ' . $group['name'] . $isPlural . ' ' . 'ago';

    return $message;
  }

  public function notificationAlerts(): bool
  {
    $currentUser = User::find($this->user);

    $newAlerts = $currentUser->notifications()->where('type', '=', $this->type)->whereNull('read_at')->first();

    return is_null($newAlerts) ? false : true;
  }

  public function deleteInteractionNotification(string $id)
  {
    try {

      $currentUser = User::find($this->user);

      $currentUser
        ->notifications()
        ->where('notifiable_id', '=', $currentUser->id)
        ->where('id', '=', $id)
        ->delete();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
