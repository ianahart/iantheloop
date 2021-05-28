<?php


namespace App\Helpers;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Tymon\JWTAuth\Facades\JWTAuth;

class Network
{
  public object $stat;
  public object $profile;
  public object $user;
  public array $userProfiles;
  public array $followingMetaData;
  public int $page;
  public int $curIndex;
  public mixed $exception;
  private string $userId;
  public int $listCount;
  public string $ownerProfilePic;
  private array $queryFields;



  public function __construct(object $stat, object $profile, object $user)
  {
    $this->stat = $stat;
    $this->profile = $profile;
    $this->user = $user;
  }

  public function setUserId(string $userId)
  {
    $this->userId = $userId;
  }


  public function getProfiles()
  {

    return $this->userProfiles;
  }

  public function setUserProfiles($userProfiles)
  {

    $this->userProfiles = $userProfiles;
  }

  public function getException()
  {

    return isset($this->exception) ? $this->exception : NULL;
  }

  public function setPage($page)
  {

    $this->page = $page;
  }

  public function setCurIndex($curIndex)
  {

    $this->curIndex = $curIndex;
  }

  public function getListCount()
  {

    return $this->listCount;
  }

  public function getOwnerProfilePic()
  {

    return $this->ownerProfilePic;
  }

  public function  setQueryFields($fields)
  {

    $this->queryFields = $fields;
  }


  /*
  * Check if the supplied userId belongs to a user
  * @param void
  * @return void
  */
  public function checkUserExists()
  {

    try {

      $userExists = $this->user::find(intval($this->userId));

      return $userExists;
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  /*
  * Get the profile picture of the user for the current page
  * @param void
  * @return void
  */
  public function queryOwnerProfilePic()
  {
    try {

      $this->ownerProfilePic = $this->profile::where('user_id', '=', intval($this->userId))
        ->select(
          [
            'profile_picture'
          ]
        )->first();


      $this->ownerProfilePic = json_decode(
        $this->ownerProfilePic,
        true
      )['profile_picture'];
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  /*
  * Get the id's of users in the current user's following list
  * @param void
  * @return void
  */
  public function pluckFollowingMetaData()
  {

    try {


      $perPage = 2;

      $followingIDs = NULL;

      $currentUserId = JWTAuth::user()->id;

      $stats = $this->stat::where('user_id', '=', $this->userId)
        ->select($this->queryFields[0], $this->queryFields[1])
        ->first();

      $networkList = $stats->{$this->queryFields[0]};

      $this->listCount = $stats->{$this->queryFields[1]};

      if (is_null($networkList) || count($networkList) === 0) {
        return;
      }

      if ($currentUserId !== $this->userId) {

        $currentUserFollowList = $this->stat::where('user_id', '=', JWTAuth::user()->id)
          ->select('following')
          ->first();
      }

      $followingIDs = array_keys($networkList);
      /**TO TEST ALL OF LIST COMMENT BELOW**/
      $followingIDs = array_slice($followingIDs, $this->curIndex, $perPage);

      foreach ($followingIDs as $key => $value) {


        $this->followingMetaData[] = $networkList[$value];
      }

      if (isset($currentUserFollowList)) {

        $this->showCurrentUserIsFollowing($currentUserFollowList, $currentUserId);
      }
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  /*
  * Get the profiles associated with the users in the following list and construct array of data
  * @param void
  * @return void
  */
  public function makeUserList()
  {

    for ($i = 0; $i < count($this->followingMetaData); $i++) {
      $data = $this->user::find(intval($this->followingMetaData[$i]['id']))
        ->profile;

      $this->userProfiles[] = $this->selectedColumns($data->toArray());
    }

    for ($i = 0; $i < count($this->followingMetaData); $i++) {

      if ($this->userProfiles[$i]['user_id'] === intval($this->followingMetaData[$i]['id'])) {

        $this->userProfiles[$i]['name'] = $this->followingMetaData[$i]['name'];

        $followTime = $this->followingMetaData[$i]['timestamp'];

        $this->userProfiles[$i]['follow_time'] = $this->formatFollowTime($followTime);



        $this->userProfiles[$i]['curUserFollowing'] = isset($this->followingMetaData[$i]['curUserFollowing']) ? true : false;
      }
    }
  }

  /*
  * Only include specified columns of the profile
  * @param array $profile
  * @return array;
  */
  private function selectedColumns(array $profile)
  {

    $selectedColumns = [
      'profile_picture',
      'display_name',
      'company',
      'position',
      'user_id'
    ];

    return array_filter(
      $profile,
      function ($val, $key) use ($selectedColumns) {

        if (in_array($key, $selectedColumns)) {

          return $val;
        }
      },
      ARRAY_FILTER_USE_BOTH
    );
  }

  /*
  * Format the follow time into a readable date;
  * @param int $timestamp;
  * @return string;
  */
  private function formatFollowTime(int $timestamp)
  {
    $followTime = new DateTime();

    $followTime->setTimestamp($timestamp);

    $followTimeDate = $followTime->format('F jS Y');

    return $followTimeDate;
  }

  /*
  * On the users follow list add a following field to the listed following if the current user is following
  * @param object $list
  * @return int $currentUserId
  */

  private function showCurrentUserIsFollowing(object $list, int $currentUserId)
  {

    for ($i = 0; $i < count($this->followingMetaData); $i++) {

      $userOnPageKey = $this->followingMetaData[$i]['id'];

      $match = array_key_exists(
        $userOnPageKey,
        !is_null($list->following) ? $list->following : []
      );

      if ($match && $list->following[$userOnPageKey]['id']) {

        if ($this->followingMetaData[$i]['id'] === $list->following[$userOnPageKey]['id']) {


          if (intval($currentUserId) !== intval($this->userId)) {

            $this->followingMetaData[$i]['curUserFollowing'] = 'following';
          }
        }
      }
    }

    for ($i = 0; $i < count($this->followingMetaData); $i++) {

      $found = array_search(strval($currentUserId), $this->followingMetaData[$i]);

      if ($found) {

        $this->followingMetaData[$i]['curUserFollowing'] = 'following';
        break;
      }
    }
  }
}
