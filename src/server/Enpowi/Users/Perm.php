<?php

namespace Enpowi\Users;

use Aura\Session\Exception;
use RedBeanPHP\R;
use Respect\Validation\Validator as v;

class Perm
{

  public $groupName;
  public $module;
  public $component;

  private $_bean;
  private $_group;

  public function __construct($module, $component, Group $group = null, $bean = null)
  {
    $this->module = $module;
    $this->component = $component;

    if ($bean === null) {
      $this->_bean = R::findOne('perm', ' groupName = ? AND module = ? AND component = ?', [$this->groupName, $module, $component]);
    } else {
      $this->_bean = $bean;
    }

    if ($group !== null) {
      $this->_group = $group;
      $this->groupName = $group->name;
    }
  }

  public static function getGroupsFromPerm($module, $component)
  {
    $groups = [];
    $result = R::findAll('perm', ' module = ? AND component = ?', [$module, $component]);

    foreach ($result as $permBean) {
      $groups[] = new Group($permBean->groupName);
    }

    return $groups;
  }

  public static function isUnique($module, $component, $groupName)
  {
    $result = R::count('perm', ' groupName = ? AND module = ? AND component = ?', [$groupName, $module, $component]);
    return $result === 0;
  }

  public static function create($module, $component, $group)
  {
    if (self::isUnique($module, $component, $group->name)) {
      $bean = R::dispense('perm');
      $groupBean = $group->bean();

      $bean->groupName = $group->name;
      $bean->module = $module;
      $bean->component = $component;
      $groupBean->sharedPermList[] = $bean;

      R::storeAll([$bean, $groupBean]);

      return new Perm($module, $component, $group);
    }

    return null;
  }

  public function bean()
  {
    return $this->_bean;
  }

  public function id()
  {
    return $this->_bean->getID();
  }

  public static function parse($string)
  {
    //module/component
    //module/*
    //*/component
    //*/*

    preg_match('/(?P<group>[a-zA-Z][a-zA-Z0-9-_ ]+)[@](?P<module>([a-zA-Z][a-zA-Z0-9-_]+|[*]))[\/]?(?P<component>([a-zA-Z][a-zA-Z0-9-_]+|[*]))?/', $string, $matches);

    return [
      'group' => $matches['group'],
      'module' => $matches['module'],
      'component' => $matches['component'],
    ];
  }
}