<?php

namespace Enpowi;

class Setup
{
	public $clientScripts;
	public $authentication;
	public $session;


	function __construct()
	{
		$this->clientScripts = new ClientScripts();
		$this->authentication = new Authentication();
		$this->session = include '../vendor/aura/session/scripts/instance.php';
	}
}