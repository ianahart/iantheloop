<?php


namespace App\Helpers;

use App\Events\UserStatusChanged;
use App\Models\User;
use Exception;

class Status
{
  public string $currentUserId;
  public string $exception = '';
  public string $userStatus = '';

  public function __construct($currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getUserStatus()
  {
    return $this->userStatus;
  }

  public function getException()
  {

    return $this->exception ? $this->exception : '';
  }

  /*
  * update authentication column
  * @param bool $authStatus
  * @param string $userStatus
  * @return void
  */

  public function updateStatus(bool $authStatus, string $userStatus)
  {

    try {
      User::where('id', '=', $this->currentUserId)
        ->update(
          [
            'is_logged_in' => $authStatus,
            'status' => $userStatus
          ]
        );

      $user = User::find($this->currentUserId);
      broadcast(new UserStatusChanged($user));

      $this->userStatus = $userStatus;
    } catch (Exception $e) {

      error_log(print_r($e->getMessage(), true));

      $this->exception = $e->getMessage();
    }
  }
}
