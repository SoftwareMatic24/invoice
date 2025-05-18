@extends('layouts.portal')
@inject('pluginController','App\Http\Controllers\PluginController')

@section('main-content')

<div class="price-plan-info | hide">
	<h2 class="price-plan-info-heading">Current Package</h2>
	<p class="price-plan-info-text | margin-top-1"><b>Name:</b> <span data-is="name"></span></p>
	<p class="price-plan-info-text"><b>Subscription date:</b> <span data-is="date"></span></p>
	<p class="price-plan-info-text"><b>Expiry date:</b> <span data-is="expiry"></span></p>
	<p class="price-plan-info-text"><b>Status:</b> <span data-is="status"></span></p>
</div>
<div class="price-plan-info | margin-top-3 hide">
	<h2 class="price-plan-info-heading">Packages</h2>
</div>
<div class="price-plans | margin-top-1"></div>

<!-- modals -->
{!! $pluginController->loadWidget('payment-method', 'choose-payment-method-modal') !!}

@stop

@section('page-script')

<script>
	let primaryCurrencySymbol = `{!! $primaryCurrencySymbol ?? "" !!}`;
	let selectedPackageId = null;	

	async function fap() {
		let plans = await fetchPricePlans();
		let subscription = await fetchUserSubscription();
		populatePricePlans(plans);
		populateUserPackage(subscription);
	}

	async function fetchPricePlans(plans) {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/subscription/packages/active/all'
		});
		return response.data;
	}

	async function fetchUserSubscription() {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/subscription/user/one'
		});
		return response.data;
	}

	function populatePricePlans(plans) {
		let pricePlansEl = document.querySelector('.price-plans');

		let plansHTML = plans.map(plan => {

			let detailsHTML = plan.details.map(detail => `<li>${detail.name}</li>`).join('');

			return `
				<div class="price-plan" data-is="package" data-package-id="${plan.id}">
					<div class="price-plan-header">
						<h2 class="price-plan-heading">${plan.title}</h2>
						<p class="price-plan-description">${plan.description !== null ? plan.description : ''}</p>
						<p class="price-plan-price">${primaryCurrencySymbol}${formatNumber(plan.price)} <span>/ month</span></p>
						<button onclick="showChoosePaymentMethod(${plan.id})" class="button button-sm button-primary-border price-plan-button">Choose Plan</button>
					</div>
					<div class="price-plan-body">
						<ul>${detailsHTML}</ul>
					</div>
				</div>
			`;

		}).join('');

		pricePlansEl.innerHTML = plansHTML;
	}

	function populateUserPackage(subscription) {

		let nameEl = document.querySelector('[data-is="name"]');
		let dateEl = document.querySelector('[data-is="date"]');
		let expiryEl = document.querySelector('[data-is="expiry"]');
		let statusEl = document.querySelector('[data-is="status"]');

		if(subscription === '') return;
		else document.querySelectorAll('.price-plan-info').forEach(el => el.classList.remove('hide'));
		

		let status = {text: 'active',class: 'tag-success'};
		if(isSubscriptionExpired(subscription.expiry_datetime)) {
			status.text = 'expired';
			status.class = 'tag-danger';
		}

		nameEl.innerHTML = subscription.subscription_package.title;
		dateEl.innerHTML = toLocalDateTime(subscription.create_datetime, true);
		statusEl.innerHTML = `<span class="tag ${status.class}">${status.text}</span>`;
		expiryEl.innerHTML = toLocalDateTime(subscription.expiry_datetime, true);
		if(subscription.expiry_datetime === null) expiryEl.innerHTML = 'Lifetime';

		function isSubscriptionExpired(expiryDate) {
			let now = moment().format(CLIENT_DATETIME_FORMAT);
			let clientExpiryDate = toLocalDateTime(expiryDate);
			return moment(now, CLIENT_DATETIME_FORMAT).isSameOrAfter(moment(clientExpiryDate, CLIENT_DATETIME_FORMAT));
		}
	}

	function showChoosePaymentMethod(packageId){
		selectedPackageId = packageId;
		showModal('payment-methods-modal');
	}

	function handlePaymentMethodClick(paymentMethodSlug){
		let url = PREFIXED_URL + '/subscription/packages/subscribe/' + selectedPackageId + '/' + paymentMethodSlug;
		window.location.href = url;
	}

	fap();
</script>
@parent
@stop