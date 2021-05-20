<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class AmazonS3
{

  public $fileName;
  public $file;

  public function __construct($fileName, $file)
  {

    $this->fileName = $fileName;
    $this->file = $file;
    $this->s3 = new S3Client(
      [
        'version' => 'latest',
        'region' => env('AWS_DEFAULT_REGION'),
      ]
    );
  }

  public function uploadToBucket()
  {

    try {

      $body = file_get_contents($this->file);

      $contentType = $this->file->getClientMimeType();

      $this->s3->putObject(
        [
          'Bucket' => env('AWS_BUCKET'),
          'Key' => $this->fileName,
          'Body' => $body,
          'ContentType' => $contentType,
          'ACL' => 'public-read',
        ]
      );
    } catch (S3Exception $e) {

      return $e->getMessage();
    }
  }

  public function downloadFromBucket()
  {

    $fileURL = $this->s3
      ->getObjectUrl(env('AWS_BUCKET'), $this->fileName);

    return $fileURL;
  }

  public function deleteFromBucket()
  {
    try {

      $this->s3->deleteObject(
        [
          'Bucket' => env('AWS_BUCKET'),
          'Key' => $this->fileName,
        ]
      );
    } catch (S3Exception $e) {
      error_log(print_r($e->getMessage(), true));
      return $e->getMessage();
    }
  }
}
