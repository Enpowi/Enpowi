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
                    url;
                this.app.bindRouteUrls(tempRouter, function (_url, request, module, component) {
                    url = new String(_url);
	                url.request = request;
	                url.module = module;
	                url.component = component;
                });

	            tempRouter.parse(moduleAndComponentOrPlainUrl);

                return url;
            }
        }
    });