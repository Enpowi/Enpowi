<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/3/15
 * Time: 11:13 AM
 */

namespace Enpowi\Modules;


class Component {

	public $module;
	public $name;
	public $file;

	private $extensions = [
		'php',
		'html'
	];

	public function __construct(Module $module, $componentName)
	{
		$this->module = $module;
		$this->name = $componentName;

		foreach ($this->extensions as $extension) {
			$file = $module->folder . '/' . $componentName . '.' . $extension;
			if (file_exists($file)) {
				$this->file = $file;
			}
		}
	}
}