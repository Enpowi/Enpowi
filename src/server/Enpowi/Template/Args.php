<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 5/26/15
 * Time: 1:45 PM
 */

namespace Enpowi\Template;

use Enpowi\App;

class Args
{

  static public function get($args = [])
  {

    $properties = App::$config;

    foreach ($properties as $field => $value) {
      $args[$field] = $value;
    }

    return $args;
  }
}