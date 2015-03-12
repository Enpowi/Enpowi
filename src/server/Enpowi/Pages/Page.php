<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/10/15
 * Time: 9:26 PM
 */

namespace Enpowi\Pages;

use R;

class Page
{
	public $name;
	public $content;
	public $created;
	public $createdBy;
	public $contributors;

	private $_bean;

	public function __construct($name, $bean = null)
	{
		if ($bean === null) {
			$this->_bean = $bean = R::findOne('page', ' name = ? ', [$name]);
		} else {
			$this->_bean = $bean;
		}

		$this->convertFromBean();
	}

	private function convertFromBean()
	{
		$bean = $this->_bean;

		if (!$this->exists()) return;

		$this->name = $bean->name;
		$this->content = $bean->content;
		$this->created = $bean->created;
		$this->createdBy = $bean->createdBy;
		$this->contributors = $bean->sharedUserList;
	}

	public function exists()
	{
		if ($this->_bean === null) {
			return false;
		} else {
			return true;
		}
	}

	public function create($name, $content)
	{

	}
}