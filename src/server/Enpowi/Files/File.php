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
	public $userId;
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
		$this->_bean = $bean;
		$this->convertFromBean();
	}

	public function convertFromBean()
	{
		$this->classType = __CLASS__;
		$bean = $this->_bean;
		if ($bean !== null) {
			$this->id = $bean->id;
			$this->userId = $bean->userId;
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

		$bean->classType = $this->classType;
		$bean->date = R::isoDateTime();
		$bean->description = $this->description;
		$bean->name = $this->name;
		$bean->tags = $this->tags;
		$bean->hash = $this->hash;

		return $this->id = R::store( $bean );
	}

	public static function getUserFiles() {
		$id = App::user()->id;
		$beans = R::findAll('file', ' user_id = :userId ', [ 'userId' => $id ]);
		$files = [];
		foreach ($beans as $bean) {
			$files[] = !empty($bean->classType) ? new $bean->classType($bean) : new File($bean);
		}
		return $files;
	}

	public static function getFromHash($hash)
	{
		if (($bean = R::findOne('file', ' hash = :hash ', ['hash' => $hash]))) {
			return !empty($bean->classType) ? new $bean->classType($bean) : new File($bean);
		}
		return null;
	}

	public function toString()
	{
		return file_get_contents(self::$path . '/' . $this->hash);
    }

    public function bean()
    {
        return $this->_bean;
	}

	//http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size#answer-22500394
	//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
	private static function convertPHPSizeToBytes($sSize)
	{
		if ( is_numeric( $sSize) ) {
			return $sSize;
		}
		$sSuffix = substr($sSize, -1);
		$iValue = substr($sSize, 0, -1);
		switch(strtoupper($sSuffix)){
			case 'P':
				$iValue *= 1024;
			case 'T':
				$iValue *= 1024;
			case 'G':
				$iValue *= 1024;
			case 'M':
				$iValue *= 1024;
			case 'K':
				$iValue *= 1024;
				break;
		}
		return $iValue;
	}

	//http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size#answer-22500394
	public static function getMaximumFileUploadSize()
	{
		return min(self::convertPHPSizeToBytes(ini_get('post_max_size')), self::convertPHPSizeToBytes(ini_get('upload_max_filesize')));
	}
}