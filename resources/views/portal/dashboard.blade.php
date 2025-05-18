@extends('layouts.portal')
@inject('pluginController','App\Http\Controllers\PluginController')

@php
$plugins = $pluginController->getActivePlugins();
@endphp

@section('main-content')

{{ $pluginController->loadLayout($plugins, 'dashboard') }}

<!-- Modal -->

<div id="force-password-update-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">Update Password</p>
	</div>
	<div class="modal-body">
		<form onsubmit="updateUserPassword()" action="#">

			<label class="input-style-1-label">New Password</label>
			<div class="form-group has-right-icon" style="max-width: 90%;">
			
				<div class="input-wrapper">
					<svg onclick="generatePassword()" title="Generate Password" class="generate-password-icon">
						<use xlink:href="{{ asset('assets/icons.svg#solid-generate') }}" />
					</svg>
					
					<input autocomplete="new-password" name="password" type="password" class="input-style-1">
					<svg class="form-group-icon right | can-interact" data-is="password-visibility-toggler">
					<use xlink:href="{{ asset('assets/icons.svg#solid-eye') }}" />
				</svg>
				</div>
			</div>

			<label class="input-style-1-label margin-top-2">Confirm Password</label>
			<div class="form-group has-right-icon" style="margin-top:0;max-width:90%;" >			
				<input type="password" name="confirm-password" class="input-style-1" autocomplete="new-password">
				<svg class="form-group-icon right | can-interact" data-is="password-visibility-toggler">
					<use xlink:href="{{ asset('assets/icons.svg#solid-eye') }}" />
				</svg>
			</div>

			<div class="form-group" style="max-width: 90%;">
				<button class="button button-primary button-block">Update Password</button>
			</div>
		</form>
	</div>

</div>

@stop


@section('page-script')

<script>
	let forcePasswordUpdate = '{!! $forcePassword  !!}';
	forcePasswordUpdate = JSON.parse(forcePasswordUpdate);

	if (forcePasswordUpdate.length > 0) {
		showModal('force-password-update-modal', true);
	}

	async function updateUserPassword() {
		event.preventDefault();
		let password = document.querySelector('#force-password-update-modal [name="password"]').value;
		let confirmPassword = document.querySelector('#force-password-update-modal [name="confirm-password"]').value;

		let n = Notification.show({
			text: 'Updating password, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/users/update/password',
			body: {
				password,
				confirmPassword
			}
		});


		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});


		if (response.data.status === 'success') hideModal('force-password-update-modal');

	}


	let eyeIconURL = '{{ asset("assets/icons.svg#solid-eye") }}';
	let eyeOffIconURL = '{{ asset("assets/icons.svg#solid-eye-off") }}';

	let passwordVisivilityTogglers = document.querySelectorAll(`[data-is="password-visibility-toggler"]`);


	for (element of passwordVisivilityTogglers) {
		element.addEventListener("click", toggleLoginPasswordVisibility);
	}

	function toggleLoginPasswordVisibility() {

		let target = event.currentTarget;
		let passwordInput = target.closest('.form-group').querySelector('input');
		let passwordVisibility = false;

		if (passwordInput.getAttribute('type') == 'password') passwordVisibility = true;

		let use = target.querySelector('use');

		if (passwordVisibility === true) {
			use.setAttribute('xlink:href', eyeOffIconURL);
			passwordInput.setAttribute('type', 'text');
		} else {
			use.setAttribute('xlink:href', eyeIconURL);
			passwordInput.setAttribute('type', 'password');
		}
	}

</script>
@parent
@stop