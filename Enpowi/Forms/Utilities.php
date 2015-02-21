<?php
namespace Enpowi\Forms;

/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 4:36 PM
 */

use Gregwar\Captcha\CaptchaBuilder;

class Utilities
{

	public static function captcha()
	{
		if (!isset($_SESSION['Enpowi.Utilities.captcha.phrase'])) {
			$builder = new CaptchaBuilder();
			$builder->build();

			$_SESSION['Enpowi.Utilities.captcha.phrase'] = $builder->getPhrase();
			$_SESSION['Enpowi.Utilities.captcha.img'] = $builder->inline();
		}

		return $_SESSION['Enpowi.Utilities.captcha.img'];
	}

	public static function isCaptchaMatch($phrase)
	{
		if (empty($_SESSION['Enpowi.Utilities.captcha.phrase'])) {
			return $_SESSION['Enpowi.Utilities.captcha.phrase'] === $phrase;
		}
		return false;
	}
}