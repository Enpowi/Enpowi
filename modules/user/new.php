<?php
require_once '../../vendor/autoload.php';

$img = Enpowi\Forms\Utilities::captcha();

?><form v-module action="create.php">
	<div class="form-group">
		<input name="username" type="text" v-placeholder="Username" class="form-control">
	</div>
	<div class="form-group">
		<input name="email" type="text" v-placeholder="Email" class="form-control">
	</div>
	<div class="form-group">
		<input name="password" type="password" v-placeholder="Password" class="form-control">
	</div>
	<div class="form-group">
		<img src="<?php echo $img?>"/>
		<input name="captcha" type="text" v-placeholder="Captcha" class="form-control">
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>