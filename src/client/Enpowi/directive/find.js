Vue.directive('find', {
  update: function (value) {
    if (this.el.vFindActive) return;

    var el = this.el,
      find = Enpowi.utilities.url(value.find),
      init = function () {
        el.setAttribute('autocomplete', 'off');
        $(el).typeahead({
          ajax: {
            url: find,
            triggerLength: 1
          },
          item: '<li><a href="#"></a></li>',
          onSelect: function (selection) {
            if (value.url) {
              app.go(value.url + selection.value);
            } else {
              el.value = selection.value;
              Enpowi.utilities.trigger(el, 'change');
              Enpowi.utilities.trigger(el, 'input');
            }
          }
        });

        el.vFindActive = true;
      };

    if (el.nodeName !== 'INPUT') {
      console.log('Warning: find should be used with input elements');
    }

    if ($.fn.typeahead === undefined) {
      app.loadScript('vendor/bs-typeahead/js/bootstrap-typeahead.js', init);
    } else {
      init();
    }
  }
});