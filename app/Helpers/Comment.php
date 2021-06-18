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
      $query = NULL;
      $TIME_THRESHOLD = $this->makeTimeThreshold();

      if (empty($this->commentId)) {

        $query = CommentModel::where('post_id', '=', $this->postId)
          ->where('user_id', '=', $this->userId)->whereNull('reply_to_comment_id');
      } else {

        $query = CommentModel::where('post_id', '=', $this->postId)
          ->where('user_id', '=', $this->userId)
          ->where('reply_to_comment_id', '=', $this->commentId);
      }


      $numOfComments = $query->where('created_at', '>', $TIME_THRESHOLD)->count();

      if ($numOfComments >= $MAX_LIMIT) {
        $error = empty($this->commentId) ? 'Maximum of 5 comments on the same post per 5 minutes.' :
          'Maximum of 5 replies on the same comment per 5 minutes.';

        throw new CommentMaxLimitException($error, 400);
      }

      $commentModel = new CommentModel();

      $commentModel->post_id = $this->postId;
      $commentModel->user_id = $this->userId;
      $commentModel->comment_text = $this->commentText;
      $commentModel->reply_to_comment_id = empty($this->commentId) ? NULL : $this->commentId;

      $commentModel->save();
      $latestComment = $commentModel->refresh();

      $latestComment->profile_picture = $latestComment->user->profile->profile_picture;
      $latestComment->full_name = $this->formatName($latestComment->user->full_name);
      $latestComment->posted_date = 'Just Posted';
      $latestComment->commentLikes;

      if (!empty($this->commentId)) {

        $latestComment->type = 'reply';
      } else {

        $latestComment->reply_comments = [];
        $latestComment->reply_comments_count = 0;
        $latestComment->type = 'normal';
      }

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

  /*
  *Delete a comment and all of its replies or if it is a reply (singular) delete reply
  *@param String $type
  *@return void
  */
  public function deleteComment(String $type)
  {
    try {

      $comment = CommentModel::find($this->commentId);

      $isCommentAuthor = $comment->user_id === Auth::user()->id;
      $isWallOwner = Auth::user()->id === $comment->post->subject_user_id;

      if (!$isCommentAuthor && !$isWallOwner) {
        throw new Exception('Forbidden action: cannot delete comment that is not yours');
      }
      unset($comment->post);

      if ($type === 'comment') {
        CommentModel::where('reply_to_comment_id', '=', $this->commentId)->delete();
        $comment->delete();
      }

      if ($type === 'reply_comment') {

        $comment->delete();
      }
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  *fetch more comments for the specified post
  *@param String $action
  *@return array
  */
  public function refillComments(String $action)
  {

    try {

      if ($action === 'reply') {
        $comments = CommentModel::where('post_id', '=', $this->postId)
          ->whereNotNull('reply_to_comment_id')
          ->where('reply_to_comment_id', '=', $this->commentId)
          ->where('id', '<', $this->lastComment)
          ->orderBy('id', 'DESC')
          ->paginate($this->getMaxLimit());
      }

      if ($action === 'comment') {
        $comments = CommentModel::where('post_id', '=', $this->postId)
          ->where('id', '<', $this->lastComment)
          ->whereNull('reply_to_comment_id')
          ->orderBy('id', 'DESC')
          ->paginate($this->getMaxLimit());
      }

      if ($comments->count() <= 0 || is_null($comments)) {

        $msg = $action === 'reply' ? 'All replies have been loaded' : 'All comments have been loaded';
        throw new Exception($msg);
      }

      $commentsArray = [];

      foreach ($comments as $comment) {

        $this->buildComment($comment);

        if ($action === 'comment') {
          $replyComments = CommentModel::where('reply_to_comment_id', '=', $comment->id)
            ->orderBy('id', 'DESC')
            ->paginate($this->getMaxLimit());

          $comment->reply_comments_count = $replyComments->total();
          $replies = [];

          for ($i = 0; $i < count($replyComments); $i++) {
            $this->buildComment($replyComments[$i]);

            unset($replyComments[$i]->user);
            array_push($replies, $replyComments[$i]);
          }
          $comment->reply_comments = $replies;
        }

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


  /*
  *Create an offset date
  *@param void
  *@return DateTime
  */
  private function makeTimeThreshold()
  {
    $date = new DateTime();
    $date->format('YYYY-MM-DD hh:mm:ss');
    $timestamp = $date->format('U');

    $timestamp = $timestamp - 300;

    return  $date->setTimestamp($timestamp);
  }

  /*
  *Build comment with additional fields for presentation
  *@param object $comment
  *@return void
  */

  function buildComment(object $comment)
  {
    $comment->profile_picture = $comment
      ->user
      ->profile
      ->profile_picture;
    $comment->full_name = $this->formatName($comment->user->full_name);
    $comment->posted_date = $this->createPostedDate($comment->created_at);
    $comment->commentLikes;
  }
}
