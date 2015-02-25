<?php

namespace Enpowi;

use Slim\Slim;

class App
{
	public $clientScripts;
	public $authentication;
	public $session;
	public $user;
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
}