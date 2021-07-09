<?php

namespace App\Helpers;

use Exception;

class Statistics
{

  private int $timestamp;
  public bool $userIsFollowing;
  public object $user;
  public object $subject;
  public array $userList;
  public string $userId;
  public array $notifications;
  private string $status;
  public int $listCount;

  public function __construct($user, $subject)
  {

    $this->user = $user;
    $this->subject = $subject;

    $this->timestamp = time();
    $this->userUpdated = false;
    $this->userIsFollowing = false;
  }

  public function getUserIsFollowing()
  {

    return $this->userIsFollowing;
  }

  public function setUserList($userList)
  {
    $this->userList = !isset($userList) ? [] : $userList;
  }

  public function setUserId($userId)
  {

    $this->userId = $userId;
  }

  public function getUserList()
  {

    return $this->userList;
  }

  public function setListCount($listCount)
  {

    $this->listCount = $listCount;
  }

  public function getListCount()
  {

    return $this->listCount;
  }

  public function setNotifications($notifications)
  {

    $this->notifications = !isset($notifications) ? [] : $notifications;
  }

  public function getNotifications()
  {

    return $this->notifications;
  }

  public function setStatus($status)
  {

    $this->status = $status;
  }

  /*
  * Check if the current user is following the subject
  * @param void
  * @return void
  */
  public function checkCurrUserFollowing()
  {
    try {

      $id = strval($this->user->user_id);

      $following = $this->subject->followers[$id];

      if (isset($following)) {

        $this->userIsFollowing = true;
      }
    } catch (Exception $e) {

      if (strlen($e->getMessage())) {

        $this->userIsFollowing = false;
      }
    }
  }

  /*
     * the user being followed is added to the current user's following column
     * the current user is added to the user being followed follower's column
     * @param void
     * @return void
     */
  public function addFollow()
  {
    try {

      $this->userList[$this->userId] = [

        'id' => $this->userId,
        'name' => $this->subject->name,
        'timestamp' => $this->timestamp,
      ];

      $this->listCount = $this->listCount + 1;
    } catch (Exception $e) {

      if (strlen($e->getMessage())) {

        $this->userIsFollowing = false;
      }
    }
  }
  /*
     * the current user is removed from the user's being followed list
     * the user being followed is removed from the current user's follow list
     * @param void
     * @return void
     */
  public function removeFollow()
  {

    try {

      $remove = array_search(strval($this->userId), $this->userList[$this->userId]);

      if ($remove) {

        unset($this->userList[$this->userId]);
      }

      $this->listCount = $this->listCount - 1;
    } catch (Exception $e) {


      $this->exception = $e->getMessage();
    }
  }

  public function addNotification()
  {

    try {

      $message = $this->makeMessage();

      $this->notifications[$this->subject->name] = [

        'name' => $this->subject->name,
        'notification' => $message,
        'timestamp' => $this->timestamp,
      ];
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  private function makeMessage()
  {

    $message = null;

    switch ($this->status) {

      case 'unfollowed':

        $message = 'You unfollowed ' . $this->subject->name . '.';
        break;

      case 'unfollowed by':

        $message = $this->subject->name . ' has unfollowed you.';
        break;

      case 'followed':

        $message = $this->subject->name . ' has started following you.';
        break;

      case 'following':

        $message = 'You started following ' . $this->subject->name . '.';
        break;
    }

    return $message;
  }
}
