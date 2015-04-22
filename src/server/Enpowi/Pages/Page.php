<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/10/15
 * Time: 9:26 PM
 */

namespace Enpowi\Pages;

use Aura\Session\Exception;
use RedBeanPHP\R;
use Enpowi;
use WikiLingo\Parser;

class Page
{
    public $id;
	public $name;
	public $content;
	public $created;
	public $createdBy;
	public $contributors;

	private $_bean;

	public function __construct($name, $bean = null)
	{
		if ($bean === null) {
			$bean = $this->_bean = $bean = R::findOne('page', ' name = ? and is_revision = 0 ', [$name]);
		} else {
			$this->_bean = $bean;
		}

		if ($bean === null) {
			$this->name = $name;
		} else {
			$this->convertFromBean();
		}
	}

    public static function byId($id)
    {
        $bean = R::findOne('page', ' id = ? ', [$id]);
        if ($bean !== null) {
            return new Page($bean->name, $bean);
        }

        return null;
    }

	private function convertFromBean()
	{
		$bean = $this->_bean;

		if (!$this->exists()) return;

        $this->id = $bean->getID();
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

	public function replace($content = '')
	{
        if (empty($this->name)) throw new Exception('Page needs name before it can be saved');

		$username = Enpowi\App::get()->user->username;

		R::exec( 'UPDATE page SET is_revision = 1 WHERE name = ?', [$this->name] );

		//TODO: ensure createdBy is set once and contributors is an incremental list
		$bean = R::dispense('page');
		$bean->name = $this->name;
		$bean->content = $content;
		$bean->created = R::isoDateTime();
		$bean->createdBy = $username;
		$this->contributors = [$username];
		$bean->isRevision = false;

		R::store($bean);
	}

	public function render()
	{
		return (new Parser)->parse($this->content);
	}

	public static function pages()
	{
		//TODO: paging

		$beans = R::findAll('page', ' is_revision = 0 order by name ');
		$pages = [];

		foreach($beans as $bean) {
			$pages[] = new Page($bean->name, $bean);
		}

		return $pages;
	}

	public function history()
	{
		//TODO: paging

		$beans = R::findAll('page', ' order by created ');
		$pages = [];

		foreach($beans as $bean) {
			$pages[] = new Page($bean->name, $bean);
		}

		return $pages;
	}
}