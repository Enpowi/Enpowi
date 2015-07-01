<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/19/15
 * Time: 2:17 PM
 */

namespace Enpowi\Files;

use Enpowi\App;
use Enpowi\Generic\DataItem;
use RedBeanPHP\R;

class File extends DataItem
{
	public $id;
	public $date;
	public $description;
	public $name;
	public $tags;
	public $email;
	public $hash;
	public $type;
	public $classType;
	public $tempPath;
	public $size;

	public static $path = null;

    private $_bean = null;

	public function __construct($bean = null)
	{
		if (self::$path === null) {
			self::$path = path . '/protected/files/';
		}
		$this->convertFromBean();
		$this->_bean = $bean;
	}

	public function convertFromBean()
	{
		$this->classType = __CLASS__;
		$bean = $this->_bean;
		if ($bean !== null) {
			$this->id = $bean->id;
			$this->email = $bean->email;
			$this->hash = $bean->hash;
			$this
				->setType($bean->type)
				->setSize($bean->size)
				->setDescription($bean->description)
				->setName($bean->name)
				->setTags($bean->tags);
		}
		return $this;
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

	public function upload()
	{
		$hash = $this->hash = hash_file('md5', $this->tempPath);
		$newPath = self::$path . '/' . $hash;
		if (file_exists($this->tempPath)) {
			$uploaded = move_uploaded_file( $this->tempPath, $newPath );
			$this->save();
		} else {
			throw new \Exception('File not found');
		}

		return $uploaded;
	}

	public function save() {
		$bean = $this->_bean;
		if ($bean === null) {
			$bean = $this->_bean = R::dispense( 'file' );
            $bean->userId = App::get()->user()->id;
		}

		$bean->date = R::isoDateTime();
		$bean->description = $this->description;
		$bean->name = $this->name;
		$bean->tags = $this->tags;
		$bean->hash = $this->hash;

		return $this->id = R::store( $bean );
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
		if (($bean = R::findOne('file', ' hash = :hash ', ['hash' => $hash])) && self::inShare($bean)) {
			return !empty($bean->classType) ? new $bean->classType($bean) : new File($bean);
		}
		return null;
	}

	public function toString()
	{
		return file_get_contents(self::$path . '/' . $this->hash, $this->tempPath);
    }

    public function bean()
    {
        return $this->_bean;
	}
}