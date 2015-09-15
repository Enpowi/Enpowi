'use strict';

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
            setup: function () {
                var me = this,
	                app = Enpowi.app,
	                emptyEl = function(el) {
		                while (el.firstChild) el.removeChild(el.lastChild);
	                };

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
	                        el = this.el,
                            parent = null,
                            listenFn = function () {
                                var form;

                                if (parent === null) {
                                    parent = el.parentNode;

                                    while (parent !== null && parent.nodeName !== 'FORM') {
                                        parent = parent.parentNode;

                                    }

                                    if (parent === null) return;
                                }

                                form = parent;

                                Enpowi.utilities.trigger(form, 'submit');
                            };

                        el.addEventListener('change', listenFn);
                        el.addEventListener('keyup', listenFn);
                        el.addEventListener('click', listenFn);
                    }
                });

                Vue.directive('header', {
                    bind: function () {
                        var el = this.el,
	                        directive = this;

	                    me.setBinding(directive);

	                    emptyEl(el);

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

	                    emptyEl(el);

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
	                    var directive = this,
		                    el = this.el;

	                    me.setBinding(directive);

	                    emptyEl(el);

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

	                    emptyEl(el);

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
			            var me = this,
				            el = this.el;

			            this.vm.reload = function() {
				            me.update(me.urlRaw);
			            };

			            $(el).bind('reload', this.vm.reload);
		            },
		            update: function (urlRaw) {
			            this.urlRaw = urlRaw;
			            var el = this.el,
				            url;

			            if (el.style.display === 'none') return;
			            if (this.expression === null) return;
			            if (urlRaw === null) return;

			            if (el.hasAttribute('static')) {
                            emptyEl(el);
				            app.load(Enpowi.utilities.url(urlRaw), function (html) {
					            $(el).append(html);
				            });
				            this.expression = '';
			            } else {
				            app.load(url = Enpowi.utilities.url(urlRaw), function (html) {
                                emptyEl(el);
					            el.appendChild(app.process(html, url.m, url.c));
				            });
			            }
		            }
	            });

                Vue.directive('pager', {
                    update: function(value) {
                        value.pages = value.pages || 0;

                        if (value.pages <= 1) return;

                        var page = value.page,
                            pages = value.pages,
                            size = value.size || 5,
                            url = value.url,
                            i = Math.floor(Math.max(1, page - size)),
                            max = Math.ceil(Math.min(pages, page + size)),
                            pageBeforeAnchor = '',
                            pageAfterAnchor = '',
                            pageAnchors = '',
                            el = this.el;

                        if (el.nodeName !== 'NAV') {
                            console.log('Warning: pager should be used with nav elements');
                        }

                        for (; i <= max; i++) {
                            pageAnchors += '<li' + (page === i ? ' class="active"' : '') + '><a href="' + url + i + '">' + i + '</a></li>';
                        }

                        if (page > 1) {
                            pageBeforeAnchor = '<li>\
                                <a href="' + url + (page - 1) + '" aria-label="Previous">\
                                    <span aria-hidden="true">&laquo;</span>\
                                </a>\
                            </li>';
                        } else {
                            pageBeforeAnchor = '<li>\
                                <a aria-label="Previous">\
                                    <span aria-hidden="true">&laquo;</span>\
                                </a>\
                            </li>';
                        }

                        if (page < pages) {
                            pageAfterAnchor = '<li>\
                                <a href="' + url + (page + 1) + '" aria-label="Next">\
                                    <span aria-hidden="true">&raquo;</span>\
                                </a>\
                            </li>';
                        } else {
                            pageAfterAnchor = '<li>\
                                <a aria-label="Next">\
                                    <span aria-hidden="true">&raquo;</span>\
                                </a>\
                            </li>';
                        }

                        el.innerHTML = '<ul class="pagination">\
                            ' + pageBeforeAnchor + pageAnchors + pageAfterAnchor + '\
                        </ul>';
                    }
                });

                Vue.directive('find', {
                    update: function (value) {
                        if (this.el.vFindActive) return;

                        var el = this.el,
                            find = Enpowi.utilities.url(value.find),
                            init = function() {
                                el.setAttribute('autocomplete', 'off');
                                $(el).typeahead({
                                    ajax: {
                                        url: find,
                                        triggerLength: 1
                                    },
                                    item: '<li><a href="#"></a></li>',
                                    onSelect: function(selection) {
                                        app.go('users/list?email=' + selection.value);
                                    }
                                });

                                el.vFindActive = true;
                            };

                        if (el.nodeName !== 'INPUT') {
                            console.log('Warning: find should be used with input elements');
                        }

                        if ($.fn.typeahead === undefined) {
                            app.loadScript('vendor/bs-typeahead/js/bootstrap-typeahead.js', init);
                        } else {
                            init();
                        }
                    }
                });
            }
        }
    });