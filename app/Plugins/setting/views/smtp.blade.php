@extends('layouts.portal')

@php
$encryption = $settings['smtp-encryption']['column_value'] ?? NULL;
@endphp

@section('main-content')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
			<form action="#" id="page-form" onsubmit="return false;">
			<div class="form-group">
				<label class="input-style-1-label">{{ __('host') }}</label>
				<input name="host" type="text" class="input-style-1" value="{{ $settings['smtp-host']['column_value'] ?? '' }}">
			</div>
			<div class="form-group">
				<div class="grids grids-2 | gap-3">
					<div class="grid">
						<label class="input-style-1-label">{{ __('port') }}</label>
						<input name="port" type="text" class="input-style-1" value="{{ $settings['smtp-port']['column_value'] ?? '' }}">
					</div>

					<div class="grid">
						<label class="input-style-1-label">{{ __('encryption') }}</label>
						<div class="select-container chevron">
							<select name="encryption" class="input-style-1">
								<option @if($encryption==="ssl" ) selected @endif value="ssl">SSL</option>
								<option @if($encryption==="tls" ) selected @endif value="tls">TLS</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="grids grids-2 | gap-3">
					<div class="grid">
						<label class="input-style-1-label">{{ __('username-email') }}</label>
						<input name="email" type="text" class="input-style-1" value="{{ $settings['smtp-email']['column_value'] ?? '' }}">
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ __('password') }}</label>
						<div class="form-group has-right-icon">
							<input name="password" type="password" class="input-style-1" value="{{ $settings['smtp-password']['column_value'] ?? '' }}">
							<svg class="form-group-icon right | can-interact" data-is="password-visibility-toggler">
								<use xlink:href="{{ asset('assets/icons.svg#solid-eye') }}" />
							</svg>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="grids grids-2 | gap-3">
					<div class="grid">
						<label class="input-style-1-label">{{ __('from name') }}</label>
						<input name="from-name" type="text" class="input-style-1" value="{{ $settings['smtp-from-name']['column_value'] ?? '' }}">
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ __('domain') }}</label>
						<div class="input-wrapper">
							<svg class="info-icon">
								<use xlink:href="{{ asset('assets/icons.svg#solid-info') }}" />
							</svg>
							<span data-info="true">{{ __('domain-is-required-for-smtp-config') }}</span>
							<input name="domain" type="text" class="input-style-1" placeholder="e.g xyz.com" value="{{ $settings['smtp-domain']['column_value'] ?? '' }}" />
						</div>
					</div>
				</div>
			</div>
		</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid | flex-column-reverse-on-md">
			<div class="grid-widget | margin-bottom-2">
				<div class="button-group">
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveSMTP()" class="button button-primary button-block">{{ __('update') }}</button>
					<button onclick="showTestMailModal()" class="button button-primary-border button-block">{{ __('test email') }}</button>
				</div>
			</div>
			<div class="grid-widget | margin-bottom-2">
				<p class="grid-widget-text"><b>{{ __('outgoing email') }}</b></p>
				<p class="grid-widget-text | margin-top-2"><b>{{ __('host') }}:</b> <span data-is="host">{{ $settings['smtp-host']['column_value'] ?? '' }}</span></p>
				<p class="grid-widget-text | margin-top-2"><b>{{ __('port') }}:</b> <span data-is="port">{{ $settings['smtp-port']['column_value'] ?? '' }}</span></p>
				<p class="grid-widget-text | margin-top-2"><b>{{ __('username-email') }}:</b> <span data-is="email">{{ $settings['smtp-email']['column_value'] ?? '' }}</span></p>
				<p class="grid-widget-text | margin-top-2"><b>{{ __('encryption') }}:</b> <span data-is="encryption">{{ $settings['smtp-encryption']['column_value'] ?? '' }}</span></p>
				<p class="grid-widget-text | margin-top-2"><b>{{ __('from name') }}:</b> <span data-is="from-name">{{ $settings['smtp-from-name']['column_value'] ?? '' }}</span></p>
			</div>
		</div>
	</div>
</div>

<div id="smtp-modal" class="modal" style="width: min(90%, 50rem)">
	<div class="modal-header">
		<p class="modal-title">{{ __('smtp email test') }}</p>
		<span onclick="hideModal('smtp-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form id="smtp-test-form" action="#">
			<div class="form-group">
				<label class="input-style-1-label">{{ __('recipient email') }}</label>
				<input name="email" type="text" class="input-style-1">
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __('subject') }}</label>
				<input name="subject" type="text" class="input-style-1">
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __('message') }}</label>
				<textarea name="message" class="input-style-1"></textarea>
			</div>
			<div class="form-group">
				<button data-xhr-name="send-email" data-xhr-loading.attr="disabled" class="button button-primary button-block">{{ __('send email') }}</button>
			</div>
		</form>
	</div>
</div>

@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'setting') }}

<script>
	let eyeIconURL = '{{ asset("assets/icons.svg#solid-eye") }}';
	let eyeOffIconURL = '{{ asset("assets/icons.svg#solid-eye-off") }}';

	let smtpTestForm = document.querySelector('#smtp-test-form');

	let passwordVisivilityTogglers = document.querySelectorAll(`[data-is="password-visibility-toggler"]`);

	for (element of passwordVisivilityTogglers) {
		element.addEventListener("click", toggleLoginPasswordVisibility);
	}

	async function saveSMTP() {

		let host = document.querySelector('[name="host"]').value;
		let port = document.querySelector('[name="port"]').value;
		let encryption = document.querySelector('[name="encryption"]').value;
		let email = document.querySelector('[name="email"]').value;
		let password = document.querySelector('[name="password"]').value;
		let fromName = document.querySelector('[name="from-name"]').value;
		let domain = document.querySelector('[name="domain"]').value;

		let postData = {
			host,
			port,
			encryption,
			email,
			password,
			fromName,
			domain
		};

		let n = showSavingNotification();
		let response = await Setting.updateSMTP(postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') {
			document.querySelector('[data-is="host"]').innerHTML = host;
			document.querySelector('[data-is="port"]').innerHTML = port;
			document.querySelector('[data-is="encryption"]').innerHTML = encryption;
			document.querySelector('[data-is="email"]').innerHTML = email;
			document.querySelector('[data-is="from-name"]').innerHTML = fromName;
		}

	}

	function showTestMailModal() {
		resetSMTPTestForm();
		showModal('smtp-modal')
	}

	smtpTestForm.addEventListener('submit', async function() {
		event.preventDefault();

		let email = smtpTestForm.querySelector('[name="email"]').value;
		let subject = smtpTestForm.querySelector('[name="subject"]').value;
		let msg = smtpTestForm.querySelector('[name="message"]').value;

		let postData = {
			email,
			subject,
			msg
		};

		let n = showProcessingNotification();
		let response = await Setting.smtpTest(postData, {target: 'send-email'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') {
			resetSMTPTestForm();
			hideModal('smtp-modal');
		}

	});

	function resetSMTPTestForm() {
		smtpTestForm.querySelector('[name="email"]').value = '';
		smtpTestForm.querySelector('[name="subject"]').value = '';
		smtpTestForm.querySelector('[name="message"]').value = '';
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

@stop