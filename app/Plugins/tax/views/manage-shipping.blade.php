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
			<a href="{{ $util->prefixedURL($pluginConfig['slug']) }}/shipping/save" class="button button-primary">New Shipping Class</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>Shipping Class</th>
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

	async function fetchShippingClasses(){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/tax/user/shipping-class/all'
		});
		return response.data;
	}

	async function deleteShippingClass(shippingClassId){
		let n = Notification.show({
			text: 'Deleting, please wait...',
			time: 0
		})

		let response = await xhrRequest({
			method:'DELETE',
			url: BASE_URL + '/api/tax/user/shipping-class/delete/' + shippingClassId
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		if(response.data.status === 'success') fap();
	}

	async function fap(){
		let taxClasses = await fetchShippingClasses();
		populateShippingClasses(taxClasses);
	}

	function populateShippingClasses(shippingClasses){
		let tabelData = shippingClasses.map((shippingClass, shippingClassIndex)=>{
			return [
				{
					type:'text',
					value: shippingClassIndex + 1
				},
				{
					type:'text',
					value: shippingClass.title
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/tax/shipping/save/' + shippingClass.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteShippingClass(shippingClass.id)
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