<?php
use Enpowi\Modules\Module;

Module::is();

$img = Enpowi\Forms\Utilities::captcha(true);

?><form
	action="user/registerService"
	v-module
	data-done="user/view"
	class="container">
	<h2 v-t>Register</h2>
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
		<table>
			<tr>
				<td><img class="captcha" src="<?php echo $img?>"/></td>
				<td>
					<input type="text" class="form-control" name="captcha" v-placeholder="Captcha">
					<span v-text="captcha"></span>
				</td>
			</tr>
		</table>
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>