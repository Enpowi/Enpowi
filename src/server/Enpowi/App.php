<?php

namespace Enpowi;

use Slim\Slim;
use RedBeanPHP\R;
use Enpowi\Modules;
use Aura\Session;

class App
{
	public $clientScripts;
	public $authentication;
	public $session;
	public $user;

	/**
	 * @var Slim null
	 */
	public static $api = null;

	/**
	 * @var App null
	 */
	public static $app = null;

	/**
	 * @var Config
	 */
	public static $config = null;

	/**
	 * @var Modules\Component
	 */
	public static $component;

	/**
	 * @var Modules\Module
	 */
	public static $module;

	function __construct()
	{
		$this->clientScripts = new ClientScripts();
		$this->session = (new Session\SessionFactory)->newInstance($_COOKIE);
		$authentication = $this->authentication = new Authentication($this);
		$this->user = $authentication->getUser();
	}

	public static function param($param)
	{
		if (self::$api === null) {
			self::$api = new Slim();
		}
		return self::$api->request->params($param);
	}

	public static function get()
	{
		if (self::$app === null) {
			self::$app = new self();
		}

		return self::$app;
	}

	public static function log($username, $moduleName, $componentName, $detail = '') {
		$bean = R::dispense('log');

		$bean->username = $username;
		$bean->ip = $_SERVER['REMOTE_ADDR'];
		$bean->time = R::isoDateTime();
		$bean->moduleName = $moduleName;
		$bean->componentName = $componentName;
		$bean->detail = $detail;

		R::store($bean);
	}

	public static function loadComponent($folder, $moduleName, $componentName = 'index')
	{
		$app = self::get();
		$user = $app->user;

		if (empty($componentName)) {
			$componentName = 'index';
		}

		App::log($user->username, $moduleName, $componentName);

		if ($user->hasPerm($moduleName, $componentName)) {
			$module    = new Modules\Module( $folder, $moduleName );
			$component = new Modules\Component( $module, $componentName );

			define('moduleName', $moduleName);
			define('componentName', $componentName);
			self::$module = $module;
			self::$component = $component;

			return $component;
		}

		return null;
	}

	public static function url()
	{
		return ( ! empty( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . '/';
	}

	public static function uri()
	{
		return self::$api->request()->getRootUri();
	}

	public static function guid() {
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
			        .substr($charid, 0, 8).$hyphen
			        .substr($charid, 8, 4).$hyphen
			        .substr($charid,12, 4).$hyphen
			        .substr($charid,16, 4).$hyphen
			        .substr($charid,20,12)
			        .chr(125);// "}"
			return $uuid;
		}
	}
}