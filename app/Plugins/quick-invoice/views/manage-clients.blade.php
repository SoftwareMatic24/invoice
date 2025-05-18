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
			<a href="{{ url('/portal/quick-invoice/clients/save') }}" class="button button-primary">{{ __('new client') }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("email") }}</th>
				<th>{{ __("country") }}</th>
				<th>{{__("date created") }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/client.js', 'quick-invoice') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateClietns(staticClients());
	}

	/**
	 * Static data
	 */

	function staticClients() {
		let clients = '{!! addSlashes(json_encode(QuickInvoiceClient::userClients($userId))) !!}';
		return JSON.parse(clients);
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let clients = await fetchClients();
		populateClietns(clients);
	}

	async function fetchClients() {
		let response = await QuickInvoiceClient.userClients();
		return response.data;
	}

	/**
	 * Delete
	 */

	async function deleteClient(clientId) {
		let n = showDeletingNotification();
		let response = await QuickInvoiceClient.deleteUserClient(clientId);
		showResponseNotification(n, response);
		fap();
	}

	/**
	 * Populate
	 */

	function populateClietns(clients) {

		let countryMap = countryList().reduce((acc, country) => {
			acc[country.code] = country.name;
			return acc;
		}, {});

		let tableData = clients.map((client, clientIndex) => {
			return [{
					type: 'text',
					value: clientIndex + 1
				},
				{
					type: 'text',
					value: client.name
				},
				{
					type: 'text',
					value: client.email
				},
				{
					type: 'text',
					value: countryMap[client.country]
				},
				{
					type: 'text',
					value: toLocalDateTime(client.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/clients/save/' + client.id,
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
												deleteClient(client.id);
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