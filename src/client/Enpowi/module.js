Enpowi.module = {
	setup: function(app) {
		this.app = app;
		this.data = {};
	},
	url: function(moduleAndComponent) {
		var tempRouter = crossroads.create(),
			url = '';
		this.app.bindRouteUrls(tempRouter, function(_url) {
			url = _url;
		});
		tempRouter.parse(moduleAndComponent);

		return url;
	}
};