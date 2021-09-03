<?php

namespace App\Helpers;

use App\Models\Story as StoryModel;


class Story
{

  private int $currentUserId;
  private ?string $error;


  public function setCurrentUserId($currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }
}
