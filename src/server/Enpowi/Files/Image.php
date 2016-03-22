<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/19/15
 * Time: 2:18 PM
 */

namespace Enpowi\Files;

use Imagick;

class Image extends File
{
  public static $thumbnailWidth = 220;
  public static $thumbnailHeight = 220;

  public function __construct($bean = null)
  {
    parent::__construct($bean);
    $this->classType = __CLASS__;
  }

  public function thumbnail()
  {
    if (empty($this->hash)) return $this;

    $imagick = new Imagick(self::$path . '/' . $this->hash);
    $imagick->scaleImage(self::$thumbnailWidth, self::$thumbnailHeight, true);
    $imagick->writeImage(self::$path . '/' . $this->hash . 'thumb');

    return $this;
  }

  public function upload()
  {
    if (
      file_exists($this->tempPath)
      && getimagesize($this->tempPath) !== false
    ) {
      if (parent::upload()) {
        $this->thumbnail();
        return true;
      }
    }
    return false;
  }

  public function toThumbString()
  {
    $path = self::$path . '/' . $this->hash . 'thumb';
    if (!file_exists($path)) {
      $this->thumbnail();
    }
    return file_get_contents($path);
  }
}