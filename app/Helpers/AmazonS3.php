<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class AmazonS3
{
  public $fileName;
  public $file;
  private string $type;

  public function __construct($fileName, $file)
  {

    $this->fileName = $fileName;
    $this->file = $file;
    $this->s3 = Storage::disk('s3');
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function uploadToBucket()
  {

    try {

      $body =  isset($this->type) && !is_null($this->type) ?   $this->file : file_get_contents($this->file);
      $this->s3->put($this->fileName, $body, 'public');
    } catch (S3Exception $e) {

      return $e->getMessage();
    }
  }

  public function downloadFromBucket()
  {

    $fileURL = $this->s3->url($this->fileName);

    return $fileURL;
  }

  public function deleteFromBucket()
  {
    try {

      $this->s3->delete($this->fileName);
    } catch (S3Exception $e) {
      error_log(print_r($e->getMessage(), true));
      return $e->getMessage();
    }
  }
}
