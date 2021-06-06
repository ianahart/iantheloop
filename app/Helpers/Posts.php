<?php

namespace App\Helpers;

use App\Helpers\AmazonS3;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Posts
{
  private object $user;
  private object $post;
  public array $data;
  public int $subjectUserId;
  public int $lastPostItem;
  public string $exception;
  public array $posts;

  public function __construct(object $user, object $post)
  {
    $this->user = $user;
    $this->post = $post;
  }

  public function setData($data)
  {

    $this->data = $data;
  }

  public function setSubjectUserId($subjectUserId)
  {

    $this->subjectUserId = $subjectUserId;
  }

  public function setLastPostItem($lastPostItem)
  {
    $this->lastPostItem = $lastPostItem;
  }

  public function getException()
  {

    return isset($this->exception) ? $this->exception : NULL;
  }

  public function getPosts()
  {
    return $this->posts;
  }

  public function getLastPostItem()
  {
    return $this->lastPostItem;
  }


  public function addPost()
  {

    $post = new $this->post();

    $post->author_user_id = intval($this->data['author_user_id']);
    $post->subject_user_id = intval($this->data['subject_user_id']);
    $post->post_text = trim($this->data['post_text']);

    $file = null;
    $fileIdentifier = null;

    if (isset($this->data['photofile'])) {

      $file = $this->data['photofile'];
      $fileIdentifier = 'photo_link';
    } else {

      $file = $this->data['videofile'];
      $fileIdentifier = 'video_link';
    }

    if (!is_null($file)) {

      $fileName = "posts/" . time() . $file->getClientOriginalName();

      $s3Client = new AmazonS3($fileName, $file);

      $s3Client->uploadToBucket();
      $link = $s3Client->downloadFromBucket();

      $fileNameColumn = $fileIdentifier === 'video_link' ? 'video_filename' : 'photo_filename';

      $post->$fileNameColumn = $fileName;
      $post->$fileIdentifier = $link;

      $post->save();
    } else {

      $post->save();
    }
  }

  public function findPosts()
  {

    try {
      // how many models to be returned from the database
      $offset = 2;

      $posts = $this->user::find($this->subjectUserId)
        ->posts()
        ->where('id', '>', $this->lastPostItem)
        ->orderBy('id', 'ASC')
        ->paginate($offset)->toArray();

      if ($posts['total'] <= 0) {

        throw new ModelNotFoundException('all records fetched');
      }

      $enhancedPosts = $this->enhancePosts($posts);

      $this->lastPostItem = $posts['data'][count($posts['data']) - 1]['id'];
      $posts['data'] = $enhancedPosts;
      $this->posts = $posts;
    } catch (ModelNotFoundException $e) {

      $this->exception = $e->getMessage();
    }
  }

  /*
   * format the created_at timestamp and add as a post property
   * @param  string $timestamp
   * @return string;
  */
  private function createPostedDate(string $timestamp)
  {

    $date = new DateTime($timestamp);

    $date->setTimezone(new DateTimeZone('America/New_York'));

    $formattedDate = $date->format('M j Y h:i a');

    return $formattedDate;
  }

  /*
  * retrieve the subject's name to append to post
  * @param  void
  * @return string;
  */
  private function appendSubjectName()
  {
    $user = $this->user::select(['full_name'])
      ->find($this->subjectUserId);

    return $this->formatName($user->full_name);
  }

  /*
  * format a user's name
  * @param  string $fullName
  * @return string;
  */
  private function formatName(string $fullName)
  {
    $formattedName = explode(' ', $fullName);

    $formattedName = array_map(
      function ($word) {

        return strtoupper(substr($word, 0, 1)) . substr($word, 1);
      },
      $formattedName
    );

    return implode(' ', $formattedName);
  }

  /*
  * retrieve properties from the authors profile to append to a post
  * @param  int $authorUserId;
  * @return array;
  */
  private function appendAuthorColumns(int $authorUserId)
  {

    $user = $this->user::find($authorUserId);

    $author = $user
      ->profile()
      ->with('user')
      ->join('users', 'users.id', '=', 'profiles.user_id')
      ->select(
        [
          'user_id',
          'profile_picture',
          'full_name'
        ]
      )
      ->get()
      ->toArray();

    $author = array_shift($author);

    unset($author['user']);

    $author['full_name']  = $this->formatName($author['full_name']);

    return $author;
  }

  /*
  * add on properties to a post
  * @param  array $posts
  * @return array;
  */
  private function enhancePosts(array $posts)
  {
    $enhancedPosts = [];

    $subjectName = $this->appendSubjectName();

    foreach ($posts['data'] as $post) {

      $post['post_posted'] = $this->createPostedDate($post['created_at']);
      $post['subject_name'] = $subjectName;
      $authorColumns = $this->appendAuthorColumns($post['author_user_id']);

      $post = array_merge($post, $authorColumns);

      $enhancedPosts[] = $post;
    }

    return $enhancedPosts;
  }
}
