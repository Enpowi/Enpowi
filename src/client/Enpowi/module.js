Enpowi.module = {
	defaultModuleElement: null,
	queue: [],
	data: {},
	app: null,
	setup: function(app) {
		var me = this;
		me.app = app;

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

				app.loadModule('default/header', function(html) {
					$(el).html(html);
				});
			}
		});

		Vue.directive('navigation', {
			bind: function() {
				var el = this.el;

				app.loadModule('default/navigation', function(html) {
					$(el).html(html);
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

				app.loadModule('default/footer', function(html) {
					$(el).html(html);
				});
			}
		});
	},

	url: function(moduleAndComponent) {
		var tempRouter = crossroads.create(),
			url = '';
		app.bindRouteUrls(tempRouter, function(_url) {
			url = _url;
		});
		tempRouter.parse(moduleAndComponent);

		return url;
	}
};