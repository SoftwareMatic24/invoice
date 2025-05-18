function showModal(id, cantClose = false) {

	showOverlay();
	document.querySelector(`#${id}`).classList.add('active');

	if (cantClose !== true) {
		Trigger.OVERLAY_CLICK = function () {
			hideModal(id);
		}
	}


}

function hideModal(id) {
	document.querySelector(`#${id}`).classList.remove('active');
	hideOverlay();
} 