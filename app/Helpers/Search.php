<?php

namespace App\Helpers;

use Illuminate\Pagination\Paginator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Search as SearchModel;
use App\Models\User;
use Exception;


class Search
{
  private int $currentUserId;
  private array $exception = [];
  private string $searchValue;
  private array $results;

  public function __construct(int $currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getException()
  {
    return $this->exception;
  }

  public function getResults()
  {
    return $this->results;
  }

  public function setSearchValue(string $searchValue)
  {
    $this->searchValue = $searchValue;
  }

  /**
   * It will take the search value and try to produce results
   * @param void
   * @return bool
   */
  public function processSearch()
  {

    try {

      $this->searchValue = '%' . $this->searchValue . '%';

      $results = User::join(
        'profiles',
        function ($join) {
          $join->on('users.id', '=', 'profiles.user_id')
            ->where(
              function ($query) {
                $query
                  ->where('users.first_name', 'like', $this->searchValue)
                  ->orWhere('users.last_name', 'like', $this->searchValue)
                  ->orWhere('profiles.company', 'like', $this->searchValue);
              }
            );
        }
      )
        ->orderBy('users.id', 'DESC')
        ->select(
          [
            'users.id as searched_user_id',
            'users.full_name as full_name',
            'profiles.id as profile_id',
            'profiles.company',
            'profiles.profile_picture as profile_picture',
          ]
        )->simplePaginate(5);

      $userIds = $results->pluck('searched_user_id');

      $currentUserFollowing = $userIds->intersect(
        array_keys(User::find($this->currentUserId)->stat->following)
      );

      foreach ($results as $key => $result) {

        $result->cur_user_following = $currentUserFollowing
          ->contains($result->searched_user_id) ? true : false;
      }

      $this->results = $results->toArray();

      return count($this->results) ? true : false;
    } catch (Exception $e) {
      $this->exception['error'] = $e->getMessage();
    }
  }

  /**
   * If there are relevant search results they will be saved
   * @param Array
   */

  public function saveSearch(array $data)
  {
    try {

      if (JWTAuth::user()->id !== intval($data['user_id'])) {
        throw new Exception('Unauthorized action', 403);
      }

      $exists = SearchModel::where('searcher_user_id', '=', intval($data['user_id']))
        ->where(
          function ($query) use ($data) {
            $query
              ->where('searched_user_id', '=', intval($data['searched_user_id']));
          }
        )->first();

      if (!is_null($exists)) {
        throw new Exception('Search has already been saved', 400);
      }

      $search = new SearchModel;

      $search->searcher_user_id = intval($data['user_id']);
      $search->searched_user_id = intval($data['searched_user_id']);

      $search->profile_id = intval($data['profile_id']);

      $search->search_value = $data['search_value'];
      $search->formatted_search_value = implode(
        ' ',
        array_map(
          fn ($word) => strtoupper(substr($word, 0, 1)) . strtolower(substr($word, 1)),
          explode(' ', $data['search_value'])
        )
      );

      $search->created_in_unix = now()->timestamp;
      $search->purge_in_unix = now()->timestamp + 86400 * 90;

      $search->save();
    } catch (Exception $e) {

      $this->exception['error'] = $e->getMessage();
      $this->exception['code'] = $e->getCode();
    }
  }

  /**
   * @param void
   * @return void
   */

  public function recent()
  {
    try {

      $collection = SearchModel::where('searcher_user_id', '=', $this->currentUserId)
        ->with(
          [
            'searchedUser' => function ($query) {
              $query->select(
                [
                  'id as id',
                  'full_name as full_name'
                ]
              );
            }
          ]
        )
        ->with(
          [
            'profile' => function ($query) {
              $query->select(
                [
                  'id as id',
                  'profile_picture as profile_picture',
                  'company as company'
                ]
              );
            }
          ]
        )
        ->orderBy('id', 'DESC')
        ->simplePaginate(3);

      $nextPageUrl = $collection->nextPageUrl();

      $searches = collect(['data' => collect([])]);

      foreach ($collection as $key => $value) {

        $relations = array_merge(
          $value->getRelations()['profile']->toArray(),
          $value->getRelations()['searchedUser']->toArray()
        );

        $searches['data']->push(array_merge($relations, $value->getAttributes()));
      }

      $searches['next_page_url'] = $nextPageUrl;

      $searches = $searches->toArray();

      $this->results = count($searches) > 0 ? $searches : [];
    } catch (Exception $e) {
      $this->exception['error'] = $e->getMessage();
      $this->exception['code'] = $e->getCode();
    }
  }

  /**
   * @param Array $data;
   * @return void
   */
  public function removeSearch(array $data)
  {
    try {

      if (JWTAuth::user()->id !== $this->currentUserId) {
        throw new Exception('Unauthorized action', 403);
      }

      if (is_null($data['id']) && is_null($data['searched_user_id'])) {

        SearchModel::where('searcher_user_id', '=', $this->currentUserId)
          ->delete();
      } else {

        SearchModel::where('id', '=', $data['id'])
          ->where('searched_user_id', '=', $data['searched_user_id'])
          ->where('searcher_user_id', '=', $this->currentUserId)
          ->delete();
      }
    } catch (Exception $e) {
      $this->exception['error'] = $e->getMessage();
      $this->exception['code'] = $e->getCode();
    }
  }
};
