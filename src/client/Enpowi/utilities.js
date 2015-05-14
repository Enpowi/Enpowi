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
	        }
        }
    });