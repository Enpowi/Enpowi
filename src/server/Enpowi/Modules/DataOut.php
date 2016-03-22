<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/27/15
 * Time: 4:57 PM
 */

namespace Enpowi\Modules;

use mindplay\jsonfreeze\JsonSerializer;

class DataOut
{

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

  public function bind()
  {
    $data = $this->serializer->serialize($this->objects);
    echo "<script type='text/data'>$data</script>";
    return $this;
  }
}