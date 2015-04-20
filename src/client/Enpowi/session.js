Namespace('Enpowi').
    Class('session', {
        user: {},
        theme: null,
        update: function(type, sessionItems) {
            var oldSessionItems = this[type],
                key;

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

            return this;
        }
    });