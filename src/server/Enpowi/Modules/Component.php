<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/3/15
 * Time: 11:13 AM
 */

namespace Enpowi\Modules;

define('Enpowi_Modules_Component_Active', 'active');
define('Enpowi_Modules_Component_Static', 'static');

class Component
{

  public $module;
  public $name;
  public $file;

  private $extensions = [
    '.php' => Enpowi_Modules_Component_Active,
    '.html' => Enpowi_Modules_Component_Static,
    ''
  ];

  private $active = false;

  public function __construct(Module $module, $componentName = 'index')
  {
    $this->module = $module;
    $this->name = $componentName;

    foreach ($this->extensions as $extension => $processType) {
      $file = $module->folder . '/' . $componentName . $extension;
      if (file_exists($file)) {
        $this->active = ($processType === Enpowi_Modules_Component_Active);
        $this->file = $file;
        break;
      }
    }
  }

  public function isActive()
  {
    return $this->active;
  }

  public function runInit()
  {
    $file = $this->module->folder . '/init.php';
    if (file_exists($file)) {
      require $file;
    }
    return $this;
  }

  public function template()
  {
    $module = $this->module;
    $name = $this->name;

    foreach ($this->extensions as $extension) {
      $file = $module->folder . '/' . $name . 'Template' . $extension;
      if (file_exists($file)) {
        return file_get_contents($file);
        break;
      }
    }

    return '';
  }
}