Vue.directive('footer', {
  bind: function () {
    var directive = this;
    Enpowi.directives.setBinding(directive);
    var el = this.el;

    while (el.firstChild !== null) {
      el.removeChild(el.lastChild);
    }

    app.loadModule(Enpowi.session.theme + '/footer', function (html) {
      el.appendChild(html);
      Enpowi.directives.doneBinding(directive);
    });
  }
});