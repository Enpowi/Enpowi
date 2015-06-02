<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 4/22/15
 * Time: 10:14 PM
 */

namespace Enpowi\Blog;

use Aura\Session\Exception;
use Enpowi\App;
use RedBeanPHP\R;
use WikiLingo\Parser;

class Post {
    public $id;
    public $name;
    public $content;
    public $created;
    public $createdBy;

    private $_bean;

    public function __construct($name, $bean = null)
    {
        if ($bean === null) {
            $bean = $this->_bean = $bean = R::findOne('blog', ' name = ? ', [$name]);
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
        $bean = R::findOne('blog', ' id = ? ', [$id]);
        if ($bean !== null) {
            return new Post($bean->name, $bean);
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
        if (empty($this->name)) throw new Exception('Blog post needs name before it can be saved');

        $username = App::user()->username;

        //TODO: ensure createdBy is set once and contributors is an incremental list
        $bean = R::dispense('blog');
        $bean->name = $this->name;
        $bean->content = $content;
        $bean->created = R::isoDateTime();
        $bean->createdBy = $username;

        R::store($bean);
    }

    public function render()
    {
        return (new Parser)->parse($this->content);
    }

    public static function posts()
    {
        //TODO: paging

        $beans = R::findAll('blog', ' order by created ');
        $posts = [];

        foreach($beans as $bean) {
            $posts[] = new Post($bean->name, $bean);
        }

        return $posts;
    }
}