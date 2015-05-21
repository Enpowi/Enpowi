$.holdReady(true);

Enpowi.App
	.sub('directive.ready', function() {
		$.holdReady(false);
	})
	.sub('app.delay', function() {
		app.modal = $('<div>').modal('show');
	})
	.sub('app.continue', function() {
		if (app.modal) app.modal.modal('hide');
	});

var app = new Enpowi.App(function(html) {
    var el = Enpowi.directives.defaultModuleElement;
    while (el.firstChild !== null) {
        el.removeChild(el.firstChild);
    }
    el.appendChild(html);
});

app.load('modules/default/loading.html', function(html){
	app.loadingElement = html;
});

new Vue({
	el: document.body
});