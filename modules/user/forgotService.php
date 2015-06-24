<?php
use Enpowi\App;
use Enpowi\Modules\Module;
use Enpowi\Users\User;
use Enpowi\Mail;

Module::is();

$email = App::param('email');

$user = User::getByEmail($email);

$password = $user->resetPassword();

$mailed = ( new Mail() )
    ->setArgs([
        'email' => $user->email,
        'password' => $password
    ])
    ->send(function ( PHPMailer $mail ) use ( $user ) {
        $mail->addAddress( $user->email );
        $mail->Subject = App::$config->siteName . ' Registration';
    });