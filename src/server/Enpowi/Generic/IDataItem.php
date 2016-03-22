<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/30/15
 * Time: 5:23 PM
 */

namespace Enpowi\Generic;


interface IDataItem
{
  public function convertFromBean();

  public function bean();

  public function exists();
}