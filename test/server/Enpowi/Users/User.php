<?php
use Enpowi\Users\User;

$tf->test('Adding a user', function(\Testify\Testify $tf) {
  $email = 'user@enpwi.com';
  User::create($email, '123123123');
  $user = new User($email);
  $tf->assertFalse($user->bean() === null);
  $user->remove();
});

$tf->test('Removing a user', function(\Testify\Testify $tf) {
  $email = 'user@enpwi.com';
  User::create($email, '123123123');
  (new User($email))->remove();
  $user = new User($email);
  $tf->assertTrue($user->bean() === null);
});