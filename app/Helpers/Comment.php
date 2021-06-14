<?php

namespace App\Helpers;

use App\Models\Comment as CommentModel;
use DateTime;
use App\Exceptions\CommentMaxLimitException;


class Comment
{
  private string $commentText;
  private string $error;
  private int $postId;
  private int $userId;
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
      $latestComment->author_name = $this->formatName($latestComment);
      $latestComment->posted_date = 'Just Posted';
      unset($latestComment->user);

      return $latestComment ?? NULL;
    } catch (CommentMaxLimitException $e) {

      $this->error = $e->getMessage();
      $this->code = $e->getCode();
    }
  }

  /*
  *format user name
  *@param Object $comment
  *@return string
  */
  private function formatName(object $comment)
  {

    $arr = explode(' ', $comment->user->full_name);
    $fullName = '';

    foreach ($arr as $value) {
      $fullName .= strtoupper(substr($value, 0, 1))  . substr($value, 1) . ' ';
    }
    return trim($fullName);
  }
}
