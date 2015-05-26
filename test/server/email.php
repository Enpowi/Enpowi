<?php

require_once '../../vendor/autoload.php';

require_once '../../modules/setup/config.php';

use Enpowi\Mail;

echo ( new Mail())->send(function ( PHPMailer $mail ) {
	$mail->addAddress( 'rplummer@visop-dev.com' );
	$mail->Subject = 'email test';
	$mail->Body = 'test';
} );