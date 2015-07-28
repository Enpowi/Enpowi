<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use Slim;
use Enpowi\Users\User;

class Authentication
{
    //1 week = 7 * 24 * 60 * 60 = 604800
    public static $rememberDuration = 604800;
	public $segment;
	public function __construct()
	{
		$this->segment = App::get()
			->session
			->getSegment(__CLASS__);
	}

	public function getRealUser()
	{
		$segment = $this->segment;

		if (($_user = $segment->get('user')) !== null) {
			$user = User::fromId( $_user );
			if ($user === null) {
				$this->logout();
			} else {
				return $user;
			}
		}

		return new User('');
	}

	public function getUser()
	{
		$segment = $this->segment;

		if (($_user = $segment->get('user')) !== null) {
			if (($_impersonatedUser = $segment->get('impersonateUser')) !== null) {
				$user = User::fromId( $_impersonatedUser );
			} else {
				$user = User::fromId( $_user );
			}
			if ($user === null) {
				$this->logout();
			} else {
				return $user;
			}
		}

		return new User('');
	}

	public function login(User $user)
	{
		$segment = $this->segment;

		if ($user->exists()) {
			Event\User\BeforeLogin::pub(true, $user);

			if ($segment->get('impersonateUser') !== null) {
				$segment->set('impersonateUser', $user->id);
			} else {
				$segment->set( 'user', $user->id );
			}
			return true;
		} else {
			return false;
		}

	}

    public function rememberUserId()
    {
        $segment = $this->segment;

        if (($_user = $segment->get('user')) !== null) {
            $hour = time() + self::$rememberDuration;
            setcookie(App::$config->siteName . '-remember', $_user, $hour);
        }

        return $this;
    }

    public function recallUserId()
    {
        $key = App::$config->siteName . '-remember';
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return null;
    }

	public function logout()
	{
		$segment = $this->segment;

		if ($segment->get('impersonateUser') !== null) {
			$segment->set('impersonateUser', null);
		} else {
			$segment->clear();
		}

		return $this;
	}

	public function isAuthenticated()
	{
		return $this->segment->get('user') !== null;
	}

	public function isImpersonate()
	{
		return ($this->segment->get('impersonateUser') !== null ? true : false);
	}

	public function impersonateAnonymous()
	{
		$this->segment->set( 'impersonateUser', -1 );
		Event\Authentication\Impersonate::pub(null, $this);
		return $this;
	}

	public function impersonate(User $user)
	{
		if ($user->exists()) {
			$this->segment->set( 'impersonateUser', $user->id );
			Event\Authentication\Impersonate::pub($user, $this);
			return true;
		}

		return false;
	}

	public function clearImpersonate()
	{
		$segment = $this->segment;

		Event\Authentication\ClearImpersonate::pub($segment->get('impersonateUser'));

		$segment->set('impersonateUser', null);

		return $this;
	}

    public static function generatePassword($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }
}