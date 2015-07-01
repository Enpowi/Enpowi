<?php

namespace Enpowi\Files;

use Enpowi\Generic\PageableDataItem;
use RedBeanPHP\R;
use Enpowi\App;

class Gallery extends PageableDataItem
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

	public static function isUnique($name)
	{
		$count = R::count('gallery', ' name = :name and user_id = :userId', [
			'name' => $name,
			'userId' => App::user()->id
		]);

		return $count < 1;
	}

	public function convertFromBean()
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

    public function addImage(Image $value)
    {
	    $bean = $this->bean();
	    $bean->ownFileList[] = $value->bean();
        return $this;
    }

	/**
	 * @param int $pageNumber
	 *
	 * @return Image[]
	 */
	public function images($pageNumber = 1)
	{
		$imageBeans = R::findAll('file', ' gallery_id = :galleryId ORDER BY name LIMIT :offset, :limit ',[
			'galleryId' => $this->id,
			'offset' => App::pageOffset($pageNumber),
			'limit' => App::$pagingSize
		]);

		$images = [];

		foreach ($imageBeans as $imageBean) {
			$images[] = new Image($imageBean);
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
            $bean->userId = App::user()->id;
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

	/**
	 * @param $userId
	 * @param int $pageNumber
	 *
	 * @return Gallery[]
	 */
    public static function galleries($userId, $pageNumber = 1)
    {
        $beans = R::findAll('gallery', ' user_id = :userId order by name limit :offset, :count', [
            'userId' => $userId,
            'offset' => App::pageOffset($pageNumber),
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
	    $bean->userId = App::user()->id;
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

	public static function pages()
	{
		return R::count('gallery', '  ') / App::$pagingSize;
	}
}