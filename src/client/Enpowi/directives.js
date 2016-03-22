'use strict';

Namespace('Enpowi').Class('directives', {
  Static: {
    defaultModuleElement: null,
    lookupPath: 'src/client/directive',
    queue: [],
    data: {},
    app: null,
    bindingDirectives: [],
    setBinding: function (directive) {
      this.bindingDirectives.push(directive);
    },
    doneBinding: function (directive) {
      this.bindingDirectives.splice(this.bindingDirectives.indexOf(directive), 1);

      if (this.bindingDirectives.length < 1) {
        Enpowi.app.pubTo().directiveReady();
      }
    },
    setup: function () {
      var me = this,
        app = Enpowi.app;


      me.app = app;


















    }
  }
});
