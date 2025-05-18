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
			<a href="{{ url('/portal/quick-invoice/products')}}/{{ $productType }}/save" class="button button-primary">{{ __('new') }} {{ __($productType) }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('title') }}</th>
				<th>{{ __('code') }}</th>
				<th>{{ __('unit price') }}</th>
				<th>{{ __('unit') }}</th>
				<th>{{ __('date created') }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/product.js', 'quick-invoice') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		populateProducts(staticProducts());
	}

	/**
	 * Static data
	 */

	function staticProducts(){
		let products = '{!! addSlashes(json_encode(QuickInvoiceProduct::userProducts($userId))) !!}';
		return JSON.parse(products);
	}

	function staticProductType(){
		return '{!! $productType !!}'
	}

	/**
	 * Fetch
	 */

	async function fap(){
		let products = await fetchProducts();
		populateProducts(products);
	}

	async function fetchProducts(){
		let response = await QuickInvoiceProduct.userProducts();
		return response.data;
	}

	async function deleteProduct(productId){
		let n = showDeletingNotification();
		let response = await QuickInvoiceProduct.deleteUserProduct(productId);
		showResponseNotification(n, response);
		fap();
	}

	function populateProducts(products){
		
		products = products.filter(product => product.type === staticProductType());

		let tableData = products.map((product, productIndex) => {
			return [
				{
					type:'text',
					value:productIndex + 1
				},
				{
					type:'text',
					value: product.title
				},
				{
					type:'text',
					value: product.code
				},
				{
					type:'text',
					value: product.price
				},
				{
					type:'text',
					value: product.unit
				},
				{
					type:'text',
					value: toLocalDateTime(product.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/products/' + product.type + '/save/' + product.id,
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
												deleteProduct(product.id);
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