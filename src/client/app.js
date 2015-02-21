var app = new Enpowi(function(html) {
	$('#main').html(html);
});

app
	.logRoutes();
	//.go('user/new');