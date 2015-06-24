<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/19/15
 * Time: 2:17 PM
 */

namespace Enpowi\Files;

use Enpowi\App;
use RedBeanPHP\R;

class File {
	public $id;
	public $date;
	public $description;
	public $name;
	public $tags;
	public $sharedGroupNames;
	public $sharedEmails;
	public $email;
	public $hash;
	public $type;
	public $classType;
	public $tempPath;
	public $size;

	public static $path = null;

	private $bean = null;

	public function __construct($bean = null)
	{
		if (self::$path === null) {
			self::$path = path . '/protected/files/';
		}
		$this->classType = __CLASS__;
		$this->email = App::get()->user()->email;
		$this->bean = $bean;

		if ($bean !== null) {
			$this->email = $bean->email;
			$this->hash = $bean->hash;
			$this
				->setType($bean->type)
				->setSize($bean->size)
				->setDescription($bean->description)
				->setName($bean->name)
				->setTags($bean->tags)
				->setSharedGroupNames($bean->sharedGroupNames)
				->setSharedEmails($bean->sharedEmails);
		}
	}

	public function setType($value) {
		$this->type = $value;

		return $this;
	}

	public function setSize($value) {
		$this->size = $value;
		return $this;
	}

	public function setTempPath($value) {
		$this->tempPath = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->description = $value;
		return $this;
	}

	public function setName($value)
	{
		$this->name = $value;
		return $this;
	}
	public function setTags($value)
	{
		$this->tags = $value;
		return $this;
	}
	public function setSharedGroupNames($value)
	{
		$this->sharedGroupNames = $value;
		return $this;
	}
	public function setSharedEmails($value)
	{
		$this->sharedEmails = $value;
		return $this;
	}

	public function save() {
		$bean = $this->bean;
		if ($bean === null) {
			$bean = $this->bean = R::dispense('file');
			$bean->date = R::isoDateTime();
			$bean->description = $this->description;
			$bean->name = $this->name;
			$bean->tags = $this->tags;
			$bean->sharedGroupNames = $this->sharedGroupNames;
			$bean->sharedEmails = $this->sharedEmails;
			$bean->email = $this->email;
			$hash = $bean->hash = $this->hash = hash_file('md5', $this->tempPath);
			$newPath = self::$path . '/' . $hash;
			$uploaded = move_uploaded_file($this->tempPath, $newPath);

			if ($uploaded) {
				$this->id = R::store( $bean );
				return true;
			}
		}
		return false;
	}

	public static function getUserFiles() {
		$email = App::get()->user()->email;
		$beans = R::findAll('file', ' email = :email ', [ 'email' => $email ]);
		$files = [];
		foreach ($beans as $bean) {
			$files[] = !empty($bean->classType) ? new $bean->classType($bean) : new File($bean);
		}
		return $files;
	}

	public static function getFromHash($hash)
	{
		if ($bean = R::findOne('file', ' hash = :hash ', ['hash' => $hash])) {
			return !empty($bean->classType) ? new $bean->classType($bean) : new File($bean);
		}
		return null;
	}

	public function toString()
	{
		return file_get_contents(self::$path . '/' . $this->hash, $this->tempPath);
	}
}