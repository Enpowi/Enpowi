<?php
require_once '../module.php';

$authentication = new Enpowi\Authentication();
$user = $authentication->getUser();

?><form v-module>
	<h2 v-t>View User</h2>
</form>