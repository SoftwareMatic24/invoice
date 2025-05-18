@extends('layouts.portal')
@section('main-content')
<div class="data-table-container">
	<div class="data-table-toolbar">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __("status") }}:</label>
				<div class="select-container chevron">
					<div class="custom-select-container">
						<select id="status-filter" class="filter-by-search input-style-1" style="min-width: 10rem;">
							<option value="all">{{ __('all') }}</option>
							<option value="Status:complete">{{ __('complete') }}</option>
							<option value="Status:pending">{{ __('pending') }}</option>
							<option value="Status:cancel">{{ __('cancel') }}</option>
							<option value="Status:return">{{ __('return') }}</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("email") }}</th>
				<th>{{ __("product") }}</th>
				<th>{{ __("quantity") }}</th>
				<th>{{ __("amount") }}</th>
				<th>{{ __("status") }}</th>
				<th>{{ __("method") }}</th>
				<th>{{ ucwords(__("date created")) }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateTransactions(staticTransactions());
	}

	/**
	 * Static data
	 */

	function staticTransactions() {
		let trans = '{!! addSlashes(json_encode(Transaction::transactions())) !!}';
		return JSON.parse(trans);
	}

	/**
	 * Populate
	 */

	function populateTransactions(transactions) {
		let pagesData = transactions.map((transaction, transactionIndex) => {

			return [{
					type: 'text',
					value: (transactionIndex + 1)
				},
				{
					type: 'excerpt',
					value: transaction.customer_name,
				},
				{
					type: 'text',
					value: transaction.customer_email,
				},
				{
					type: 'excerpt',
					value: transaction.product_name,
				},
				{
					type: 'text',
					value: transaction.product_quantity,
				},
				{
					type: 'text',
					value: `${transaction.currency.toUpperCase()} ${transaction.product_amount}`,
				},
				{
					type: 'tag',
					itemClasses: [transaction.status === 'complete' ? 'tag-success' : 'tag-danger'],
					value: capitalize(transaction.status)
				},
				{
					type: 'text',
					value: transaction.payment_method,
				},
				{
					type: 'text',
					value: toLocalDateTime(transaction.create_datetime)
				},
			];
		});

		pageTable.init(pagesData);
	}

</script>

@stop