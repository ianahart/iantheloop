<?php


namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use DateTime;
use Exception;

class Network
{

  private ?string $error;
  private int $currentUserId;
  private int $ownerUserId;
  private LengthAwarePaginator $network;
  private array $owner;
  private bool $hasNetwork;


  public function __construct(int $currentUserId, $ownerUserId)
  {
    $this->currentUserId = $currentUserId;
    $this->ownerUserId = $ownerUserId;
  }


  public function getError()
  {

    return isset($this->error) ? $this->error : NULL;
  }

  public function getNetwork()
  {
    return $this->network;
  }

  public function getHasNetwork()
  {
    return $this->hasNetwork;
  }

  public function getOwner()
  {
    return $this->owner;
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



  /**
   * Get the id's of users in the current user's following list
   * @param String
   * @return void
   */
  public function aggregateUserList(String $type)
  {

    try {


      $currentUser = User::find($this->currentUserId);
      $ownerUser = User::find($this->ownerUserId);

      $this->owner = [
        'id' => $ownerUser->id,
        'owner_full_name' => $ownerUser->full_name,
        'profile_id' => $ownerUser->profile->id,
        'owner_profile_picture' => $ownerUser->profile->profile_picture ?? '',
      ];


      $list = $type === 'following' ? $ownerUser->stat->following : $ownerUser->stat->followers;


      $actualUserList = is_null($list) ? [] : array_keys($list);

      $users = User::whereIn(
        'id',
        $actualUserList
      )
        ->select(['id', 'full_name'])
        ->with(
          ['profile' => function ($query) {
            $query->select(
              [
                'company',
                'position',
                'user_id',
                'profile_picture',
                'display_name'
              ]
            );
          }]
        )->paginate(3);

      $this->cleanUpStats($actualUserList, $type, $ownerUser);

      if ($this->currentUserId !== $this->ownerUserId) {
        $currentUserFollowList = $currentUser->stat->following;
      }

      if (isset($currentUserFollowList)) {

        $this->makeUserList($users, $currentUserFollowList);
      } else {

        $this->network = $users;
      }
    } catch (Exception $e) {
      error_log(print_r($e->getLine(), true));

      $this->exception = $e->getMessage();
    }
  }

  /**
   * Get the profiles associated with the users in the following list and construct array of data
   * @param Illuminate\Pagination\LengthAwarePaginator
   * @param Array
   * @param Int
   *
   */
  public function makeUserList(LengthAwarePaginator $users, array $currentUserFollowList)
  {
    foreach ($users as $user) {

      $currentUserFollowIDs = array_keys($currentUserFollowList);
      if (is_null($currentUserFollowIDs)) {

        $user->curUserFollowing = false;
        return;
      }

      if (in_array($user->id, $currentUserFollowIDs)) {
        $user->curUserFollowing = true;

        $user->follow_time = $this->formatFollowTime($currentUserFollowList[$user->id]['timestamp']);
      } else {
        $user->curUserFollowing = false;
      }
    }

    $this->network = $users;
  }

  /**
   * Remove old follow/followers left behind from a deactivated/removed account
   * @param Array
   * @param String
   * @param User
   * @return void
   */

  private function cleanUpStats(array $actualUserList, String $type, User $ownerUser): void
  {
    try {

      $expectedUserList = User::whereIn('id', $actualUserList)->pluck('id')
        ->toArray();

      $toCleanUp = array_values(array_diff($actualUserList, $expectedUserList));

      $cleanedList = collect($ownerUser->stat->{$type})->filter(function ($item, $key) use ($toCleanUp) {
        if (!in_array($key, $toCleanUp)) {
          return $item;
        }
      });

      $count = $type === 'following' ? 'following_count' : 'followers_count';

      $subtractCount = count($actualUserList) - count($expectedUserList);

      $ownerUser->stat->update(
        [
          $type => $cleanedList,
          $count => count($actualUserList) - $subtractCount,
        ]
      );
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * Format the follow time into a readable date;
   * @param int $timestamp;
   * @return string;
   */
  private function formatFollowTime(int $timestamp): string
  {
    $followTime = new DateTime();

    $followTime->setTimestamp($timestamp);

    $followTimeDate = $followTime->format('F jS Y');

    return $followTimeDate;
  }
}
