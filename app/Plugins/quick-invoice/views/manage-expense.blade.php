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
			<a href="{{ url('/portal/quick-invoice/expense/save') }}" class="button button-primary">{{ __('new expense') }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('title') }}</th>
				<th>{{ __('category') }}</th>
				<th>{{ __('reference number') }}</th>
				<th>{{ __('price/cost') }}</th>
				<th>{{ __('expense date') }}</th>
				<th>{{ __('date created') }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="page-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body"></div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/expense.js', 'quick-invoice') !!}

<script>
	let pageTable = dataTable('page-table');
	let expenses = staticExpenses();

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateExpenses(staticExpenses());
	}

	/**
	 * Static data
	 */

	 function staticExpenses(){
		let expenses = '{!! addSlashes(json_encode(QuickInvoiceExpense::userExpenses($userId))) !!}';
		return JSON.parse(expenses);
	 }

	/**
	 * Fetch
	 */

	async function fap() {
		let expenses = await fetchExpenses();
		populateExpenses(expenses);
	}

	async function fetchExpenses() {
		let response = await QuickInvoiceExpense.userExpenses();
		return expenses = response.data;
	}

	async function deleteExpense(expenseId) {
		let n = showDeletingNotification();
		let response = await QuickInvoiceExpense.deleteUserExpense(expenseId);
		showResponseNotification(n, response);
		fap();
	}

	function populateExpenses(expenses) {

		let countryMap = countryList().reduce((acc, country) => {
			acc[country.code] = country.name;
			return acc;
		}, {});

		let tableData = expenses.map((expense, expenseIndex) => {
			return [{
					type: 'text',
					value: expenseIndex + 1
				},
				{
					type: 'excerpt',
					value: expense.title
				},
				{
					type: 'excerpt',
					value: expense.category === null ? '' : expense.category.name
				},
				{
					type: 'text',
					value: expense.reference_number
				},
				{
					type: 'text',
					value: expense.price + ' ' + (expense.currency === null ? '' : expense.currency)
				},
				{
					type: 'text',
					value: toLocalDateTime(expense.expense_date, true)
				},
				{
					type: 'text',
					value: toLocalDateTime(expense.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							attributes: ['data-popover="View"'],
							event: {
								click: function() {
									showExpense(expense.id);
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/expense/save/' + expense.id,
							attributes: ['data-popover="Edit"'],
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							attributes: ['data-popover="Delete"'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteExpense(expense.id);
											}
										}
									});

								}
							}
						}
					]
				}
			];
			console.log(expense);
		});

		pageTable.init(tableData);
		popover.init();
	}

	function showExpense(expenseId) {
		let modalEl = document.querySelector('#page-modal');
		let modalTitleEl = modalEl.querySelector('.modal-title');
		let modalBodyEl = modalEl.querySelector('.modal-body');

		let expense = expenses.find(ex => ex.id == expenseId);
		if (expense === undefined) return;


		let contentHTML = `
			<div class="modal-text-group">
				<p><b>{{ __('title') }}:</b> ${expense.title}</p>
				<p><b>{{ __('category') }}:</b> ${expense.category === null ? '' : expense.category.name}</p>
				<p><b>{{ __('reference number') }}:</b> ${expense.reference_number === null ? '' : expense.reference_number}</p>
				<p><b>{{ __('price/cost') }}:</b> ${(expense.price === null ? '' : expense.price) + ' ' + (expense.currency === null ? '' : expense.currency)}</p>
				<p><b>{{ __('expense date') }}:</b> ${(expense.expense_date === null ? '' : toLocalDateTime(expense.expense_date, true))}</p>
				<p><b>{{ __('tax') }}:</b> ${ toStr(expense, 'tax') + ' ' + ((toStr(expense, 'tax_type') === 'percentage') ? '%' : toStr(expense, 'currency')) }</p>
				<p><b>{{ __('business') }}:</b> ${toStr(expense, 'business', 'name')}</p>
				<p><b>{{ __('client') }}:</b> ${toStr(expense, 'client', 'name')}</p>
			</div>
		`;

		modalTitleEl.innerHTML = expense.title;
		modalBodyEl.innerHTML = contentHTML;
		showModal('page-modal');
	}
</script>

@parent
@stop