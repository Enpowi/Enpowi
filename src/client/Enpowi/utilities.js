/**
 * @name utilities
 * @memberOf Enpowi
 */
Namespace('Enpowi').
    Class('utilities', {
        Static: {
	        trigger: function(el, eventName) {
		        var event = document.createEvent('HTMLEvents');
		        event.initEvent(eventName, true, true);
		        el.dispatchEvent(event);

		        return this;
	        },
	        url: function (moduleAndComponentOrPlainUrl) {
		        if (moduleAndComponentOrPlainUrl.charAt(0) === '~') {
			        return moduleAndComponentOrPlainUrl.substring(1);
		        }

		        var tempRouter = crossroads.create(),
			        url;

		        Enpowi.app.bindRouteUrls(tempRouter, function (_url, request, m, c) {
			        url = new String(_url);
			        url.r = request;
			        url.m = m;
			        url.c = c;
		        });

		        tempRouter.parse(moduleAndComponentOrPlainUrl);

		        return url;
	        }
        }
    });