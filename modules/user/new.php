<?php
require_once '../../vendor/autoload.php';

$img = Enpowi\Forms\Utilities::captcha();

?><form>
	<div class="form-group">
		<input type="text" placeholder="Email" class="form-control">
	</div>
	<div class="form-group">
		<input type="password" placeholder="Password" class="form-control">
	</div>
	<div class="form-group">
		<img src="<?php echo $img?>"/>
		<input type="text" placeholder="Captcha" class="form-control">
	</div>
	<button type="submit" class="btn btn-success">Submit</button>
</form>