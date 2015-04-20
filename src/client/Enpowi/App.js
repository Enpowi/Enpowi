/**
 * @name App
 * @memberOf {Enpowi}
 * @constructor
 */
Namespace('Enpowi').

    Class('App', {
        construct: function(callback) {
            this.router = crossroads;
            this.routes = [];
            this.hasher = hasher;
            this.loadingElement = null;

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
        },

        logRoutes: function() {
            this.router.routed.add(console.log, console); //log all routes

            return this;
        },

        qMod: function(query) {
            return Enpowi.directives.defaultModuleElement.querySelector(query);
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
         * @type {HTMLElement}
         */
        processScript: function(script, scriptFrag) {
            var replacementScript;

            if (script.innerHTML.length > 0) {
                (new Function(script.innerHTML))();
            } else {
                replacementScript = document.createElement('script');
                replacementScript.setAttribute('src', script.getAttribute('src'));
                scriptFrag.appendChild(replacementScript);
            }

            return this;
        },
        process: function(html) {
            var el = document.createElement('div'),
                children,
                frag = document.createDocumentFragment(),
                scriptFrag = document.createDocumentFragment(),
                scriptAnchor = document.querySelector('script'),
                child;

            el.innerHTML = html;
            children = el.children;

            while(children.length > 0) {
                child = el.firstChild;
                console.log(child);
                el.removeChild(child);

                if (child.nodeName === 'SCRIPT') {
                    this.processScript(child, scriptFrag);
                } else if (child.nodeType === 1) {
                    frag.appendChild(child);
                    new Vue({
                        el: child,
                        data: (function () {
                            var data = {
                                    session: Enpowi.session
                                },
                                hasData = child.hasAttribute('data'),
                                key = child.getAttribute('data'),
                                moduleData,
                                i;

                            if (hasData) {
                                moduleData = Enpowi.module.data[key];
                                for (i in moduleData) if (moduleData.hasOwnProperty(i)) {
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
                } else {
                    frag.appendChild(child);
                }
            }

            scriptAnchor.parentNode.insertBefore(scriptFrag, scriptAnchor);

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
        load: function(url, callback) {
            var request = new XMLHttpRequest();

            request.open('POST', url, true);

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
                el.modal();
            }

            else if (this.isLoading && isLoading === false) {
                this.isLoading = isLoading;
                el.modal('hide');
            }

            return this;
        }
    });