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
  public static function captcha($isNew = false)
  {
    $app = Enpowi\App::get();

    $segment = $app->session->getSegment(__CLASS__);

    if (!isset($segment->phrase) || $isNew) {
      $builder = new CaptchaBuilder();
      $builder->build();

      $segment->set('phrase', $builder->getPhrase());
      $segment->set('image', $builder->inline());
    }

    return $segment->get('image');
  }

  public static function isCaptchaMatch($phraseAttempt)
  {
    $app = Enpowi\App::get();
    $segment = $app->session->getSegment(__CLASS__);
    $phrase = $segment->get('phrase');
    if (isset($phrase)) {
      return $phrase === $phraseAttempt;
    }
    return false;
  }

  public static function checkableDictionary($dictionary, $lookupArray = null)
  {
    $checkable = [];
    if ($lookupArray === null) {
      foreach ($dictionary as $key => $language) {
        $checkable[$key] = [
          'value' => $language,
          'checked' => false
        ];
      }
    } else {
      foreach ($dictionary as $key => $language) {
        if (in_array($key, $lookupArray)) {
          $checkable[$key] = [
            'value' => $language,
            'checked' => true
          ];
        } else {
          $checkable[$key] = [
            'value' => $language,
            'checked' => false
          ];
        }
      }
    }

    return $checkable;
  }
}