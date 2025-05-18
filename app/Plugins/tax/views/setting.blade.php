@extends('layouts.portal')
@section('page-style')
<link rel="stylesheet" href="{{ asset('css/tagify.css') }}">
<link rel="stylesheet" href="{{ asset('css/tagify-child.css') }}">
@parent
@stop
@section('main-content')
@inject('pluginController','App\Http\Controllers\PluginController')
<div class="tabs-container">
	<div class="tabs-header">
		<ul>
			<li class="active">
				<span style="min-width: 5.8rem;">Tax</span>
			</li>
			<li>
				<span>Shipping</span>
			</li>
		</ul>
	</div>
	<div class="tabs-body | margin-top-2">
		<div class="active">
			<div class="grids main-sidebar-grids">
				<div class="grid">
					<form action="#" onsubmit="return false;">
						<div class="form-group">
							<div class="grids grids-2 gap-3">
								<div class="grid">
									<label class="input-style-1-label">Status</label>
									<select name="tax" class="input-style-1">
										<option value="0">Off</option>
										<option value="1">On</option>
									</select>
								</div>
								<div class="grid"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="grids grids-2 gap-3">
								<div class="grid">
									<label class="input-style-1-label">I will add prices exclusive of tax</label>
									<select name="price-inclusive-tax" class="input-style-1">
										<option value="0">Yes</option>
										<option value="1">No</option>
									</select>
								</div>
								<div class="grid">
									<label class="input-style-1-label hide">Show tax in product prices?</label>
									<select name="product-pirce-display-tax" class="input-style-1 hide">
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
									</select>
									<label class="input-style-1-label">Calculate tax based on</label>
									<select name="calculate-tax-on-address" class="input-style-1">
										<option value="shipping">Shipping address</option>
										<option value="billing">Billing address</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<button onclick="upateTaxSettings()" class="button button-sm button-primary">Update</button>
						</div>
					</form>
				</div>
				<div class="grid"></div>
			</div>
		</div>
		<div>
			<div class="grids main-sidebar-grids">
				<div class="grid">
					<form action="#" onsubmit="return false;">
						<div class="form-group">
							<div class="grids grids-2 gap-3">
								<div class="grid">
									<label class="input-style-1-label">Status</label>
									<select name="shipping" class="input-style-1">
										<option value="0">Off</option>
										<option value="1">On</option>
									</select>
								</div>
								<div class="grid">
									<label class="input-style-1-label">Shipping Countries</label>
									<input name="shipping-countries" type="text" class="input-style-1">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="grids grids-2 gap-3">
								<div class="grid">
									<label class="input-style-1-label">Shipping Calculation Method</label>
									<select name="shipping-calculation-method" class="input-style-1">
										<option value="cart-price">Based on Total Cart Price</option>
										<option value="individial-item-price">Based on Individual Item Price</option>
									</select>
								</div>
								<div class="grid"></div>
							</div>
						</div>
						<div class="form-group">
							<button onclick="updateShippingSettings()" class="button button-sm button-primary">Update</button>
						</div>
					</form>
				</div>
				<div class="grid">
					<div class="grid-widget | margin-bottom-2">
						<p class="grid-widget-text | margin-bottom-2"><b>Tips:</b></p>
						<p class="grid-widget-text-2 | margin-bottom-1">Keep shipping countries empty for world wide.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')
<script src="{{ asset('js/tagify.js') }}"></script>
<script src="{{ asset('js/tagify.polyfills.min.js') }}"></script>
<script>
	let shippingCountriesTagify = null;
	
	function init() {
		let countries = countryList().map(country => {
			return {
				value: country.name,
				code: country.code
			};
		});
		let shippingCountriesEl = document.querySelector('[name="shipping-countries"]');
		shippingCountriesTagify = new Tagify(shippingCountriesEl, {
			whitelist: [...[{
				value: 'World wide',
				code: null
			}], ...countries],
			enforceWhitelist: true
		});
	}

	async function fetchSettings() {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/tax/user/settings/all'
		});
		return response.data;
	}

	async function upateTaxSettings() {
		let taxEl = document.querySelector('select[name="tax"]');
		let inclusiveTaxEl = document.querySelector('select[name="price-inclusive-tax"]');
		let calculateOnAddressEl = document.querySelector('select[name="calculate-tax-on-address"]');
		let displayTaxInProductEl = document.querySelector('select[name="product-pirce-display-tax"]');
		let postData = {
			'tax': taxEl.value,
			'price-inclusive-tax': inclusiveTaxEl.value,
			'calculate-tax-on-address': calculateOnAddressEl.value,
			'product-pirce-display-tax': displayTaxInProductEl.value
		};
		updateSettings(postData);
	}

	async function updateShippingSettings() {
		let shippingEl = document.querySelector('[name="shipping"]');
		let shippingCountriesEl = document.querySelector('[name="shipping-countries"]');
		let shippingCalculationMethod = document.querySelector('[name="shipping-calculation-method"]');
		let postData = {
			'shipping': shippingEl.value,
			'shipping-countries': shippingCountriesEl.value == '' ? null : shippingCountriesEl.value,
			'shipping-calculation-method':shippingCalculationMethod.value
		};
		updateSettings(postData);
	}

	async function updateSettings(postData) {
		let n = Notification.show({
			text: 'Updating, please wait...',
			time: 0
		});
		let response = await xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/tax/user/settings/update',
			body: postData
		});
		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});
	}

	async function fap() {
		let settings = await fetchSettings();
		if (settings !== '') populateSettings(settings);
	}

	function populateSettings(settings) {
		let tagifySettings = ['shipping-countries'];
		settings.forEach((setting) => {
			let columnName = setting.column_name;
			let columnValue = setting.column_value;
			let el = document.querySelector(`[name="${columnName}"]`);
			if (el !== null && !tagifySettings.includes(columnName)) {
				el.value = columnValue;
			} else if (el !== null && tagifySettings.includes(columnName)) {
				if (columnName === 'shipping-countries') {
					setTimeout(() => {
						shippingCountriesTagify.loadOriginalValues(columnValue !== null ? JSON.parse(columnValue) : [{
							value: 'World wide',
							code: null
						}])
					}, 200)
				}
			}
		});
	}

	init();
	fap();
</script>
@stop