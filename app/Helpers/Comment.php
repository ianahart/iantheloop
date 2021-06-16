<?php

namespace App\Helpers;

use App\Models\Comment as CommentModel;
use App\Models\CommentLike as CommentLikeModel;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CommentMaxLimitException;
use DateTimeZone;
use DateTime;
use Exception;

class Comment
{
  const MAX_LIMIT = 3;

  private array $commentLike;
  private string $commentText;
  private string $error;
  private int $postId;
  private int $userId;
  private int $lastComment;
  private int $commentId;
  private int $code;

  public function setUserId($userId)
  {
    $this->userId = $userId;
  }

  public function setPostId($postId)
  {
    $this->postId = $postId;
  }

  public function setCommentText($commentText)
  {
    $this->commentText = $commentText;
  }

  public function setCommentId($commentId)
  {
    $this->commentId = $commentId;
  }

  public function setLastComment($lastComment)
  {

    $this->lastComment = $lastComment;
  }

  public function setCommentLike($commentLike)
  {
    return $this->commentLike = $commentLike;
  }

  public function getMaxLimit()
  {
    return Comment::MAX_LIMIT;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getCode()
  {
    return $this->code;
  }

  /*Add a comment to a post if conditions met
  *@param void
  *@return object $latestComment
  */
  public function addComment()
  {

    try {

      $MAX_LIMIT = 5;
      $TIME_THRESHOLD = NULL;

      $date = new DateTime();
      $date->format('YYYY-MM-DD hh:mm:ss');
      $timestamp = $date->format('U');

      $timestamp = $timestamp - 300;

      $TIME_THRESHOLD = $date->setTimestamp($timestamp);

      $query = CommentModel::where('post_id', '=', $this->postId)
        ->where('user_id', '=', $this->userId);

      $numOfComments = $query->where('created_at', '>', $TIME_THRESHOLD)->count();

      if ($numOfComments >= $MAX_LIMIT) {

        throw new CommentMaxLimitException('Maximum of 5 comments on the same post per 5 minutes.', 400);
      }

      $commentModel = new CommentModel();

      $commentModel->post_id = $this->postId;
      $commentModel->user_id = $this->userId;
      $commentModel->comment_text = $this->commentText;

      $commentModel->save();
      $latestComment = $commentModel->refresh();

      $latestComment->profile_picture = $latestComment->user->profile->profile_picture;
      $latestComment->author_name = $this->formatName($latestComment->user->full_name);
      $latestComment->posted_date = 'Just Posted';
      $latestComment->commentLikes;

      unset($latestComment->user);

      return $latestComment ?? NULL;
    } catch (CommentMaxLimitException $e) {

      $this->error = $e->getMessage();
      $this->code = $e->getCode();
    }
  }

  /*
  *format user name
  *@param String $comment
  *@return String
  */
  private function formatName(string $name)
  {

    $arr = explode(' ', $name);
    $fullName = '';

    foreach ($arr as $value) {
      $fullName .= strtoupper(substr($value, 0, 1))  . substr($value, 1) . ' ';
    }
    return trim($fullName);
  }

  public function deleteComment()
  {
    try {

      $comment = CommentModel::find($this->commentId);

      $isCommentAuthor = $comment->user_id === Auth::user()->id;
      $isWallOwner = Auth::user()->id === $comment->post->subject_user_id;

      if (!$isCommentAuthor && !$isWallOwner) {
        throw new Exception('Forbidden action: cannot delete comment that is not yours');
      }
      unset($comment->post);

      $comment->delete();
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  *fetch more comments for the specified post
  *@void
  *@return void
  */
  public function refillComments()
  {

    try {

      $comments = CommentModel::where('post_id', '=', $this->postId)
        ->where('id', '<', $this->lastComment)
        ->orderBy('id', 'DESC')
        ->paginate($this->getMaxLimit());

      if ($comments->count() <= 0 || is_null($comments)) {

        throw new Exception('All comments for this post have been loaded');
      }

      $commentsArray = [];

      foreach ($comments as $comment) {

        $comment->profile_picture = $comment
          ->user
          ->profile
          ->profile_picture;
        $comment->full_name = $this->formatName($comment->user->full_name);
        $comment->posted_date = $this->createPostedDate($comment->created_at);
        $comment->commentLikes;

        unset($comment->user);

        array_push($commentsArray, $comment->toArray());
      }

      return $commentsArray;
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  *make a readable date for the ui
  *@void
  *@return void
  */
  private function createPostedDate(string $date)
  {

    $date = new DateTime($date);

    $date->setTimezone(new DateTimeZone('America/New_York'));

    $formattedDate = $date->format('M j Y h:i a');

    return $formattedDate;
  }

  // does the current user like it? how to show that

  public function addCommentLike()
  {
    try {

      $userLikedAlready = CommentLikeModel::where('user_id', '=', $this->commentLike['user_id'])
        ->where('comment_id', '=', $this->commentLike['comment_id'])
        ->first();

      if (!is_null($userLikedAlready)) {
        throw new Exception('You have already liked this comment');
      }

      $commentLike = new CommentLikeModel();

      foreach ($this->commentLike as $key => $value) {
        $void = ['action', 'post_id'];

        if (!in_array($key, $void)) {

          $commentLike->$key = $value;
        }
      }

      $this->updateLikeCount();

      $commentLike->save();
      $commentLike->refresh();

      return $commentLike->id;
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  * Update the comment's like to reflect the action
  * @param void
  * @return void
  */
  private function updateLikeCount()
  {

    $comment = CommentModel::find($this->commentLike['comment_id']);
    $updatedValue = NULL;

    if ($this->commentLike['action'] === 'like') {

      $updatedValue = $comment->likes + 1;
    }

    if ($this->commentLike['action'] === 'unlike') {

      $updatedValue = $comment->likes - 1;
    }
    $comment->likes = $updatedValue;

    $comment->save();
  }

  public function removeCommentLike()
  {

    try {

      $commentLike = CommentLikeModel::where('user_id', '=', $this->commentLike['user_id'])
        ->where('id', '=', $this->commentLike['comment_like_id'])
        ->first();

      $commentLike->delete();

      $this->updateLikeCount();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
