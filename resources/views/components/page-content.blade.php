@section('page-style')
<link rel="stylesheet" href="{{ asset('css/ckeditor-child.css') }}">
@parent
@stop

<div id="content-modal" class="modal modal-xs-sm">
	<div class="modal-header">
		<p class="modal-title">{{ ucwords(__("content section")) }}</p>
		<span onclick="hideModal('content-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form action="#" onsubmit="createContentSection()">
			<div class="form-group">
				<label class="input-style-1-label">{{ __("section title") }}</label>
				<input name="title" type="text" class="input-style-1">
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button onclick="createContentSection()" class="button button-block button-primary">{{ __("create") }}</button>
	</div>
</div>

<div id="content-list"></div>
<button type="button" class="button button-primary-border" onclick="showContentModal()">{{ __("add content section") }}</button>

@section('page-script')
<script src="{{ asset('js/ckeditor.js') }}"></script>
<script>

	let contentEditors = {};

	function showContentModal() {
		let modal = document.querySelector('#content-modal');
		modal.querySelector('[name="title"]').value = '';
		showModal('content-modal');
	}

	function createContentSection(content = null) {
		if (event) event.preventDefault();

		let contentList = document.querySelector('#content-list');
		let editorUid = uid();

		let modal = document.querySelector('#content-modal');
		let title = modal.querySelector('[name="title"]').value;
		let theContent = '';

		if (content !== null) {
			title = content.title;
			theContent = content.content;
		}

		if (title === '') {
			Notification.show({
				classes: ['fail'],
				text: 'Section title is required.'
			});
			return;
		}

		let layout = `
				<div class="form-group content-container | margin-bottom-1 margin-top-2">
					<label data-id="${editorUid}" class="input-style-1-label content-label | d-flex justify-content-space-between align-items-center">
						<span class="text">${title}</span>
						<span onclick="removeContentSection('${editorUid}')">
							<svg class="ckeditor-remove-icon"><use xlink:href="${BASE_URL}/assets/icons.svg#cross" /></svg>
						</span>
					</label>
					<div id="${editorUid}" class="editor"></div>
				</div>
		`;

		contentList.insertAdjacentHTML('beforeend', layout);

		ClassicEditor.create(document.querySelector(`#${editorUid}`), {
				licenseKey: '',
				initialData: theContent == null ? '' : theContent,
			})
			.then(editor => {
				contentEditors[editorUid] = editor;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: htu0gx4ou56h-hrgzwh179cfe');
				console.error(error);
			});

		hideModal('content-modal');
	}

	function removeContentSection(editorUid) {
		Confirmation.show({
			positiveButton: {
				function: function() {
					let editor = document.querySelector(`#${editorUid}`);
					let container = editor.closest('.content-container');
					container.remove();
				}
			}
		});
	}

	function getContentSectionsData() {
		let sections = [];
		let contentContainers = document.querySelectorAll('#content-list .content-container');
		contentContainers.forEach((contentContainer) => {
			let sectionTitle = contentContainer.querySelector('.content-label .text').innerHTML;
			let editorId = contentContainer.querySelector('.content-label').dataset.id;
			let htmlContent = contentEditors[editorId].getData();
			sections.push({
				title: sectionTitle,
				content: htmlContent
			});
		});

		return sections;
	}
</script>

@parent
@stop