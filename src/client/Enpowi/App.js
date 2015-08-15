'use strict';

/**
 * @name App
 * @memberOf {Enpowi}
 * @constructor
 */
Namespace('Enpowi').
    Class('App', {
		Static: {
			m: null,
			c: null,
			subs: {},
			one: function(eventName, callback) {
				var parentCallback = function() {
					var array = this.subs[eventName] || [],
					index = array.indexOf(parentCallback);
					if (index > -1) {
						callback.apply(this, arguments);

						this.subs[eventName][index] = null;
					}
				};

				return this.sub(eventName, parentCallback);
			},
			sub: function(eventName, callback) {
				var subs;
				if ((subs = this.subs[eventName]) === undefined) subs = this.subs[eventName] = [];
				subs.push(callback);

				return this;
			},
			pub: function(eventName, data) {
				var subs,
					max,
					i;

				if ((subs = this.subs[eventName]) === undefined) return this;

				i = 0;
				max = subs.length;
				
				data = data || [];

				if (data.constructor === Array) {
					for (;i <max;i++) {
						subs[i].apply(this, data);
					}
				} else {
					for (;i <max;i++) {
						subs[i].call(this, data);
					}
				}

				this.subs[eventName] = subs.filter(function(v){ return v !== null && v !== undefined; });

				return this;
			},

			oneTo: function() {
				var caller = {},
					me = this,
					n;

				for (n in this.event) if (this.event.hasOwnProperty(n)) {
					caller[n] = (function(eventName) {
						return function(callback) {
							me.one(eventName, callback);
							return caller;
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
							me.sub(eventName, callback);
							return caller;
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
							me.pub(eventName, args);
							return caller;
						};
					})(n);
				}

				return caller;
			},

			event: {
				_continue: 'continue',
				delay: 'delay',
				deny: 'deny',
				go: 'go',
				land: 'land',
				listened: 'listened',
				listen: 'listen',
				paramResponse: 'paramResponse',
				process: 'process',
				processed: 'processed',
				sessionChange: 'sessionChange',
				successResponse: 'successResponse'
			},

			nonPersistentEvents: {
				paramResponse: 'paramResponse',
				successResponse: 'successResponse'
			},

			garbageEvents: function() {
				var nPE = this.nonPersistentEvents,
					i;

				for (i in nPE) if (nPE.hasOwnProperty(i)) {
					this.subs[i] = [];
				}

				return this;
			}
		},

        sessionTick: 1000 * 60,
        sessionInterval: null,

        construct: function(callback) {
            Enpowi.app = this;

	        this.titleElement = document.querySelector('title');
            this.router = crossroads;
            this.routes = [];
            this.hasher = hasher;
            this.loadingElement = null;
            this.routeCallback = callback;

            Enpowi.directives.setup();
            Enpowi.translation.setup();

            this
                .setupRoutes()
                .sessionListen();
        },

		changeTitle: function(title) {
			this.titleElement.textContent = title;

			return this;
		},

        setupRoutes: function() {
            //setup router
            var router = this.router,
                app = this,
                landRoute = function(url, route, m, c) {
	                app.garbageEvents();

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
	                    var pubTo = app.pubTo();
	                    if (data == -1) {
		                    var result = pubTo.deny([url]);
		                    if (result === false) {
			                    return;
		                    }
	                    }
                        app.loadScript('modules/?module=app&component=session.js', function() {
	                        completed = true;
	                        Enpowi.App.m = m;
	                        Enpowi.App.c = c;
                            var result = app.process(data, m, c),
	                            title = result.querySelector('title');

	                        if (title) {
		                        app.changeTitle(title.textContent);
		                        result.removeChild(title);
	                        } else {
		                        app.changeTitle(Enpowi.session.siteName);
	                        }

                            app.routeCallback(result);
                            pubTo.land([route]);
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

            return this;
        },

        bindRouteUrls: function(router, callback) {
	        var pubTo = this.pubTo();
            //none
            //moduleName
            //moduleName/component
            //moduleName/component/id
            //moduleName/component?querystring
            router.normalizeFn = crossroads.NORM_AS_OBJECT;

            router.addRoute('/', function() {
                callback('modules/?module=' + Enpowi.session.theme + '&component=index', '', '', '');
            });
            router.addRoute('/{m}', function(path) {
                callback('modules/?module=' + path.m, path.request_, path.m, '');
            });
            router.addRoute('/{m}{?query}', function(path) {
                callback('modules/?module=' + path.m + '&'  + path['?query_'], path.request_, path.m, '');
            });
            router.addRoute('/{m}/{c}', function(path) {
                callback('modules/?module=' + path.m + '&component=' + path.c, path.request_, path.m, path.c);
            });
            router.addRoute('/{m}/{c}/{id}', function(path) {
                callback('modules/?module=' + path.m + '&component=' + path.c + '&id' + path.id, path.request_, path.m, path.c);
            });
            router.addRoute('/{m}/{c}{?query}', function(path) {
                callback('modules/?module=' + path.m + '&component=' + path.c + '&'  + path['?query_'], path.request_, path.m, path.c);
            });

	        router.routed.add(function(route) {
		        pubTo.go([route]);
	        });
        },

        sessionListen: function() {
            this.sessionInterval = window.setInterval(function() {
                app.loadScript('modules/?module=app&component=session.js');
            }, this.sessionTick);

            return this;
        },
        sessionUnlisten: function() {
            window.clearInterval(this.sessionInterval);

            return this;
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
        process: function(html, m, c) {
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
                                    'session': Enpowi.session,
		                            'module': m,
		                            'component': c,
		                            'appModule': Enpowi.App.m,
		                            'appComponent': Enpowi.App.c
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
                            hasPerm: function (m, c) {
                                return app.hasPerm(m, c);
                            },
                            hasKey: function (key, array) {
                                return array[key] !== undefined;
                            },
	                        indexOf: function (array, value) {
		                        return array.indexOf(value);
	                        },
	                        inArray: function (array, value) {
		                        return array.indexOf(value) > -1;
	                        },
	                        dateFormatted: function(value) {
		                        return moment(value * 1000).format('LL');
	                        },
                            fileSize: function (value) {
                                return Enpowi.utilities.humanFileSize(value);
                            },
	                        timeFormatted: function(value, format) {
		                        return moment(value * 1000).format(format || 'LLL');
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
            var el,
                pC;

            if ((pC = this.processContainer) !== null) {
                el = pC.querySelector('#' + id);
            }

            if (el !== null) return el;

            return document.getElementById(id);
		},
        loadModule: function(urlRaw, callback) {
            var app = this,
	            url;

            this.load(url = Enpowi.utilities.url(urlRaw), function(moduleHtml) {
                callback(app.process(moduleHtml, url.m, url.c));
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
        },
        hasPerm: function (m, c) {
            var hasPerm = false;
            if (Enpowi.session.user.groups === undefined) return false;
            $.each(Enpowi.session.user.groups, function () {
                $.each(this.perms, function () {
                    if (this['module'] === m || this['module'] === '*') {
                        if (this['component'] === c || this['component'] === '*') {
                            hasPerm = true;
                        }
                    }
                });
            });

            return hasPerm;
        }
    });
