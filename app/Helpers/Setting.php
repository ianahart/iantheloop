<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Privacy;
use App\Models\Setting as SettingModel;
use DateTime;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class Setting
{
  private int $currentUserId;
  private ?string $error;
  private array $exception = [];
  private array $searches = [];
  private array $users = [];

  public function __construct(int $currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
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

      $currentUser = User::find($this->currentUserId);

      $newUserSettings = new SettingModel();

      $newUserSettings->user_id = $this->currentUserId;

      $newUserSettings->remember_me = false;

      $newUserSettings->block_profile_on = false;
      $newUserSettings->block_messages_on = false;
      $newUserSettings->block_stories_on = false;

      $newUserSettings->save();

      $currentUser->refresh();

      $token = JWTAuth::fromUser($currentUser, $currentUser->getJWTCustomClaims());

      return json_encode([
        'access_token' => $token,
        'profile_pic' => $currentUser->profile->profile_picture ?? '',
        'profile_created' => JWTAuth::user()->profile_created,
        'status' => 'online',
      ]);
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

      if (JWTAuth::user()->id !== $this->currentUserId) {
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

      if (JWTAuth::user()->id !== $currentUser->id) {
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
      if (JWTAuth::user()->id !== $this->currentUserId) {
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
}
