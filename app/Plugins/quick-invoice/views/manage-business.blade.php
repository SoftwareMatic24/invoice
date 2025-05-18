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
			<a href="{{ url('/portal/quick-invoice/business/save') }}" class="button button-primary">{{ __('new business') }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("email") }}</th>
				<th>{{ ucwords(__("country")) }}</th>
				<th>{{ ucwords(__("date added")) }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

</div>
@stop

@section('page-script')

{!! loadPluginFile('js/business.js', 'quick-invoice') !!}

<script>

	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		populateBusinesses(staticBusinesses());
	}

	/**
	 * Static data
	 */

	function staticBusinesses(){
		let businesses = '{!! addSlashes(json_encode(QuickInvoiceBusiness::userBusinesses($userId))) !!}';
		return JSON.parse(businesses);
	}

	/**
	 * Fetch
	 */

	async function fap(){
		let businesses = await fetchBusinesses();
		populateBusinesses(businesses);
	}

	async function fetchBusinesses(){
		let response = await QuickInvoiceBusiness.userBusinesses();
		return response.data;
	}

	async function deleteBusiness(businessId){
		let n = showDeletingNotification();
		let response = await QuickInvoiceBusiness.deleteUserBusiness(businessId);
		showResponseNotification(n, response);
		fap();
	}

	function populateBusinesses(businesses){

		let countryMap = countryList().reduce((acc, country) => {
			acc[country.code] = country.name;
			return acc;
		}, {});

		let tableData = businesses.map((business, businessIndex) => {
			return [
				{
					type:'text',
					value:businessIndex + 1
				},
				{
					type:'text',
					value: business.name
				},
				{
					type:'text',
					value: business.email
				},
				{
					type:'text',
					value: countryMap[business.country]
				},
				{
					type:'text',
					value: toLocalDateTime(business.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/business/save/' + business.id,
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
												deleteBusiness(business.id);
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