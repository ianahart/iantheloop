<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Review as ReviewModel;
use App\Models\User;
use Exception;

class Review
{
  const LIMIT = 1;
  private string $error;
  private int $userId;
  private array $review;
  private int $total;
  private int $page;

  public function getTotal()
  {
    return $this->total;
  }

  public function getPage()
  {
    return $this->page;
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

  public function retrieve()
  {
    try {

      $reviews = ReviewModel::join('profiles', 'reviews.user_id', '=', 'profiles.user_id')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->select('profiles.profile_picture', 'users.full_name', 'reviews.*')
        ->orderBy('reviews.id', 'DESC')
        ->orderBy('reviews.created_at', 'DESC')
        ->paginate(self::LIMIT);

      $this->total = $reviews->total();
      $this->page = $reviews->currentPage();
      $this->reviews = $reviews->items();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
