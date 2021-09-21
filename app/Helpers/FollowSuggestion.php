<?php

namespace App\Helpers;

use App\Models\FollowSuggestion as FollowSuggestionModel;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;



class FollowSuggestion
{

  const PAG_LIMIT = 2;
  const CHUNK_SIZE = 40;

  private ?int $total;
  private int $userId;
  private string $error;
  private array $followSuggestions = [];
  private bool $suggestionsExist;

  public function __construct($userId)
  {
    $this->userId = $userId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getFollowSuggestions()
  {
    return $this->followSuggestions;
  }

  public function getTotal()
  {
    return $this->total;
  }

  public function getSuggestionsExist()
  {
    return $this->suggestionsExist;
  }

  public function checkSuggestionsExist()
  {
    try {

      $followSuggestion = FollowSuggestionModel::where('user_id', '=', $this->userId)
        ->where('rejected', '=', 0)
        ->first();

      $this->suggestionsExist = is_null($followSuggestion) ? false : true;
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function suggestionsStaticTotal(): int
  {
    try {

      return FollowSuggestionModel::where('user_id', '=', $this->userId)
        ->where('rejected', '=', 0)
        ->where('suggest', '=', 1)
        ->where('pending', '=', 0)
        ->count();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /*
  * Retrieve the current user's follow suggestions in batches
  * @param int $lastSuggestion
  * @return void
  */
  public function retrieve(?int $lastSuggestion): void
  {
    try {


      $followSuggestions = FollowSuggestionModel::OrderBy('id', 'ASC')
        ->OrderBy('created_at', 'ASC')
        ->when($lastSuggestion, function ($query, $lastSuggestion) {
          return $query->where('id', '>', $lastSuggestion);
        })
        ->where('user_id', '=', $this->userId)
        ->where('pending', '=', 0)
        ->where('suggest', '=', 1)
        ->where('rejected', '=', 0)
        ->with(
          [
            'prospect' => function ($queryA) {
              $queryA->with(['profile' => function ($queryB) {
                $queryB->select(['id', 'profile_picture', 'user_id']);
              }]);
            }
          ]
        )
        ->paginate(self::PAG_LIMIT);

      $this->total = $followSuggestions->total();

      if ($this->total === 0) {
        $this->followSuggestions = [];
        return;
      }
      $this->followSuggestions = $followSuggestions->items();

      $this->followSuggestions = $followSuggestions->toArray()['data'];
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function aggregate()
  {
    try {

      $currentUser = User::find($this->userId);
      $followingIDs = array_keys($currentUser->stat->following);

      $currentUserFollowing = collect([]);

      User::whereIn('id', $followingIDs)->whereHas('stat', function ($query) {
        $query->whereNotNull('following');
      })
        ->chunkById(self::CHUNK_SIZE, function ($users) use ($currentUserFollowing) {
          foreach ($users as $user) {

            if (!is_null($user->stat->following) && $currentUserFollowing->count() <= 20) {

              $currentUserFollowing->push($user);
            } else {
              return false;
            }
          }
        });

      $prospects = collect([]);
      $prospectIDs = $currentUserFollowing
        ->flatten()
        ->pluck('stat.following')
        ->flatten(1)
        ->pluck('id')
        ->unique()
        ->values();

      User::join('stats', 'users.id', '=', 'stats.user_id')
        ->select(
          [
            'users.id as id',
            'users.full_name as full_name',
            'stats.following as following'
          ]
        )
        ->whereIn('users.id', $prospectIDs)
        ->whereNotIn('users.id', array_merge($followingIDs, [$this->userId]))
        ->whereNotNull('stats.following')
        ->orderBy('id', 'DESC')

        ->chunkById(
          self::CHUNK_SIZE,
          function ($users) use ($followingIDs, $prospects) {

            foreach ($users as $user) {

              $mutualCount = count(array_intersect($followingIDs, array_keys($user->stat->following)));

              if ($prospects->count() <= 15) {
                if ($mutualCount > 0) {
                  $prospect = $this->packageProspect($user, $mutualCount);
                  $prospects->push($prospect);

                  unset($user->stat);
                  unset($user->profile);
                }
              } else {
                return false;
              }
            }
          }
        );

      $this->createRecords($prospects,  $followingIDs);
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /*
  * Creates a record to be saved in follow suggestions (mostly to cleanup main func for readability)
  * @param object $prospect
  * @param int $count
  * @return void
  */
  private function packageProspect(object $prospect, int $count): array
  {

    return [
      'profile_id' => $prospect->profile->id,
      'user_id' => $this->userId,
      'prospect_user_id' => $prospect->id,
      'unique_identifier' => $prospect->id . '_' . $this->userId,
      'mutual_follows' =>  $count,
      'rejected' => false,
      'rejected_at' => NULL,
      'created_at' => Carbon::now()->toDateTimeString(),
      'updated_at' => NULL,
      'pending' => false,
      'suggest' => true,
    ];
  }

  /*
  *Create records for each follow suggestion
  * @param object $prospect
  * @return void
  */
  public function createRecords(object $prospects): void
  {
    try {

      FollowSuggestionModel::upsert(
        $prospects->toArray(),
        [
          'unique_identifier'
        ],
        [
          'updated_at'
        ]
      );
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /*
  * Update follow suggestion depending on action provided
  * @param array $data
  * @param string $suggestionId
  * @return void
  */
  public function updateFollowSuggestion(array $data, string $suggestionId = NULL): void
  {
    try {

      if ($data['current_user_id'] !== JWTAuth::user()->id) {
        throw new Exception('Unauthorized to make this action');
      }

      $followSuggestion = FollowSuggestionModel::where('user_id', '=', $data['suggestion_user_id'])
        ->where('prospect_user_id', '=', $data['prospect_user_id'])
        ->when(
          $suggestionId,
          function ($query, $suggestionId) {
            return $query
              ->where('id', '=', $suggestionId);
          },
          function ($query) use ($data) {
            return $query
              ->where('unique_identifier', '=', $data['prospect_user_id'] . '_' . $data['suggestion_user_id']);
          }
        )
        ->first();


      switch ($data['suggestion_action']) {
        case "follow":

          $followSuggestion->suggest = 0;
          $followSuggestion->pending = 1;
          break;
        case "reject":

          $followSuggestion->rejected = 1;
          $followSuggestion->rejected_at = time();
          $followSuggestion->suggest = 0;
          break;
        case "unreject":

          $followSuggestion->rejected = 0;
          $followSuggestion->rejected_at = NULL;
          $followSuggestion->pending = 0;
          $followSuggestion->suggest = 1;
          break;
        default:
          break;
      }
      $followSuggestion->save();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
