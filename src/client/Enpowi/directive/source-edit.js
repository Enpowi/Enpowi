Vue.directive('source-edit', {
  bind: function () {
    var directive = this,
      el = this.el,
      form = el.form,
      styleUrls = ["vendor/codemirror/lib/codemirror.css"],
      scriptUrls = ["vendor/codemirror/lib/codemirror.js"],
      mode = '';

    Enpowi.directives.setBinding(directive);

    switch (this.expression.toLowerCase()) {
      case "wikilingo":
        styleUrls.push("vendor/codemirror.wikilingo/wikiLingo.css");
        scriptUrls.push("vendor/codemirror.wikilingo/wikiLingo.js");
        mode = 'text/wikiLingo';
        break;
      default:
    }

    el.parentNode.appendChild(app.loadStyles(styleUrls));
    app.loadScripts(scriptUrls, function () {
      var cm = CodeMirror.fromTextArea(el, {
        mode: mode
      });

      cm.on('change', function () {
        el.value = cm.getValue();
      });

      form.addEventListener('submit', function () {
        el.value = cm.getValue();
      });

      Enpowi.directives.doneBinding(directive);
    });
  }
});