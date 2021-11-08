<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Privacy;
use App\Models\Setting as SettingModel;
use Exception;
use DateTime;
use DateTimeZone;

class Setting
{
  private int $currentUserId;
  private ?string $error;
  private array $exception = [];
  private array $searches = [];
  private array $users = [];

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function setCurrentUserId($currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getException()
  {
    return $this->exception;
  }

  public function getUsers()
  {
    return $this->users;
  }

  public function getSearches()
  {
    return $this->searches;
  }

  public function makeUserSettings()
  {
    try {

      $newUserSettings = new SettingModel();

      $newUserSettings->user_id = $this->currentUserId;

      $newUserSettings->remember_me = false;

      $newUserSettings->block_profile_on = false;
      $newUserSettings->block_messages_on = false;
      $newUserSettings->block_stories_on = false;

      $newUserSettings->password_updated = false;
      $newUserSettings->password_updated_on = NULL;

      $newUserSettings->save();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param String $type
   * @param String $value
   * @return void
   */
  public function incrementalSearch(String $type, String $value)
  {
    try {

      if (is_null($value) || strlen($value) === 0) {
        $this->searches = [];
        return;
      }

      $currentUser = User::find($this->currentUserId);

      if (
        $currentUser->stat->following === NULL ||
        $currentUser->stat->followers === NULL
      ) {
        $this->searches = [];
        return;
      }

      $network = [
        'following' => array_keys($currentUser->stat->following),
        'followers' => array_keys($currentUser->stat->followers),
      ];

      $users = User::where(
        function ($query) use ($network) {
          $query
            ->whereIn('id', $network['following'])
            ->orWhereIn('id', $network['followers']);
        }
      )
        ->where('full_name', 'like', '%' . strtolower($value) . '%')
        ->whereNotIn(
          'id',
          function ($query) use ($type) {
            $query
              ->select('blocked_user_id')
              ->from('privacies')
              ->where($type, '=', 1)
              ->where('blocked_by_user_id', '=', $this->currentUserId);
          }
        )->select(
          [
            'id',
            'full_name'
          ]
        )
        ->with([
          'profile' =>
          function ($query) {
            $query->select(
              [
                'id as profile_id',
                'user_id',
                'profile_picture'
              ]
            );
          }
        ])->orderBy('id', 'DESC')
        ->paginate(2);

      foreach ($users as $key => $user) {

        $user->is_cur_user_following = in_array($user->id, $network['following']) ? true : false;

        $user->is_user_follower = in_array($user->id, $network['followers']) ? true : false;
      }

      $filtered = array_filter(
        $users->toArray(),
        function ($item, $key) {
          $keep = ['last_page', 'current_page', 'next_page_url', 'data', 'path'];

          if (in_array($key, $keep)) {
            return $key;
          }
        },
        ARRAY_FILTER_USE_BOTH
      );
      foreach ($filtered as $key => $item) {
        if ($key === 'data') {
          $this->searches[$key] = $item;
        } else {
          $this->searches['pagination'][$key] = $item;
        }
      }
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param Array $data
   */
  public function blockUser(array $data)
  {
    try {

      if (Auth::guard('sanctum')->user()->id !== $this->currentUserId) {
        throw new Exception('unAuthorized', 403);
      }

      $currentUser = User::find($this->currentUserId);

      $fields = [
        'setting_id' => $currentUser->setting->id,
        'blocked_user_id' => $data['user_id'],
        'blocked_by_user_id' => $this->currentUserId,
        'profile_id' => $data['profile_id'],
        'stat_id' => $currentUser->stat->id,
        $data['type'] => 1,
        'blocked_profile_duration' => NULL,
        'blocked_messages_duration' => NULL,
        'blocked_stories_duration' => NULL,
        'created_in_unix' => now()->timestamp,
      ];

      $constraints = array_filter(
        array_combine(
          array_keys($fields),
          array_map(
            function ($key, $field) {
              $keep = ['blocked_user_id', 'blocked_by_user_id'];
              if (in_array($key, $keep)) {
                return $field;
              }
            },
            array_keys($fields),
            $fields
          )
        ),
        function ($constraint) {
          if ($constraint !== '' && $constraint !== NULL) {
            return $constraint;
          }
        }
      );

      $blockedUser = Privacy::firstOrNew($constraints, $fields);

      $blockedUser[$data['type']] = 1;
      $blockedUser->save();
    } catch (Exception $e) {

      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else {
        $this->exception = ['msg' => 'Something went wrong blocking user.', 'code' => 500];
      }
    }
  }

  public function retrieveBlockedUsers()
  {
    try {

      $currentUser = User::find($this->currentUserId);
      $IDs = $currentUser->blockedList->pluck('blocked_user_id');

      if ($IDs->count() === 0) {
        $this->users = [];
        return;
      }

      $blockedUsers = User::whereIn('id', $IDs)
        ->select(
          [
            'id',
            'full_name',
          ]
        )->with(
          ['blockedByList' =>
          function ($query) use ($currentUser) {
            $query
              ->where('blocked_by_user_id', '=', $this->currentUserId)
              ->where('stat_id', '=', $currentUser->stat->id)
              ->select(
                [
                  'setting_id',
                  'blocked_user_id',
                  'blocked_profile',
                  'blocked_stories',
                  'created_in_unix',
                  'id as privacy_id',
                  'blocked_messages',
                  'blocked_by_user_id',
                ]
              );
          }]
        )->with(
          ['profile' => function ($query) {
            $query->select(
              [
                'id as id',
                'user_id as user_id',
                'profile_picture as profile_picture'
              ]
            );
          }]
        )->orderBy('id', 'DESC')->cursorPaginate(2);

      foreach ($blockedUsers as $blockedUser) {

        $time = new DateTime();

        $blockedUser->blocked_by_list = $blockedUser->blockedByList->first();
        unset($blockedUser->blockedByList);

        $time->setTimestamp($blockedUser->blocked_by_list->created_in_unix);

        $blockedUser->blocked_by_list->blocked_date = $time->format('M d y');
      }

      $this->users = $blockedUsers->toArray();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param Array $data
   * @return Int
   */
  public function updateBlockedUser(array $data): int
  {
    try {

      $currentUser = User::find($this->currentUserId);

      if (is_null($currentUser)) {
        throw new Exception('UnAuthorized action, user does not exist or is not authorized.', 403);
      }

      if (Auth::guard('sanctum')->user()->id !== $currentUser->id) {
        throw new Exception('UnAuthorized action', 403);
      }

      $types = ['blocked_messages', 'blocked_profile', 'blocked_stories'];
      $dataKeys = array_keys($data['blocked_by_list']);

      $typesBlocked = array_combine(
        $dataKeys,
        array_map(
          function ($key, $value) use ($types, $dataKeys) {
            if (in_array($key, $types)) {
              return $value;
            }
          },
          $dataKeys,
          $data['blocked_by_list']
        )
      );
      $typesBlocked[$data['type']] = $data['is_toggled'];
      $typesBlocked = count(array_filter($typesBlocked));

      $query = Privacy::where('id', '=', $data['blocked_by_list']['privacy_id'])
        ->where('blocked_user_id', '=', $data['blocked_by_list']['blocked_user_id'])
        ->where('blocked_by_user_id', '=', $data['blocked_by_list']['blocked_by_user_id'])
        ->with(
          [
            'setting' => function ($query) use ($data) {
              $query
                ->where('id', '=', $data['blocked_by_list']['setting_id']);
            }
          ]
        );

      if ($typesBlocked !== 0) {
        $query
          ->update(
            [
              $data['type'] => !$data['blocked_by_list'][$data['type']]
            ]
          );
      } else if ($typesBlocked === 0) {

        $query->delete();
      }

      return $typesBlocked;
    } catch (Exception $e) {
      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else {
        $this->exception = ['msg' => 'Something went wrong updating the blocked user.', 'code' => 500];
      }
    }
  }

  /**
   * @param Int $privacyId
   * @return void
   */
  public function deleteBlockedUser(Int $privacyId)
  {
    try {
      if (Auth::guard('sanctum')->user()->id !== $this->currentUserId) {
        throw new Exception('Unauthorized action', 403);
      }

      $currentUser = User::find($this->currentUserId);

      $result = Privacy::where('id', '=', $privacyId)
        ->with(
          ['setting' =>
          function ($query) use ($currentUser) {
            $query
              ->where('id', '=', $currentUser->setting->id);
          }]
        )
        ->where('blocked_by_user_id', '=', $currentUser->id)
        ->delete();

      if ($result === 0 || !$result) {
        throw new Exception('Blocked user not found.', 404);
      }
    } catch (Exception $e) {

      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else if ($e->getCode() === 404) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else {
        $this->exception = ['msg' => 'Something went wrong deleting the blocked user', 'code' => 500];
      }
    }
  }

  /**
   * @param Array
   * @return Array
   */
  public function updateRememberMe(array $data): array
  {
    try {

      $authUser = Auth::guard('sanctum')->user()->id;
      $setting = SettingModel::find($data['setting_id']);
      $cookie = [];

      if ($authUser !== $this->currentUserId || $setting->user_id !== $authUser) {
        throw new Exception('Unauthorized Action', 403);
      }

      if ($data['remember_me'] === false) {
        $setting->lookup = NULL;
        $setting->remember_me = 0;
        $setting->validator = NULL;
        $setting->expire_in = NULL;
        $setting->ip_address = NULL;
        $setting->user_agent = NULL;

        $setting->save();

        return [];
      }

      $lookup = base64_encode(random_bytes(9));
      $storedNonce = base64_encode(random_bytes(18));
      $thirtyDays = 86400 * 30;

      $validator = hash_hmac('sha256', $setting->user->full_name, $data['ip_address'], $storedNonce);

      $setting->lookup = $lookup;
      $setting->remember_me = 1;
      $setting->validator = $validator;
      $setting->expire_in = $thirtyDays;
      $setting->ip_address = $data['ip_address'];
      $setting->user_agent = $data['user_agent'];

      $setting->save();
      $setting->refresh();

      $cookie = [
        'name' => 'remember_me',
        'value' => $lookup . ' ' . $validator,
        'exp' => $thirtyDays
      ];

      return count($cookie) ? $cookie : [];
    } catch (Exception $e) {

      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      }
    }
  }

  /**
   * @param String
   * @return array
   */
  public function retrieveSecuritySettings(String $settingId): array
  {
    try {

      $setting = SettingModel::where('id', '=', $settingId)
        ->select('remember_me', 'password_updated', 'password_updated_on')
        ->first();

      if (is_null($setting)) {
        throw new Exception('Something went wrong loading your settings.');
      }
      $data = $setting->ToArray();

      if (!is_null($data['password_updated_on'])) {
        $time = new DateTime;
        $timezone = new DateTimeZone('America/New_York');

        $time->setTimestamp($data['password_updated_on']);
        $time->setTimezone($timezone);

        $passwordUpdatedOn = $time->format('h:ia, M d Y');

        unset($data['password_updated_on']);
        $data['password_updated_on'] = $passwordUpdatedOn;
      }

      return $data;
    } catch (Exception $e) {
      $this->error = $e->getMessage();
      return false;
    }
  }

  /**
   *@param String
   *@return array
   */
  public function validateRememberMe(String $userAgent)
  {
    try {
      $clientCookie = Cookie::get('remember_me');

      if (is_null($clientCookie)) {
        throw new Exception('No cookie on request');
      }

      [$lookup, $validator] = explode(' ', $clientCookie);

      $match = SettingModel::where('lookup', '=', $lookup)->first();

      if (is_null($match)) {
        throw new Exception('Cookie does not exist');
      }

      $validation = [
        'validator_matched' => hash_equals($match->validator, $validator),
        'user_agent' => $match->user_agent === $userAgent,
        'is_not_expired' => now()->timestamp - $match->updated_at->timestamp < $match->expire_in ? true : false,
      ];

      $requestValidated = true;

      foreach ($validation as $check) {
        if (!$check) {
          $requestValidated = false;
        }
      }

      if (!$requestValidated) {
        throw new Exception('Remember me cookie expired');
      }

      return ['validated' => $requestValidated, 'user_id' => $match->user_id];
    } catch (Exception $e) {

      $this->error = $e->getMessage();
      $this->removeCookie($match->user_id);

      return ['validated' => false, 'user_id' => null];
    }
  }

  /**
   * Remove remember_me cookie
   * @param Int
   * @return void
   */
  private function removeCookie(Int $userId)
  {
    try {
      $userSetting = SettingModel::where('user_id', '=', $userId)
        ->first();

      $userSetting->remember_me = 0;
      $userSetting->lookup = NULL;
      $userSetting->validator = NULL;
      $userSetting->ip_address = NULL;
      $userSetting->user_agent = NULL;
      $userSetting->expire_in = NULL;

      $userSetting->save();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param array
   * @param string
   */
  public function updatePassword(array $data, String $settingId)
  {
    try {

      $setting = SettingModel::find($settingId);


      if (!Hash::check($data['form']['old_password'], $setting->user->password)) {
        throw new Exception('Forbidden action, cannot change settings', 403);
      }

      $setting->user->password = Hash::make($data['form']['password']);
      $setting->user->save();

      $setting->password_updated = true;
      $setting->password_updated_on = now()->timestamp;

      $setting->save();
      $setting->refresh();
    } catch (Exception $e) {

      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else if ($e->getCode() === 404) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else {
        $this->exception = ['msg' => 'Something went wrong updating your password, please try again soon.', 'code' => 500];
      }
    }
  }

  /**
   * @param String
   * @param String
   * @return void
   */
  public function deleteAccount(String $settingId, String $token)
  {
    try {
      $currentUser = User::find(Auth::guard('sanctum')->user()->id);

      if ($currentUser->setting->id !== intval($settingId)) {
        throw new Exception('Forbidden action', 403);
      }

      $fileNames = [];

      if (!is_null($currentUser->profile->profile_filename)) {
        $fileNames[] = $currentUser->profile->profile_filename;
      }

      if (!is_null($currentUser->profile->background_filename)) {
        $fileNames[] = $currentUser->profile->background_filename;
      }

      if (count($fileNames)) {

        foreach ($fileNames as $fileName) {

          $bucket = new AmazonS3($fileName, null);
          $bucket->deleteFromBucket();
        }
      }

      $currentUser->tokens()->delete();
      $currentUser->delete();
    } catch (Exception $e) {
      if ($e->getCode() === 403) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else if ($e->getCode() === 404) {
        $this->exception = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
      } else {
        $this->exception = ['msg' => 'Something went wrong Deleting Account.', 'code' => 500];
      }
    }
  }
}
