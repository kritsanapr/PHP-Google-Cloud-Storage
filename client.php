<?php

require_once 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

$privateKeyFileContent = '{
  "type": "service_account",
  "project_id": "[PROJECT-ID]",
  "private_key_id": "[KEY-ID]",
  "private_key": "-----BEGIN PRIVATE KEY-----\n[PRIVATE-KEY]\n-----END PRIVATE KEY-----\n",
  "client_email": "[SERVICE-ACCOUNT-EMAIL]",
  "client_id": "[CLIENT-ID]",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://accounts.google.com/o/oauth2/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/[SERVICE-ACCOUNT-EMAIL]"
  }';

$bucketName = 'gcs-scg_home_distnext_b2b2c-rudysalesforce';
$projectId  = 'scg-cbm-bpd-ba';
$cloudPath  = 'files';

class UploadFile
{

  function __construct()
  { }

  function uploadFile($bucketName, $cloudPath)
  {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    $fileContent = '555.png';
    // connect to Google Cloud Storage using private key as authentication
    try {
      $storage = new StorageClient([
        'keyFile' => json_decode($privateKeyFileContent, true)
      ]);
    } catch (Exception $e) {
      // maybe invalid private key ?
      print $e;
      return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);

    // upload/replace file 
    $storageObject = $bucket->upload(
      $fileContent,
      ['name' => $cloudPath]
      // if $cloudPath is existed then will be overwrite without confirmation
      // NOTE: 
      // a. do not put prefix '/', '/' is a separate folder name  !!
      // b. private key MUST have 'storage.objects.delete' permission if want to replace file !
    );

    // is it succeed ?
    return $storageObject != null;
  }

  function getFileInfo($bucketName, $cloudPath)
  {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    // connect to Google Cloud Storage using private key as authentication
    try {
      $storage = new StorageClient([
        'keyFile' => json_decode($privateKeyFileContent, true)
      ]);
    } catch (Exception $e) {
      // maybe invalid private key ?
      print $e;
      return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->object($cloudPath);

    // $storage = new StorageClient();
    // $bucket = $storage->bucket($bucketName);
    try {
      $object = $bucket->object($cloudPath);
      $object->downloadToFile(__DIR__ . '/file' . 'test.txt');
      echo 'Path :' . __DIR__ . '/file' . "\n";
      echo 'Success : ' . getcwd() . '/file' . "\n";
    } catch (Exception $e) {
      echo $e->getMessage();
      return $e->getMessage();
    }


    print_r($object->info());
    echo "\n";
    echo "List of files \n";
    $this->listFiles($bucket);
    return $object->info();
  }

  //this (listFiles) method not used in this example but you may use according to your need 
  function listFiles($bucket, $directory = null)
  {

    if ($directory == null) {
      // list all files
      $objects = $bucket->objects();
    } else {
      // list all files within a directory (sub-directory)
      $options = array('prefix' => $directory);
      $objects = $bucket->objects($options);
    }

    foreach ($objects as $object) {
      print $object->name() . PHP_EOL;
      // NOTE: if $object->name() ends with '/' then it is a 'folder'
    }
  }
}

$file = new UploadFile();
$file->uploadFile($bucketName, $cloudPath);
$file->getFileInfo($bucketName, $cloudPath);
