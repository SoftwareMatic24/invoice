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
			<a href="{{ url('/portal/payment-method/methods') }}/{{ $type }}/{{ $paymentMethodSlug }}/save" class="button button-primary with-plus-icon">
				{{ __('new entry') }}
			</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("email") }}</th>
				<th>{{ __('identifier') }}</th>
				<th>{{ __("status") }}</th>
				<th>{{ __("date created") }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="page-modal" class="modal" style="width: min(60rem, 90%)">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'payment-method') }}

<script>
	let pageTable = dataTable('page-table');
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateEntries(staticPaymentMethodEntries());
	}

	/**
	 * Static data
	 */

	function staticType() {
		return '{{ $type }}';
	}

	function staticPaymentMethodSlug() {
		return '{{ $paymentMethodSlug ?? "" }}';
	}

	function staticPaymentMethodEntries() {
		let paymentMethodEntries = '{!! addSlashes(json_encode($paymentMethodEntries)) !!}';
		return JSON.parse(paymentMethodEntries);
	}

	/**
	 * Fetch
	 */

	async function fetchEntries(type, paymentMethodSlug) {
		let response = await PaymentMethod.entries(type, paymentMethodSlug);
		return response.data;
	}

	async function fap(type, paymentMethodSlug) {
		let entries = await fetchEntries(type, paymentMethodSlug);
		populateEntries(entries);
	}

	/**
	 * Delete
	 */

	async function deleteEntry(entryId) {
		let n = showDeletingNotification();
		let response = await PaymentMethod.deleteEntry(staticType(), entryId);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap(staticType(), staticPaymentMethodSlug());
	}

	/**
	 * Populate
	 */

	function populateEntries(entries) {
		let tableData = entries.map((entry, entryIndex) => {
			return [{
					type: 'text',
					value: entryIndex + 1
				},
				{
					type: 'text',
					value: entry.name
				},
				{
					type: 'text',
					value: entry.email
				},
				{
					type: 'text',
					value: entry.payment_method_identifier
				},
				{
					type: 'tag',
					value: entry.status,
					itemClasses: [entry.status === 'active' ? 'tag-success' : 'tag-warning']
				},
				{
					type: 'text',
					value: toLocalDateTime(entry.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							event: {
								'click': function() {
									showEntry(entry.id);
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/payment-method/methods/' + staticType() + '/' + staticPaymentMethodSlug() + '/save/' + entry.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteEntry(entry.id);
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
	}

	/**
	 * Other
	 */

	function showEntry(entryId) {
		let entry = staticPaymentMethodEntries().find(entry => entry.id == entryId);
		if (entry === undefined) return;

		let modalEl = document.querySelector('#page-modal');
		let modalTitleEl = modalEl.querySelector('.modal-title');
		let modalBodyEl = modalEl.querySelector('.modal-body');

		modalTitleEl.innerHTML = entry.name == null ? '{{ __("payment method") }}' : entry.name;

		let otherHTML = ``;
		if (entry.other !== null) {
			entry.other = JSON.parse(entry.other);
			for (let key in entry.other) {
				let value = entry.other[key];
				otherHTML += `<p><b>${slugToText(key)}:</b> ${value}</p>`;
			}
		}

		modalBodyEl.innerHTML = `
			<div class="modal-text-group">
				<p class="${entry.public_key == null ? 'hide' : ''}"><b>{{ __('public key') }}:</b> ${entry.public_key}</p>
				<p class="${entry.public_key == null ? 'hide' : ''}"><b>{{ __('private key') }}:</b> ${entry.private_key}</p>
				<p><b>{{ __('status') }}:</b> ${entry.status}</p>
				${otherHTML}
			</div>
		`;

		showModal('page-modal');
	}
</script>
@parent
@stop