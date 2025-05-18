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
								<label class="input-style-1-label">{{ __('name') }} <span class="required">*</span></label>
								<input name="name" type="text" class="input-style-1" value="{{ $business['name'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('email') }}</label>
								<input name="email" type="email" class="input-style-1" value="{{ $business['email'] ?? '' }}">
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
										@if(($business['country'] ?? NULL) === $countryCode)
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
								<input name="city" type="text" class="input-style-1" value="{{ $business['city'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('province/state') }}</label>
								<input name="province" type="text" class="input-style-1" value="{{ $business['province_state'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('street') }}</label>
								<input name="street" type="text" class="input-style-1" value="{{ $business['street'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('street 2') }}</label>
								<input name="street-2" type="text" class="input-style-1" value="{{ $business['street_2'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('postcode/zip code') }}</label>
								<input name="postal-code" type="text" class="input-style-1" value="{{ $business['postcode'] ?? '' }}">
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
			<div class="section-body margin-top-2">
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
											<input name="telephone" type="text" class="input-style-1" value="{{ $business['telephone'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('cell phone number') }}</label>
											<input name="phone" type="text" class="input-style-1" value="{{ $business['phone'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('fax') }}</label>
											<input name="fax" type="text" class="input-style-1" value="{{ $business['fax'] ?? '' }}">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="input-style-1-label">{{ __('website') }}</label>
									<input name="website" type="text" class="input-style-1" value="{{ $business['website'] ?? '' }}">
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
											<label class="input-style-1-label">{{ __('business id') }}</label>
											<input name="business-id" type="text" class="input-style-1" value="{{ $business['business_id'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('tax id') }}</label>
											<input name="tax-id" type="text" class="input-style-1" value="{{ $business['tax_id'] ?? '' }}">
										</div>
										<div class="grid">
											<label class="input-style-1-label">{{ __('trade register') }}</label>
											<input name="trade-register" type="text" class="input-style-1" value="{{ $business['trade_register'] ?? '' }}">
										</div>
									</div>
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
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveBusiness()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
		<div data-is="featured-image-widget" class="grid-widget | margin-bottom-2">
			<div>
				<p class="grid-widget-text"><b>{{ __('business logo') }}</b></p>
				@if($business["logo"]["url"] ?? false)
				<img onclick="chooseLogo()" data-is="logo-image" class="width-100 margin-top-2 cursor-pointer" data-src="{{ $business['logo']['id'] }}" src="{{ url('storage/'.$business['logo']['url']) }}" alt="logo">
				@else
				<img onclick="chooseLogo()" data-is="logo-image" class="width-100 margin-top-2 cursor-pointer" src="{{ asset('assets/default-image-300x158.jpg') }}" alt="logo">
				@endif
			</div>
		</div>
		<div data-is="featured-image-widget" class="grid-widget | margin-bottom-2">
			<div>
				<p class="grid-widget-text"><b>{{ __('business signature') }}</b></p>
				@if($business["signature"]["url"] ?? false)
				<img onclick="chooseSignature()" data-is="signature-image" class="width-100 margin-top-2 cursor-pointer" data-src="{{ $business['signature']['id'] }}" src="{{ url('storage/'.$business['signature']['url']) }}" alt="signature">
				@else
				<img onclick="chooseSignature()" data-is="signature-image" class="width-100 margin-top-2 cursor-pointer" src="{{ asset('assets/default-image-300x158.jpg') }}" alt="signature">
				@endif
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/business.js', 'quick-invoice') !!}

<script>

	/**
	 * Static data
	 */

	function staticBusinessId(){
		return '{{ $businessId ?? "" }}';
	}

	/**
	 * Save
	 */
	
	async function saveBusiness() {

		let nameEl = document.querySelector('[name="name"]');
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
		let businessIdEl = document.querySelector('[name="business-id"]');
		let taxIdEl = document.querySelector('[name="tax-id"]');
		let tradeRegisterEl = document.querySelector('[name="trade-register"]');
		let logoEl = document.querySelector('[data-is="logo-image"]');
		let signatureEl = document.querySelector('[data-is="signature-image"]');

		let logoMediaId = logoEl.getAttribute('data-src');
		let signatureMediaId = signatureEl.getAttribute('data-src');

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
			businessId: businessIdEl.value,
			taxId: taxIdEl.value,
			tradeRegister: tradeRegisterEl.value,
			logoMediaId: logoMediaId,
			signatureMediaId: signatureMediaId
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceBusiness.saveUserBusiness(staticBusinessId(), postData, {target: 'save-button'});
		showResponseNotification(n, response);
		
		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}

	function chooseLogo() {
		mediaCenter.show({
			useAs: {
				title: 'Set as logo',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector('[data-is="logo-image"]').setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector('[data-is="logo-image"]').setAttribute('data-src', media[0].id);
				}
			}
		});
	}

	function chooseSignature() {
		mediaCenter.show({
			useAs: {
				title: 'Set as signature',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector('[data-is="signature-image"]').setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector('[data-is="signature-image"]').setAttribute('data-src', media[0].id);
				}
			}
		});
	}
</script>
@stop