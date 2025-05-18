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
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1 separator--">
				<label for="status-filter" class="input-style-1-label">{{ __("status") }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Visibility:Visible">{{ __('visible') }}</option>
						<option value="Visibility:Hidden">{{ __('hidden') }}</option>
					</select>
				</div>
			</div>

		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("title") }}</th>
				<th>{{ __("slug") }}</th>
				<th>{{ __("visibility") }}</th>
				<th>{{ __("presistence") }}</th>
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

{!! loadPluginFile('js/script.js', 'components') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateComponents(staticComponents());
	}

	/**
	 * Static data
	 */

	function staticComponents() {
		let components = '{!! addSlashes(json_encode(Component::components())) !!}';
		return JSON.parse(components);
	}

	/**
	 * Fetch
	 */

	async function fetchComponents() {
		let response = await Component.components();
		return response.data;
	}

	async function fap() {
		let components = await fetchComponents();
		populateComponents(components);
	}

	function populateComponents(components) {

		let tableData = Object.values(components).map((component, componentIndex) => {
			return [{
					type: 'text',
					value: (componentIndex + 1)
				},
				{
					type: 'text',
					value: component.title
				},
				{
					type: 'text',
					value: component.slug
				},
				{
					type: 'tag',
					value: capitalize(component.visibility),
					itemClasses: [component.visibility === 'visible' ? 'tag-success' : 'tag-danger']
				},
				{
					type: 'text',
					value: component.persistence == 'permanent' ? 'Default' : 'Custom'
				},
				{
					type: 'text',
					value: toLocalDateTime(component.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/components/save/' + component.slug
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteComponent(component.id)
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

	async function deleteComponent(componentId) {
		let n = showDeletingNotification();
		let response = await Component.deleteComponent(componentId);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}

	fetchComponents();
</script>
@parent
@stop