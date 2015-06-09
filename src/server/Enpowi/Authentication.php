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
	public $segment;
	public function __construct()
	{
		$this->segment = App::get()
			->session
			->getSegment(__CLASS__);
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

		return new User('Anonymous');
	}

	public function login(User $user)
	{
		$segment = $this->segment;

		if ($user->exists()) {
			Event\UserBeforeLogin::pub(true, $user);

			if ($segment->get('impersonateUser') !== null) {
				$segment->set('impersonateUser', $user->id());
			} else {
				$segment->set( 'user', $user->id() );
			}
			return true;
		} else {
			return false;
		}

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
	}

	public function impersonate(User $user)
	{
		if ($user->exists()) {
			$this->segment->set( 'impersonateUser', $user->id() );
			return true;
		}

		return false;
	}

	public function clearImpersonate()
	{
		$this->segment->set('impersonateUser', null);

		return $this;
	}
}