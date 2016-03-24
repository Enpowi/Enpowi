'use strict';

Namespace('Enpowi').Class('directives', {
  Static: {
    queue: [],
    data: {},
    bindingDirectives: [],
    directiveMap: {
      'src/client/Enpowi/directive/': [
        'article',
        'find',
        'footer',
        'frame',
        'header',
        'module',
        'module-item',
        'navigation',
        'pager',
        'side',
        'source-edit'
      ]
    },
    setBinding: function (directive) {
      this.bindingDirectives.push(directive);
    },
    doneBinding: function (directive) {
      this.bindingDirectives.splice(this.bindingDirectives.indexOf(directive), 1);

      if (this.bindingDirectives.length < 1) {
        Enpowi.app.pubTo().directiveReady();
      }
    },
    matchingDirectives: function(html) {
      var potentialDirectives = Enpowi.utilities.removeScripts(html).match(/v[-][a-z][a-z-]+/g);
      if (!potentialDirectives) return [];

      return potentialDirectives
        .map(function(potentialDirective) {
          return potentialDirective.substring(2);
        })
        .filter(function(value, index, self) {
          return self.indexOf(value) === index;
        });
    },
    loadDirectivesFromHtml: function(html, done) {
      var matchingDirectives = this.matchingDirectives(html),
        remaining = matchingDirectives.length,
        i = 0,
        max = matchingDirectives.length;

      if (max === 0) {
        done();
        return;
      }

      for (; i < max; i++) {
        Enpowi.directives.loadDirective(matchingDirectives[i], function() {
          remaining--;

          if (remaining < 1) {
            done();
          }
        });
      }
    },
    loadDirective: function(lookupName, done) {
      var i,
        max,
        map = this.directiveMap,
        names,
        name,
        url;

      for (url in map) {
        if (!map.hasOwnProperty(url)) continue;
        names = map[url];
        max = names.length;
        for (i = 0; i < max; i++) {
          name = names[i];
          if (Vue.options.directives[lookupName]) {
            done();
            return;
          }
          if (lookupName === name) {
            app.loadScript(url + lookupName + '.js', done);
            return;
          }
        }
      }

      console.log('directive: ' + lookupName + ' not found');
      done();
    }
  }
});
