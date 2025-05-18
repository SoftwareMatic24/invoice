var tabs = function (id) {
	this.tabsEl = null;

	function addClickEvent(listEl) {
		let liEls = listEl.querySelectorAll('li');
		liEls.forEach(el => {
			el.addEventListener('click', function () {
				let target = event.target;
				handleClickEvent(target);
			});
		});
	}

	function handleClickEvent(el) {

		let tabsContainer = el.closest('.tabs-container');
		let tabsEl = el.closest('.tabs');
		let tabsDataContainer = tabsContainer.querySelector('.tabs-data-container');
		let dataContainerEls = tabsDataContainer.querySelectorAll('[data-tab-data]');
		let liEl = el;
		let liEls = tabsEl.querySelectorAll('li');

		if (el.nodeName.toString().toLowerCase() !== 'li') liEl = el.closest('li');

		liEls.forEach(listEl => {
			if (listEl === liEl) listEl.classList.add('active');
			else listEl.classList.remove('active');
		});

		dataContainerEls.forEach(dataContainerEl => {
			if (liEl.dataset.tab === dataContainerEl.dataset.tabData) dataContainerEl.classList.add('active');
			else dataContainerEl.classList.remove('active');
		});

	}

	this.tabsEl = document.querySelector(`#${id}`);
	if (this.tabsEl !== null) addClickEvent(this.tabsEl);

	return this.tabsEl;
};
