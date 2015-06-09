<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;
Module::is();

$app = App::get();
$auth = $app->authentication;

$data = (new DataOut())
	->add('impersonateUserId', App::paramInt('impersonatedUserId'))
	->add('impersonateUser', $auth->isImpersonate() ? $auth->getUser() : null)
	->out();

?><form
	v-module
	class="container"
	action="users/impersonateService"
	data-done="users/impersonate"
	data="<?php echo $data?>">

	<div v-show="impersonatedUser !== null">
		<span v-t>Impersonating: </span>{{impersonatedUser.username}}<br>
		<button v-t type="submit">Remove Impersonation</button>
	</div>
	<div v-show="impersonatedUser === null">

	</div>

</form>
