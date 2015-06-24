<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;
Module::is();

$user = App::user();
$password = App::param('password');
$passwordRepeat = App::param('passwordRepeat');
$update = App::paramBool('update');
$stop = false;

Module::paramRespond('password', '');
Module::paramRespond('passwordRepeat', '');
Module::paramRespond('passwordUpdated', '');

if (empty($password)) {
    $stop = true;
}

if (!$stop && $password !== $passwordRepeat) {
    Module::paramRespond('passwordRepeat', 'Passwords do not match');
    $stop = true;
}

if (!$stop && !User::isValidPassword($password)) {
    Module::paramRespond('password', 'Invalid');
    $stop = true;
}

if (!$stop) {
    if ($update) {
        if ($user->updatePassword($password)) {
            Module::paramRespond('passwordUpdated', 'Password updated');
        }
    }
}