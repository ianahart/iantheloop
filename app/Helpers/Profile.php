<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\FormattingUtil;
use App\Helpers\Statistics;
use App\Helpers\FollowRequest;
use App\Models\User;
use App\Models\Profile as ProfileModel;
use App\Models\Stat;
use stdClass;

use Exception;



class Profile
{
  private Int $userId;
  private ?String $error;
  private String $fullName;
  private array $capitalizedColumns;
  private array $baseProfile = [];
  private array $restrictedUser;
  private Bool $currentUserDoesNotFollow;

  public function __construct(Int $userId)
  {
    $this->userId = $userId;
  }

  public function getRestrictedUser()
  {
    return $this->restrictedUser;
  }

  public function getCurrentUserDoesNotFollow()
  {
    return $this->currentUserDoesNotFollow;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getCapitalizedColumns()
  {
    return $this->capitalizedColumns;
  }

  public function getFullName()
  {
    return $this->fullName;
  }

  public function getBaseProfile()
  {
    return $this->baseProfile;
  }


  /**
   * Check to see if the user's block type is profile
   * @param User
   * @param Int
   */
  private function checkBlockType(User $user, Int $targetId)
  {

    $list = $user->blockedList
      ->filter(
        function ($blockedUser, $key) use ($targetId) {

          if ($blockedUser['blocked_user_id'] === $targetId) {
            return $blockedUser['blocked_profile'];
          }
        }
      );

    return $list->count() > 0 ? true : false;
  }

  /**
   * Check if the user viewing the profile is blocked from the subject
   * Or the subject is blocked from the user viewing the profile
   * @param Collection
   * @return Array
   */
  private function isABlockedProfile(Collection $users): array
  {

    $users = $users->map(
      function ($user) {
        return $user->id === $this->userId ? ['viewing_user' => $user] : ['current_user' => $user];
      }
    )->collapse();

    if ($users->count() === 1) {
      return ['users' => [], 'block_exists' => false];
    }

    $currentUserHasBlocked = $this->checkBlockType($users['current_user'], $users['viewing_user']->id);
    $viewingUserHasBlocked = $this->checkBlockType($users['viewing_user'], $users['current_user']->id);

    if (is_null($users['current_user']->stat->following)) {
      $currentUserDoesNotFollow = true;
    } else {
      $currentUserFollowers = array_keys($users['current_user']->stat->following);
      $currentUserDoesNotFollow = !in_array($users['viewing_user']->id, $currentUserFollowers) ? true : false;
    }

    return [
      'users' => [
        'current_user' =>  $currentUserHasBlocked ? $users['current_user']->id : NULL,
        'viewing_user' => $viewingUserHasBlocked ? $users['viewing_user']->id : NULL,
      ],
      'block_exists' => $currentUserHasBlocked || $viewingUserHasBlocked ? true : false,
      'current_user_does_not_follow' => $currentUserDoesNotFollow,
    ];
  }

  /**
   * @param void
   * @return void
   */
  public function retrieveBaseProfile(): void
  {

    try {

      $profileExists = User::where('id', $this->userId)
        ->select(['profile_created', 'full_name'])
        ->first();

      if (!$profileExists['profile_created']) {
        throw new Exception();
      }

      $results = User::whereIn('id', [$this->userId, JWTAuth::user()->id])
        ->get();


      $blocks = $this->isABlockedProfile($results);

      if ($blocks['block_exists']) {
        $this->restrictedUser = $blocks['users'];
        $this->currentUserDoesNotFollow = $blocks['current_user_does_not_follow'];

        throw new Exception('Blocked profile');
      }

      $profile = ProfileModel::where('user_id', '=', $this->userId)
        ->select(
          [
            'id',
            'user_id',
            'company',
            'position',
            'display_name',
            'profile_picture',
            'background_picture',
          ]
        )
        ->first();

      if (!empty($profile)) {

        $this->fullName = $this->makeFullName($this->userId);

        $profile->full_name = $this->fullName;

        $profile = $this->capitalizeColumns($profile->getAttributes(), ['bio']);
      }

      $stat = Stat::where('user_id', '=', $this->userId)->first();

      $currentUserId = JWTAuth::user()->id;
      $currentUser = new stdClass();
      $currentUser->user_id = $currentUserId;


      $statisticInst = new Statistics($currentUser, $stat);

      $statisticInst->checkCurrUserFollowing();

      $currUserFollowing = $statisticInst->getUserIsFollowing();

      if (!$currUserFollowing) {

        $currUserFollowing = false;
      }

      $followRequest = new FollowRequest;

      $followRequest->setRequesterUserId($currentUserId);
      $followRequest->setReceiverUserId($profile['user_id']);

      $currentUserHasRequested = $followRequest->checkRequestExists();

      $profile['current_user_full_name'] = FormattingUtil::capitalize(JWTAuth::user()->full_name);
      $profile['current_user_first_name'] = FormattingUtil::capitalize(JWTAuth::user()->first_name);

      $profile['view_user_first_name'] = FormattingUtil::capitalize(explode(' ', $profileExists['full_name'])[0]);

      $this->baseProfile = [
        'profile' => $profile,
        'stats' => $stat,
        'currUserFollowing' => $currUserFollowing,
        'currUserHasRequested' => $currentUserHasRequested
      ];
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * capitalize table columns
   * @param array
   * @param array
   * @return array
   */
  private function capitalizeColumns(array $data, array $excludes)
  {

    $array = [];

    array_walk($data, function ($val, $key) use (&$array, $excludes) {

      if (isset($val)) {

        $array[$key] = preg_match('/^https?:\/\//', $val)
          || in_array($key, $excludes) ? $val : ucwords($val);
      }
    });
    return $array;
  }

  /** Get user full name
   * @param string $userId
   * @return string
   */
  public function makeFullName(string $userId)
  {
    $user = User::where('id', '=', $userId)->first();

    return $user->full_name;
  }

  /**
   * Format about data
   * @param array
   * @return array;
   */
  public function formatAboutData(array $data)
  {

    $data = $this->capitalizeColumns($data, ['bio', 'description']);

    $data['interests'] = array_map(
      function ($interest) {
        $interest['name'] = ucwords($interest['name']);
        return $interest;
      },
      json_decode(json_decode($data['interests'], true), true)
    );

    $data['links'] = json_decode(json_decode($data['links'], true));

    $data['bio'] = $this->punctuateParagraph($data['bio']);

    $data['description'] = $this->punctuateParagraph($data['description']);

    return $data;
  }

  /**
   * Punctuate paragraph text
   * @param string
   * @return string
   */
  private function punctuateParagraph(string $paragraph)
  {
    $words = explode(' ', strtolower(trim($paragraph)));

    $punctuated = [];

    for ($i = 0; $i < count($words); $i++) {

      if (str_ends_with($words[$i], '.')) {

        if ($i === count($words) - 1) {

          array_push($punctuated, $words[$i]);
        } else if ($i < count($words)) {

          $start = strtoupper(
            substr($words[$i + 1], 0, 1)
          ) . substr($words[$i + 1], 1);

          array_push($punctuated,  $words[$i], $start);
        }
      } else {

        array_push($punctuated,  $words[$i]);
      }
    }

    foreach ($punctuated as $key => $word) {

      if ($key < count($punctuated) - 1 && str_ends_with($punctuated[$key], '.')) {

        $firstChar = substr($punctuated[$key + 1], 0, 1);

        if ($firstChar === ucfirst($firstChar)) {

          array_splice($punctuated, $key + 2, 1);
        }
      }
    }

    $punctuated[0] = ucfirst($punctuated[0]);

    return implode(' ', $punctuated);
  }
}
