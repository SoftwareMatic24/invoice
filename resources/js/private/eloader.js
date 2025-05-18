let eLoader = function () {
	let output = {};
	let show = function (element) {
		element.classList.add('e-loader');
	}
	let hide = function (element) {
		element.classList.remove('e-loader');
	}
	output.show = show;
	output.hide = hide;
	return output;
};
