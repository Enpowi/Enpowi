Enpowi.translation = {
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
	},
	translate: function(string, callback) {
		console.log(string);
		callback(string);
	}
};