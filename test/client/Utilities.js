Namespace('Enpowi.Test').
	Class('Utilities', {
		Static: {
			open: function(url, callback) {
				var w = window.open(url),
					d;
				//popup blockers
				if (w !== undefined) {
					d = w.document;
					callback.document = d;
					var interval = setInterval(function() {
						if (w.app !== undefined) {
							window.clearInterval(interval);
							callback(w.app, w.jQuery, w, d);
						}
					}, 20)
				}
			},
			crawl: function(steps) {
				return function() {
					var step = steps.pop();
					if (step) {
						step();
					}
				};
			}
		}
	});