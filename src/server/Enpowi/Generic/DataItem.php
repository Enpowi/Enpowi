<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/30/15
 * Time: 5:24 PM
 */

namespace Enpowi\Generic;


abstract class DataItem extends Shareable implements IDataItem
{
	public function exists()
	{
		if ($this->bean() === null) {
			return false;
		}
		return true;
	}
}