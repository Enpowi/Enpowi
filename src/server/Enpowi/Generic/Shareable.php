<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/30/15
 * Time: 8:04 PM
 */

namespace Enpowi\Generic;

use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Users\User;


abstract class Shareable
{
  public function addSharedGroup(Group $group)
  {
    $bean = $this->bean();
    if ($bean === null) return $this;

    $bean->sharedGroupList[] = $group->bean();

    return $this;
  }

  public function addSharedUser(User $user)
  {
    $bean = $this->bean();
    if ($bean === null) return $this;

    $bean->sharedUserList[] = $user->bean();

    return $this;
  }


  public function removeSharedGroup(Group $group)
  {
    $bean = $this->bean();
    if ($bean === null) return $this;

    unset($bean->sharedGroupList[$group->id]);

    return $this;
  }

  public function removeSharedUser(User $user)
  {
    $bean = $this->bean();
    if ($bean === null) return $this;

    unset($bean->sharedGroupList[$user->id]);

    return $this;
  }

  public function inShare()
  {
    $bean = $this->bean();
    $user = App::user();

    //owner
    if ($bean->userId === App::user()->id) {
      return true;
    }

    //shared to group
    foreach ($user->groups as $userGroup) {
      if (isset($bean->sharedGroupList[$userGroup->id])) {
        return true;
      }
    }

    //shared to specific user
    if (isset($bean->sharedUserList[$user->id])) {
      return true;
    }

    return false;
  }

  public abstract function bean();
}