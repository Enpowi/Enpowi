<?php
require_once '../module.php';

$authentication = new Enpowi\Authentication();
$user = $authentication->getUser();
if ($user !== null) {
	echo json_encode(['module', 'user/view']);
	die;
}
$img = Enpowi\Forms\Utilities::captcha();

?><form action="user/newService" v-module data-done="user/view">
	<h2 v-t>Register</h2>
	<div class="form-group">
		<input type="text" class="form-control" name="username" v-placeholder="Username">
		<span v-text="username"></span>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" name="email" v-placeholder="Email">
		<span v-text="email"></span>
	</div>
	<div class="form-group">
		<input type="password" class="form-control" name="password" v-placeholder="Password">
		<input type="password" class="form-control" name="repeatPassword" v-placeholder="Repeat Password">
		<span v-text="password"></span>
	</div>
	<div class="form-group">
		<img class="captcha" src="<?php echo $img?>"/>
		<input type="text" class="form-control" name="captcha" v-placeholder="Captcha">
		<span v-text="captcha"></span>
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>