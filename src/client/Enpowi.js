/**
 * @constructor
 * @name Enpowi
 */
Class('Enpowi', {
	construct: function(callback) {
		this.router = crossroads;
		this.hasher = hasher;

		this.setupRoutes(callback);
		this.setupTranslations();
	},

	setupRoutes: function(callback) {
		//setup router
		var router = this.router = crossroads,
			app = this,
			landRoute = function(url) {
				$.post(url, function (data) {
					callback(app.process(data));
				});
			};

		this.hasher = hasher;

		//none
		//moduleName
		//moduleName/component
		//moduleName/component/id
		//moduleName/component?querystring
		router.normalizeFn = router.NORM_AS_OBJECT;

		router.addRoute('/', function(path) {
			landRoute('modules/default');
		});
		router.addRoute('/{module}', function(path) {
			landRoute('modules/' + path.module);
		});
		router.addRoute('/{module}/{component}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php');
		});
		router.addRoute('/{module}/{component}/{id}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php?id' + path.id);
		});
		router.addRoute('/{module}/{component}{?query}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php' + '?' + path['?query_']);
		});


		//setup hasher
		function parseHash(newHash, oldHash){
			router.parse(newHash);
		}

		hasher.initialized.add(parseHash); //parse initial hash
		hasher.changed.add(parseHash); //parse hash changes
		hasher.init(); //start listening for history change

	},

	logRoutes: function() {
		this.router.routed.add(console.log, console); //log all routes

		return this;
	},

	setupTranslations: function() {
		Vue.directive('placeholder', {
			isLiteral: true,
			bind: function () {
				this.el.setAttribute('placeholder', Enpowi.utilities.translate(this.expression));
			}
		});

		Vue.directive('t', {
			isLiteral: true,
			bind: function () {
				this.el.innerHTML = Enpowi.utilities.translate(this.el.innerHTML);
			}
		});
	},

	process: function(data) {
		var el = document.createElement('div');
		el.innerHTML = data;

		new Vue({
			el: el
		});

		return el.children;
	},

	go: function(route) {
		this.hasher.setHash(route);

		return this;
	}
});