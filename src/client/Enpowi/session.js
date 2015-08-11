'use strict';

Namespace('Enpowi').
    Class('session',
	/**
	 * properties can have a method {property}Compare which receives a left and right argument for complex objects to be compared
	 * @memberOf Enpowi
	 * @name session
	 *
	 */
	{
        user: {},
        theme: 'default',
		siteName: '',
		siteUrl: '',

		userCompare: function(leftUser, rightUser) {
			if (leftUser.email !== rightUser.email) return true;

			var leftGroups = leftUser.groups,
				rightGroups = rightUser.groups,
				leftPerms,
				rightPerms,
				groupIndex = 0,
				maxGroups = leftGroups.length;

			if (leftGroups.length !== rightGroups.length) return true;

			for (;groupIndex < maxGroups; groupIndex++) {
				leftPerms = leftGroups[groupIndex].perms;
				rightPerms = rightGroups[groupIndex].perms;

				if (Object.keys(leftPerms).join(',') !== Object.keys(rightPerms).join(',')) return true;
			}

			return false;
		},

        update: function(type, sessionItems) {
            var util = Enpowi.utilities,
				oldSessionItems = this[type],
                key,
	            changed = false,
	            compare = this[type + 'Compare'];

	        //if object has properties, and has a compare function
	        if (compare !== undefined && util.isObject(this[type])) {
		        if (!compare(this[type], sessionItems)) return this;
	        }

            if (sessionItems instanceof Array) {
                for (key = 0; key < sessionItems.length; key++) {
	                oldSessionItems[key] = sessionItems[key];
                }
            } else if (typeof sessionItems === 'object') {
                for (key in sessionItems) if (key && sessionItems.hasOwnProperty(key)) {
                    oldSessionItems[key] = sessionItems[key];
                }
            } else {
                this[type] = sessionItems;
            }

	        Enpowi.App.pubTo().sessionChange([type, this[type]]);

            return this;
        }
    });