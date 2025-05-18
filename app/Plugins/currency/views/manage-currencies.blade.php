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
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __('type') }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Type:primary">{{ __('primary') }}</option>
						<option value="Type:secondary">{{ __('secondary') }}</option>
					</select>
				</div>
			</div>
			<a href="{{ url('/portal/currency/save') }}" class="button button-primary">{{ __('new currency') }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('currency') }}</th>
				<th>{{ __('symbol') }}</th>
				<th>{{ __('exchange rate') }}</th>
				<th>{{ __('Type') }}</th>
				<th>{{ __('action') }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'currency') }}

<script>
	
	var pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		if(!isEmpty(staticCurrencies())) populateCurrencies(staticCurrencies());
	}

	/**
	 * Static data
	 */

	function staticCurrencies(){
		let currencies = '{!! addSlashes(json_encode(Currency::currencies())) !!}';
		return JSON.parse(currencies);
	}

	/**
	 * Fetch
	 */

	async function fetchCurrencies() {
		let response = await Currency.currencies();
		return response.data;
	}

	async function fap(){
		let currency = await fetchCurrencies();
		populateCurrencies(currency);
	}

	/**
	 * Delete
	 */

	async function deleteCurrency(id) {
		let n = showDeletingNotification();
		let response = await Currency.deleteCurrency(id);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}


	/**
	 * Populate
	 */

	function populateCurrencies(currencies) {

		let currencyData = currencies.map((currency, currencyIndex) => {

			let rate = currency.rate;

			if (currencyIndex !== 0) rate = `${currencies[0].symbol}1  = ${currency.symbol}${currency.rate}`

			return [{
					type: 'text',
					value: (currencyIndex + 1)
				},
				{
					type: 'text',
					value: currency.currency
				},
				{
					type: 'text',
					value: currency.symbol
				},
				{
					type: 'text',
					value: rate
				},
				{
					type: 'tag',
					itemClasses: [currency.type === 'primary' ? 'tag-success' : 'tag-warning'],
					value: currency.type
				},

				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/currency/save/' + currency.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteCurrency(currency.id);
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

		pageTable.init(currencyData);
	}

</script>
@parent
@stop