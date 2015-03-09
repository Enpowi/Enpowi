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

		Enpowi.directives.setup(this);
		Enpowi.module.setup(this);
		Enpowi.translation.setup(this);

		this.setupRoutes(callback);
	},


	setupRoutes: function(callback) {
		//setup router
		var router = this.router,
			app = this,
			landRoute = function(url) {
				app.activeUrl = url;

				$.get(url, function (data) {
					$.getScript('modules?module=app&component=session.js', function() {
						var result = app.process(data);
						callback(result);
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

	process: function(html) {
		var el = document.createElement('div'),
			me = this,
			children,
			child,
			i = 0;

		el.innerHTML = html;

		$(el.querySelector('script')).insertAfter('script:first');

		for(children = el.children;i < children.length; i++) {
			child = children[i];

			new Vue({
				el: child,
				data: (function() {
					var data = {
							session: me.session
						},
						hasData = child.hasAttribute('data'),
						key = child.getAttribute('data'),
						moduleData,
						i;

					if (hasData) {
						moduleData = Enpowi.module.data[key];
						for(i in moduleData) if (moduleData.hasOwnProperty(i)) {
							data[i] = moduleData[i];
						}
					}

					return data;
				})(),
				methods: {
					go: function(sig) {
						app.go(sig);
					},
					stringify: function(json) {
						return JSON.stringify(json);
					},
					hasPerm: function(module, component) {
						var hasPerm = false;
						$.each(me.session.user.groups, function() {
							$.each(this.perms, function() {
								if (this.module === module || module ==='*') {
									if (this.component === component || component ==='*') {
										hasPerm = true;
									}
								}
							});
						});

						return hasPerm;
					},
					hasKey: function(key, array) {
						return array[key] !== undefined;
					}
				}
			});
		}


		return children;
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