@extends('layouts.portal')
@inject("constants", "App\Classes\Constants")

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
				<label class="input-style-1-label">Shipping Class Name</label>
				<input name="name" type="text" class="input-style-1">
			</div>
		</form>

		<label class="input-style-1-label | margin-top-3">Shipping Zones</label>
		<div class="sheet-table-wrapper">
			<div class="sheet-table-container">
				<table id="zones" class="sheet">
					<thead>
						<tr>
							<th>Country</th>
							<th>State</th>
							<th>Postcode</th>
							<th>City</th>
							<th>Conditions</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
			<button onclick="addNewShippingZone()" class="button button-xs | margin-top-2">Add New Row</button>
		</div>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button onclick="saveShippingClass()" class="button button-sm button-primary button-block">Save Shipping Class</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal -->
<div id="conditions-modal" class="modal" style="width: min(80rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">Pricing Conditions</p>
		<span onclick="hideModal('conditions-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="max-height: 70vh;">
		<div class="modal-text-group | margin-bottom-2">
			<p>Tip: Keep "To Price" empty when upper limit is endless.</p>
		</div>
		<div class="margin-bottom-2">
			<select name="" id="" class="input-style-1">
				<option value="">Price</option>
				<option value="">Weight</option>
			</select>
		</div>
		<div data-is="placeholder"></div>
	</div>
	<div class="modal-footer | d-flex justify-content-end">
		<button data-is="save-button" class="button button-primary button-sm">Save Conditions</button>
	</div>
</div>
@stop

@section('page-script')

<script>
	let shippingClassId = '{{ $shippingClassId ?? "" }}';
	let shippingConditions = {};
	let zoneIndexCounter = 0;

	async function fetchShippingClass(shippingClassId) {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/tax/user/shipping-class/one/' + shippingClassId
		});

		return response.data;
	}

	async function saveShippingClass() {

		let nameEl = document.querySelector('input[name="name"]');
		let data = getShippingData();

		let apis = {
			add: {
				method: 'POST',
				url: BASE_URL + '/api/tax/user/shipping-class/save'
			},
			update: {
				method: 'PUT',
				url: BASE_URL + '/api/tax/user/shipping-class/save/' + shippingClassId
			}
		};

		let api = shippingClassId === '' ? apis.add : apis.update;

		let postData = {
			shippingClassName: nameEl.value,
			zones: data
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

		if (response.data.status === 'success') shippingClassId = response.data.shippingClassId;
	}

	function populateShippingClass(shippingClass) {

		let nameEl = document.querySelector('input[name="name"]');
		nameEl.value = shippingClass.title;

		shippingClass.shipping_zones.forEach(zone => {
			addNewShippingZone(zone);
		});
		
		populateConditionsCount();
	}

	function addNewShippingZone(zone = {}) {
		
		let countriesOptionView = generateCountriesOptions(zone.country !== undefined ? zone.country : null);
		let shippingData = getShippingData();
		shippingConditions[zoneIndexCounter] = [];
		
	
		if(zone.shipping_zone_conditions !== undefined) {
			zone.shipping_zone_conditions.forEach(condition => {
				shippingConditions[zoneIndexCounter].push({
					from:condition.from,
					to:condition.to === null ? '' : condition.to,
					cost:condition.cost
				});
			});
		}
		

		let htmlContent = `
			<tr data-index="${zoneIndexCounter}">
				<td>
					<select onchange="handleCountryChange()" name="country" style="width: 15rem;">
						<option value="">*</option>
						${countriesOptionView}
					</select>
				</td>
				<td><input name="state" type="text" placeholder="*" value="${(zone.state !== undefined && zone.state !== null) ? zone.state : ''}"></td>
				<td><input name="postcode" type="text" placeholder="*" value="${(zone.postcode !== undefined && zone.postcode !== null) ? zone.postcode : ''}"></td>
				<td><input name="city" type="text" placeholder="*" value="${(zone.city !== undefined && zone.city !== null) ? zone.city : ''}"></td>
				<td><input name="conditions" type="text" value="not added" style="opacity:0.7; font-style:italic;font-size:1.4rem" readonly></td>
				
				<td class="d-flex justify-content-center align-items-center">
					<div class="button-group">
						<button onclick="showConditionsModal(${zoneIndexCounter})" class="button button-icon button-icon-primary ">
							<svg>
								<use xlink:href="${BASE_URL}/assets/icons.svg#solid-dollar" />
							</svg>
						</button>
						<button onclick="removeShippingZone(${zoneIndexCounter})" class="button button-icon button-icon-danger ">
							<svg>
								<use xlink:href="${BASE_URL}/assets/icons.svg#solid-trash" />
							</svg>
						</button>
					</div>
				</td>
			</tr>
		`;

		zoneIndexCounter++;
		addSheetTableRow('zones', htmlContent);
	}

	function removeShippingZone(rowIndex) {
		delete shippingConditions[rowIndex];
		removeSheetTableRow();
	}

	function getShippingData() {
		let tableEl = document.querySelector('table.sheet');
		let tableBodyEl = tableEl.querySelector('tbody');
		let trEls = tableBodyEl.querySelectorAll('tr');

		let zones = Array.from(trEls).map(tr => {

			let rowIndex = tr.dataset.index;

			let countryEl = tr.querySelector('[name="country"]');
			let stateEl = tr.querySelector('[name="state"]');
			let postcodeEl = tr.querySelector('[name="postcode"]');
			let cityEl = tr.querySelector('[name="city"]');

			return {
				country: countryEl.value,
				state: stateEl.value,
				postcode: postcodeEl.value,
				city: cityEl.value,
				conditions: shippingConditions[rowIndex]
				
			};
		});

		return zones;
	}

	async function fap() {
		let shippingClass = await fetchShippingClass(shippingClassId);
		populateShippingClass(shippingClass);
	}


	// Conditions

	function validateConditions(conditions) {
		let errors = [];

		function isNumber(value) {
			let numberRegex = /^[0-9]+([.][0-9]+)?$/;
			return numberRegex.test(value);
		}

		conditions.forEach(condition => {
			if (!isNumber(condition.from)) errors.push('From price must be a number');
			else if (condition.to !== '' && !isNumber(condition.to)) errors.push('To price can only be a number or empty');
			else if (!isNumber(condition.cost)) errors.push('Cost must be a number');
			else if (isNumber(condition.from) && isNumber(condition.to) && parseFloat(condition.from) >= parseFloat(condition.to)) errors.push('From price must be greater than to price');
		});

		return errors.length > 0 ? errors[0] : true;
	}

	function getConditionsData() {
		let modalEl = document.querySelector('#conditions-modal');
		let tableEl = modalEl.querySelector('table.sheet');
		let tableBodyEl = tableEl.querySelector('tbody');
		let trEls = tableBodyEl.querySelectorAll('tr');

		let data = Array.from(trEls).map(tr => {

			let fromEl = tr.querySelector('[name="from"]');
			let toEl = tr.querySelector('[name="to"]');
			let costEl = tr.querySelector('[name="cost"]');

			return {
				from: fromEl.value,
				to: toEl.value,
				cost: costEl.value
			};
		});

		return data;
	}

	function addNewConditionRow(condition = {}) {

		let view = `
				<tr>
					<td><input name="from" type="text" placeholder="0.00" value="${condition.from !== undefined ? condition.from : ''}"></td>
					<td><input name="to" type="text" placeholder="0.00" value="${condition.to !== undefined ? condition.to : ''}"></td>
					<td><input name="cost" type="text" placeholder="0.00" value="${condition.cost !== undefined ? condition.cost : ''}"></td>
					
					<td class="d-flex justify-content-center align-items-center">
						<div class="button-group">
							<button onclick="removeSheetTableRow()" class="button button-icon button-icon-danger ">
								<svg>
									<use xlink:href="${BASE_URL}/assets/icons.svg#solid-trash" />
								</svg>
							</button>
						</div>
					</td>
				</tr>
			`;

		addSheetTableRow('conditions', view);
	}

	function saveConditions(rowIndex) {

		let conditions = getConditionsData();
		let isValid = validateConditions(conditions);

		if (isValid !== true) {

			Notification.show({
				text: isValid,
				classes: ['fail']
			});
			return;
		}

		shippingConditions[rowIndex] = conditions;
		populateConditionsCount();
		hideModal('conditions-modal');
	}

	function populateConditionsCount(){
		let zonesTableEl = document.querySelector('#zones');
		let zonesTableBodyEl = document.querySelector('tbody');
		let trEls = zonesTableBodyEl.querySelectorAll('tr');

		for(let rowIndex in shippingConditions){
			let conditions = shippingConditions[rowIndex];
			let trEl = trEls[rowIndex];
			let conditionsEl = trEl.querySelector('input[name="conditions"]');
			
			if(conditions.length === 0) conditionsEl.value = 'not added';
			else if(conditions.length === 1) conditionsEl.value = '1 condition';
			else conditionsEl.value = conditions.length + ' conditions';
		}
	}

	function showConditionsModal(rowIndex) {
		let conditions = shippingConditions[rowIndex];

		let modalEl = document.querySelector('#conditions-modal');
		let modalBodyEl = modalEl.querySelector('.modal-body');
		let placeholderEl = modalBodyEl.querySelector('[data-is="placeholder"]');
		let saveConditionButtonEl = modalEl.querySelector('[data-is="save-button"]')

		let view = `
			<div class="sheet-table-wrapper">
				<div class="sheet-table-container">
					<table id="conditions" class="sheet">
						<thead>
							<tr>
								<th>From</th>
								<th>To</th>
								<th>Shipping Cost</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<button onclick="addNewConditionRow()" class="button button-xs | margin-top-2">Add New Row</button>
			</div>
		`;

		placeholderEl.innerHTML = view;

		if (conditions.length === 0) addNewConditionRow();
		else conditions.forEach(condition => addNewConditionRow(condition));

		saveConditionButtonEl.setAttribute('onclick', `saveConditions(${rowIndex})`);
		showModal('conditions-modal');
	}


	// Other

	function handleCountryChange(){
		let targetEl = event.target;
		let countryCode = targetEl.value;
		
		let states = getStates();

	}

	function getStates(){
		let states = '{!! addSlashes(json_encode($constants::$states))  !!}';
		return JSON.parse(states);
	}

	if (shippingClassId !== '') fap();
	else addNewShippingZone();
</script>

@stop