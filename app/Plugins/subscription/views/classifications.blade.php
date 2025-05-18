@extends('layouts.portal')

@php
$classifications = Subscription::classifications();
@endphp

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
			<a href="{{ url('/portal/subscription/classifications/save') }}" class="button button-primary with-plus-icon">
				{{ __('new classification') }}
			</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __('name') }}</th>
				<th>{{ __('slug') }}</th>
				<th>{{ __('date') }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'subscription') }}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateClassifications(staticClassifications());
	}


	/**
	 * Static data
	 */

	function staticClassifications() {
		let classifications = '{!! addSlashes(json_encode($classifications)) !!}';
		return JSON.parse(classifications);
	}

	/**
	 * Fetch
	 */

	async function fetchClassifications() {
		let response = await Subscription.classifications();
		return response.data;
	}

	async function fap() {
		let classifications = await fetchClassifications();
		populateClassifications(classifications);
	}

	/**
	 * Delete
	 */

	async function deleteClassification(slug) {

		let n = showDeletingNotification();
		let response = await Subscription.deleteClassificationBySlug(slug);
		showResponseNotification(n, response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateClassifications(classifications) {
		let userData = classifications.map((classification, index) => {
			return [{
					type: 'text',
					value: (index + 1)
				},
				{
					type: 'text',
					value: classification.name
				},
				{
					type: 'text',
					value: classification.slug
				},
				{
					type: 'text',
					value: toLocalDateTime(classification.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/subscription/classifications/save/' + classification.slug
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteClassification(classification.slug);
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
		pageTable.init(userData);
	}
</script>
@parent
@stop