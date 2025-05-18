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
			<a href="{{ url('/portal/quick-invoice/expense/categories/save') }}" class="button button-primary">{{ __('new category') }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("date created") }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/expense.js', 'quick-invoice') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);
	
	function init(){
		ppopulateCategories(staticCategories());
	}

	/**
	 * Static data
	 */

	function staticCategories(){
		let categories = '{!! addSlashes(json_encode(QuickInvoiceExpense::userCategories($userId))) !!}';
		return JSON.parse(categories);
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let categories = await fetchCategories();
		ppopulateCategories(categories);
	}

	async function fetchCategories() {
		let response = await QuickInvoiceExpense.userCategories();
		return response.data;
	}

	async function deleteCategory(categoryId) {
		let n = showDeletingNotification();
		let response = await QuickInvoiceExpense.deleteUserCategory(categoryId);
		showResponseNotification(n, response);
		fap();
	}

	function ppopulateCategories(categories) {

		let tableData = categories.map((category, categoryIndex) => {
			return [{
					type: 'text',
					value: categoryIndex + 1
				},
				{
					type: 'text',
					value: category.name
				},
				{
					type: 'text',
					value: toLocalDateTime(category.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/expense/categories/save/' + category.id,
							attributes: ['data-popover="{{ __("edit") }}"'],
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							attributes: ['data-popover="{{ __("Delete") }}"'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteCategory(category.id);
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