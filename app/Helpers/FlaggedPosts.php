<?php

namespace App\Helpers;

use App\Models\FlaggedPost;
use Exception;


class FlaggedPosts
{
  private int $userId;
  private int $postId;
  private array $flaggedData;
  private string $error;
  private string $reasons;


  public function setUserId($userId)
  {
    $this->userId = $userId;
  }

  public function setPostId($postId)
  {
    $this->postId = $postId;
  }

  public function setFlaggedData($flaggedData)
  {

    $this->flaggedData = $flaggedData;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function addFlaggedPost()
  {

    try {

      $alreadyFlaggedByCurUser = FlaggedPost::where('user_id', '=', $this->userId)
        ->where('post_id', '=', $this->postId)
        ->first();

      if (!is_null($alreadyFlaggedByCurUser)) {

        throw new Exception('You have already flagged this post');
      }

      $flaggedPost = new FlaggedPost();

      $flaggedPost->post_id = $this->postId;
      $flaggedPost->user_id = $this->userId;

      $this->formatReasons();
      $flaggedPost->reasons = $this->reasons;

      $flaggedPost->save();
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  private function formatReasons()
  {
    $text = '';

    for ($i = 0; $i < count($this->flaggedData); $i++) {

      if ($i === count($this->flaggedData) - 1 && count($this->flaggedData) - 1 !== 0) {

        $text .= 'and ' . $this->flaggedData[$i]['reasonText'];
      } else if (count($this->flaggedData) === 2 && $i === 0) {

        $text .= $this->flaggedData[$i]['reasonText'] . ' ';
      } else if ($i < count($this->flaggedData) - 1) {

        $text .= $this->flaggedData[$i]['reasonText'] . ', ';
      } else {

        $text .= $this->flaggedData[$i]['reasonText'];
      }
    }

    $this->reasons = $text;
  }
}
