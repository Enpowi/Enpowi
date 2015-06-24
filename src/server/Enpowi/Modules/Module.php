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
    private static $paramResponse = [];

	public function __construct($folder, $moduleName)
	{
		$this->name = $moduleName;
		$this->folder = $folder . '/' . $moduleName;

	}

	public static function is() {
		if(!defined('Enpowi_Modular')) die('Direct access not permitted');
	}

	public static function run() {
		define('Enpowi_Modular', TRUE);
	}

	public static function map()
	{
		$parentDir = dirname(__FILE__) . '/../../../../modules/';
		$notModule = array('.', '..', 'app', 'setup', 'default', 'index.php');
		$notComponent = array('.', '..');
		$moduleFolders = array_diff(scandir($parentDir), $notModule);


		$moduleMap = [
			'*'=> '*'
		];

		foreach($moduleFolders as $moduleFolder) {
			$componentsRaw = array_diff(scandir($parentDir . $moduleFolder), $notComponent);

			$components = [];
			foreach($componentsRaw as $componentRaw) {
				if (!preg_match('/Service[.]php$|[.]js$|[.]html$/', $componentRaw)) {
					$components[] = preg_replace('/[.](php|html)$/i', '', $componentRaw);
				}
			}
			array_unshift($components, '*');
			$moduleMap[$moduleFolder] = $components;
		}

		return $moduleMap;
	}

    public static function paramRespond($param, $value)
    {
        self::$paramResponse[$param] = $value;
    }

    public static function getParamResponse()
    {
        if (!empty(self::$paramResponse)) {
            return self::$paramResponse;
        }
        return null;
    }
}