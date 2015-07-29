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
				<td>
                    <img id="captcha" class="captcha" src="<?php echo $img?>"/>
                    <a href="#" id="reload-captcha"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></a>
                </td>
				<td>
					<input type="text" class="form-control" name="captcha" v-placeholder="Captcha">
					<span v-text="captcha"></span>
				</td>
			</tr>
		</table>
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>
<script>
    var captchaImage = app.getElementById('captcha'),
        reloadCaptchaAnchor = app.getElementById('reload-captcha');

    reloadCaptchaAnchor.onclick = function() {
        $.get(Enpowi.utilities.url('app/captcha'), function(imageData) {
            captchaImage.setAttribute('src', imageData);
        });

        return false;
    };
</script>