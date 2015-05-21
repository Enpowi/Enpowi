/**
 * @name App
 * @memberOf {Enpowi}
 * @constructor
 */
Namespace('Enpowi').

    Class('App', {
		Static: {
			pubs: {},
			sub: function(eventName, callback) {
				var pubs;
				if ((pubs = this.pubs[eventName]) === undefined) pubs = this.pubs[eventName] = [];
				pubs.push(callback);

				return this;
			},
			pub: function(eventName, data) {
				var pubs,
					max,
					i;

				if ((pubs = this.pubs[eventName]) === undefined) return this;

				i = 0;
				max = pubs.length;

				for (;i <max;i++) {
					pubs[i](this, data);
				}

				return this;
			}
		},
        construct: function(callback) {
            this.router = crossroads;
            this.routes = [];
            this.hasher = hasher;
            this.loadingElement = null;
            this.routeCallback = callback;

            Enpowi.directives.setup(this);
            Enpowi.module.setup(this);
            Enpowi.translation.setup(this);

            this.setupRoutes();
        },

        setupRoutes: function() {
            //setup router
            var router = this.router,
                app = this,
                landRoute = function(url) {
                    var loading = setTimeout(function() {
                        loading = null;
                        app.setLoading(true);
                    }, 500);

                    app.load(url, function(data) {
                        app.loadScript('modules?module=app&component=session.js', function() {
                            if (loading !== null) {
                                clearTimeout(loading);
                            }
                            app.setLoading(false);
                            var result = app.process(data);
                            app.routeCallback(result);
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
                callback('modules?module=' + Enpowi.session.theme + '&component=index');
            });
            router.addRoute('/{module}', function(path) {
                callback('modules?module=' + path.module);
            });
            router.addRoute('/{module}{?query}', function(path) {
                callback('modules?module=' + path.module + '&'  + path['?query_']);
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

	        router.routed.add(function(route) {
		        Enpowi.App.pub('app.go', route);
	        });
        },

        logRoutes: function() {
            this.router.routed.add(console.log, console); //log all routes

            return this;
        },

        loadScripts: function(urls, callback) {
            this.loadAll(urls, function(results) {
                var i = 0,
                    max = results.length;

                for(;i < max; i++) {
                    (new Function(results[i]))();
                }

                if (callback) callback();
            });
        },
        loadStyles: function(urls) {
            var i = 0,
                max = urls.length,
                style,
                styles = document.createDocumentFragment();

            for(;i<max;i++) {
                style = document.createElement('link');
                style.setAttribute('href', urls[i]);
                style.setAttribute('rel', 'stylesheet');
                styles.appendChild(style);
            }

            return styles;
        },
        process: function(html) {
            var el = document.createElement('div'),
                frag = document.createDocumentFragment(),
                child;

            el.innerHTML = html;

            while (el.children.length > 0) {
                child = el.firstChild;
                el.removeChild(child);
                frag.appendChild(child);

                if (child.nodeType === 1) {
                    new Vue({
                        el: child,
                        data: (function () {
                            var data = {
                                    session: Enpowi.session
                                },
                                jsonEncoded = child.getAttribute('data'),
                                jsonDecoded,
                                moduleData,
                                i;

                            if (jsonEncoded) {
                                child.removeAttribute('data');
                                jsonDecoded = decodeURIComponent(jsonEncoded);
                                moduleData = JSON.parse(jsonDecoded);

                                for (i in moduleData) if (i && moduleData.hasOwnProperty(i)) {
                                    data[i] = moduleData[i];
                                }
                            }

                            return data;
                        })(),
                        methods: {
                            go: function (sig) {
                                app.go(sig);
                            },
                            stringify: function (json) {
                                return JSON.stringify(json);
                            },
                            arrayLookup: function(array, key, value) {
                                var i = 0,
                                    max = array.length;

                                for(;i < max; i++) {
                                    if (array[i][key] === value) return array[i];
                                }

                                return null;
                            },
                            hasPerm: function (module, component) {
                                var hasPerm = false;
                                $.each(Enpowi.session.user.groups, function () {
                                    $.each(this.perms, function () {
                                        if (this.module === module || this.module === '*') {
                                            if (this.component === component || this.component === '*') {
                                                hasPerm = true;
                                            }
                                        }
                                    });
                                });

                                return hasPerm;
                            },
                            hasKey: function (key, array) {
                                return array[key] !== undefined;
                            }
                        }
                    });
                }
            }

            return frag;
        },

        go: function(route) {
            if ('#/' + route === window.location.hash.toString()) {
                this.hasher.setHash(route + '/');
            } else {
                this.hasher.setHash(route);
            }

            return this;
        },

        loadScript: function(url, callback) {
            this.load(url, function(scriptHTML) {
                (new Function(scriptHTML))();
                callback();
            });
        },

        loadAll: function(urls, callback) {
            var app = this,
                i = 0,
                progress = 0,
                max = urls.length,
                results = [];

            for (;i < max; i++) {
                (function(i) {
                    app.load(urls[i], function(result) {
                        results[i] = result;
                        progress++;
                        if (progress === max) {
                            callback(results);
                        }
                    });
                })(i);
            }

            return this;
        },
        load: function(url, callback, usePost) {
            var request = new XMLHttpRequest();

            request.open((usePost ? 'POST' : 'GET'), url, true);

            request.onload = function() {
                if (request.status >= 200 && request.status < 400) {
                    // Success!
                    callback(request.responseText);
                } else {
                    // Error
                    callback(null, request.responseText);

                }
            };

            request.onerror = function() {};

            request.send();

            return this;
        },
        loadModule: function(url, callback) {
            var app = this;

            this.load(Enpowi.module.url(url), function(moduleHtml) {
                callback(app.process(moduleHtml));
            });

            return this;
        },
        isLoading: false,
        /**
         *
         * @param {Boolean} isLoading
         * @returns {Enpowi.App}
         */
        setLoading: function(isLoading) {
            var el;
            if ((el = this.loadingElement) === null) return this;

            if (this.isLoading === false && isLoading) {
                this.isLoading = isLoading;
	            Enpowi.App.pub('app.delay');
            }

            else if (this.isLoading && isLoading === false) {
                this.isLoading = isLoading;
	            Enpowi.App.pub('app.continue');
            }

            return this;
        }
    });