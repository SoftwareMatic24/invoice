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
			<a href="{{ url('/portal/quick-invoice/custom-fields/save') }}" class="button button-primary">{{ __('new custom field') }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('name') }}</th>
				<th>{{ __('document') }}</th>
				<th>{{ __('position') }}</th>
				<th>{{ __('business') }}</th>
				<th>{{ __('date created') }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/document.js', 'quick-invoice') }}

<script>
	let pageTable = dataTable('page-table');
	
	document.addEventListener('DOMContentLoaded', init);

	function init(){
		populateFields(staticCustomFields());
	}

	/**
	 * Static data
	 */

	function staticCustomFields() {
		let fields = '{!! addSlashes(json_encode($fields)) !!}';
		return JSON.parse(fields);
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let fields = await fetchFields();
		populateFields(fields);
	}

	async function fetchFields() {
		let response = await QuickInvoiceDocument.userDocumentCustomFields();
		return response.data;
	}

	/**
	 * Delete
	 */

	async function deleteField(fieldId) {
		let n = showDeletingNotification();
		let response = await QuickInvoiceDocument.deleteUserDocumentCustomField(fieldId);
		showResponseNotification(n, response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateFields(fields) {
		let tableData = fields.map((field, fieldIndex) => {
			return [{
					type: 'text',
					value: fieldIndex + 1
				},
				{
					type: 'text',
					value: field.name
				},
				{
					type: 'text',
					value: slugToText(field.document_type)
				},
				{
					type: 'text',
					value: capitalize(field.position),
				},
				{
					type: 'text',
					value: field.business.name
				},
				{
					type: 'text',
					value: toLocalDateTime(field.create_datetime)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/custom-fields/save/' + field.id,
							attributes: ['data-popover="{{ __("edit") }}"'],
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							attributes: ['data-popover="{{ __("delete") }}"'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteField(field.id);
											}
										}
									});

								}
							}
						}
					]
				}
			];
		});

		pageTable.init(tableData);
		popover.init();
	}
</script>
@parent
@stop