Namespace('Enpowi').

    Class('directives', {
        Static: {
            defaultModuleElement: null,
            queue: [],
            data: {},
            app: null,
	        bindingDirectives: [],
	        setBinding: function(directive) {
		        this.bindingDirectives.push(directive);
	        },
	        doneBinding: function(directive) {
		        this.bindingDirectives.splice(this.bindingDirectives.indexOf(directive), 1);

		        if (this.bindingDirectives.length < 1) {
			        Enpowi.App.pub('directive.ready');
		        }
	        },
            setup: function (app) {
                var me = this;
                me.app = app;

                Vue.directive('module', {
                    bind: function () {
                        var el = this.el,
	                        directive = this;
	                    me.setBinding(directive);
                        switch (el.nodeName) {
                            case 'FORM':
                                Enpowi.forms.strategy(el, this.vm, this);
	                            me.doneBinding(directive);
                        }
                    }
                });

                Vue.directive('module-item', {
                    deep: true,
                    bind: function () {
                        var me = this,
	                        el = this.el;

	                    el.onchange = function () {
		                    var form,
			                    parent;

		                    parent = el.parentNode;

		                    while (parent !== null && parent.nodeName !== 'FORM') {
			                    parent = parent.parentNode;

		                    }

		                    if (parent === null) return;

		                    form = parent;

	                        Enpowi.utilities.trigger(form, 'submit');
                        };
                    }
                });

                Vue.directive('header', {
                    bind: function () {
                        var el = this.el,
	                        directive = this;

	                    me.setBinding(directive);

                        app.loadModule(Enpowi.session.theme + '/header', function (html) {
                            el.appendChild(html);
	                        me.doneBinding(directive);
                        });
                    }
                });

                Vue.directive('navigation', {
                    bind: function () {
                        var el = this.el,
	                        directive = this;

	                    me.setBinding(directive);

                        app.loadModule(Enpowi.session.theme + '/navigation', function (html) {
                            el.appendChild(html);
	                        me.doneBinding(directive);
                        });
                    }
                });

                Vue.directive('article', {
                    bind: function () {
	                    var el = this.el;
                        me.defaultModuleElement = el;
                    }
                });

                Vue.directive('side', {
                    bind: function () {
	                    var directive = this;
	                    me.setBinding(directive);
	                    var el = this.el;

	                    app.loadModule(Enpowi.session.theme + '/' + this.expression, function (html) {
		                    el.appendChild(html);
		                    me.doneBinding(directive);
	                    });
                    }
                });

                Vue.directive('footer', {
                    bind: function () {
	                    var directive = this;
	                    me.setBinding(directive);
                        var el = this.el;

                        app.loadModule(Enpowi.session.theme + '/footer', function (html) {
                            el.appendChild(html);
	                        me.doneBinding(directive);
                        });
                    }
                });

                Vue.directive('source-edit', {
                    bind: function() {
                        var directive = this,
	                        el = this.el,
                            form = el.form,
                            styleUrls = ["vendor/codemirror/lib/codemirror.css"],
                            scriptUrls = ["vendor/codemirror/lib/codemirror.js"],
                            mode = '';

	                    me.setBinding(directive);

                        switch (this.expression.toLowerCase()) {
                            case "wikilingo":
                                styleUrls.push("vendor/codemirror.wikilingo/wikiLingo.css");
                                scriptUrls.push("vendor/codemirror.wikilingo/wikiLingo.js");
                                mode = 'text/wikiLingo';
                                break;
                            default:

                        }

                        el.parentNode.appendChild(app.loadStyles(styleUrls));
                        app.loadScripts(scriptUrls, function() {
                            var cm = CodeMirror.fromTextArea(el, {
                                mode: mode
                            });

                            cm.on('change', function() {
                                el.value = cm.getValue();
                            });

                            form.addEventListener('submit', function() {
                                el.value = cm.getValue();
                            });

	                        me.doneBinding(directive);
                        });
                    }
                });

	            Vue.directive('frame', {
		            bind: function() {
			            var el = this.el;

			            if (el.hasAttribute('static')) {
				            app.load(Enpowi.module.url(this.expression), function (html) {
					            $(el).append(html);
				            });
			            }
		            },
		            update: function (url) {
			            var el = this.el;

			            if (el.hasAttribute('static')) return;

			            app.load(Enpowi.module.url(url), function (html) {
				            $(el).append(html);
			            });
		            }
	            });
            }
        }
    });