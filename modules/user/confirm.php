<?php
use Enpowi\App;
use Enpowi\Mail;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();

$user = App::$app->user();
$key = App::param('key');
$mailed = false;

$isValid = $user->isValid();
$justValidated = false;

if ($isValid === false) {
	if (empty($key)) {
		$mailed = ( new Mail() )
            ->setArgs([
                'key'      => $user->validationKey(),
                'email' => $user->email
            ])
            ->send(function ( PHPMailer $mail ) use ( $user ) {
                $mail->addAddress( $user->email );
                $mail->Subject = App::$config->siteName . ' Registration';
            });
	} else if ($user->canValidate() && $user->validationKey() === $key) {
		$user->setValid(true);
		$isValid = true;
	}
}

(new DataOut)
	->add('isValid', $isValid)
	->add('justValidated', $justValidated)
	->add('mailed', $mailed)
	->bind();

?>
<div class="container">
	<div v-show="!isValid && mailed">
		<h5 v-t>Confirmation On it's way!</h5>
		<span v-t>We cannot wait to have you on board!</span>
	</div>
    <div v-show="justValidated">
        <h5 v-t>Welcome!</h5>
        <span v-t>You have just been validated.  Welcome aboard!</span>
    </div>
    <div v-show="isValid">
        <h5 v-t>You have already been validated</h5>
        <span v-t>You are already on board.</span>
    </div>
</div>