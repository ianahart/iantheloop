<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Story as StoryModel;
use App\Models\User;
use App\Helpers\AmazonS3;
use App\Jobs\ProcessStoryPhoto;

use Exception;


class Story
{

  private int $currentUserId;
  private ?string $error;
  private array $stories;

  public function __construct(int $currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getStories()
  {
    return $this->stories;
  }

  /**
   * @param void
   * @return Bool
   */
  public function storyLimit()
  {
    try {
      $storyCount = StoryModel::where('user_id', '=', $this->currentUserId)
        ->where('expire_in_unix', '>', now()->timestamp)
        ->count();

      return $storyCount >= 5 ? true : false;
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param Int $userId
   * @return Int
   */

  public function userHasStories(Int $userId)
  {
    return StoryModel::where('user_id', '=', $userId)
      ->where('expire_in_unix', '>', now()
        ->timestamp)->count();
  }

  /**
   * @param Array $data
   * @return void
   */
  public function add(array $data): void
  {

    try {

      $data['duration'] = $data['duration']['value'];

      if (isset($data['file']) && !is_null($data['file'])) {

        $fileName = uniqid()  . '_' .  $data['file']->getClientOriginalName();
        $tmpDirectory = 'storage/stories';

        if (!Storage::exists($tmpDirectory)) {

          Storage::makeDirectory($tmpDirectory);
        }

        Storage::putFileAs($tmpDirectory, $data['file'], $fileName, 'public');

        $data['file'] = ['file_name' => $fileName, 'tmp_directory' => $tmpDirectory];
      }

      ProcessStoryPhoto::dispatch($data);
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /**
   * @param Int $userId
   */

  public function specifiedUserStory(Int $userId)
  {
    try {

      $specifiedUserStories = collect([]);

      StoryModel::where('user_id', '=', $userId)
        ->orderBy('created_at', 'ASC')
        ->where('expire_in_unix', '>', now()->timestamp)
        ->with(
          ['authorUser' => function ($query) {
            return $query->select('id', 'full_name');
          }]
        )
        ->with(
          ['profile' => function ($query) {
            return $query->select('id', 'profile_picture');
          }]
        )
        ->chunkById(
          20,
          function ($stories) use ($specifiedUserStories) {

            foreach ($stories as $story) {

              $story->displayed_time = $this->makeDisplayTime($story->created_at_unix);
              $story->full_name = $story->authorUser->full_name;
              $story->profile_picture = $story->profile->profile_picture;

              unset($story->authorUser);
              unset($story->profile);

              $specifiedUserStories->push($story);
            }
          }
        );

      $this->stories = $specifiedUserStories->toArray();
    } catch (ModelNotFoundException $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * @param Int
   * @return String
   */
  private function makeDisplayTime(Int $createdAt)
  {

    $timeElapsed = now()->timestamp - $createdAt;

    $displayTime = NULL;

    if ($timeElapsed < 60) {

      $displayTime = $timeElapsed . 's';
    } else if ($timeElapsed > 60 && $timeElapsed < 3600) {

      $displayTime = floor($timeElapsed / 60) . 'm';
    } else if ($timeElapsed > 3600) {

      $displayTime = floor($timeElapsed / 3600) . 'hr';
    }

    return $displayTime;
  }

  /**
   * @param Int
   */
  public function removeExpiredUserStories(Int $userId)
  {
    try {

      StoryModel::where('user_id', '=', $userId)
        ->where('expire_in_unix', '<', now()->timestamp)
        ->chunkById(
          20,
          function ($stories) {

            foreach ($stories as $story) {

              if (!is_null($story->photo_link)) {

                $s3Bucket = new AmazonS3('stories/' . $story->photo_filename, null);
                $s3Bucket->deleteFromBucket();
              }
              $story->delete();
            }
          }
        );
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
