<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/19/15
 * Time: 2:18 PM
 */

namespace Enpowi\Files;

use Imagick;

class Image extends File {
	public function __construct($bean = null) {
		parent::__construct($bean);
		$this->classType = __CLASS__;
	}

	public function resize()
	{

	}

	public function upload()
	{
		if (getimagesize($this->tempPath) !== false) {
			return parent::upload();
		}
		return false;
	}
}