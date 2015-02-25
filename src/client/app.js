var app = new Enpowi(function(html) {
	var main = $('#app-main'),
		container = $('#app-container');


	if (main.length > 0) {
		main.html(html);
	} else if (container.length > 0) {
		container.html(html);
	}
});

app.logRoutes();