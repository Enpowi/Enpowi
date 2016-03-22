Vue.directive('article', {
  bind: function () {
    Enpowi.directives.defaultModuleElement = this.el;
  }
});