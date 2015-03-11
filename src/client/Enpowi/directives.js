Enpowi.Class('directives', {
	Static: {
		defaultModuleElement: null,
		queue: [],
		data: {},
		app: null,
		setup: function (app) {
			var me = this;
			me.app = app;

			Vue.directive('module', {
				bind: function () {
					var el = this.el;

					switch (el.nodeName) {
						case 'FORM':
							Enpowi.forms.strategy(el, this.vm, this);
					}
				}
			});

			Vue.directive('module-item', {
				deep: true,
				bind: function () {
					var me = this;

					$(this.el).change(function () {
						$(me.vm.$parent.$el).submit();
					});
				}
			});

			Vue.directive('header', {
				bind: function () {
					var el = this.el;

					app.loadModule(Enpowi.session.theme + '/header', function (html) {
						$(el).html(html);
					});
				}
			});

			Vue.directive('navigation', {
				bind: function () {
					var el = this.el;

					app.loadModule(Enpowi.session.theme + '/navigation', function (html) {
						$(el).html(html);
					});
				}
			});

			Vue.directive('article', {
				bind: function () {
					me.defaultModuleElement = this.el;
				}
			});

			Vue.directive('side', {
				bind: function () {
					this.el.className += ' ' + this.expression;
				}
			});

			Vue.directive('footer', {
				bind: function () {
					var el = this.el;

					app.loadModule(Enpowi.session.theme + '/footer', function (html) {
						$(el).html(html);
					});
				}
			});
		}
	}
});