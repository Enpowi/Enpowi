<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap core CSS -->
	<link href="vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="vendor/bootstrap/dist/css/bootstrap-theme.css" rel="stylesheet">

	<link href="src/style/default.css" rel="stylesheet">

	<title></title>
</head>

<body>
	<header v-header></header>
	<nav v-navigation></nav>
	<article v-article></article>
	<aside v-side="left"></aside>
	<aside v-side="right"></aside>
	<footer v-footer></footer>
</body>

<script src="vendor/jquery/dist/jquery.js"></script>
<script src="vendor/js-signals/dist/signals.js"></script>
<script src="vendor/hasher/dist/js/hasher.js"></script>
<script src="vendor/crossroads.js/dist/crossroads.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/vue/dist/vue.js"></script>
<script src="vendor/class/Class.js"></script>

<script src="src/client/Enpowi/App.js"></script>
<script src="src/client/Enpowi/forms.js"></script>
<script src="src/client/Enpowi/directives.js"></script>
<script src="src/client/Enpowi/module.js"></script>
<script src="src/client/Enpowi/session.js"></script>
<script src="src/client/Enpowi/translation.js"></script>
<script src="src/client/Enpowi/utilities.js"></script>
<script src="modules?module=app&component=session.js"></script>
<script src="modules/app/app.js"></script>
</html>