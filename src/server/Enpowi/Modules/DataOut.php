<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/27/15
 * Time: 4:57 PM
 */

namespace Enpowi\Modules;

use mindplay\jsonfreeze\JsonSerializer;

class DataOut {

	public $serializer;
	public $objects = [];
	public $json;
	public $id;

	public function __construct()
	{
		$this->serializer = new JsonSerializer();
		$this->serializer->skipPrivateProperties();
	}

	public function add($key, $object)
	{
		$this->objects[$key] = $object;
		return $this;
	}

	public function out()
	{
		return rawurlencode($this->serializer->serialize($this->objects));
	}
}