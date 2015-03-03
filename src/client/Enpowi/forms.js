Enpowi.forms = {
	strategy: function(el, vue) {
		var $el = $(el),
			me = this;

		$el.on('submit', function(e) {
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

			me.socket(el.getAttribute('action'), $el.serialize(), elements, items, el, vue);
		});
	},
	socket: function(url, serialized, elements, serializedArray, form, vue) {
		$.getJSON(Enpowi.module.url(url), serialized, function(json) {
			$.each(serializedArray, function() {
				vue.$delete(this.name);
			});

			if (json.paramResponse) {
				var response = json.paramResponse,
					i;

				for (i in response) if (i && response.hasOwnProperty(i)) {
					(function(i) {
						Enpowi.translation.translate(response[i], function(v) {
							vue.$add(i, v);
						});
					})(i);
				}
			} else {
				app.go(form.getAttribute('data-done'));
			}
		});
	}
};