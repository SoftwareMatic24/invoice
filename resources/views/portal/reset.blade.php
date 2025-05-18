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
			<button 
				data-xhr-name="reset-all" 
				data-xhr-loading.attr="disabled"
				onclick="doResetAll()" 
				class="button button-primary with-plus-icon">
				{{ __('reset all') }}
			</button>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("status") }}</th>
				<th>{{ __("action") }}</th>
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
		populateResets(staticResets());
	}

	/**
	 * Static data
	 */

	function staticResets() {
		let resets = '{!! addSlashes(json_encode($resets)) !!}';
		return JSON.parse(resets);
	}

	/**
	 * Fetch
	 */

	async function fap(){
		let response = await Reset.activeResets();
		populateResets(response.data);
	}
	

	async function doReset(id){
	
		let postData = {
			id: id
		};

		let n = showProcessingNotification();
		let response = await Reset.doReset(postData);
		showResponseNotification(n, response);

		fap();
	}

	async function doResetAll(){
		let n = showProcessingNotification();
		let response = await Reset.doResetAll({target: 'reset-all'});
		showResponseNotification(n, response);
		fap();
	}

	/**
	 * Populate
	 */

	function populateResets(resets) {
		let pagesData = resets.map((reset, reseetIndex) => {

			return [{
					type: 'text',
					value: (reseetIndex + 1)
				},
				{
					type: 'text',
					value: reset.name
				},
				{
					type: 'tag',
					itemClasses: [reset.status === 'active' ? 'tag-success' : 'tag-warning'],
					value: capitalize(reset.status)
				},
				{
					type: 'button-group',
					value: [

						{
							text: '{{ __("reset") }}',
							classes: ['button', 'button-text', 'button-primary-border'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												doReset(reset.id);
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

		pageTable.init(pagesData);
	}
</script>

@stop