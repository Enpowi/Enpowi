<?php
require_once dirname(dirname(dirname(__FILE__))) . '/modules/setup/run.php';
use Enpowi\App;
$app = App::get();
$auth = $app->authentication;
$user = $auth->getRealUser();

if (!$user->hasPerm('*', '*')) die;

?><!DOCTYPE html>
<html>
<head>
	<title>({{pass}}|{{fail}}) {{title}} · Enpowi Test Suite</title>
	<meta charset="utf-8" />
	<script src="../../vendor/testify.js/Testify.js"></script>
	<script src="../../vendor/vue/dist/vue.js"></script>
	<script src="../../vendor/class/Class.js"></script>
	<script src="Utilities.js"></script>
	<script>
		var tf = new Testify("Enpowi Test Suite");
	</script>
	<script src="reg.js"></script>
	<script>
		tf.run();
	</script>
</head>
<body></body>
<script>
	document.body.innerHTML = Testify.report.html.ui;
	Testify.report.html(tf);
</script>
</html>