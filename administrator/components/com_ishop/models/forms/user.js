window.addEvent('domready', function() {
	document.formvalidator.setHandler('fio',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
});
