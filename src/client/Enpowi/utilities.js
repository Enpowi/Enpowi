'use strict';

/**
 * @name utilities
 * @memberOf Enpowi
 */
Namespace('Enpowi').Class('utilities', {
  Static: {
    trigger: function (el, eventName) {
      var event = document.createEvent('HTMLEvents');
      event.initEvent(eventName, true, true);
      el.dispatchEvent(event);

      return this;
    },
    url: function (moduleAndComponentOrPlainUrl) {
      if (moduleAndComponentOrPlainUrl.charAt(0) === '~') {
        return moduleAndComponentOrPlainUrl.substring(1);
      }

      var tempRouter = crossroads.create(),
        url;

      Enpowi.app.bindRouteUrls(tempRouter, function (_url, request, m, c) {
        url = new String(_url);
        url.r = request;
        url.m = m;
        url.c = c;
      });

      tempRouter.parse(moduleAndComponentOrPlainUrl);

      return url;
    },

    isObject: function (obj) {
      return obj === Object(obj);
    },
    //[modified] http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable#answer-14919494
    humanFileSize: function (bytes, si) {
      if (si === undefined) {
        si = true;
      }
      var thresh = si ? 1000 : 1024;
      if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
      }
      var units = si
        ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
        : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
      var u = -1;
      do {
        bytes /= thresh;
        ++u;
      } while (Math.abs(bytes) >= thresh && u < units.length - 1);
      return bytes.toFixed(1) + ' ' + units[u];
    },


    fileTooLarge: 'file too large',

    fileReponseCheck: function (maxFileSize, data) {
      if (data.response === -1) {
        if (data.files) {
          var files = data.files,
            max = files.length,
            i = 0,
            file;

          for (; i < max; i++) {
            file = files[i];
            if (maxFileSize < file.size) {
              return this.fileTooLarge;
            }
          }
        }

        return null;
      }
    },

    /**
     *
     * @param html
     * @returns {*}
     */
    parseHtml: function (html) {
      var ids = {},
        elements,
        element,
        parser = document.createElement('span'),
        i = 0,
        max;
      parser.innerHTML = html;

      elements = parser.querySelectorAll('[id]');
      max = elements.length;

      for (; i < max; i++) {
        element = elements[i];
        ids[element.getAttribute('id').replace(/-([a-z])/g, function (g) {
          return g[1].toUpperCase();
        })] = element;
        element.removeAttribute('id');
      }

      if (max > 0) {
        return ids;
      }

      return parser.children;
    },
    removeScripts: function(html) {
      return html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    }
  }
});