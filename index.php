<?php

require_once "vendor/autoload.php";

use Google\Cloud\Storage\StorageClient;

define('API_KEY', "AIzaSyC11LLjzpxj4Q-7fhjO6Bn3WMsGQ7yT_gU");
define('SERVICE_ACCOUNT', 'service-764003332647@gs-project-accounts.iam.gserviceaccount.com');
define('SERVICE_ACCOUNT_KEY', 'a0e9d24dbebe96e0d1842ff8a6202d5e0114bc61');

// Storage URI https://storage.googleapis.com
// Access key GOOG1EHGZVHRVJOBHQOWWQ73O3Y4ISFRGEILWYNIJCMTV6YSXU54TEXOSRMMQ
// Secret Jc+TZ4Nb9jL6avP8YpOuN6W09JyqIQLWouz5W5yu

class FileUpload
{

  function __construct()
  {
    $bucketName = 'gcs-scg_home_distnext_b2b2c-rudysalesforce';
    $projectId = 'scg-cbm-bpd-ba';
    $serviceAccountPath = getcwd() . '/key_part/cbm-dist-rudy-salesforce-dev-72e485d900aa.json';

    // $this->auth_cloud_explicit($projectId, $serviceAccountPath);  // Check authorization
    // $this->create_bucket_class_location($bucketName);  // Create new bucket service account ไม่น่ามีสิทธิ์ถึง
    $this->uploadFile($bucketName, $serviceAccountPath);  // Upload file to google cloud storage 
  }

  function auth_cloud_explicit($projectId, $serviceAccountPath)
  {
    # Explicitly use service account credentials by specifying the private key
    # file.
    try {
      $config = [
        'keyFilePath' => $serviceAccountPath,
        'projectId' => $projectId,
      ];
      $storage = new StorageClient($config);

      # Make an authenticated API request (listing storage buckets)
      foreach ($storage->buckets() as $bucket) {
        printf('Bucket: %s' . PHP_EOL, $bucket->name());
        echo 'Bucket: %s' . PHP_EOL, $bucket->name();
      }
    } catch (Exception $e) {
      printf($e->getMessage());
      // echo $e->getMessage();
    }
  }

  function create_bucket_class_location($bucketName)
  {
    // $bucketName = 'my-bucket';
    try {
      $storage = new StorageClient();
      $storageClass = 'COLDLINE';
      $location = 'ASIA';
      $bucket = $storage->createBucket($bucketName, [
        'storageClass' => $storageClass,
        'location' => $location,
      ]);

      $objects = $bucket->objects([
        'encryption' => [
          'defaultKmsKeyName' => null,
        ]
      ]);

      printf('Created bucket %s in %s with storage class %s', $bucketName, $storageClass, $location);
    } catch (Exception $e) {
      echo $e->getMessage();
      return $e->getMessage();
    }
  }

  function uploadFile($bucketName, $serviceAccountPath)
  {
    try {
      $storage = new StorageClient([
        'keyFilePath' => $serviceAccountPath,
      ]);

      $fileName = 'test.txt';
      $bucket = $storage->bucket($bucketName);
      
      $object = $bucket->upload($fileName, [
        'name' => 'rudy_object'
      ]);
      echo "File uploaded successfully. File path is: https://storage.googleapis.com/$bucketName/$fileName";
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}

$storeFile = new FileUpload();
$storeFile;
