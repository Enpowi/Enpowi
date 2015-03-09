<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/6/15
 * Time: 3:22 PM
 */

namespace Enpowi\Modules;

use mindplay\jsonfreeze\JsonSerializer;
use Enpowi\App;

class DataIn
{
	public $key;
	public $serializer;
	public $objects = [];
	public $jsons = [];

	public function __construct() {
		$this->serializer = new JsonSerializer();
	}

	public function in($name) {
		$json = App::param($name);

		if (is_array($json)) {
			$objects = [];
			foreach ($json as $jsonItem) {
				$objects[] = $this->serializer->unserialize( $jsonItem );
			}

			return $objects;
		}

		$object = $this->serializer->unserialize( $json );
		return $object;
	}
}