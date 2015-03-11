var app = new Enpowi(function(html) {
	$(Enpowi.directives.defaultModuleElement).html(html);
});

new Vue({
	el: $('body')[0]
});

app.logRoutes();