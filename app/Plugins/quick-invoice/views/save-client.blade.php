@extends('layouts.portal')

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">

		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('billing name') }} <span class="required">*</span></label>
								<input name="billing-name" type="text" class="input-style-1" value="{{ $client['name'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('email') }}</label>
								<input name="email" type="text" class="input-style-1" value="{{ $client['email'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('address') }}</h2>
			</div>
			<div class="section-body margin-top-2">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('country') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="country" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach(Constant::alpha2Countries() as $countryCode=>$country)
										@if(($client['country'] ?? NULL) === $countryCode)
										<option value="{{ $countryCode }}" selected>{{ $country }}</option>
										@else
										<option value="{{ $countryCode }}">{{ $country }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('city') }}</label>
								<input name="city" type="text" class="input-style-1" value="{{ $client['city'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('province/state') }}</label>
								<input name="province" type="text" class="input-style-1" value="{{ $client['province_state'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('street') }}</label>
								<input name="street" type="text" class="input-style-1" value="{{ $client['street'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('street 2') }}</label>
								<input name="street-2" type="text" class="input-style-1" value="{{ $client['street_2'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('postcode/zip code') }}</label>
								<input name="postal-code" type="text" class="input-style-1" value="{{ $client['postcode'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('other') }}</h2>
			</div>
			<div class="section-body | margin-top-2">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="accordion">
							<div class="accordion-header">
								<h2 class="accordion-title">{{ __('contact information') }}</h2>
								<svg class="icon plus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-plus') }}">
								</svg>
								<svg class="icon minus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-minus') }}">
								</svg>
							</div>
							<div class="accordion-body">
								<div class="form-group">
									<div class="grids grids-2 gap-2">
										<div class="grid">
											<label class="input-style-1-label">{{ __('telephone number') }}</label>
											<input name="telephone" type="text" class="input-style-1" value="{{ $client['telephone'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('cell phone number') }}</label>
											<input name="phone" type="text" class="input-style-1" value="{{ $client['phone'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('fax') }}</label>
											<input name="fax" type="text" class="input-style-1" value="{{ $client['fax'] ?? '' }}">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="input-style-1-label">{{ __('website') }}</label>
									<input name="website" type="text" class="input-style-1" value="{{ $client['website'] ?? '' }}">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="accordion">
							<div class="accordion-header">
								<h2 class="accordion-title">{{ __('business information') }}</h2>
								<svg class="icon plus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-plus') }}">
								</svg>
								<svg class="icon minus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-minus') }}">
								</svg>
							</div>
							<div class="accordion-body">
								<div class="form-group">
									<div class="grids grids-2 gap-2">
										<div class="grid">
											<label class="input-style-1-label">n° SIREN</label>
											<input name="registration-number" type="text" class="input-style-1" value="{{ $client['registration_number'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">n° RCS</label>
											<input name="registration-number-2" type="text" class="input-style-1" value="{{ $client['registration_number_2'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">n° TVA</label>
											<input name="tax-number" type="text" class="input-style-1" value="{{ $client['tax_number'] ?? '' }}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="accordion">
							<div class="accordion-header">
								<h2 class="accordion-title">{{ __('default settings') }}</h2>
								<svg class="icon plus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-plus') }}">
								</svg>
								<svg class="icon minus">
									<use xlink:href="{{ asset('assets/icons.svg#solid-minus') }}">
								</svg>
							</div>
							<div class="accordion-body">
								<div class="form-group">
									<div class="grids grids-2 gap-2">
										<div class="grid">
											<label class="input-style-1-label">
												{{ __('discount') }}
												<select name="discount-type" style="margin-left: 0.6rem;">
													@foreach(["percentage"=>"%", "amount"=>"amount"] as $key=>$value)
													@if($key == ($client['default']['discount_type'] ?? NULL))
													<option value="{{ $key }}" selected>{{ $value }}</option>
													@else
													<option value="{{ $key }}">{{ $value }}</option>
													@endif
													@endforeach
												</select>
											</label>
											<input name="discount" type="text" class="input-style-1" value="{{ $client['default']['discount'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('payment method') }}</label>
											<div class="custom-select-container">
												<select name="payment-method" class="input-style-1">
													<option value="">{{ __('select') }}</option>
													@foreach(PaymentMethod::paymentMethods() as $row)
													@if(($client['default']['payment_method'] ?? NULL) == $row['title'])
													<option value="{{ $row['title'] }}" selected>{{ $row['title'] }}</option>
													@else
													<option value="{{ $row['title'] }}">{{ $row['title'] }}</option>
													@endif
													@endforeach
												</select>
											</div>
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('currency') }}</label>
											<div class="custom-select-container">
												<select name="currency" class="input-style-1">
													<option value="">{{ __('select') }}</option>
													@foreach(Constant::currencies() as $currencyCode=>$currency)
													@if(($client['default']['currency_code'] ?? NULL) === $currencyCode)
													<option value="{{ $currencyCode }}" selected>{{ $currency }}</option>
													@else
													<option value="{{ $currencyCode }}">{{ $currency }}</option>
													@endif
													@endforeach
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="input-style-1-label">{{ __('introduction/salutation') }}</label>
									<textarea name="salutation" class="input-style-1" style="resize: none;">{{ $client["default"]["salutation"] ?? "" }}</textarea>
								</div>
								<div class="form-group">
									<label class="input-style-1-label">{{ __('note') }}</label>
									<textarea name="note" class="input-style-1" style="resize: none;">{{ $client["default"]["note"] ?? "" }}</textarea>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveClient()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/client.js', 'quick-invoice') !!}

<script>
	/**
	 * Static data
	 */

	function staticClientId() {
		return '{{ $clientId ?? "" }}';
	}

	/**
	 * Save
	 */

	async function saveClient() {

		let nameEl = document.querySelector('[name="billing-name"]');
		let emailEl = document.querySelector('[name="email"]');
		let countryEl = document.querySelector('[name="country"]');
		let cityEl = document.querySelector('[name="city"]');
		let provinceEl = document.querySelector('[name="province"]');
		let streetEl = document.querySelector('[name="street"]');
		let street2El = document.querySelector('[name="street-2"]');
		let postcodeEl = document.querySelector('[name="postal-code"]');
		let telephoneEl = document.querySelector('[name="telephone"]');
		let phoneEl = document.querySelector('[name="phone"]');
		let faxEl = document.querySelector('[name="fax"]');
		let websiteEl = document.querySelector('[name="website"]');
		let registrationNumberEl = document.querySelector('[name="registration-number"]');
		let registrationNumber2El = document.querySelector('[name="registration-number-2"]');
		let taxNumberEl = document.querySelector('[name="tax-number"]');
		let discountEl = document.querySelector('[name="discount"]');
		let discountTypeEl = document.querySelector('[name="discount-type"]');
		let paymentMethodEl = document.querySelector('[name="payment-method"]');
		let currencyEl = document.querySelector('[name="currency"]');
		let salutationEl = document.querySelector('[name="salutation"]');
		let noteEl = document.querySelector('[name="note"]');

		let postData = {
			name: nameEl.value,
			email: emailEl.value,
			country: countryEl.value,
			city: cityEl.value,
			province: provinceEl.value,
			street: streetEl.value,
			street2: street2El.value,
			postcode: postcodeEl.value,
			telephone: telephoneEl.value,
			phone: phoneEl.value,
			fax: faxEl.value,
			website: websiteEl.value,
			registrationNumber: registrationNumberEl.value,
			registrationNumber2: registrationNumber2El.value,
			taxNumber: taxNumberEl.value,
			default: {
				discountType: discountTypeEl.value,
				discount: discountEl.value,
				paymentMethod: paymentMethodEl.value,
				currency: currencyEl.value,
				salutation: salutationEl.value,
				note: noteEl.value
			}
		};

		let n = showSavingNotification();

		let response = await QuickInvoiceClient.saveUserClient(staticClientId(), postData, {
			target: 'save-button'
		});

		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}
</script>
@stop