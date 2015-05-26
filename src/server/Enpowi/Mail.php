<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 5/26/15
 * Time: 11:40 AM
 */

namespace Enpowi;

use PHPMailer;

class Mail {

	/**
	 * @var PHPMailer
	 */
	public static $mailer;
	public static $from;
	public static $fromName;
	public static $host;
	public static $username;
	public static $password;
	public static $smtpAuth;
	public static $smtpSecure;
	public static $port;

	public static function setup($from, $fromName, $host, $username, $password, $smtpAuth, $smtpSecure, $port) {
		self::$from = $from;
		self::$fromName = $fromName;
		self::$host = $host;
		self::$username = $username;
		self::$password = $password;
		self::$smtpAuth = $smtpAuth;
		self::$smtpSecure = $smtpSecure;
		self::$port = $port;

		$mail = self::$mailer = new PHPMailer();
		$mail->isSMTP();
		$mail->From = self::$from;
		$mail->FromName = self::$fromName;
		$mail->Host = self::$host;
		$mail->SMTPAuth = self::$smtpAuth;
		$mail->Username = self::$username;
		$mail->Password = self::$password;
		$mail->SMTPSecure = self::$smtpSecure;
		$mail->Port = self::$port;
	}

	public function send($configMailCallback) {


		$configMailCallback(self::$mailer);

		return self::$mailer->send();
	}

	public static function body( $args = [], $html = null ) {
		if ($html === null) {
			$component = App::$component;
			$html = $component->template();
		}

		$config = App::$config;
		$templatePath = $config->moduleDirectory . '/' . $config->themeModule . '/mail.html';

		$r = new Template\Renderer($templatePath);
		$r->template = str_replace('{{body}}', $r->template, $html);
		$templateProcessed = $r->out($html, $args);

		return $templateProcessed;
	}
}