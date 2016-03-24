Vue.directive('side', {
  bind: function () {
    var directive = this,
      el = this.el;

    Enpowi.directives.setBinding(directive);

    while (el.firstChild !== null) {
      el.removeChild(el.lastChild);
    }

    app.loadModule(Enpowi.session.theme + '/' + this.expression, function (html) {
      el.appendChild(html);
      Enpowi.directives.doneBinding(directive);
    });
  }
});