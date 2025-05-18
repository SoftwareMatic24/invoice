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
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __('status') }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Status:active">{{ __('active') }}</option>
						<option value="Status:expired">{{ __('expired') }}</option>
					</select>
				</div>
			</div>
			<a href="{{ url('/portal/subscription/subscribers/save') }}" class="button button-primary with-plus-icon">{{ __('new subscriber') }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('subscriber') }}</th>
				<th>{{ __('subscriber email') }}</th>
				<th>{{ __('package') }}</th>
				<th>{{ __('amount paid') }}</th>
				<th>{{ __('status') }}</th>
				<th>{{ __('subscription date') }}</th>
				<th>{{ __('expiry date') }}</th>
				<th>{{ __('action') }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<!-- modals -->
<div id="subscriber-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('subscriber-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group">
			<p><b class="underline">1. Subscriber Details</b></p>
			<p><b>Subscriber:</b> <span data-is="name"></span></p>
			<p><b>Subscriber email:</b> <span data-is="email"></span></p>
			<p><b class="underline margin-top-3">2. Package Details</b></p>
			<p><b>Package:</b> <span data-is="package"></span></p>
			<p><b>Status:</b> <span data-is="status"></span></p>
			<p><b>Subscription date:</b> <span data-is="date"></span></p>
			<p><b>Expiry date:</b> <span data-is="expiry"></span></p>
			<p><b class="underline margin-top-3">3. Transaction Details</b></p>
			<p><b>Payment method:</b> <span data-is="payment-method"></span></p>
			<p><b>Price paid:</b> <span data-is="price"></span></p>
		</div>
	</div>

</div>

@stop

@section('page-script')
<script>
	let pageTable = dataTable('page-table');

	async function fap() {
		let subscribers = await fetchSubscribers();
		populateSubscribers(subscribers);
	}

	async function fetchSubscribers() {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/subscription/packages/subscribers/all'
		});
		return response.data;
	}

	function populateSubscribers(subscribers) {

		let tableData = subscribers.map((subscriber, subscriberIndex) => {

			let transaction = {
				amount: '',
				currency: ''
			};

			let status = {
				text: 'active',
				class: 'tag-success'
			};

			if (subscriber.transaction !== null) {
				transaction.amount = subscriber.transaction.product_amount;
				transaction.currency = subscriber.transaction.currency;
			}

			if (isSubscriptionExpired(subscriber.expiry_datetime)) {
				status.text = 'expired';
				status.class = 'tag-danger';
			}

			if(subscriber.disable == 1) {
				status.text = 'disabled';
				status.class = 'tag-danger';
			}

			return [{
					type: 'text',
					value: subscriberIndex + 1
				},

				{
					type: 'text',
					value: fullName(subscriber.subscriber.first_name, subscriber.subscriber.last_name)
				},
				{
					type: 'text',
					value: subscriber.subscriber.email
				},
				{
					type: 'text',
					value: `<a target="_blank" href="${PREFIXED_URL}/subscription/save/${subscriber.subscription_package.id}">${subscriber.subscription_package.title}</a>`
				},
				{
					type: 'text',
					value: transaction.currency + ' ' + transaction.amount
				},
				{
					type: 'tag',
					value: status.text,
					itemClasses: [status.class]
				},

				{
					type: 'text',
					value: toLocalDateTime(subscriber.create_datetime, true)
				},
				{
					type: 'text',
					value: toLocalDateTime(subscriber.expiry_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							attributes: [`data-popover="{{ __("view") }}"`],
							event: {
								'click': function() {
									showSubscriber(subscriber);
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							attributes: [`data-popover="{{ __('edit') }}"`],
							link: `${PREFIXED_URL}/subscription/subscribers/save/${subscriber.user_id}`
						}
					]
				}
			];
		});

		pageTable.init(tableData);
		popover.init();
	}

	function showSubscriber(subscriber) {
		let modalEl = document.querySelector('#subscriber-modal');
		let nameEl = modalEl.querySelector('[data-is="name"]');
		let emailEl = modalEl.querySelector('[data-is="email"]');
		let packgeEl = modalEl.querySelector('[data-is="package"]');
		let statusEl = modalEl.querySelector('[data-is="status"]');
		let dateEl = modalEl.querySelector('[data-is="date"]');
		let expiryDateEl = modalEl.querySelector('[data-is="expiry"]');
		let paymentMethodEl = modalEl.querySelector('[data-is="payment-method"]');
		let priceEl = modalEl.querySelector('[data-is="price"]');

		let transaction = {
			amount: '',
			currency: '',
			paymentMethod: ''
		};

		let status = {
			text: 'active',
			class: 'tag-success'
		};

		if (subscriber.transaction !== null) {
			transaction.amount = subscriber.transaction.product_amount;
			transaction.currency = subscriber.transaction.currency;
			transaction.paymentMethod = subscriber.transaction.payment_method;

		}

		if (isSubscriptionExpired(subscriber.expiry_datetime)) {
			status.text = 'expired';
			status.class = 'tag-danger';
		}

		if(subscriber.disable == 1) {
				status.text = 'disabled';
				status.class = 'tag-danger';
			}

		nameEl.innerHTML = fullName(subscriber.subscriber.first_name, subscriber.subscriber.last_name);
		emailEl.innerHTML = subscriber.subscriber.email;
		packgeEl.innerHTML = subscriber.subscription_package.title;
		statusEl.innerHTML = `<span class="tag ${status.class}">${status.text}</span>`;
		dateEl.innerHTML = toLocalDateTime(subscriber.create_datetime, true);
		expiryDateEl.innerHTML = toLocalDateTime(subscriber.expiry_datetime, true);
		paymentMethodEl.innerHTML = transaction.paymentMethod;
		priceEl.innerHTML = transaction.currency + ' ' + transaction.amount;

		showModal('subscriber-modal');
	}

	function isSubscriptionExpired(expiryDate) {
		let now = moment().format(CLIENT_DATETIME_FORMAT);
		let clientExpiryDate = toLocalDateTime(expiryDate);
		return moment(now, CLIENT_DATETIME_FORMAT).isSameOrAfter(moment(clientExpiryDate, CLIENT_DATETIME_FORMAT));
	}

	fap();
</script>
@parent
@stop