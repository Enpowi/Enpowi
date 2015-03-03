Enpowi.module = {
	defaultModuleElement: null,
	queue: [],
	data: {},
	setup: function(app) {
		var me = this;

		Vue.directive('module', {
			bind: function() {
				var el = this.el,
					moduleData,
					moduleDataKey = el.getAttribute('data'),
					vm = this.vm;

				switch (el.nodeName) {
					case 'FORM':
						Enpowi.forms.strategy(el, this.vm);
				}

				if (moduleDataKey) {
					moduleData = Enpowi.module.data[moduleDataKey];
					if (moduleData) {
						vm.$set('data', moduleData);
					}
				}

			}
		});

		Vue.directive('header', {
			bind: function() {
				var el = this.el;

				$.post('modules/default/header.html', function(html) {
					$(el).html(app.process(html));
				});
			}
		});

		Vue.directive('navigation', {
			bind: function() {
				var el = this.el;

				$.post('modules/default/navigation.html', function(html) {
					$(el).html(app.process(html));
				});
			}
		});

		Vue.directive('article', {
			bind: function() {
				me.defaultModuleElement = this.el;
			}
		});

		Vue.directive('side', {
			bind: function() {}
		});

		Vue.directive('footer', {
			bind: function() {
				var el = this.el;

				$.post('modules/default/footer.html', function(html) {
					$(el).html(app.process(html));
				});
			}
		});
	},

	url: function(url) {
		var urlActual = 'modules/';

		switch (url.charAt(0)) {
			case '/':
				if (url.length > 1) {
					urlActual += url;
				} else {
					urlActual = '';
				}
				break;
			default:
				urlActual += url + '.php';
		}

		return urlActual;
	}
};