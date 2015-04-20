var app = new Enpowi.App(function(html) {
    var el = Enpowi.directives.defaultModuleElement;
    while (el.firstChild !== null) {
        el.removeChild(el.firstChild);
    }
    el.appendChild(html);
});

app.load('modules/default/loading.html', function(html){
	app.loadingElement = $(html);
});

new Vue({
	el: $('body')[0]
});