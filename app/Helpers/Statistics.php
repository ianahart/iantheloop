<?php

namespace App\Helpers;

use Exception;


class Statistics
{
  public object $viewingUser;
  public object $currentUser;
  public string $exception;
  public  $timestamp;



  /*
     * the user being followed is added to the current user's following column
     * the current user is added to the user being followed follower's column
     * @param void
     * @return void
     */
  public function updateFollow()
  {

    try {

      $this->currentUser->following = !isset($this->currentUser->following) ? [] : json_decode($this->currentUser->following, true);
      $this->viewingUser->followers = !isset($this->viewingUser->followers) ? [] : json_decode($this->viewingUser->followers, true);
      $this->currentUser->notifications = !isset($this->currentUser->notifications) ? [] : json_decode($this->currentUser->notifications, true);
      $this->viewingUser->notifications = !isset($this->viewingUser->notifications) ? [] : json_decode($this->viewingUser->notifications, true);

      $this->currentUser->following[] = [
        'id' => $this->viewingUser->user_id,
        'name' => $this->viewingUser->name,
        'timestamp' => $this->timestamp,
      ];

      $this->viewingUser->followers[] = [
        'id' => $this->currentUser->user_id,
        'name' => $this->currentUser->name,
        'timestamp' => $this->timestamp,
      ];

      $this->currentUser->following_count = $this->currentUser->following_count + 1;
      $this->viewingUser->followers_count = $this->viewingUser->followers_count + 1;

      $this->currentUser->notifications[] = [
        'name' => $this->viewingUser->name,
        'notification' => 'You started following ' . $this->viewingUser->name . '.',
        'timestamp' => $this->timestamp,
      ];

      $this->viewingUser->notifications[] = [
        'name' => $this->currentUser->name,
        'notification' => $this->currentUser->name . ' started following you.',
        'timestamp' => $this->timestamp,
      ];
    } catch (Exception $e) {

      error_log(print_r($e->getMessage(), true));

      $this->exception = $e->getMessage();
    }
  }

  /*
     * the user being followed is removed from the current user's following list
     *  the current user is removed from the user being followed followers list.
     * @param void
     * @return void
     */

  public function removeFollow()
  {

    try {
    } catch (Exception $e) {

      error_log(print_r($e->getMessage()));

      $this->exception = $e->getMessage();
    }
  }
}
