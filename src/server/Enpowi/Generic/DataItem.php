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

	public function updateBean($fn = null)
	{
		$fn = $fn  ?: function() {};

		$getPublicProperties = function($obj) {
			return get_object_vars($obj);
		};

		$properties = $getPublicProperties($this);

		foreach($properties as $property => $value) {
			$methodValidate = 'validate' . ucfirst($property);
			$methodSet = 'set' . ucfirst($property);

			if (method_exists($this, $methodValidate)) {
				if ($this->{$methodValidate}( $property, $value, $fn)) {
					if ( method_exists( $this, $methodSet ) ) {
						$this->{$methodSet}( $value );
					}
				}
			} else if ( method_exists($this, 'validateEach')) {
				if ($this->validateEach( $property, $value, $fn )) {
					if ( method_exists( $this, $methodSet ) ) {
						$this->{$methodSet}( $value );
					}
				}
			} else if ( method_exists( $this, $methodSet ) ) {
				$this->{$methodSet}( $value );
			}
		}

		return $this;
	}
}