<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Mail;
use Enpowi\Modules\DataOut;

$user = App::$app->user();
$key = App::param('key');
$mailed = false;

$isValid = $user->isValid();

if ($isValid === false) {
	if (empty($key)) {
		$mailed = ( new Mail() )->send(function ( PHPMailer $mail ) use ( $user ) {
			$mail->addAddress( $user->email, $user->username );
			$mail->isHTML( true );
			$mail->Subject = App::$config->siteName . ' Registration';
			$mail->Body    = Mail::body( [
				'key'      => $user->validationKey(),
				'username' => $user->username
			] );
		} );
	} else if ($user->validationKey() === $key) {
		$user->setValid(true);
		$isValid = true;
	}
}

$data = (new DataOut())
	->add('isValid', $isValid)
	->add('mailed', $mailed)
	->out();

?>
<div data="<?php echo $data?>" class="container" v-show="!isValid">
	<div v-show="!mailed">
		<h5 v-t>Confirmation On it's way!</h5>
		<span v-t>A confirmation is on to your email.  We cannot wait to have you on board!</span>
	</div>
</div>