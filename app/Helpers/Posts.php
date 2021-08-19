<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use App\Helpers\AmazonS3;
use App\Helpers\FormattingUtil;
use App\Jobs\ProcessInteraction;
use App\Models\User;


use DateTime;
use DateTimeZone;
use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class Posts
{

  const PAG_LIMIT = 3;

  private object $user;
  private object $post;
  private array $newPost;
  private int $currentUserId;
  private int $activePostId;
  public array $data;
  public int $subjectUserId;
  public int $lastPostItem;
  public string $exception;
  public int $statusCode;
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

  public function setCurrentUserId($currentUserId)
  {

    $this->currentUserId = $currentUserId;
  }

  public function setColumnToUpdate($columnToUpdate)
  {

    $this->columnToUpdate = $columnToUpdate;
  }

  public function setActivePostId($activePostId)
  {
    $this->activePostId = $activePostId;
  }

  public function getException()
  {

    return isset($this->exception) ? $this->exception : NULL;
  }

  public function getNewPost()
  {

    return $this->newPost;
  }

  public function getStatusCode()
  {
    return $this->statusCode;
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

    $sender = User::find($this->data['author_user_id']);

    $interaction = [
      'sender_name' => FormattingUtil::capitalize($sender->full_name),
      'recipient_name' => FormattingUtil::capitalize($post->user->full_name),
      'sender_user_id' => $sender->id,
      'recipient_user_id' => $post->user->id,
      'post_id' => $post->id,
      'photo_link' => $post->photo_link,
      'blurb' => FormattingUtil::blurb($post->post_text),
      'sender_profile_picture' => $sender->profile->profile_picture,
      'text' =>  FormattingUtil::capitalize($sender->full_name) . ' posted to your wall.',
    ];

    ProcessInteraction::dispatch($interaction, $post->user);

    $this->newPost = $post->refresh()->toArray();

    $this->subjectUserId = $this->newPost['subject_user_id'];
    $this->newPost = $this->enhancePosts([$this->newPost]);
  }

  /*
  * find all posts for a given profile wall
  * @param Mixed String|NULL $filters
  * @return void
  */

  public function findPosts(Mixed $filters)
  {

    try {

      $formattedFilters = [];

      if (!is_null($filters)) {
        $filters = json_decode($filters, true);

        foreach ($filters as $outerIndex => $filter) {
          $key = array_key_first($filter);
          $formattedFilters[$key] = $filter[$key];
        }
      }

      if ($this->lastPostItem === 0) {
        if (is_null($filters)) {
          $posts = $this->user::find($this->subjectUserId)
            ->posts()
            ->latest()
            ->paginate(self::PAG_LIMIT);
        } else {
          $posts = $this->filterPosts($formattedFilters, 'initialReq');
        }
      } else {

        if (count($formattedFilters) > 0) {

          $posts = $this->filterPosts($formattedFilters, 'subseqReq');
        } else {

          $posts = $this->user::find($this->subjectUserId)
            ->posts()
            ->where('id', '<', $this->lastPostItem)
            ->orderBy('id', 'DESC')
            ->paginate(self::PAG_LIMIT);
        }
      }

      if ($posts->total() <= 0) {

        throw new ModelNotFoundException('all records fetched');
      }

      $postsCollection = [];

      foreach ($posts as $post) {
        array_push($postsCollection, $this->collectPostComments($post));
      }

      $enhancedPosts = $this->enhancePosts($postsCollection);

      $this->lastPostItem = $postsCollection[count($postsCollection) - 1]['id'];

      $this->posts = $enhancedPosts;
    } catch (ModelNotFoundException $e) {

      $this->exception = $e->getMessage();
    }
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

    return FormattingUtil::capitalize($user->full_name);
  }

  /*
  * fetch all of the comments and reply comments for a post
  *@param object $post
  *@return array $post
  */

  private function collectPostComments(object $post): array
  {

    $post->postLikes;
    $comments = $post->comments()
      ->orderBy('id', 'DESC')
      ->whereNull('reply_to_comment_id')
      ->paginate(self::PAG_LIMIT);

    $post->last_comment = $comments[count($comments) - 1];
    $postComments = [];

    foreach ($comments as $comment) {
      $this->applyDisplayFields($comment);
      $comment->commentLikes;

      $replyComments = $comment
        ->where('reply_to_comment_id', '=', $comment->id)
        ->orderBy('id', 'DESC')
        ->paginate(self::PAG_LIMIT);

      foreach ($replyComments as $replyComment) {
        $this->applyDisplayFields($replyComment);
        $replyComment->commentLikes;
        unset($replyComment->user);
      }

      $comment->reply_comments = $replyComments->toArray()['data'];
      $comment->reply_comments_count = $replyComments->total();

      unset($comment->user);
      array_push($postComments, $comment);
    }

    $post->post_comments = $postComments;
    $post->comments_count = $post->comments()->count();

    return $post->toArray();
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

    $author['full_name']  = FormattingUtil::capitalize($author['full_name']);

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

    foreach ($posts as $post) {

      $post['post_posted'] = FormattingUtil::date($post['created_at']);
      $post['subject_name'] = $subjectName;
      $post['seen'] = false;

      $authorColumns = $this->appendAuthorColumns($post['author_user_id']);

      $post = array_merge($post, $authorColumns);

      $enhancedPosts[] = $post;
    }

    return $enhancedPosts;
  }

  public function deletePost()
  {
    try {

      $post = $this->findSinglePost();

      if (is_array($post)) {

        $this->statusCode = $post['status'];
        throw new Exception($post['msg']);
      }

      $filename = null;

      if (isset($post->video_filename)) {

        $filename = $post->video_filename;
      }

      if (isset($post->photo_filename)) {

        $filename = $post->photo_filename;
      }

      if (!is_null($filename)) {

        $this->deleteFile($filename);
      }

      $post->delete();
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }

  /*
    * find post by id and verify the user is allowed to delete
    * @param void
    * @return mixed
    */
  private function findSinglePost()
  {

    $post = $this->post::find($this->activePostId);

    if (is_null($post)) {

      return [
        'msg' => 'Post not found',
        'status' => 404
      ];
    }

    if (
      $this->currentUserId !== $post->author_user_id
      && $this->currentUserId !== $post->subject_user_id
    ) {

      return [
        'msg' => 'User not allowed to delete post',
        'status' => 403
      ];
    }

    return $post;
  }

  /*
  * delete file belonging to post from amazon s3
  * @param string $filename
  * @return void
  */
  private function deleteFile(string $filename)
  {

    $bucket = new AmazonS3($filename, null);
    $bucket->deleteFromBucket();
  }

  /*
  * delete file belonging to post from amazon s3
  * @param object $comment
  * @return void
  */
  private function applyDisplayFields(object $comment)
  {
    $comment->full_name = FormattingUtil::capitalize($comment->user->full_name);
    $comment->profile_picture = $comment->user->profile->profile_picture;
    $comment->posted_date = FormattingUtil::date($comment->created_at);
  }

  /*
  * filter posts by the provided array contents
  * @param array $filters
  * @param string $type
  * @return Illuminate\Pagination\LengthAwarePaginator Object
  */
  private function filterPosts(array $filters, string $type)
  {

    $postedBy = strtolower($filters['posted_by']);

    $query = NULL;

    if ($postedBy === 'you') {

      $query = ['author_user_id', '=', $this->currentUserId];
    }
    if ($postedBy === 'others') {

      $query =  ['author_user_id', '!=', $this->currentUserId];
    }

    $posts = $postedBy === 'anyone' ? $this
      ->user::find($this->subjectUserId)
      ->posts() : $this->user::find($this->subjectUserId)
      ->posts()
      ->where(...$query);

    if ($type === 'initialReq') {

      $filteredPosts = $posts
        ->whereMonth('created_at', '=', $filters['go_to_month'])
        ->whereYear('created_at', '=', $filters['go_to_year'])
        ->latest()
        ->paginate(self::PAG_LIMIT);
    }

    if ($type === 'subseqReq') {

      $filteredPosts = $posts
        ->where('id', '<', $this->lastPostItem)
        ->whereMonth('created_at', '=', $filters['go_to_month'])
        ->whereYear('created_at', '=', $filters['go_to_year'])
        ->orderBy('id', 'DESC')
        ->paginate(self::PAG_LIMIT);
    }

    return $filteredPosts;
  }

  public function newsfeedPosts(string $slug)
  {

    try {

      $newsfeedPosts = [];
      $currentUser = $this->user->where('slug', '=', $slug)->first();

      if (is_null($currentUser->stat->following)) {
        throw new ModelNotFoundException('all records fetched');
      }

      $followingIds = array_keys($currentUser->stat->following);

      $followingIds[] = $currentUser->id;

      $baseQuery = $this->post
        ->whereIn('subject_user_id', $followingIds)
        ->whereIn('author_user_id', $followingIds)
        ->whereColumn('subject_user_id', '=', 'author_user_id');

      if ($this->lastPostItem === 0) {

        $posts = $baseQuery
          ->latest()
          ->paginate(self::PAG_LIMIT);
      } else {

        $posts = $baseQuery
          ->where('id', '<', $this->lastPostItem)
          ->orderBy('id', 'DESC')
          ->paginate(self::PAG_LIMIT);
      }

      if ($posts->total() <= 0) {
        throw new ModelNotFoundException('all records fetched');
      }

      foreach ($posts as $post) {

        array_push($newsfeedPosts,  $this->collectPostComments($post));
      }
      $enhancedPosts = $this->enhancePosts($newsfeedPosts);

      $this->lastPostItem = $newsfeedPosts[count($newsfeedPosts) - 1]['id'];
      $this->posts = $enhancedPosts;
    } catch (ModelNotFoundException $e) {

      $this->exception = $e->getMessage();
    }
  }
}
