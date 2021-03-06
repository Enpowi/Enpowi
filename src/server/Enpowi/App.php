<?php

namespace Enpowi;

use Slim\Slim;
use RedBeanPHP\R;
use Enpowi\Modules;
use Aura\Session;
use Enpowi\Files\File;

class App
{
  public $clientScripts;
  public $authentication;
  public $session;
  public $user;

  /**
   * @var Slim null
   */
  public static $api = null;

  /**
   * @var App null
   */
  public static $app = null;

  /**
   * @var Config
   */
  public static $config = null;

  /**
   * @var Modules\Component
   */
  public static $component;

  /**
   * @var Modules\Module
   */
  public static $module;

  public static $pagingSize = 25;

  function __construct()
  {
    self::$app = $this;
    if (self::$api === null) {
      self::$api = new Slim();
    }

    $this->clientScripts = new ClientScripts();
    $this->session = (new Session\SessionFactory)->newInstance($_COOKIE);
    $this->authentication = new Authentication($this);
  }

  public static function user()
  {
    return self::get()->authentication->getUser();
  }

  public static function param($param)
  {
    return self::getApi()->request->params($param);
  }

  public static function paramInt($param)
  {
    return (int)self::param($param);
  }

  public static function paramBool($param)
  {
    $value = self::param($param);
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public static function paramString($param, $limit = 0)
  {

    $value = (string)self::param($param);

    if ($limit > 0 && strlen($value > $limit)) {
      $value = substr($value, 0, $limit);
    }

    return $value;
  }

  public static function paramFloat($param)
  {
    return (float)self::param($param);
  }

  public static function params($param)
  {
    $result = [];
    $values = self::param($param);
    foreach ($values as $value) {
      $result[] = $value;
    }
    return $result;
  }

  public static function paramInts($param)
  {
    $result = [];
    $values = self::param($param);
    foreach ($values as $value) {
      $result[] = (int)$value;
    }
    return $result;
  }

  public static function paramBools($param)
  {
    $result = [];
    $values = self::param($param);
    foreach ($values as $value) {
      $result[] = (bool)$value;
    }
    return $result;
  }

  public static function paramStrings($param, $limit = 0)
  {
    $result = [];
    $values = self::param($param);
    if ($limit > 0) foreach ($values as $value) {
      $value = substr($value, 0, $limit);
      $result[] = (string)$value;
    } else foreach ($values as $value) {
      $result[] = (string)$value;
    }
    return $result;
  }

  public static function paramFloats($param)
  {
    $result = [];
    $values = self::param($param);
    foreach ($values as $value) {
      $result[] = (float)$value;
    }
    return $result;
  }

  /**
   * @param {String} $param
   * @param {String} [$class]
   *
   * @return Files\*
   */
  public static function paramFile($param, $class = null)
  {
    if (isset($_FILES[$param])) {
      $file = $_FILES[$param];
      $type = ($class !== null ? new $class() : new File());

      if (method_exists($type, 'bean')) {
        return $type
          ->setName($file['name'])
          ->setTempPath($file['tmp_name'])
          ->setType($file['type'])
          ->setSize($file['size']);
      } else if (method_exists($type, 'setUploadData')) {
        return $type->setUploadData($file);
      } else {
        return $type;
      }
    }


    return null;
  }

  public static function paramIs($param)
  {
    return isset($_REQUEST[$param]);
  }

  /**
   * @param {String} $param
   * @param {String} [$class]
   *
   * @return Array(File)|null
   */
  public static function paramFiles($param, $class = null)
  {
    if (isset($_FILES[$param])) {
      $result = [];
      $files = $_FILES[$param];
      foreach ($files['tmp_name'] as $i => $path) {
        $file = ($class !== null ? new $class() : new File());
        $file
          ->setName($files['name'][$i])
          ->setTempPath($path)
          ->setType($files['type'][$i])
          ->setSize($files['size'][$i]);

        $result[] = $file;
      }
      return $result;
    }
    return null;
  }

  public static function get()
  {
    if (self::$app === null) {
      new self();
    }

    return self::$app;
  }

  public static function getApi()
  {
    if (self::$api === null) {
      self::$api = new Slim();
    }

    return self::$api;
  }

  public static function log($moduleName, $componentName, $detail = '')
  {
    $bean = R::dispense('log');

    $bean->email = self::user()->email;
    $bean->ip = self::getApi()->request->getIp();
    $bean->time = R::isoDateTime();
    $bean->moduleName = $moduleName;
    $bean->componentName = $componentName;
    $bean->detail = $detail;

    R::store($bean);
  }

  public static function logError($detail = '')
  {
    $bean = R::dispense('error');

    $bean->email = self::user()->email;
    $bean->ip = self::getApi()->request->getIp();
    $bean->time = R::isoDateTime();
    $bean->detail = $detail;

    R::store($bean);
  }

  public static function loadComponent($folder, $moduleName, $componentName = 'index')
  {
    $user = App::user();

    if (empty($componentName)) {
      $componentName = 'index';
    }

    App::log($moduleName, $componentName);

    if ($user->hasPerm($moduleName, $componentName)) {
      $module = new Modules\Module($folder, $moduleName);
      $component = new Modules\Component($module, $componentName);

      define('moduleName', $moduleName);
      define('componentName', $componentName);
      self::$module = $module;
      self::$component = $component;

      return $component;
    }

    return null;
  }

  public static function url()
  {
    return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
  }

  public static function uri()
  {
    return self::$api->request()->getRootUri();
  }

  public static function guid()
  {
    if (function_exists('com_create_guid')) {
      return com_create_guid();
    } else {
      mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
      $charid = strtoupper(md5(uniqid(rand(), true)));
      $hyphen = chr(45);// "-"
      $uuid = chr(123)// "{"
        . substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12)
        . chr(125);// "}"
      return $uuid;
    }
  }

  public static function pageOffset($pageNumber, $pageSizeOverride = null)
  {
    if ($pageNumber < 1) $pageNumber = 1;

    if ($pageSizeOverride === null) {
      return ($pageNumber - 1) * self::$pagingSize;
    } else {
      return ($pageNumber - 1) * $pageSizeOverride;
    }
  }

  public static function requireSSL()
  {
    if ($_SERVER['SERVER_PORT'] != '443') {
      header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
      exit();
    }
  }
}