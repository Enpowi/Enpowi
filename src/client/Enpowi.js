/**
 * @constructor
 * @name Enpowi
 */
Class('Enpowi', {
	construct: function(callback) {
		this.router = crossroads;
		this.hasher = hasher;

		this.session = {};

		this.setupRoutes(callback);

		Enpowi.module.setup(this);
		Enpowi.translation.setup(this);
	},

	setupRoutes: function(callback) {
		//setup router
		var router = this.router,
			app = this,
			landRoute = function(url) {
				app.activeUrl = url;

				$.post(url, function (data) {
					$.getScript('modules/app/session.js.php', function() {
						callback(app.process(data));
					});
				});
			};

		this.hasher = hasher;

		//none
		//moduleName
		//moduleName/component
		//moduleName/component/id
		//moduleName/component?querystring
		router.normalizeFn = router.NORM_AS_OBJECT;

		router.addRoute('/', function() {
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

	process: function(data, callback) {
		var el = document.createElement('div');

		el.innerHTML = data;

		$(el.querySelector('script')).insertAfter('script:first');

		new Vue({
			el: el,
			data: {
				session: this.session
			}
		});

		return el.children;
	},

	go: function(route) {
		this.hasher.setHash(route);

		return this;
	},

	updateSession: function(type, sessionItems) {
		var oldSessionItems = this.session[type] || (this.session[type] = {}),
			key;

		for(key in sessionItems) if (key && sessionItems.hasOwnProperty(key)) {
			oldSessionItems[key] = sessionItems[key];
		}

		return this;
	}

});