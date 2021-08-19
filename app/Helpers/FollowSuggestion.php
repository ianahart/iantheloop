<?php

namespace App\Helpers;

use App\Models\FollowSuggestion as FollowSuggestionModel;
use App\Models\User;
use Carbon\Carbon;

use Exception;

class FollowSuggestion
{

  const PAG_LIMIT = 3;

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
        ->paginate(2);


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

      // get current users following (paginate eventually)
      // paginate
      $users = User::whereIn('id', $followingIDs)->with(['stat'])->get();

      $prospects = [];

      foreach ($users as $key => $user) {
        if (!is_null($user->stat->following)) {
          // $prospectFollowing represents a single user's following list
          // paginate
          $prospectFollowing = User::whereIn('id', array_keys($user->stat->following))
            ->with(
              [
                'stat' => function ($query) {
                  $query->select(
                    [
                      'user_id',
                      'following'
                    ]
                  );
                }
              ]
            )->get();
          array_push($prospects, ...$prospectFollowing);
        }
      }

      // return only if the prospect is not already in the current user's following list
      $prospects = collect($prospects)->unique()->map(function ($prospect, $key) use ($followingIDs) {

        if ($prospect->id === $this->userId) {
          return;
        }

        if (
          !in_array($prospect->id, $followingIDs) &&
          $prospect->stat->following !== null &&
          count(array_intersect($followingIDs, array_keys($prospect->stat->following))) > 0
        ) {
          return $prospect;
        }
      });

      $prospects = $prospects->filter();

      $this->createRecords($prospects,  $followingIDs);
    } catch (Exception $e) {
      error_log(print_r('Error: ' . $e->getMessage(), true));
      $this->error = $e->getMessage();
    }
  }

  /*
  *Create records for each follow suggestion
  * @param object $prospect
  * @param array $mutuals
  * @return void
  */
  public function createRecords(object $prospects, array $followingIDs): void
  {
    try {
      $records = [];
      foreach ($prospects as $key => $prospect) {
        $records[] =  [
          'profile_id' => $prospect->profile->id,
          'user_id' => $this->userId,
          'prospect_user_id' => $prospect->id,
          'unique_identifier' => $prospect->id . '_' . $this->userId,
          'mutual_follows' =>  count(array_intersect($followingIDs, array_keys($prospect->stat->following))),
          'rejected' => false,
          'rejected_at' => NULL,
          'created_at' => Carbon::now()->toDateTimeString(),
          'updated_at' => NULL,
        ];

        unset($prospect->stat);
        unset($prospect->profile);
      }

      $poo = FollowSuggestionModel::upsert(
        $records,
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
}
