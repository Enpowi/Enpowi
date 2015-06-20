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
use RedBeanPHP\RedException;
use WikiLingo\Parser;
use Enpowi\Users\User;

class Post {
    public $id;
    public $name;
    public $content;
    public $created;
	public $edited;
    public $user = null;
	public $contributorIds = [];
	public $publishedOn;

    private $_bean = null;

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
	    $this->edited = $bean->edited;
        $this->created = $bean->created;
	    $this->contributorIds = explode(',', $bean->contributorIds);
        $this->user = new User(null, $bean->user);
	    $this->publishedOn = R::isoDateTime(date($bean->publishedOn));
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

	    $bean = $this->_bean;

	    if ($bean === null) {
		    $this->_bean = $bean = R::dispense('blog');
	    }

        $bean->name = $this->name;
        $this->content = $bean->content = $content;
	    $bean->edited = R::isoDateTime();
        $bean->created = $this->created ?: R::isoDateTime();

	    $otherUserBean = App::user()->bean();
        $bean->user = $this->user !== null ? $this->user->bean() : $otherUserBean;
	    $this->contributorIds[] = $otherUserBean->getID();
	    $this->contributorIds = array_unique( $this->contributorIds );
	    $bean->contributorIds = implode(',',$this->contributorIds);
	    $bean->publishedOn = $this->publishedOn;

	    R::store( $bean );
    }

    public function render()
    {
        return (new Parser)->parse($this->content);
    }

    public static function posts($pageNumber = 0, $showAll = false)
    {
        $beans = R::findAll('blog', '
            where
                true = :show_all
                or date(published_on) >= now()
	        order by created
	        limit :offset, :count',[
		        'offset' => $pageNumber * App::$pagingSize,
		        'count' => App::$pagingSize,
	            'show_all' => $showAll
        ]);

        $posts = [];

        foreach($beans as $bean) {
            $posts[] = new Post($bean->name, $bean);
        }

        return $posts;
    }

	public static function pages($showAll = false)
	{
		$count = R::count('blog', '
			where
				true = :show_all
				or date(published_on) >= now()
			order by created', [
				'show_all' => $showAll
		]);

		$result = [];
		$max = $count / App::$pagingSize;
		$i = 0;
		for(;$i < $max; $i++) {
			$result[] = $i;
		}
		return $result;
	}

	public static function mostRecentPost($showAll = false)
	{
		$bean = R::findOne('blog', '
			where
				true = :show_all
				or date(published_on) >= now()
			order by created limit 0, 1', [
				'show_all' => $showAll
		]);

		if ($bean !== null) {
			return new Post($bean->name, $bean);
		}

		return null;
	}

	public function userPosts($pageNumber = 0, $showAll = false)
	{
		$beans = R::findAll('blog', '
			where
				true = :show_all
				or date(published_on) >= now()
			order by created
			limit :offset, :count', [
				'offset' => $pageNumber * App::$pagingSize,
				'count' => App::$pagingSize,
				'show_all' => $showAll
		]);

		$posts = [];

		foreach($beans as $bean) {
			$posts[] = new Post($bean->name, $bean);
		}

		return $posts;
	}

	public function bean()
	{
		if ($this->_bean === null) {
			$this->_bean = R::findOne( 'blog', ' name = ? ', [ $this->name ] );
		}
		return $this->_bean;
	}
}