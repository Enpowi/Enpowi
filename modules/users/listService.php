<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;
use RedBeanPHP\R;

Module::is();

switch (App::param('action')) {
	case 'delete':
		foreach(App::param('emails') as $email) {
			(new User($email))->remove();
		}
		echo 1;
		break;
	case 'impersonate':
		$user = new User(App::param('impersonateUser'));
		echo App::get()
			->authentication
			->impersonate($user) ? 1 : -1;
		break;
	case 'impersonateAnonymous':
		App::get()
			->authentication
			->impersonateAnonymous();
		echo 1;
		break;
	case 'find':
		$beans = R::findAll('user', 'email like :like limit 5', ['like' => '%' . App::param('query') . '%']);
		$users = [];
		foreach ($beans as $bean) {
			$users[] = $bean->email;
		}
		echo json_encode($users);
		break;
	default:
		echo 0;
}