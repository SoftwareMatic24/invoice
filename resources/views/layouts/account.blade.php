@extends('layouts.master')

@section("content")
@yield("layout-content")
@stop

@section("script")

{!! loadPluginFile('js/account.js', 'user-manager') !!}

<script>
	let uniqueId = '{{ $uid ?? "" }}';
	let eyeIconURL = null;
	let eyeOffIconURL = null;
	let accountForm = document.querySelector('#account-form');
	let recentResponse = null;

	// Events

	if (accountForm !== null) accountForm.addEventListener("submit", handleSubmit);

	function handleSubmit() {
		event.preventDefault();
		if (typeof page === "undefined") return;
		else if (page === 'login') login();
		else if (page === "register") register();
		else if (page === "forgot-password") forgotPassword();
		else if (page === "reset-password") resetPassword();
		else if (page === "2fa-verify") twoFactorAuthVerify();
	}

	// Login

	async function login() {

		let email = accountForm.querySelector("[name='email']").value;
		let password = accountForm.querySelector("[name='password']").value;

		let n = Notification.show({
			heading: '{{ __("auth-notification-heading") }}',
			description: '{{ __("auth-notification-msg") }}',
			time: 0
		});

		let response = await UserManagerAccount.auth({
			email,
			password
		}, {
			target: 'account-button'
		});

		recentResponse = response;

		if (response.data.redirect !== undefined) window.location.href = response.data.redirect;

		if (response.data.status !== 'success' || response.data.user === undefined) {
			Notification.hideAndShowDelayed(n.data.id, {
				classes: [response.data.status],
				heading: response.data.heading,
				description: response.data.description
			});
		} else if (response.data.status === 'success' && response.data.user !== undefined) window.location.href = PREFIXED_URL + '/dashboard';

	}

	async function register() {

		let nameEl = accountForm.querySelector("[name='name']");
		let emailEl = accountForm.querySelector("[name='email']");
		let passwordEl = accountForm.querySelector("[name='password']");
		let confirmPasswordEl = accountForm.querySelector("[name='confirm-password']");
		let roleEl = accountForm.querySelector("[name='role']");

		let name = nameEl.value;
		let email = emailEl.value;
		let password = passwordEl.value;
		let confirmPassword = null;
		let role = 'user';


		if (confirmPasswordEl === null) confirmPassword = password;
		else confirmPassword = confirmPasswordEl.value;

		if (roleEl !== null) role = roleEl.value;

		if (email !== '' && password !== '' && confirmPassword !== '' && name === '') {
			changeRegistrationScreen('registration-2');
			return;
		}

		let postData = {
			name: name,
			email: email,
			password: password,
			confirmPassword: confirmPassword,
			roleTitle: role
		};

		let n = Notification.show({
			heading: '{{ __("reg-notification-heading") }}',
			description: '{{ __("reg-notification-description") }}',
			time: 0
		});

		let response = await UserManagerAccount.register(postData, {
			target: 'account-button'
		});

		recentResponse = response;

		
		if(!isEmpty(response.data.msg)){
			response.data.heading = response.data.msg;
			response.data.description = ''
		}

		Notification.hideAndShowDelayed(n.data.id, {
			classes: [response.data.status],
			heading: response.data.heading,
			description: response.data.description
		});
	}

	async function forgotPassword() {
		let email = accountForm.querySelector("[name='email']").value;

		let n = Notification.show({
			heading: '{{ __("processing-notification-heading") }}',
			description: '{{ __("processing-notification-description") }}',
			time: 0
		});

		let response = await UserManagerAccount.forgotPassword({
			email
		}, {
			target: 'account-button'
		});

		recentResponse = response;

		Notification.hideAndShowDelayed(n.data.id, {
			classes: [response.data.status],
			heading: response.data.heading,
			description: response.data.description
		});
	}

	async function resetPassword() {
		let password = accountForm.querySelector("[name='password']").value;
		let confirmPassword = accountForm.querySelector("[name='confirm-password']").value;

		let postData = {
			password: password,
			confirmPassword: confirmPassword,
			uid: uniqueId
		};

		let n = Notification.show({
			heading: '{{ __("processing-notification-heading") }}',
			description: '{{ __("processing-notification-description") }}',
			time: 0
		});

		let response = await UserManagerAccount.resetPassword(postData, {
			target: 'account-button'
		});

		recentResponse = response;

		if (response.data.status !== 'success') {
			Notification.hideAndShowDelayed(n.data.id, {
				classes: [response.data.status],
				heading: response.data.heading,
				description: response.data.description
			});
		} else if (response.data.status === 'success') {
			let url = PREFIXED_URL + "/login"; 
			window.location.href = url;
		}
	}

	async function twoFactorAuthVerify() {
		let code = accountForm.querySelector("[name='code']").value;

		let n = Notification.show({
			heading: '{{ __("processing-notification-heading") }}',
			description: '{{ __("processing-notification-description") }}',
			time: 0
		});

		let response = await xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/setting/2fa/code/verify',
			body: {
				code: code,
				uid: uniqueId
			}
		});

		recentResponse = response;
		if (!isEmpty(response.data.data) && response.data.data !== undefined && response.data.data.redirect) window.location.href = response.data.data.redirect;

		if (response.data.status !== 'success' || response.data.user === undefined) {
			Notification.hideAndShowDelayed(n.data.id, {
				classes: [response.data.status],
				heading: response.data.heading,
				description: response.data.description
			});
		} else if (response.data.status === 'success' && response.data.user !== undefined) window.location.href = PREFIXED_URL + '/dashboard';

	}

	// Screen

	function changeRegistrationScreen(id) {

		let screen1 = document.querySelector('#registration-1');
		let screen2 = document.querySelector('#registration-2');

		let email = document.querySelector("[name='email']").value;
		let password = document.querySelector("[name='password']").value;
		let confirmPassword = document.querySelector("[name='confirm-password']").value;

		if (id === 'registration-1') {
			screen1.classList.remove("hide");
			screen2.classList.add("hide");

			screen2.querySelectorAll('.input-style-1').forEach(el => el.value = null);

		} else if (id === 'registration-2') {
			if (email === '' || password == '' || confirmPassword == '') {
				Notification.show({
					classes: ['fail'],
					text: "All fields are required"
				});
			} else {
				screen1.classList.add("hide");
				screen2.classList.remove("hide");
			}

		}

	}

	// Password Toggler

	function initPasswordToggler() {
		let passwordVisivilityTogglers = document.querySelectorAll(`[data-is="password-visibility-toggler"]`);

		for (element of passwordVisivilityTogglers) {
			element.addEventListener("click", toggleLoginPasswordVisibility);
		}
	}

	function toggleLoginPasswordVisibility() {

		let target = event.currentTarget;
		let passwordInput = target.closest('.form-group').querySelector('input');
		let passwordVisibility = false;

		if (passwordInput.getAttribute('type') == 'password') passwordVisibility = true;

		let use = target.querySelector('use');

		if (passwordVisibility === true) {
			use.setAttribute('xlink:href', eyeIconURL);
			passwordInput.setAttribute('type', 'text');
		} else {
			use.setAttribute('xlink:href', eyeOffIconURL);
			passwordInput.setAttribute('type', 'password');
		}
	}


	// Invoke

	initPasswordToggler();
</script>
@yield("layout-script")
@stop