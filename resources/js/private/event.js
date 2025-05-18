
function handleInputForUppercaseInputs() {
	let els = document.querySelectorAll(`[data-input="uppercase"]`);
	els.forEach(el => {
		if (el.dataset.inputEvent) return;
		el.setAttribute("data-input-event", "true");
		el.addEventListener("input", function () {
			let targetEl = event.target;
			targetEl.value = targetEl.value.toUpperCase();
		});
	});
}

function handleInputForSentenccaseInputs() {
	let els = document.querySelectorAll(`[data-input="sentence-case"]`);
	els.forEach(el => {
		if (el.dataset.inputEvent) return;
		el.setAttribute("data-input-event", "true");
		el.addEventListener("input", function () {
			let targetEl = event.target;
			targetEl.value = capitalizeAll(targetEl.value);
		});
	});
}

handleInputForUppercaseInputs();
handleInputForSentenccaseInputs();