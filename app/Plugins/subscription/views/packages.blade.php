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
				<label for="status-filter" class="input-style-1-label">{{ __('status') }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Status:active">{{ __('active') }}</option>
						<option value="Status:inactive">{{ __('inactive') }}</option>
					</select>
				</div>
			</div>
			<a href="{{ url('/portal/subscription/save') }}" class="button button-primary">{{ __('new package') }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('title') }}</th>
				<th>{{ __('price') }}</th>
				<th>{{ __('status') }}</th>
				<th>{{ __('date') }}</th>
				<th>{{ __('action') }}</th>
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
		populatePackages(staticPackages());
	}

	/**
	 * Static data
	 */

	function staticPackages() {
		let packages = '{!! addSlashes(json_encode(Subscription::packages())) !!}';
		return JSON.parse(packages);
	}

	/**
	 * Fetch
	 */

	async function fetchPackages() {
		let response = await Subscription.packages();
		return response.data;
	}

	async function fap(){
		let packages = await fetchPackages();
		populatePackages(packages);
	}

	/**
	 * Delete
	 */

	async function deletePackage(packageId) {
		let n = showDeletingNotification();
		let response = await Subscription.deletePackage(packageId);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populatePackages(packages) {

		let tableData = packages.map((package, packageIndex) => {

			let status = {
				text: 'active',
				class: 'tag-success'
			};


			return [{
					type: 'text',
					value: (packageIndex + 1)
				},
				{
					type: 'text',
					value: package.title
				},
				{
					type: 'text',
					value: package.price
				},
				{
					type: 'tag',
					value: package.status,
					itemClasses: [package.status === 'active' ? 'tag-success' : 'tag-warning']
				},
				{
					type: 'text',
					value: toLocalDateTime(package.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/subscription/save/' + package.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deletePackage(package.id);
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


</script>
@parent
@stop