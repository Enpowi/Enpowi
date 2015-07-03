/**
 * @name App
 * @memberOf {Enpowi}
 * @constructor
 */
Namespace('Enpowi').
    Class('App', {
		Static: {
			pubs: {},
			one: function(eventName, callback) {
				var parentCallback = function() {
					callback.apply(this, arguments);

					var array = this.pubs[eventName];

					array.splice(array.indexOf(parentCallback), 1);
				};

				return this.sub(eventName, parentCallback);
			},
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
					pubs[i].apply(this, data);
				}

				return this;
			},

			oneTo: function() {
				var caller = {},
					me = this,
					n;

				for (n in this.event) if (this.event.hasOwnProperty(n)) {
					caller[n] = (function(eventName) {
						return function(callback) {
							return me.one(eventName, callback);
						};
					})(n);
				}

				return caller;
			},

			subTo: function() {
				var caller = {},
					me = this,
					n;

				for (n in this.event) if (this.event.hasOwnProperty(n)) {
					caller[n] = (function (eventName) {
						return function (callback) {
							return me.sub(eventName, callback);
						};
					})(n);
				}

				return caller;
			},

			pubTo: function() {
				var caller = {},
					me = this,
					n;

				for (n in this.event) if (this.event.hasOwnProperty(n)) {
					caller[n] = (function (eventName) {
						return function (args) {
							return me.pub(eventName, args);
						};
					})(n);
				}

				return caller;
			},


			event: {
				deny: 'deny',
				go: 'go',
				land: 'land',
				process: 'process',
				processed: 'processed',
				delay: 'delay',
				_continue: 'continue',
                listened: 'listened',
                listen: 'listen'
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
                landRoute = function(url, route, module, component) {
	                var init = false,
		                completed = false,
		                timer = setInterval(function() {
			                if (!init) {
				                init = true;
				                app.setLoading(true);
			                }

			                if (completed) {
				                clearInterval(timer);
				                app.setLoading(false);
			                }
		                }, 500);

                    app.load(url, function(data) {
	                    if (data == -1) {
		                    var result = app.pubTo().deny([url]);
		                    if (result === false) {
			                    return;
		                    }
	                    }
                        app.loadScript('modules/?module=app&component=session.js', function() {
	                        completed = true;
                            var result = app.process(data, module, component);
                            app.routeCallback(result);
                            app.pubTo().land([route]);
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
	        var me = this;
            //none
            //moduleName
            //moduleName/component
            //moduleName/component/id
            //moduleName/component?querystring
            router.normalizeFn = crossroads.NORM_AS_OBJECT;

            router.addRoute('/', function() {
                callback('modules/?module=' + Enpowi.session.theme + '&component=index', '', '', '');
            });
            router.addRoute('/{module}', function(path) {
                callback('modules/?module=' + path.module, path.request_);
            });
            router.addRoute('/{module}{?query}', function(path) {
                callback('modules/?module=' + path.module + '&'  + path['?query_'], path.request_, path.module, '');
            });
            router.addRoute('/{module}/{component}', function(path) {
                callback('modules/?module=' + path.module + '&component=' + path.component, path.request_, path.module, path.component);
            });
            router.addRoute('/{module}/{component}/{id}', function(path) {
                callback('modules/?module=' + path.module + '&component=' + path.component + '&id' + path.id, path.request_, path.module, path.component);
            });
            router.addRoute('/{module}/{component}{?query}', function(path) {
                callback('modules/?module=' + path.module + '&component=' + path.component + '&'  + path['?query_'], path.request_, path.module, path.component);
            });

	        router.routed.add(function(route) {
		        me.pubTo().go([route]);
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
		/**
		 * @type {DocumentFragment}
		 */
		processContainer: null,
        process: function(html, module, component) {
            var el = document.createElement('div'),
                frag = this.processContainer = document.createDocumentFragment(),
                child,
	            scripts,
	            script,
	            i = 0,
	            max,
	            scriptXSS = [],
	            scriptsRemote = [],
	            scriptsLocal = [],
	            vues = [],
                datas = [],
                elements = [],
                runJS = function() {
                    if (scriptsLocal.length > 0) {
                        for(var i = 0; i < scriptsLocal.length; i++) {
                            try {
                                (new Function('datas', 'vues', 'elements', scriptsLocal[i]))
                                (datas, vues, elements);
                            } catch (e) {
                                console.log(e);
                            }
                        }
                    }
                };

            el.innerHTML = html;

	        this.pubTo().process([el]);

	        //obtain scripts
	        scripts = el.querySelectorAll('script');

	        max = scripts.length;

	        for(;i<max;i++) {
		        script = scripts[i];
		        if (script.hasAttribute('src')) {
			        if (script.hasAttribute('xss')) {
				        scriptXSS.push(script);
			        } else {
				        scriptsRemote.push(script.getAttribute('src'));
			        }
		        } else {
			        scriptsLocal.push(script.innerHTML);
		        }
	        }


	        //process html
            while (el.children.length > 0) {
                child = el.firstChild;
                el.removeChild(child);
                elements.push(child);
                frag.appendChild(child);

                if (child.nodeType === 1) {
                    vues.push(new Vue({
                        el: child,
                        data: (function () {
                            var data = {
                                    session: Enpowi.session,
		                            module: module,
		                            component: component
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
	                            datas.push(data);
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
                    }));
                }
            }

	        //process scripts
	        if (scriptsRemote.length > 0) {
		        this.loadScripts(scriptsRemote, function() {
                    runJS();
		        });
	        } else {
                runJS();
	        }

	        if (scriptXSS.length > 0) {
		        $('body').append(scriptXSS);
	        }

	        this.pubTo().processed([vues]);

            return frag;
        },

        go: function(route) {
	        var existingHash = window.location.hash.toString(),
		        hasSlashes = existingHash.match(/\/$/);

            if ('#/' + route === existingHash) {
	            if (hasSlashes) {
		            this.hasher.setHash(route.substring(0, route.length - 1));
	            } else {
		            this.hasher.setHash(route + '/');
	            }
            } else {
                this.hasher.setHash(route);
            }

            return this;
        },

        loadScript: function(url, callback) {
            this.load(url, function(scriptHTML) {
                (new Function(scriptHTML))();
                if (callback !== undefined) callback();
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
		getElementById: function(id) {
			var el = document.getElementById(id);

			if (el !== null) return el;

			if (this.processContainer !== null) {
				return this.processContainer.querySelector('#' + id);
			}
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
            if (this.loadingElement === null) return this;

            if (this.isLoading === false && isLoading) {
                this.isLoading = isLoading;
	            this.pubTo().delay();
            }

            else if (this.isLoading && isLoading === false) {
                this.isLoading = isLoading;
	            this.pubTo()._continue();
            }

            return this;
        }
    });