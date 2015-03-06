<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/27/15
 * Time: 4:57 PM
 */

namespace Enpowi\Modules;


class Data {

	public $object;
	public $json;
	public $id;

	public function __construct($object) {
		$this->object = $object;
		$this->json = $json = json_encode($object);
		$this->id = md5($json);
	}

	public function toScript()
	{
		$id = $this->id;
		$json = $this->json;
		return "<script>Enpowi.module.data['$id'] =  $json;</script>";
	}

	public function bind()
	{
		echo $this->toScript();
	}
}