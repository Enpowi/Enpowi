<?php

namespace Enpowi\Files;

use RedBeanPHP\R;
use Enpowi\App;
use Enpowi\Files\File;

class Gallery
{
    public $name;
    public $description;
    public $userId;
	public $created;
	public $id;

    private $_bean = null;

    public function __construct($id = null, $bean = null)
    {
	    if ($bean === null && $id !== null) {
		    $this->_bean = R::findOne('gallery', ' id = :id ', ['id' => $id]);
	    } else {
		    $this->_bean = $bean;
	    }

	    if ($this->_bean === null) {
		    throw new \Exception('Need bean or id');
	    }

	    $this->convertFromBean();
    }

	public function exists()
	{
		if ($this->_bean === null) {
			return false;
		} else {
			return true;
		}
	}

	private function convertFromBean()
	{
		$bean = $this->_bean;

		if (!$this->exists()) return $this;

		$this->id = $bean->id;
		$this->name = $bean->name;
		$this->description = $bean->description;
		$this->created = $bean->created;
		$this->userId = $bean->user_id;

		return $this;
	}

    public function setName($value)
    {
        $this->bean()->name = $value;
        $this->name = $value;
        return $this;
    }

    public function setDescription($value)
    {
        $this->bean()->description = $value;
        $this->description = $value;
        return $this;
    }

    public function addImage(File $value)
    {
	    $files = $this->bean()->ownFileList;
        $files[] = $value->bean();
        array_unique($files);
        return $this;
    }

	public function images($pageNumber = 0)
	{
		$bean = $this->bean();
		$imageBeans = $bean->ownFileList;
		$max = count($imageBeans);
		$i = $pageNumber * App::$pagingSize;
        $max = min($i + App::$pagingSize, $max);
		$images = [];
		for (; $i < $max; $i++) {
			$images[] = new File($imageBeans[$i]);
		}
		return $images;
	}

    public function removeImage(File $value)
    {
        unset($this->bean()->ownFileList[$value->id]);
        return $this;
    }

    public function bean()
    {
        if ($this->_bean === null) {
            $bean = R::dispense('gallery');
            $bean->userId = App::user()->id();
	        $bean->created = R::isoDateTime();
            $this->_bean = $bean;
        }
        return $this->_bean;
    }

    public function save()
    {
        $bean = $this->bean();
        return R::store($bean);
    }

    public static function galleries($userId, $pageNumber = 0)
    {
        $beans = R::findAll('gallery', ' user_id = :userId order by name limit :offset, :count', [
            'userId' => $userId,
            'offset' => $pageNumber * App::$pagingSize,
            'count' => App::$pagingSize
        ]);

        $galleries = [];

        foreach($beans as $bean) {
            $galleries[] = new Gallery($bean->id, $bean);
        }

        return $galleries;
    }

    public static function create($name, $description)
    {
        $bean = R::dispense('gallery');
	    $bean->userId = App::user()->id();
	    $bean->created = R::isoDateTime();
        $gallery = new Gallery(null, $bean);

        return $gallery
            ->setName($name)
            ->setDescription($description)
            ->save();
    }

    public function delete()
    {
        $bean = $this->bean();
        R::trash($bean);
        return $this;
    }
}