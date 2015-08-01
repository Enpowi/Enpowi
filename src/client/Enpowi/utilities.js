'use strict';

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
	        },
			//[modified] http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable#answer-14919494
			humanFileSize: function (bytes, si) {
				if (si === undefined) {
					si = true;
				}
				var thresh = si ? 1000 : 1024;
				if (Math.abs(bytes) < thresh) {
					return bytes + ' B';
				}
				var units = si
					? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
					: ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
				var u = -1;
				do {
					bytes /= thresh;
					++u;
				} while (Math.abs(bytes) >= thresh && u < units.length - 1);
				return bytes.toFixed(1) + ' ' + units[u];
			}
        }
    });