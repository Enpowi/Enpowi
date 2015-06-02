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
		self::$app = $this;
		if (self::$api === null) {
			self::$api = new Slim();
		}

		$this->clientScripts = new ClientScripts();
		$this->session = (new Session\SessionFactory)->newInstance($_COOKIE);
		$this->authentication = new Authentication($this);
	}

	public static function user() {
		return self::get()->authentication->getUser();
	}

	public static function param($param)
	{
		return self::getApi()->request->params($param);
	}

	public static function get()
	{
		if (self::$app === null) {
			new self();
		}

		return self::$app;
	}

	public static function getApi()
	{
		if (self::$api === null) {
			self::$api = new Slim();
		}

		return self::$api;
	}

	public static function log($moduleName, $componentName, $detail = '') {
		$bean = R::dispense('log');

		$bean->username = self::user()->username;
		$bean->ip = self::getApi()->request->getIp();
		$bean->time = R::isoDateTime();
		$bean->moduleName = $moduleName;
		$bean->componentName = $componentName;
		$bean->detail = $detail;

		R::store($bean);
	}

	public static function logError($detail = '') {
		$bean = R::dispense('error');

		$bean->username = self::user()->username;
		$bean->ip = self::getApi()->request->getIp();
		$bean->time = R::isoDateTime();
		$bean->detail = $detail;

		R::store($bean);
	}

	public static function loadComponent($folder, $moduleName, $componentName = 'index')
	{
		$user = App::user();

		if (empty($componentName)) {
			$componentName = 'index';
		}

		App::log($moduleName, $componentName);

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