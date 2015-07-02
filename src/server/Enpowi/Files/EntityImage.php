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

	public function setEntityName($value)
	{
		$this->entityName = $value;
		return $this;
	}

	public function setEntityId($value)
	{
		$this->entityId = $value;
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
		$check = getimagesize($tmp);

		if($check === false) {
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

		return move_uploaded_file($tmp, $path);
	}

	public function thumbnail()
	{
		$path = $this->path();
		if (!file_exists($path)) return $this;

		$imagick = new Imagick($path);
		$imagick->scaleImage(self::$thumbnailWidth, self::$thumbnailHeight, true);
		$imagick->writeImage($path . 'thumb');

		return $this;
	}

	public function dir()
	{
		return path . "/protected/" . $this->entityName . "/";
	}

	public function path()
	{
		return $this->dir() . $this->entityId;
	}

	public function toString()
	{
		$imgPath = $this->path();
		if (file_exists($imgPath)) {
			return file_get_contents($imgPath);
		}
		return '';
	}

	public function toStringBase64()
	{
		return 'data:image/;base64,' . base64_encode($this->toString());
	}

	public function toThumbString()
	{
		$path = $this->path();
		if (!file_exists($path)) {
			$this->thumbnail();
		}
		return file_get_contents($path);
	}

	public function toThumbBase64()
	{
		return 'data:image/;base64,' . base64_encode($this->toThumbString());
	}

	public function setUploadData($file) {
		$this->uploadData = $file;
		return $this;
	}
}