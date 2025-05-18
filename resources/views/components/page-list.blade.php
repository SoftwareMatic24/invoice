<div id="page-list-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">New List</p>
		<span onclick="hideModal('page-list-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form action="#" onsubmit="createListSection()">
			<div class="form-group">
				<label class="input-style-1-label">Section Title</label>
				<input name="title" type="text" class="input-style-1">
			</div>
		</form>
	</div>
	<div class="modal-footer | ">
		<button onclick="createListSection()" class="button button-sm button-block button-primary">Create</button>
	</div>
</div>
<div id="page-list"></div>
<button type="button" class="button button-primary-border" onclick="showPageListModal()">
	Add List
</button>
@section('page-script')

<script>
	function showPageListModal() {
		let modal = document.querySelector('#page-list-modal');
		modal.querySelector('[name="title"]').value = '';
		showModal('page-list-modal');
	}

	function createListSection(content = null) {
		if (event) event.preventDefault();
		
		let contentList = document.querySelector('#page-list');
		let uniqueId = uid();

		let modal = document.querySelector('#page-list-modal');
		let title = modal.querySelector('[name="title"]').value;

		if(content !== null) title = content.title;

		if (title === '') {
			Notification.show({
				classes: ['fail'],
				text: 'Section title is required.'
			});
			return;
		}

		let layoutStyle = '';

	
		let layout = `
				<div data-id="${uniqueId}" class="form-group content-container | margin-bottom-2" style="${layoutStyle}">
					<label class="input-style-1-label content-label | d-flex justify-content-space-between align-items-center">
						<span class="text" contenteditable="true" style="min-width:27.2rem;background-color:var(--clr-neutral-150);padding:1.6rem;border-radius:0.4rem;">${title}</span> <span onclick="removeListSection('${uniqueId}')">
							<svg class="dynamic-content-close" ><use xlink:href="${BASE_URL}/assets/icons.svg#cross" /></svg>
						</span>
					</label>
					<div class="page-list-container">
						<div class="page-list-items"></div>
						<div class="margin-top-1">
							<p onclick="addNewRowToListContent('${uniqueId}')" class="link-button link-button-orange">Add new row</p>
						</div>
					</div>
				</div>
		`;

		contentList.insertAdjacentHTML('beforeend', layout);

		if(content === null) content = {detail: [{column_name:'', column_value:''}]};

		content.detail.forEach(row => {
			addNewRowToListContent(uniqueId, row);
		});
		

		hideModal('page-list-modal');
	}

	function removeListSection(uniqueId) {
		Confirmation.show({
			positiveButton: {
				function: function() {
					let el = document.querySelector(`[data-id="${uniqueId}"]`);
					let container = el.closest('.content-container');
					container.remove();
				}
			}
		});
	}

	function addNewRowToListContent(uniqueId, data = null){
		
		let container = document.querySelector(`[data-id="${uniqueId}"] .page-list-items`);

		let layout = `
			<form onsubmit="return false;" class="margin-bottom-1">
				<div class="form-group">
					<div class="grids grids-3 gap-1">
						<div class="grid">
							<input name="column-name" type="text" class="input-style-1" placeholder="Column name" value="${data !== null ? data.column_name : ''}" />
						</div>
						<div class="grid">
							<input name="column-value" type="text" class="input-style-1" placeholder="Column value" value="${data !== null ? data.column_value : ''}" />
						</div>
						<div class="grid">
							<button onclick="removeListRow()" style="padding:1.9rem;" class="button button-icon button-icon-danger button-danger-border">
								<svg class="icon"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-trash" /></svg>
							</button>
						</div>
					</div>
				</div>
			</form>
		`;
		container.insertAdjacentHTML('beforeend', layout);

	}

	function removeListRow(){
		let target = event.target;
		let formGroupEl = target.closest('.form-group');
		formGroupEl.remove();
	}

	function getListSectionData() {
		let sections = [];
		let contentContainers = document.querySelectorAll('#page-list .content-container');
		contentContainers.forEach((contentContainer) => {
			let sectionTitle = contentContainer.querySelector('.content-label .text').innerHTML;

			let columnNameEls = contentContainer.querySelectorAll('[name="column-name"]');
			let columnValueEls = contentContainer.querySelectorAll('[name="column-value"]');

			let obj = {
				title: sectionTitle,
				list: []
			};

			columnNameEls.forEach((columnNameEl, elIndex) => {
				obj.list.push({
					name: columnNameEls[elIndex].value,
					value: columnValueEls[elIndex].value
				});
			});

			sections.push(obj);
		});

		return sections;
	}
</script>

@parent
@stop