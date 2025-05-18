@extends('layouts.portal')

@php
$subscriber = Subscription::subscriberByUserId($subscriberUserId);
@endphp

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" class="section no-shadow" onsubmit="return false;">
			<div class="section-body">
				<div class="form-group">
					<div class="grids grids-2 gap-2">
						<div class="grid">
							<label class="input-style-1-label">{{ __('package') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="package" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									@foreach(Subscription::packages() as $package)
									<option value="{{ $package['id'] }}">{{ $package['title'] }} ({{ $package['classification']['name'] }})</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('user') }} <span class="required">*</span></label>
							<div class="custom-select-container">

								@php
									$roles = Subscription::subscribableRoles();
								@endphp

								<select name="user" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									@foreach(User::users() as $user)
									@if(in_array($user['role_title'], $roles))
									@if(!empty($subscriber) && $subscriber['user_id'] == $user['id'])
									<option value="{{ $user['id'] }}">{{ $user['first_name'].' '.($user['last_name'] ?? '') }}</option>
									@elseif(empty($subscriber))
									<option value="{{ $user['id'] }}">{{ $user['first_name'].' '.($user['last_name'] ?? '') }}</option>
									@endif
									@endif
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="grids grids-2 gap-2 margin-top-2">
						<div class="grid">
							<label class="input-style-1-label">{{ __('expiry date') }} <span class="required">*</span></label>
							<input type="date" name="expiry-date" class="input-style-1">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('expiry time') }} <span class="required">*</span></label>
							<input type="time" name="expiry-time" class="input-style-1">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('disable') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="disable" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									<option value="yes">{{ __('yes') }}</option>
									<option value="no">{{ __('no') }}</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="save()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
		<div class="grid-widget margin-top-2">
			<div class="grid-widget-text">
				{{ __('expiry-time-based-on') }} {{ config('app.timezone') }} {{ strtolower(__('timezone')) }}.
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'subscription') }}

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (!isEmpty(staticSubscriberUserId())) populateSubscriber(staticSubscriber());
	}

	/**
	 * Static data
	 */

	function staticSubscriberUserId() {
		return '{{ $subscriberUserId ?? "" }}';
	}

	function staticSubscriber() {
		let subscriber = '{!! addSlashes(json_encode($subscriber)) !!}';
		return JSON.parse(subscriber);
	}

	/**
	 * Save
	 */

	async function save() {
		let package = document.querySelector('select[name="package"]');
		let user = document.querySelector('select[name="user"]');
		let disable = document.querySelector('select[name="disable"]');
		let expiryDate = document.querySelector('input[name="expiry-date"]');
		let expiryTime = document.querySelector('input[name="expiry-time"]');


		let expirtDateTime = expiryDate.value + ' ' + expiryTime.value;
		if(!isEmpty(expiryDate.value) && !isEmpty(expiryTime.value)){
			expirtDateTime = moment(expirtDateTime, "YYYY-MM-DD HH:mm:ss").format("YYYY/MM/DD hh:mm:ss A");
		}
		else expirtDateTime = null;

		let n = showSavingNotification();
		let response = await Subscription.saveSubscriber(user.value, package.value, null, disable.value, expirtDateTime, {
			target: 'save-button'
		});
		showResponseNotification(n, response);
		if (response.data.status === 'success') window.location.href = `${PREFIXED_URL}/subscription/subscribers`;
	}

	/**
	 * Populate
	 */

	function populateSubscriber(subscriber) {
		let package = document.querySelector('select[name="package"]');
		let user = document.querySelector('select[name="user"]');
		let disable = document.querySelector('select[name="disable"]');
		let expiryDate = document.querySelector('input[name="expiry-date"]');
		let expiryTime = document.querySelector('input[name="expiry-time"]');

		package.value = subscriber.subscription_package_id;
		user.value = subscriber.user_id;
		disable.value = subscriber.disable == 1 ? 'yes' : 'no';

		if(!isEmpty(subscriber.expiry_datetime)){
			let formattedDate = moment(subscriber.expiry_datetime, "YYYY/MM/DD hh:mm:ss A").format("YYYY-MM-DD");
			let formattedTime = moment(subscriber.expiry_datetime, "YYYY/MM/DD hh:mm:ss A").format("HH:mm:ss");
			expiryDate.value = formattedDate;
			expiryTime.value = formattedTime;
		}
	}
</script>

@stop