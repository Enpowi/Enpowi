Vue.directive('header', {
  bind: function () {
    var el = this.el,
      directive = this;

    me.setBinding(directive);

    while (el.firstChild !== null) {
      el.removeChild(el.lastChild);
    }

    app.loadModule(Enpowi.session.theme + '/header', function (html) {
      el.appendChild(html);
      me.doneBinding(directive);
    });
  }
});