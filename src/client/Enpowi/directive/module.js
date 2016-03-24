Vue.directive('module', {
  bind: function () {
    var el = this.el,
      directive = this;

    Enpowi.directives.setBinding(directive);

    switch (el.nodeName) {
      case 'FORM':
        Enpowi.forms.strategy(el, this.vm, this);
        Enpowi.directives.doneBinding(directive);
    }
  }
});