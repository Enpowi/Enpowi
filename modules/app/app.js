$.holdReady(true);

Enpowi.App.subTo()
	._continue(function() {
		$.holdReady(false);
		if (app.modal) app.modal.modal('hide');
	})
	.delay(function() {
		app.modal = $('<div>').modal('show');
	})
	.deny(function() {
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

$(document).keydown(function(e) {
	if (e.ctrlKey) {
		switch (e.key) {
			case 'U':
			case 'u':
				window.open(Enpowi.utilities.url(app.hasher.getHash()));
				return false;
		}
	}
});