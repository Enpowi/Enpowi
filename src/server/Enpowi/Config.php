<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/4/15
 * Time: 1:06 PM
 */

namespace Enpowi;

use R;

class Config
{
	public $moduleDirectory;

	public $themeModule = 'default';

	public $db = [
		'host' => '',
		'name' => '',
		'user' => '',
		'password' => ''
	];

	public function __construct($moduleDirectory = null)
	{
		if ($moduleDirectory === null) {
			$moduleDirectory = dirname(__FILE__) . '/../../../modules/';
		}

		if (file_exists($moduleDirectory)) {
			$this->moduleDirectory = $moduleDirectory;
		} else {
			throw new \Exception("Modules directory, $moduleDirectory, does not exist");
		}
	}

	public function setupMySql($host, $name, $user, $password)
	{
		$this->db['host'] = $host;
		$this->db['name'] = $name;
		$this->db['user'] = $user;
		$this->db['password'] = $password;

		R::setup('mysql:host=' . $host . ';dbname=' . $name, $user, $password);
	}

	public function setupThemeModule($moduleName)
	{
		$moduleDir = $this->moduleDirectory . $moduleName;

		if (file_exists($moduleDir)) {
			$this->themeModule = $moduleName;
		} else {
			throw new \Exception("Module directory, $moduleDir, does not exist");
		}
	}
}