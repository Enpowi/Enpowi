Vue.directive('frame', {
  bind: function () {
    var me = this,
      el = this.el;

    this.vm.reload = function () {
      me.update(me.urlRaw);
    };

    $(el).bind('reload', this.vm.reload);
  },
  update: function (urlRaw) {
    this.urlRaw = urlRaw;
    var el = this.el,
      url;

    if (el.style.display === 'none') return;
    if (this.expression === null) return;
    if (urlRaw === null) return;

    if (el.hasAttribute('static')) {
      while (el.firstChild !== null) {
        el.removeChild(el.lastChild);
      }
      app.load(Enpowi.utilities.url(urlRaw), function (html) {
        $(el).append(html);
      });
      this.expression = '';
    } else {
      app.load(url = Enpowi.utilities.url(urlRaw), function (html) {
        while (el.firstChild !== null) {
          el.removeChild(el.lastChild);
        }
        el.appendChild(app.process(html, url.m, url.c));
      });
    }
  }
});