<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
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

  public function __construct(Int $userId)
  {
    $this->userId = $userId;
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
   * @param void
   * @return void
   */
  public function retrieveBaseProfile(): void
  {

    try {

      $profileExists = User::where('id', $this->userId)->value('profile_created');

      if (!$profileExists) {

        throw new Exception();
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

      $viewingUser = User::find($this->userId);
      $profile['view_user_first_name'] = FormattingUtil::capitalize(explode(' ', $viewingUser->full_name)[0]);

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
    error_log(print_r($data, true));
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
