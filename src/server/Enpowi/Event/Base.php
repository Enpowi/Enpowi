<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 5/27/15
 * Time: 7:15 AM
 */

namespace Enpowi\Event;

abstract class Base
{
  public static $name;
  public static $callbacks = [];

  public static function get()
  {
    $className = get_called_class();

    if (!isset(self::$callbacks[$className])) {
      self::$callbacks[$className] = [];
    }

    return self::$callbacks[$className];
  }

  public static function push($callback)
  {
    $className = get_called_class();

    if (!isset(self::$callbacks[$className])) {
      self::$callbacks[$className] = [];
    }

    self::$callbacks[$className][] = $callback;
  }

  public static function pop()
  {
    $className = get_called_class();

    if (!isset(self::$callbacks[$className])) {
      self::$callbacks[$className] = [];
    }

    return array_pop(self::$callbacks[$className]);
  }

  public static function pub()
  {
    $i = 0;
    $callbacks = self::get();
    $max = count($callbacks);

    $args = func_get_args();

    for (; $i < $max; $i++) {
      call_user_func_array($callbacks[$i], $args);
    }
  }

  public static function sub($callback)
  {
    self::push($callback);
  }

  public static function length()
  {
    return count(self::get());
  }

  public static function reset()
  {
    while (self::pop() !== null) {
    }
  }
}