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
	public $db = [
		'host' => '',
		'name' => '',
		'user' => '',
		'password' => ''
	];

	public function setupMySql($host, $name, $user, $password)
	{
		$this->db['host'] = $host;
		$this->db['name'] = $name;
		$this->db['user'] = $user;
		$this->db['password'] = $password;

		R::setup('mysql:host=' . $host . ';dbname=' . $name, $user, $password);
	}

	public function setupDB()
	{

	}
}