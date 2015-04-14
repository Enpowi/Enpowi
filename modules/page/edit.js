(function() {
	"use strict";
	var contentEl = document.querySelector('[name="content"]'),
		resizeContentEl = function() {
			contentEl.style.height = (window.innerHeight * 0.6) + 'px';
		};

	resizeContentEl();

	window.onresize = resizeContentEl;
})();