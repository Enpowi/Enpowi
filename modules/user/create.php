<?php
require_once '../../vendor/autoload.php';

$app = new \Slim\Slim();

$username = $app->request->params('username');
$email = $app->request->params('email');
$password = $app->request->params('password');
$captcha = $app->request->params('captcha');
$reply = [];

$catpchaMatch = Enpowi\Forms\Utilities::isCaptchaMatch($captcha);

if ($catpchaMatch) {

} else {
	$reply[] = 'Incorrect captcha phrase';
}

echo(json_encode([
	statis=>''
]));