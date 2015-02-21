<?php
namespace Enpowi\Forms;

/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 4:36 PM
 */

use Enpowi;
use Gregwar\Captcha\CaptchaBuilder;
use Aura\Session;

class Utilities
{

	public static function captcha()
	{
		$app = Enpowi\App::get();

		$segment = $app->session->newSegment(__CLASS__);

		if (!isset($segment->phrase)) {
			$builder = new CaptchaBuilder();
			$builder->build();

			$segment->phrase = $builder->getPhrase();
			$segment->image = $builder->inline();
		}

		return $segment->image;
	}

	public static function isCaptchaMatch($phrase)
	{
		if (empty($_SESSION['Enpowi.Utilities.captcha.phrase'])) {
			return $_SESSION['Enpowi.Utilities.captcha.phrase'] === $phrase;
		}
		return false;
	}
}