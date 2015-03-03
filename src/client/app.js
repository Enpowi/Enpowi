var app = new Enpowi(function(html) {
	var mod = Enpowi.module;
	$(mod.defaultModuleElement).html(html);
});

new Vue({
	el: $('body')[0]
});

app.logRoutes();