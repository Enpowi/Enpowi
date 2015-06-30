<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/30/15
 * Time: 5:24 PM
 */

namespace Enpowi\Generic;


abstract class DataItem implements IDataItem
{
	private $_bean = null;
	public function exists()
	{
		if ($this->_bean === null) {
			return false;
		}
		return true;
	}
}