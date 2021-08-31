<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Review as ReviewModel;
use App\Models\User;
use Exception;

class Review
{
  const LIMIT = 5;
  private string $error;
  private int $userId;
  private array $review;
  private array $pagination;


  public function getPagination()
  {
    return $this->pagination;
  }

  public function getReviews()
  {
    return $this->reviews;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function setReview(array $review)
  {
    $this->review = $review;
  }

  public function setUserId(int $userId)
  {
    $this->userId = $userId;
  }

  public function add()
  {
    try {

      if (JWTAuth::user()->id !== $this->userId) {
        throw new Exception('User is not authorized to commit this action', 403);
      }

      $duplicate = ReviewModel::where('user_id', '=', $this->userId)->first();

      if (!is_null($duplicate)) {
        throw new Exception('User has already submitted a review', 400);
      }

      $reviewModel = new ReviewModel();

      $reviewModel->user_id = $this->userId;
      $reviewModel->text = $this->review['text'];
      $reviewModel->rating = $this->review['rating'];
      $reviewModel->likes = 0;
      $reviewModel->reviewed_at = time();

      $reviewModel->save();
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  public function reviewStatus()
  {
    try {

      $user = User::find(JWTAuth::user()->id);

      return is_null($user->review) ? false : true;
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function retrieve(int $page, ?string $filter)
  {
    try {

      $map = [
        'lowest rated' => [
          'column' => 'rating',
          'direction' => 'ASC',
        ],
        'highest rated' => [
          'column' => 'rating',
          'direction' => 'DESC',
        ],
        'newest' => [
          'column' => 'created_at',
          'direction' => 'DESC',
        ],
        'oldest' => [
          'column' => 'created_at',
          'direction' => 'ASC',
        ],
      ];

      $baseQuery = ReviewModel::join('profiles', 'reviews.user_id', '=', 'profiles.user_id')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->select('profiles.profile_picture', 'users.full_name', 'reviews.*');

      if ($filter === NULL || !$filter) {
        $reviews = $baseQuery
          ->orderBy('reviews.id', 'DESC')
          ->orderBy('reviews.created_at', 'DESC')
          ->paginate(self::LIMIT);
      } else {

        $reviews = $baseQuery
          ->orderBy($map[$filter]['column'], $map[$filter]['direction'])
          ->paginate(self::LIMIT);
      }

      $this->pagination = [
        'total' => $reviews->total(),
        'page' => $reviews->currentPage(),
        'last_page' => $reviews->lastPage(),
      ];

      $this->reviews = $reviews->items();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  public function userReview()
  {
    try {

      if (JWTAuth::user()->id !== $this->userId) {
        throw new Exception('User is not authorized', 401);
      }

      $data = ReviewModel::where('reviews.user_id', '=', $this->userId)
        ->join('profiles', 'reviews.user_id', '=', 'profiles.user_id')
        ->with(
          [
            'user' => function ($query) {
              $query->select(
                [
                  'id',
                  'full_name'
                ]
              );
            }
          ]
        )
        ->select(
          [
            'reviews.*',
            'profiles.profile_picture'
          ]
        )
        ->first();

      $review = $data->toArray();
      $review['full_name'] = $review['user']['full_name'];

      unset($review['user']);

      $this->reviews = $review;
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
  /**
   * @param String $reviewId */

  public function update(String $reviewId)
  {

    try {

      if (JWTAuth::user()->id !== intval($this->userId)) {
        throw new Exception('Not authorized to make this transaction', 401);
      }

      $review = ReviewModel::where('id', '=', $reviewId)
        ->where('user_id', '=', $this->userId)
        ->first();

      $review->is_edited = 1;
      $review->text = $this->review['review'];
      $review->rating = $this->review['rating'];

      $review->save();
      $review->refresh();

      $this->reviews = [$review];
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
  /** @param String $reviewId */
  public function delete(String $reviewId)
  {
    try {

      if (intval(JWTAuth::user()->id) !== intval($this->userId)) {
        throw new Exception('Not authorized to make this transaction', 401);
      }

      $review = ReviewModel::where('id', '=', $reviewId)
        ->where('user_id', '=', $this->userId)
        ->first();

      $review->delete();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
