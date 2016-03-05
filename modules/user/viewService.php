<?php
use Enpowi\App;
use Enpowi\Types;
use Enpowi\Modules\DataIn;
use Enpowi\Users\User;
use Enpowi\Modules\Module;
Module::is();

if (!isset($user)) {
    $user = App::user();
}

$_user = Types::Users_User((new DataIn)->in('user'));
$password = App::param('password');
$passwordRepeat = App::param('passwordRepeat');
$update = App::paramBool('update');
$stopEmail = false;
$stopPassword = false;

if (empty($password)) {
    $stopPassword = true;
}

if (!$stopPassword && $password !== $passwordRepeat) {
    Module::paramRespond('passwordRepeat', 'Passwords do not match');
    $stopPassword = true;
}

if (!$stopPassword && !User::isValidPassword($password)) {
    Module::paramRespond('password', 'Invalid');
    $stopPassword = true;
}

if ($user->email !== $_user->email && !User::isEmailValid($_user->email)) {
    Module::paramRespond('email', 'Not a valid email');
    $stopEmail = true;
}

if ($update) {
    if (!$stopEmail) {
        if ($user->email !== $_user->email && $user->updateEmail($_user->email)) {
            Module::successRespond('emailUpdated', 'Updated');
        }
    }
    if (!$stopPassword) {
        if ($user->updatePassword($password)) {
            Module::successRespond('passwordUpdated', 'Password updated');
        }
    }
}