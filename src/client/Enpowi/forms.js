Namespace('Enpowi').

Class('forms', {
	Static: {
		strategy: function(el, vue, directive) {
			var $el = $(el),
				me = this;

			el.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!el.hasAttribute('data-done') && !el.hasAttribute('listen')) {
                    app.go(el.getAttribute('action'));
                    return;
                }

				var elements = {},
					i = 0,
					items = $el.serializeArray(),
					max = items.length,
					item;


				for(;i < max; i++) {
					item = items[i];
					elements[item.name] = el.querySelector('[name="' + item.name + '"]');
				}

				me.socket(Enpowi.module.url(el.getAttribute('action')), $el.serialize(), elements, items, el, vue);

				return false;
			});
		},
		socket: function(url, serialized, elements, serializedArray, form, vue) {
            var listening = form.hasAttribute('listen');

            if (listening) {
                Enpowi.App.pubTo().listen(arguments);
            }
			$.getJSON(url, serialized, function(json) {
                var response,
                    i;

				$.each(serializedArray, function() {
					//vue.$delete(this.name);
				});

				if (json.hasOwnProperty('paramResponse')) {
					response = json.paramResponse;

					for (i in response) if (i && response.hasOwnProperty(i)) {
						(function(i) {
                            if (typeof response[i] === 'string') {
                                Enpowi.translation.translate(response[i], function (v) {
                                    vue.$set(i, v);
                                });
                            } else {
                                vue.$set(i, response[i]);
                            }
						})(i);
					}

                    if (listening) {
                        Enpowi.App.pubTo().listened(response);
                    }
                    return;
				} else if(listening) {
					return;
				}

                if (json.hasOwnProperty('successResponse')) {
                    response = json.successResponse;

                    for (i in response) if (response.hasOwnProperty(i)) {
                        vue.$set(i, response[i]);
                    }
                }

                setTimeout(function() {
                    if (form.hasAttribute('data-done')) {
                        app.go(form.getAttribute('data-done'));
                    }
                },0);
			});
		}
	}
});