<?php

namespace Enpowi;

use Slim\Slim;
use RedBeanPHP\R;
use Enpowi\Modules;

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
	public static $app = null;

	function __construct()
	{
		$this->clientScripts = new ClientScripts();
		$this->session = include dirname(__FILE__) . '/../../../vendor/aura/session/scripts/instance.php';
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
}