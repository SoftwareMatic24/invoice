@extends('layouts.portal')

@section('page-style')
<link rel="stylesheet" href="{{ asset('css/tagify.css') }}">
<link rel="stylesheet" href="{{ asset('css/tagify-child.css') }}">
@parent
@stop

@section('main-content')


<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" onsubmit="return false;">
			<div class="form-group">
				<label class="input-style-1-label">Tax Class Name</label>
				<input name="name" type="text" class="input-style-1">
			</div>
		</form>

		<label class="input-style-1-label | margin-top-3">Tax Class Rates</label>
		<div class="sheet-table-wrapper">
			<div class="sheet-table-container">
				<table id="rates" class="sheet">
					<thead>
						<tr>
							<th>Country</th>
							<th>State</th>
							<th>Postcode</th>
							<th>City</th>
							<th>Rate %</th>
							<th>Tax Name</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<button onclick="addNewTaxRate()" class="button button-xs | margin-top-2">Add New Row</button>
		</div>

	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button onclick="saveTaxClass()" class="button button-sm button-primary button-block">Save Tax Class</button>
			</div>
		</div>
	</div>
</div>

@stop

@section('page-script')

<script>

	let taxClassId = '{{ $taxClassId ?? "" }}';

	async function fetchTaxClass(taxClassId){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/tax/user/class/one/' + taxClassId
		});

		return response.data;
	}

	async function saveTaxClass(){

		let nameEl = document.querySelector('input[name="name"]');
		let data = getTaxRates();

		let apis = {
			add: {
				method: 'POST',
				url: BASE_URL + '/api/tax/user/class/save'
			},
			update: {
				method: 'PUT',
				url: BASE_URL + '/api/tax/user/class/save/' + taxClassId
			}
		};

		let api = taxClassId === '' ? apis.add : apis.update;
 
		let postData = {
			taxClassName: nameEl.value,
			rates: data
		};

		let n = Notification.show({
			text: 'Saving, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: api.method,
			url: api.url,
			body: postData
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		if(response.data.status === 'success') taxClassId = response.data.taxClassId;
	}

	async function fap(){
		let taxClass = await fetchTaxClass(taxClassId);
		populateTaxClass(taxClass);
	}

	function populateTaxClass(taxClass){

		let nameEl = document.querySelector('input[name="name"]');
		nameEl.value = taxClass.title;
		
		taxClass.rates.forEach(rate => {
			addNewTaxRate(rate);
		});

	}

	function addNewTaxRate(rate = {}) {

		let countriesOptionView = generateCountriesOptions(rate.country !== undefined ? rate.country : null);

		let htmlContent = `
			<tr>
				<td>
					<select name="country" style="width: 15rem;">
						<option value="">*</option>
						${countriesOptionView}
					</select>
				</td>
				<td><input name="state" type="text" placeholder="*" value="${(rate.state !== undefined && rate.state !== null) ? rate.state : ''}"></td>
				<td><input name="postcode" type="text" placeholder="*" value="${(rate.postcode !== undefined && rate.postcode !== null) ? rate.postcode : ''}"></td>
				<td><input name="city" type="text" placeholder="*" value="${(rate.city !== undefined && rate.city !== null) ? rate.city : ''}"></td>
				<td><input name="rate" type="text" placeholder="0.00" value="${(rate.rate !== undefined && rate.rate !== null) ? rate.rate : ''}"></td>
				<td><input name="tax" type="text" value="${(rate.tax_name !== undefined && rate.tax_name !== null) ? rate.tax_name : ''}"></td>
				<td class="d-flex justify-content-center align-items-center">
					<span onclick="removeSheetTableRow()" class="button button-icon button-icon-danger ">
						<svg>
							<use xlink:href="${BASE_URL}/assets/icons.svg#solid-trash" />
						</svg>
					</span>
				</td>
			</tr>
		`;

		addSheetTableRow('rates', htmlContent);
	}

	function getTaxRates(){

		let tableEl = document.querySelector('table.sheet');
		let tableBodyEl = tableEl.querySelector('tbody');
		let trEls = tableBodyEl.querySelectorAll('tr');

		let data = Array.from(trEls).map(tr => {
			let countryEl = tr.querySelector('[name="country"]');
			let stateEl = tr.querySelector('[name="state"]');
			let postcodeEl = tr.querySelector('[name="postcode"]');
			let cityEl = tr.querySelector('[name="city"]');
			let rateEl = tr.querySelector('[name="rate"]');
			let taxEl = tr.querySelector('[name="tax"]');

			return {
				country: countryEl.value,
				state: stateEl.value,
				postcode: postcodeEl.value,
				city: cityEl.value,
				rate: rateEl.value,
				tax: taxEl.value,
			};
		});

		return data;
	}

	if(taxClassId !== '') fap();
	else addNewTaxRate();
</script>

@stop