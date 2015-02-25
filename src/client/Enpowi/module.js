Enpowi.module = {
	setup: function() {
		var me = this;

		Vue.directive('module', {
			bind: function() {
				var el = this.el;

				switch (el.nodeName) {
					case 'FORM':
						Enpowi.forms.strategy(el, this.vm);
				}
			}
		});
	},

	url: function(module, friendlyUrl) {
		var urlActual = 'modules';

		switch (friendlyUrl.charAt(0)) {
			case '/':
				if (friendlyUrl.length > 1) {
					urlActual += friendlyUrl + '.php';
				} else {
					urlActual = '';
				}
				break;
			default:
				urlActual += '/' + module + '/' + friendlyUrl + '.php';
		}

		return urlActual;
	}
};