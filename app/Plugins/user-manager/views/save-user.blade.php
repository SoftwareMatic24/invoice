@extends('layouts.portal')
@section('main-content')

<div class="grids grids-2">
	<div class="grid">
		<form class="section no-shadow" onsubmit="handleSaveUser()">
			<div class="section-body">
				<div class="{{ cache('settings')['profile-picture']['column_value'] == '0' ? 'hide' : 'grids grids-2 gap-2' }}">
					<div class="grid">
						<div class="form-group">
							<img onclick="chooseProfilePicture()" data-image="user-profile-picture" class="border-radius-round cursor-pointer _80x80" src="{{ asset('assets/avatar.png') }}" alt="profile picture" width="80" />
						</div>
					</div>
					<div class="grid"></div>
					<div class="grid"></div>
					<div class="grid"></div>
				</div>
				<div class="grids grids-2 gap-2">
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ ucwords(__("first name")) }} <span class="required">*</span></label>
							<input name="first-name" type="text" class="input-style-1">
						</div>
					</div>
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ ucwords(__("last name")) }}</label>
							<input name="last-name" type="text" class="input-style-1">
						</div>
					</div>
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ ucwords(__("email")) }} <span class="required">*</span></label>
							<input name="email" type="text" class="input-style-1">
						</div>
					</div>
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ __("status") }}</label>
							<div class="select-container chevron">
								<div class="custom-select-container">
									<select name="status" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										<option value="active">{{ __('active') }}</option>
										<option value="inactive">{{ __('inactive') }}</option>
										<option value="banned">{{ __('banned') }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ __("role") }} <span class="required">*</span></label>
							<div class="select-container chevron">
								<div class="custom-select-container">
									<select name="role" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach($roles as $role)
										<option value="{{ $role['title'] }}">{{ ucfirst($role['title']) }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="grid">
						<div class="form-group">
							<label class="input-style-1-label">{{ __("phone") }}</label>
							<input name="phone" type="text" class="input-style-1">
						</div>
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ __('date of birth') }}</label>
						<input name="dob" type="date" forma class="input-style-1">
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ ucwords(__("new password")) }} <span class="required">*</span></label>
						<div class="form-group has-right-icon">

							<div class="input-wrapper">
								<svg onclick="generatePassword()" title="Generate Password" class="generate-password-icon">
									<use xlink:href="{{ asset('assets/icons.svg#solid-generate') }}" />
								</svg>
								<input autocomplete="new-password" name="password" type="text" class="input-style-1">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="section-footer">
				<button data-xhr-name="save-button"  data-xhr-loading.attr="disabled" type="submit" class="button button-primary">{{ ucwords(__("save user")) }}</button>
			</div>
		</form>
	</div>
	<div class="grid"></div>
</div>

@stop

@section('page-script')

{!! loadPluginFile('js/script.js', 'user-manager') !!}

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (!isEmpty(staticUserId())) populateUser(staticUser())
	}

	function handleSaveUser(){
		event.preventDefault();
		saveUser();
	}

	/**
	 * Static Data
	 */

	function staticUserId() {
		let userId = '{{ $userId ?? "" }}';
		return userId;
	}

	function staticRoles() {
		let roles = '{!! addSlashes(json_encode($roles)) !!}';
		return JSON.parse(roles);
	}

	function staticUser() {
		let user = '{!! addSlashes(json_encode($user)) !!}';
		return JSON.parse(user);
	}

	/**
	 * Save
	 */

	async function saveUser() {

		let firstNameEl = document.querySelector('[name="first-name"]');
		let lastNameEl = document.querySelector('[name="last-name"]');
		let emailEl = document.querySelector('[name="email"]');
		let passwordEl = document.querySelector('[name="password"]');
		let phoneEl = document.querySelector('[name="phone"]');
		let dobEl = document.querySelector('[name="dob"]');
		let statusEl = document.querySelector('[name="status"]');
		let roleEl = document.querySelector('[name="role"]');
		let imageEl = document.querySelector('[data-image="user-profile-picture"]');

		let postData = {
			firstName: firstNameEl.value,
			lastName: lastNameEl.value,
			email: emailEl.value,
			password: passwordEl.value,
			status: statusEl.value,
			roleTitle: roleEl.value,
			image: imageEl.dataset.src || null,
			phone: phoneEl.value,
			dob: dobEl.value
		};

		let n = showSavingNotification();
		let response = await UserManager.saveUser(staticUserId(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = PREFIXED_URL + '/user-manager/manage';
	}

	/**
	 * Populate
	 */

	function populateUser(user) {
		document.querySelector('[name="first-name"]').value = user.first_name;
		document.querySelector('[name="last-name"]').value = user.last_name;
		document.querySelector('[name="email"]').value = user.email;
		document.querySelector('[name="status"]').value = user.status;
		document.querySelector('[name="role"]').value = user.role_title;
		document.querySelector('[name="phone"]').value = user.phone;
		document.querySelector('[name="dob"]').value = user.dob;

		if (user.image !== null) {
			document.querySelector('[data-image="user-profile-picture"]').setAttribute('src', BASE_URL + '/storage/' + user.image);
			imageURL = user.image;
		}

		user.details.forEach(row => {
			if (pageForm.querySelector(`[name="${row.column_name}"]`) !== null) {
				pageForm.querySelector(`[name="${row.column_name}"]`).value = row.column_value;
			}
		});
	}

	/**
	 * Other
	 */

	function chooseProfilePicture() {
		mediaCenter.show({
			useAs: {
				title: '{{ __("set user profile picture") }}',
				max: 1,
				mediaType: 'image',
				onUse: async function(params = []) {
					let media = params.media;
					imageURL = media[0].url;
					document.querySelectorAll('[data-image="user-profile-picture"]').forEach(element => {
						element.setAttribute("src", BASE_URL + '/storage/' + imageURL);
						element.setAttribute("data-src", imageURL);
					});
				}
			}
		});
	}
</script>
@stop