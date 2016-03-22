<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 7/2/15
 * Time: 10:33 AM
 */

namespace Enpowi\Files;

use Respect\Validation\Validator as v;
use Imagick;

class EntityImage
{
  public static $thumbnailWidth = 220;
  public static $thumbnailHeight = 220;

  public static $basePath = '/protected/';
  public $entityName = null;
  public $entityId = null;
  public $uploadData = [];

  private $oldEntityName = null;
  private $oldEntityId = null;

  public function setEntityName($value)
  {
    if ($this->oldEntityName === $this->entityName) {
      $this->oldEntityName =
      $this->entityName = $value;
    } else {
      $this->entityName = $value;
    }
    return $this;
  }

  public function setEntityId($value)
  {
    if ($this->oldEntityId === $this->entityId) {
      $this->oldEntityId =
      $this->entityId = $value;
    } else {
      $this->entityId = $value;
    }
    return $this;
  }

  public function upload()
  {
    if ($this->entityName === null || $this->entityId === null) {
      throw new \Exception('property entityName and entityId must be set to other than null');
    }

    $dir = $this->dir();
    if (!file_exists($dir)) return false;

    $path = $this->path();
    $tmp = $this->uploadData['tmp_name'];

    if (!file_exists($tmp)) return false;

    try {
      $check = getimagesize($tmp);
    } catch (\Exception $e) {
      return false;
    }

    if ($check === false) {
      return false;
    }

    switch ($check['mime']) {
      case image_type_to_mime_type(IMAGETYPE_GIF):
      case image_type_to_mime_type(IMAGETYPE_PNG):
      case image_type_to_mime_type(IMAGETYPE_JPEG):
        break;
      default:
        return false;
    }

    if (move_uploaded_file($tmp, $path)) {
      $this->thumbnail();
      return true;
    }

    return false;
  }

  public function thumbnail()
  {
    $path = $this->path();

    if (file_exists($path)) {
      $imagick = new Imagick($path);
      $imagick->scaleImage(self::$thumbnailWidth, self::$thumbnailHeight, true);
      $imagick->writeImage($path . 'thumb');
    }

    return $this;
  }

  public function dir()
  {
    return path . "/protected/" . $this->entityName . "/";
  }

  private function oldDir()
  {
    return path . "/protected/" . $this->oldEntityName . "/";
  }

  public function path()
  {
    return $this->dir() . $this->entityId;
  }

  private function oldPath()
  {
    return $this->oldDir() . $this->oldEntityId;
  }

  public function toString()
  {
    if ($this->entityId === null) {
      return '';
    }

    $imgPath = $this->path();
    if (file_exists($imgPath)) {
      return file_get_contents($imgPath);
    }
    return '';
  }

  public function toStringBase64()
  {
    if ($this->entityId === null) {
      return '';
    }

    return 'data:image/;base64,' . base64_encode($this->toString());
  }

  public function toThumbString()
  {
    if ($this->entityId === null) {
      return '';
    }

    $path = $this->path() . 'thumb';
    if (!file_exists($path)) {
      $this->thumbnail();
    }

    if (!file_exists($path)) {
      return file_get_contents($path);
    }

    return '';
  }

  public function toThumbBase64()
  {
    if ($this->entityId === null) {
      return '';
    }

    return 'data:image/;base64,' . base64_encode($this->toThumbString());
  }

  public function setUploadData($file)
  {
    $this->uploadData = $file;
    return $this;
  }

  public function convert()
  {
    if ($this->oldEntityId !== $this->entityId || $this->oldEntityName !== $this->entityName) {
      $oldPath = $this->oldPath();
      $newPath = $this->path();
      if (file_exists($oldPath)) {
        if (rename($oldPath, $newPath)) {
          $this->oldEntityId = $this->entityId;
          $this->oldEntityName = $this->entityName;
        }
      }
    }

    return $this;
  }

  public static function getMaximumFileUploadSize()
  {
    return File::getMaximumFileUploadSize();
  }
}