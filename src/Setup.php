<?php

namespace Enpowi;


class Setup
{
	public $clientScripts;

	function __construct()
	{
		$this->clientScripts = new ClientScripts();
	}
}