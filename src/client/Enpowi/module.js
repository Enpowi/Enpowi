Namespace('Enpowi').
    Class('module',{
        Static: {
            setup: function (app) {
                this.app = app;
            },
            url: function (moduleAndComponent) {
                var tempRouter = crossroads.create(),
                    url = '';
                this.app.bindRouteUrls(tempRouter, function (_url) {
                    url = _url;
                });
                tempRouter.parse(moduleAndComponent);

                return url;
            }
        }
    });