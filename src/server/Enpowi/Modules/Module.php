<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/3/15
 * Time: 11:12 AM
 */

namespace Enpowi\Modules;


class Module {

	public $name;
	public $folder;

	public function __construct($folder, $moduleName)
	{
		$this->name = $moduleName;
		$this->folder = $folder . '/' . $moduleName;

	}
}