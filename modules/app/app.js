var app = new Enpowi(function(html) {
	$(Enpowi.directives.defaultModuleElement).html(html);
});

$.get('modules/default/loading.html', function(html){
	app.loadingElement = $(html);
});

new Vue({
	el: $('body')[0]
});

//app.logRoutes();