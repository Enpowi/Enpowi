Vue.directive('side', {
  bind: function () {
    var directive = this,
      el = this.el;

    me.setBinding(directive);

    while (el.firstChild !== null) {
      el.removeChild(el.lastChild);
    }

    app.loadModule(Enpowi.session.theme + '/' + this.expression, function (html) {
      el.appendChild(html);
      me.doneBinding(directive);
    });
  }
});