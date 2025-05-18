@extends('layouts.portal')
@section('main-content')
<div class="grids grids-2">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<div class="grids grids-2 gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('currency') }}</label>
								<div class="custom-select-container">
									<select name="currency" class="input-style-1">
										@foreach($allCurrencies as $currencyCode=>$currency)
										<option value="{{ $currencyCode }}">{{ $currency }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('symbol') }}</label>
								<input name="symbol" type="text" class="input-style-1">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('exchange rate') }}</label>
								<input name="rate" type="text" class="input-style-1">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('type') }}</label>
								<div class="custom-select-container">
									<select name="type" class="input-style-1">
										<option value="secondary">{{ __('secondary') }}</option>
										<option value="primary">{{ __('primary') }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="section-footer">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveCurrency()" class="button button-primary">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'currency') }}

<script>

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		if(!isEmpty(staticCurrency())) populateCurrency(staticCurrency());
	}


	/**
	 * Static data
	 */

	function staticCurrencyId(){
		return '{{ $currencyId ?? "" }}';
	}

	function staticCurrency(){
		let currency = '{!! addSlashes(json_encode(Currency::currency($currencyId ?? ""))) !!}';
		return JSON.parse(currency);
	}

	/**
	 * Save
	 */

	async function saveCurrency() {

		let currencyEl = document.querySelector('select[name="currency"]');
		let symbolEl = document.querySelector('input[name="symbol"]');
		let rateEl = document.querySelector('input[name="rate"]');
		let typeEl = document.querySelector('select[name="type"]');

		let postData = {
			currency: currencyEl.value,
			symbol: symbolEl.value,
			rate: rateEl.value,
			type: typeEl.value
		};

		let n = showSavingNotification();
		let response = await Currency.saveCurrency(staticCurrencyId(), postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = `{{ $backURL }}`;
	}

	function populateCurrency(currency) {

		let currencyEl = document.querySelector('select[name="currency"]');
		let symbolEl = document.querySelector('input[name="symbol"]');
		let rateEl = document.querySelector('input[name="rate"]');
		let typeEl = document.querySelector('select[name="type"]');

		currencyEl.value = currency.currency;
		symbolEl.value = currency.symbol;
		rateEl.value = currency.rate;
		typeEl.value = currency.type;
	}

	
</script>

@stop