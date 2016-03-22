Vue.directive('module-item', {
  deep: true,
  bind: function () {
    var el = this.el,
      parent = null,
      listenFn = function () {
        var form;

        if (parent === null) {
          parent = el.parentNode;

          while (parent !== null && parent.nodeName !== 'FORM') {
            parent = parent.parentNode;
          }

          if (parent === null) return;
        }

        form = parent;
        /*
         this timeout is here so that all event handlers that happen after this event get to
         finish before the form has submit triggered
         */
        setTimeout(function () {
          Enpowi.utilities.trigger(form, 'submit');
        }, 0);
      };

    el.addEventListener('change', listenFn);
    el.addEventListener('keyup', listenFn);
    el.addEventListener('click', listenFn);
  }
});