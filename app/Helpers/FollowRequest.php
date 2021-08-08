<?php

namespace App\Helpers;

use App\Models\FollowRequest as FollowRequestModel;
use App\Models\User as UserModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use DateTime;
use DateTimeZone;

class FollowRequest
{
  private int $receiverUserId;
  private int $requesterUserId;
  private string $followStatus;
  private string $error;


  public function setReceiverUserId($receiverUserId)
  {
    $this->receiverUserId = $receiverUserId;
  }

  public function setRequesterUserId($requesterUserId)
  {
    $this->requesterUserId = $requesterUserId;
  }

  public function getFollowStatus()
  {
    return $this->followStatus;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  /*
  * Add a follow request to the follow requests table
  * @param void
  * @return void
  */

  public function addFollowRequest()
  {

    try {

      $requestExists = $this->checkRequestExists();

      $followRequest = new FollowRequestModel();

      $followRequest->requester_user_id = $this->requesterUserId;
      $followRequest->receiver_user_id = $this->receiverUserId;
      $followRequest->request_date_sent = $this->makeRequestTime();

      $followRequest->save();

      $this->followStatus = 'Requested';

      if ($requestExists) {
        throw new Exception('You have already requested to follow this user');
      }
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  * Check if a request exists by the requester to the receiver
  * @param void
  * @return boolean
  */
  public function checkRequestExists()
  {
    $requestExists = false;

    $result = FollowRequestModel::where('receiver_user_id', '=', $this->receiverUserId)
      ->where('requester_user_id', '=', $this->requesterUserId)
      ->count();

    if ($result > 0) {
      $requestExists = true;
    }

    return $requestExists;
  }

  /*
  * Create a formatted timestamp of the time of creation for the follow request
  * @param void
  * @return string
  */
  private function makeRequestTime()
  {
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('America/New_York'));
    $fDate = $date->format('g:ia,M jS Y');

    return $fDate;
  }

  public function findFollowRequests()
  {
    try {

      $currentUser = UserModel::find($this->receiverUserId);

      $receives = $currentUser->followReceives()->get();

      return $this->collectRequesterData($receives);
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
   *Collect information to display in a request in the ui
   * @param Object $receives
   * @return void
  */
  private function collectRequesterData(object $receives)
  {
    $receivesData = [];

    foreach ($receives as $key => $receive) {

      $receive->full_name = $this->formatRequesterName($receive->user->full_name);
      $receive->requester_profile_picture = $receive->user->profile->profile_picture;

      unset($receive->user);

      array_push($receivesData, $receive->toArray());
    }
    return $receivesData;
  }


  private function formatRequesterName(string $requesterName)
  {
    $requesterName = explode(' ', $requesterName);

    $formatted = array_map(
      function ($word) {
        return strtoupper(substr($word, 0, 1)) . substr($word, 1);
      },
      $requesterName
    );

    return implode(' ', $formatted);
  }

  /*
   *remove a follow request specified by the parameter
   * @param Int $requestId
   * @return void
  */
  public function removeFollowRequest(int $requestId)
  {
    try {

      $deniedRequest = FollowRequestModel::find($requestId);

      $deniedRequest->delete();
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }
}
