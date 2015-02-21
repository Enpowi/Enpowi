<?php

namespace Enpowi;

class App
{
	public $clientScripts;
	public $authentication;
	public $session;
	public static $app = null;

	function __construct()
	{
		$this->clientScripts = new ClientScripts();
		$this->authentication = new Authentication();
		$this->session = include dirname(__FILE__) . '/../../../vendor/aura/session/scripts/instance.php';
	}

	public static function get()
	{
		if (self::$app === null) {
			self::$app = new self();
		}

		return self::$app;
	}
}