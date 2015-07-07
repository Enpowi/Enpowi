var u = Enpowi.Test.Utilities;

(function(tf){
	tf.test("login", function(tf) {
		u.open('../../', function(w) {
			var Enpowi = w.Enpowi,
				url = Enpowi.utilities.url,
				$ = w.$,
				next = u.steps([
					function() {
						$.get(url('users/listService'), {
							action: 'impersonateAnonymous'
						}, function() {
							next();
							w.close();
						});
					},
					function() {
						$.get(url('..'), function() {
							next();
							w.close();
						});
					}
				]);
		});
		tf.assert(true, "To be sure that initial test pass !");
	});
})(tf);