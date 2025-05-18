@extends('layouts.portal')

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')
@inject('util','App\Classes\Util')

<div class="data-table-container">

	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
			<svg class="icon cross-icon">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
			<a href="{{ $util->prefixedURL($pluginConfig['slug']) }}/save" class="button button-primary">New Tax Class</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>Tax Class</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

</div>


@stop

@section('page-script')

<script>
	var pageTable = dataTable('page-table');

	async function fetchTaxClasses(){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/tax/user/class/all'
		});
		return response.data;
	}

	async function deleteTaxClass(taxClassId){
		let n = Notification.show({
			text: 'Deleting, please wait...',
			time: 0
		})

		let response = await xhrRequest({
			method:'DELETE',
			url: BASE_URL + '/api/tax/user/class/delete/' + taxClassId
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		if(response.data.status === 'success') fap();
	}

	async function fap(){
		let taxClasses = await fetchTaxClasses();
		populateTaxClasses(taxClasses);
	}

	function populateTaxClasses(taxClasses){
		let tabelData = taxClasses.map((taxClass, taxClassIndex)=>{
			return [
				{
					type:'text',
					value: taxClassIndex + 1
				},
				{
					type:'text',
					value: taxClass.title
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/tax/save/' + taxClass.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteTaxClass(taxClass.id)
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

		pageTable.init(tabelData);
	}

	fap();
</script>
@parent
@stop