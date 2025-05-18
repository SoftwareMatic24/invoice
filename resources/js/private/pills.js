let pillsPool = [];

function pills(index = 0, pills = [], frames = []) {
	let id = uid('pills');

	function view(parentId = null) {
		this.parentId = parentId;
		let pillsContainer = document.querySelector('#' + parentId);

		if (pills === null && frames === null) {
			let els = pillsContainer.querySelectorAll('.pills li');
			pills = Array.from(els).map(el => el.innerHTML);
		}

		let view = ``;
		pills.forEach((pill, pillIndex) => {
			view += `<li onclick="pillsClickHandler('${id}', ${pillIndex})" class="${index == pillIndex ? 'active' : ''}">${pill}</li>`;
		});

		if (pills.length > 0) view = `<ul class="pills" id="${id}">${view}</ul>`;

		if (parentId !== null) {
			if (pillsContainer !== null) pillsContainer.innerHTML = view;
			if (frames !== null) document.querySelector(`[data-pills-container="${parentId}"]`).innerHTML = frames.join('');
		}

		return view;
	}

	function select(newIndex = 0) {
		index = newIndex;
		document.querySelectorAll(`#${id} li`).forEach((li, liIndex) => {
			if (liIndex == newIndex) li.classList.add('active');
			else li.classList.remove('active');
		});

		document.querySelectorAll(`[data-pills-container="${this.parentId}"] > *`).forEach((el, elIndex) => {
			if (elIndex === newIndex) el.classList.remove('hide');
			else el.classList.add('hide');
		});

	}

	let pill = {
		id: id,
		view: view,
		select: select
	};

	pillsPool.push(pill);
	return pill;
}

function pillsClickHandler(id, index) {
	let pill = pillsPool.find(pill => pill.id == id);
	if (pill == undefined) return;
	pill.select(index);
}