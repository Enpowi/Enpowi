<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/4/15
 * Time: 1:06 PM
 */

namespace Enpowi;

use RedBeanPHP\R;

class Config
{
	public $moduleDirectory;

	public $themeModule = 'default';

	public $siteName;
	public $siteUrl;

	public $dbHost;
	public $dbName;
	public $dbUser;
	public $dbPassword;

	public function __construct($moduleDirectory = null)
	{
		App::$config = $this;

		if ($moduleDirectory === null) {
			$moduleDirectory = dirname(__FILE__) . '/../../../modules/';
		}

		if (file_exists($moduleDirectory)) {
			$this->moduleDirectory = $moduleDirectory;
		} else {
			throw new \Exception("Modules directory, $moduleDirectory, does not exist");
		}
	}

	public function setupSite($name, $url) {
		$this->siteName = $name;
		$this->siteUrl = $url;

		return $this;
	}

	public function setupMySql($host, $name, $user, $password)
	{
		$this->dbHost = $host;
		$this->dbName = $name;
		$this->dbUser = $user;
		$this->dbPassword = $password;

		R::setup('mysql:host=' . $host . ';dbname=' . $name, $user, $password);

		return $this;
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

	public function setupMail($from, $fromName, $host, $username, $password, $smtpAuth = true, $smtpSecure = 'tls', $port = 587) {
		Mail::setup($from, $fromName, $host, $username, $password, $smtpAuth, $smtpSecure, $port);

		return $this;
	}

	public function requireSSL()
	{
		App::requireSSL();
		
		return $this;
	}
}