<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 7/2/15
 * Time: 10:33 AM
 */

namespace Enpowi\Files;


class StaticFile extends Image
{
	public $entityName = null;
	public $entityId = null;

	public function setEntityName($value)
	{
		$this->entityName =
		$this->bean()->entityName = $value;

		return $this;
	}

	public function setEntityId($value)
	{
		$this->entityId =
		$this->bean()->entityId = $value;

		return $this;
	}

	public function upload()
	{
		if ($this->entityName === null || $this->entityId === null) {
			throw new \Exception('property entityName and entityId must be set to other than null');
		}

		return parent::upload();
	}

	public function inShare()
	{
		return true;
	}
}