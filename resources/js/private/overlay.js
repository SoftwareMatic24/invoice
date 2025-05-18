let overlay = document.querySelector('.overlay');
let blurOverlay = document.querySelector('.blur-overlay');

overlay.addEventListener('click', function () {
	if (Trigger.OVERLAY_CLICK === null) return;
	Trigger.OVERLAY_CLICK(Trigger.PARAMS);
});

function showOverlay() {
	overlay.classList.add('active');
}

function hideOverlay() {
	overlay.classList.remove('active');
}