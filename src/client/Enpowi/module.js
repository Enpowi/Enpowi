Namespace('Enpowi').
    Class('module',{
        Static: {
            setup: function (app) {
                this.app = app;
            },
            url: function (moduleAndComponentOrPlainUrl) {
	            if (moduleAndComponentOrPlainUrl.charAt(0) === '~') {
		            return moduleAndComponentOrPlainUrl.substring(1);
	            }

                var tempRouter = crossroads.create(),
                    url = '';
                this.app.bindRouteUrls(tempRouter, function (_url) {
                    url = _url;
                });
                tempRouter.parse(moduleAndComponentOrPlainUrl);

                return url;
            }
        }
    });