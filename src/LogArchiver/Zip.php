<?php

namespace LogArchiver;

class Zip
{
  private $path;
  private $fileName;
  private $zip;

  // constructor
  public function __construct($path, $fileName)
  {
    $this->path = $path;
    $this->fileName = $fileName;
    $this->zip = new \ZipArchive();
  }

  // check if zip file can be created and open
  public function open()
  {
    $file = $this->path . '/' . $this->fileName;
    if ($this->zip->open($file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
      throw new \Exception('Cannot open ' . $file);
    }
  }

  // close zip file
  public function close()
  {
    $this->zip->close();
  }

  // add file to zip
  public function addFile($fileName)
  {
    $file = $this->path . '/' . $fileName;
    $this->zip->addFile($file, $fileName);
  }

  // create zip file with prefix
  public function createWithPrefix($filePrefix)
  {
    $this->open();
    $files = scandir($this->path);
    foreach ($files as $file) {
      // Exclude . and .. entries
      if ($file !== '.' && $file !== '..') {
        // Check if the file name starts with the prefix
        if (strpos($file, $filePrefix) === 0) {
          // Add the file to the zip archive
          $this->addFile($this->path . $file, $file);
        }
      }
    }
    $this->close();
    // return zip path
    return $this->path . '/' . $this->fileName;
  }

  // get zip
  public function getZip()
  {
    return $this->zip;
  }
}