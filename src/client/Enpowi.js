/**
 * @constructor
 * @name Enpowi
 */
Class('Enpowi', {
	construct: function(callback) {
		this.router = crossroads;
		this.hasher = hasher;

		this.module = '';
		this.component = '';

		this.setupRoutes(callback);

		Enpowi.module.setup();
		Enpowi.translation.setup();
	},

	setupRoutes: function(callback) {
		//setup router
		var router = this.router = crossroads,
			app = this,
			landRoute = function(url, module, component) {
				$.post(url, function (data) {
					callback(app.process(data, module, component));
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
			landRoute('modules/default', 'default', '');
		});
		router.addRoute('/{module}', function(path) {
			landRoute('modules/' + path.module, path.module, '');
		});
		router.addRoute('/{module}/{component}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php', path.module, path.component);
		});
		router.addRoute('/{module}/{component}/{id}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php?id' + path.id, path.module, path.component);
		});
		router.addRoute('/{module}/{component}{?query}', function(path) {
			landRoute('modules/' + path.module + '/' + path.component + '.php' + '?' + path['?query_'], path.module, path.component);
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

	process: function(data, module, component) {
		var el = document.createElement('div');
		el.innerHTML = data;

		new Vue({
			el: el,
			data: {
				user: app.user,
				module: module,
				component: component
			}
		});

		return el.children;
	},

	go: function(route) {
		this.hasher.setHash(route);

		return this;
	}
});