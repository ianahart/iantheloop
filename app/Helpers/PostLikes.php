<?php

namespace App\Helpers;

use Exception;


class PostLikes
{

  private string $exception;
  private int $currentUserId;
  private int $postId;
  private array $postLikeToDel;
  public object $postLike;
  public object $post;
  private object $newLike;

  public function __construct($postLike, $post)
  {
    $this->postLike = $postLike;
    $this->post = $post;
  }


  public function setCurrentUserId($currentUserId)
  {

    $this->currentUserId = $currentUserId;
  }

  public function setPostId($postId)
  {

    $this->postId = $postId;
  }

  public function getException()
  {
    return isset($this->exception) ? $this->exception : NULL;
  }

  public function getNewLike()
  {

    return $this->newLike;
  }

  public function setPostLikeToDel($postLikeToDel)
  {
    $this->postLikeToDel = $postLikeToDel;
  }

  public function addLikeToPost()
  {
    try {

      $newLike = new $this->postLike;

      $newLike->post_id = $this->postId;
      $newLike->user_id = $this->currentUserId;

      $formatted = explode(' ',  $newLike->user->full_name);

      $formatted = array_map(
        function ($word) {
          return strtoupper(substr($word, 0, 1)) . substr($word, 1);
        },
        $formatted
      );

      $newLike->liker_name = implode(' ', $formatted);

      $newLike->save();
      $newLike->refresh();

      $this->newLike = $newLike;

      $post = $newLike->post;
      $post->likes = $post->likes + 1;

      $post->save();
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  public function deletePostLike()
  {
    try {

      $like = $this->postLike
        ->where('id', '=', $this->postLikeToDel['id'])
        ->where('user_id', '=', $this->postLikeToDel['user_id'])
        ->first();

      $post = $like->post;

      $post->likes = $post->likes - 1;

      $post->save();

      $like->delete();
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }
}
