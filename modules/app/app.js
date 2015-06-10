$.holdReady(true);

Enpowi.App
	.subTo()._continue(function() {
		$.holdReady(false);
		if (app.modal) app.modal.modal('hide');
	})
	.subTo().delay(function() {
		app.modal = $('<div>').modal('show');
	})
	.subTo().deny(function() {
		app.go('#/');
		return false;
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