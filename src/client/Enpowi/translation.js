Namespace('Enpowi').
    Class('translation', {
        setup: function() {
            Vue.directive('placeholder', {
                isLiteral: true,
                bind: function () {
                    var el = this.el;
                    Enpowi.translation.translate(this.expression, function(v) {
                        el.setAttribute('placeholder', v);
                    });
                }
            });

            Vue.directive('t', {
                isLiteral: true,
                bind: function () {
                    var el = this.el;
                    Enpowi.translation.translate(el.innerHTML, function(v) {
                        el.innerHTML = v;
                    });
                }
            });

            Vue.directive('title', {
                isLiteral: true,
                bind: function () {
                    var el = this.el;
                    Enpowi.translation.translate(this.expression, function(v) {
                        el.setAttribute('title', v);
                    });
                }
            });
        },
        translate: function(string, callback) {
            if (string.match(/[<>]/)) {
                throw new Error('Translate does not support html');
            }
            callback(string);
        }
    });