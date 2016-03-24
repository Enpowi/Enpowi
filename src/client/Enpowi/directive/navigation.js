Vue.directive('navigation', {
  bind: function () {
    var el = this.el,
      directive = this;

    Enpowi.directives.setBinding(directive);

    while (el.firstChild !== null) {
      el.removeChild(el.lastChild);
    }

    app.loadModule(Enpowi.session.theme + '/navigation', function (html) {
      el.appendChild(html);
      Enpowi.directives.doneBinding(directive);
    });
  }
});