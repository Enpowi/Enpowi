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

	public function updateBean()
	{
		$publicVars = create_function('$obj', 'return get_object_vars($obj);');
		foreach($publicVars($this) as $publicVar) {
			$method = 'set' . ucfirst($publicVar);
			if (method_exists($this, $method)) {
				$this->{$method}( $this->{$publicVar} );
			}
		}
		return $this;
	}
}