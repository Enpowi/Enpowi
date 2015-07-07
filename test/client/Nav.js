Namespace('Enpowi.Test').
	Class('Nav', {
		ready: null,
		window: null,
		document: null,
		$: null,
		Enpowi: null,
		app: null,
		App: null,
		construct: function(ready) {
			this.ready = ready;

			this.activate();
		},
		activate: function() {
			var self = this,
				_window = window.open(url),
				_document,
				interval;

			//popup blockers
			if (_window !== undefined) {
				_document = _window.document;
				self.ready.document = _document;
				interval = setInterval(function() {
					if (_window.app !== undefined) {
						window.clearInterval(interval);
						self.window = _window;
						self.document = _document;
						self.$ = _window.jQuery;
						self.Enpowi = _window.Enpowi;
						self.app = _window.app;
						self.App = self.Enpowi.App;

						self.App.subTo()
							.go(function() {

							})
							.land(function() {

							});

						self.ready(_window.app, _window.jQuery, _window, _document);
					}
				}, 20)
			}
		},
		go: function() {

		}
	});