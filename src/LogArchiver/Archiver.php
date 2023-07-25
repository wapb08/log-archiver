<?php

namespace LogArchiver;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use LogArchiver\Zip;

class Archiver
{
  // private variables log path
  private $logPath;
  // s3 bucket name
  private $bucketName;
  // s3 key name
  private $keyName;
  // region
  private $region;
  // acl
  private $acl;
  // version
  private $version;
  // s3 client
  private $s3;

  // constructor
  public function __construct($config = [])
  {
    $this->logPath = $config['logPath'] ?? '/writable/logs';
    $this->bucketName = $config['bucketName'] ?? null;
    $this->keyName = $config['keyName'] ?? null;
    $this->region = $config['region'] ?? 'us-east-1';
    $this->acl = $config['acl'] ?? 'public-read';
    $this->version = $config['version'] ?? 'latest';

    $this->s3 = new S3Client([
        'version' => $this->version,
        'region'  => $this->region
    ]);
  }

  public function archive($fileNamePrefix, $fileNme = null)
  {
    try {
      $fileNme = $fileNme ?? $fileNamePrefix . '.zip';
      // Create zip file
      $zip = new Zip($this->logPath, $fileNme);
      $zipArchive = $zip->createWithPrefix($fileNamePrefix);
      // Upload data.
      $result = $s3->putObject([
        'Bucket'     => $this->bucketName,
        'Key'        => $this->keyName,
        'SourceFile' => $zipArchive,
        'ACL'        => $this->acl
      ]);
  
      // Print the URL to the object.
      return $result['ObjectURL'];
    } catch (S3Exception $e) {
      throw new \Exception('Error uploading file to S3');
    }
  }
}