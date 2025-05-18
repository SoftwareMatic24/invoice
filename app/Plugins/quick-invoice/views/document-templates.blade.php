@extends('layouts.portal')

@section('main-content')
<div class="data-table-container">
	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('name') }}</th>
				<th>{{ __('primary color') }}</th>
				<th>{{ __('secondary color') }}</th>
				@if($viewOnly != 1)
				<th>{{ __('status') }}</th>
				@endif
				<th>{{ __('date') }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<p style="margin-top:20px;font-size:14px;">{{ __('more-templates-coming-soon') }}</p>

<div id="page-modal" class="modal">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('payment-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="d-flex justify-content-space-between gap-3">
			<div class="{{ $viewOnly == '1' ? 'hide' : '' }} width-100" style="max-width: 30rem;">
				<div class="d-flex gap-2">
					<div>
						<label class="input-style-1-label">{{ __('primary color') }}</label>
						<input name="primary-color" type="color">
					</div>
					<div>
						<label class="input-style-1-label">{{ __('secondary color') }}</label>
						<input name="secondary-color" type="color">
					</div>

				</div>
				<div class="d-flex flex-direction-column">
					<button onclick="updateColor()" class="button button-block button-primary button-sm margin-top-2">{{ __('update') }}</button>
					<button onclick="updateColor()" class="button button-block button-primary-border button-sm margin-top-1">{{ __('reset colors') }}</button>
				</div>
			</div>
			<div style="display: flex;justify-content: center;" class="margin-top-2">
				<img data-is="thumbnail" style="width: 500px;" src="{{ url('/storage/temp/quick-invoice/classic.jpg') }}" alt="invoice">
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/document.js', 'quick-invoice') }}

<script>
	let pageTable = dataTable('page-table');
	let selectedTemplateSlug = null;

	document.addEventListener('DOMContentLoaded', function() {
		populateTemplates(staticDocumentTemplates());
	});

	/**
	 * Static data
	 */

	function staticIsViewOnly() {
		return '{{ $viewOnly ?? false }}';
	}

	function staticDocumentTemplates() {
		let documentTemplates = '{!! addSlashes(json_encode($templates)) !!}';
		return JSON.parse(documentTemplates);
	}

	function showTemplate(id) {

		let modalEl = document.querySelector('#page-modal');
		let modalTitleEl = modalEl.querySelector('.modal-title');
		let thumbnailEl = modalEl.querySelector('[data-is="thumbnail"]');
		let primaryColorEl = modalEl.querySelector('[name="primary-color"]');
		let secondaryColorEl = modalEl.querySelector('[name="secondary-color"]');

		let template = staticDocumentTemplates().find(doc => doc.id == id);
		selectedTemplateSlug = template.slug;

		let primaryColor = template.primary_color;
		let secondaryColor = template.secondary_color;

		modalTitleEl.innerHTML = template.name;
		thumbnailEl.setAttribute('src', `${BASE_URL}/storage/temp/quick-invoice/document-${template.slug}.jpg`);
		primaryColorEl.value = primaryColor;
		secondaryColorEl.value = secondaryColor;

		showModal('page-modal');
	}

	function populateTemplates(templates) {

		let tableData = templates.map((template, templateIndex) => {

			let primaryColor = template.primary_color;
			let secondaryColor = template.secondary_color;

			let buttons = [{
					icon: 'solid-tick',
					classes: ['button', 'button-icon', 'button-icon-primary'],
					attributes: ['data-popover="{{ __("apply") }}"'],
					event: {
						click: function() {
							applyTemplate(template.slug);
						}
					}
				},
				{
					icon: 'solid-eye',
					classes: ['button', 'button-icon', 'button-icon-primary'],
					attributes: ['data-popover="{{ __("view") }}"'],
					event: {
						click: function() {
							showTemplate(template.id)
						}
					}
				}
			];

			if (template.status === 'active' || staticIsViewOnly() == '1') {
				buttons[0].classes.push('hide');
			}


			return [{
					type: 'text',
					value: (templateIndex + 1)
				},
				{
					type: 'text',
					value: template.name
				},
				{
					type: 'html',
					value: `<div style="margin-left:20px;border-radius:4px;width:20px;height:20px;background-color:${primaryColor}"></div>`
				},
				{
					type: 'html',
					value: `<div style="margin-left:20px;border-radius:4px;width:20px;height:20px;background-color:${secondaryColor}"></div>`
				},
				{
					type: 'tag',
					value: template.status === 'active' ? '{{ __("active") }}' : '{{ __("inactive") }}',
					itemClasses: [template.status === 'active' ? 'tag-success' : 'tag-warning'],
					classes: [staticIsViewOnly() == 1 ? 'hide' : '']
				},
				{
					type: 'text',
					value: toLocalDateTime(template.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: buttons
				}
			];
		});

		pageTable.init(tableData);
		popover.init();

	}

	/**
	 * Save
	 */

	async function updateColor() {

		let t = staticDocumentTemplates().find(doc => doc.slug == selectedTemplateSlug);
		if (isEmpty(t)) return;

		let primaryColorEl = document.querySelector('[name="primary-color"]');
		let secondaryColorEl = document.querySelector('[name="secondary-color"]');

		let postData = {
			templateSlug: t.slug,
			primaryColor: primaryColorEl.value,
			secondaryColor: secondaryColorEl.value
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceDocument.saveUserDocumentTemplate(postData, {
			target: 'save-template-button'
		});
		showResponseNotification(n, response);

		hideModal('page-modal');
		window.location.reload();

	}

	async function applyTemplate(templateSlug) {

		let postData = {
			templateSlug
		};

		let n = showProcessingNotification();
		let response = await QuickInvoiceDocument.activateUserDocumentTemplate(postData);
		showResponseNotification(n, response);
		window.location.reload();
	}
</script>

@parent
@stop