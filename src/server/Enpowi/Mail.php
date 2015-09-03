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

    public $args;
    public function __construct()
    {
    }

    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }

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

        self::$mailer->isHTML( true );
        self::$mailer->Body = $this->body();

		$configMailCallback(self::$mailer);

		return self::$mailer->send();
	}

	public function body( $html = null ) {
		if ($html === null) {
			$component = App::$component;
			$html = $component->template();
		}

		if (strlen($html) > 0) {
			$r = (new Template\Renderer($html))->out($this->args);
			return $r;
		}

		return '';
	}
}