'use strict';

Namespace('Enpowi').

	Class('forms', {
		Static: {
			strategy: function(el, vue, directive) {
				var $el = $(el),
					me = this;

				el.addEventListener('submit', function(e) {
					e.preventDefault();

					var elements = {},
						i = 0,
						items = $el.serializeArray(),
						max = items.length,
						item;


					for(;i < max; i++) {
						item = items[i];
						elements[item.name] = el.querySelector('[name="' + item.name + '"]');
					}

					me.socket(Enpowi.utilities.url(el.getAttribute('action')), $el.serialize(), elements, items, el, vue);

					return false;
				});
			},
			socket: function(url, serialized, elements, serializedArray, form, vue) {
				var listening = form.hasAttribute('listen'),
					pubTo = Enpowi.App.pubTo(),
					hasParamResponse = false,
					response;

				if (listening) {
					pubTo.listen(arguments);
				}

				$
					.get(url, serialized, function(result) {
						var i,
							json = null;

						if (result.length < 1) return;

						try {
							json = JSON.parse(result);
						} catch (e) {
							return;
						}

						//from here down is for json only
						if (vue.paramResponseCache) {
							for (i in vue.paramResponseCache) if (i && vue.paramResponseCache.hasOwnProperty(i)) {
								vue.$set(i, null);
							}
						}

						if (json.hasOwnProperty('paramResponse')) {
							hasParamResponse = true;
							response = vue.paramResponseCache = json.paramResponse;

							pubTo.paramResponse(response);

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
							//we return here, and because hasParamResponse is true, it will not data-done
							return;
						}

						if (json.hasOwnProperty('successResponse')) {
							response = vue.successResponseCache = json.successResponse;

							pubTo.successResponse(response);

							for (i in response) if (response.hasOwnProperty(i)) {
								vue.$set(i, response[i]);
							}
						}
					})
					.fail(function() {
						console.log('callback failure');
						console.log(arguments);
					})
					.done(function() {
						if (listening) {
							pubTo.listened(response);
							return;
						}

						if (hasParamResponse) return;

						if (form.hasAttribute('data-done')) {
							setTimeout(function () {
								app.go(form.getAttribute('data-done'));
							}, 0);
						}
					});
			}
		}
	});