/**
 * @constructor
 * @name Enpowi
 */
Class('Enpowi', {
	construct: function(callback) {
		this.router = crossroads;
		this.routes = [];
		this.hasher = hasher;

		this.session = {
			user: {}
		};

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

				$.get(url, function (data) {
					$.getScript('modules?module=app&component=session.js', function() {
						callback(app.process(data));
					});
				});
			};

		this.hasher = hasher;

		this.bindRouteUrls(router, landRoute);

		//setup hasher
		function parseHash(newHash, oldHash){
			router.parse(newHash);
		}

		hasher.initialized.add(parseHash); //parse initial hash
		hasher.changed.add(parseHash); //parse hash changes
		hasher.init(); //start listening for history change

	},

	bindRouteUrls: function(router, callback) {
		//none
		//moduleName
		//moduleName/component
		//moduleName/component/id
		//moduleName/component?querystring
		router.normalizeFn = crossroads.NORM_AS_OBJECT;

		router.addRoute('/', function() {
			callback('modules?module=default&component=index');
		});
		router.addRoute('/{module}', function(path) {
			callback('modules?module=' + path.module);
		});
		router.addRoute('/{module}/{component}', function(path) {
			callback('modules?module=' + path.module + '&component=' + path.component);
		});
		router.addRoute('/{module}/{component}/{id}', function(path) {
			callback('modules?module=' + path.module + '&component=' + path.component + '&id' + path.id);
		});
		router.addRoute('/{module}/{component}{?query}', function(path) {
			callback('modules?module=' + path.module + '&component=' + path.component + '&'  + path['?query_']);
		});
	},

	logRoutes: function() {
		this.router.routed.add(console.log, console); //log all routes

		return this;
	},

	process: function(data, callback) {
		var el = document.createElement('div'),
			me = this;

		el.innerHTML = data;

		$(el.querySelector('script')).insertAfter('script:first');

		new Vue({
			el: el,
			data: {
				session: this.session
			},
			methods: {
				go: function(sig) {
					app.go(sig);
				},
				hasPerm: function(module, component) {
					var hasPerm = false;
					$.each(me.session.user.groups, function() {
						$.each(this.perms, function() {
							if (this.module === module || module ==='*') {
								if (this.component === component || componenet ==='*') {
									hasPerm = true;
								}
							}
						});
					});

					return hasPerm;
				}
			}
		});

		return el.children;
	},

	go: function(route) {
		this.hasher.setHash(route);

		return this;
	},

	loadModule: function(url, callback) {
		var app = this;
		$.ajax({
			type: "POST",
			url: Enpowi.module.url(url),
			success: function (html) {
				var result = app.process(html);
				callback(result);
			},
			error: function (html) {
				var result = app.process(html);
				callback(null, result);
			}
		});
	},
	loadModuleScript: function(url, callback) {
		$.getScript(Enpowi.module.url(url), callback);
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