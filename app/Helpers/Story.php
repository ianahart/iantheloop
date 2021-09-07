<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
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


  public function setCurrentUserId($currentUserId)
  {
    $this->currentUserId = $currentUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
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
}
