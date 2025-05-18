@extends('layouts.portal')

@section('page-style')
<link rel="stylesheet" href="{{ asset('css/ckeditor-child.css') }}">
@parent
@stop

@section('main-content')

@php
$user = User::user();
$additionalFields = Role::roleAdditionalFields($user['role_title']);
@endphp


<div class="form-section-grids-container">

	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('profile information') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('profile-information-sub') }}</p>
		</div>
		<div class="grid">
			<form class="section" onsubmit="handleProfileInformationSave()">
				<div class="section-body">
					<div class="form-group {{ cache('settings')['profile-picture']['column_value'] == '0' ? 'hide' : '' }}">
						@if(empty($user['image']))
						<img onclick="chooseProfilePicture()" data-image="profile-picture" class="border-radius cursor-pointer profile-picture-placeholder _80x80" src="{{ asset('assets/avatar.png') }}" alt="" width="80" />
						@else
						<img onclick="chooseProfilePicture()" data-image="profile-picture" class="border-radius cursor-pointer profile-picture-placeholder _80x80" src="{{ asset('storage/'. $user['image']) }}" width="80" />
						@endif
					</div>
					<div class="grids grids-2 gap-2 grids-1-md | margin-top-2">
						<div class="grid">
							<label class="input-style-1-label">{{ __('first name') }} <span class="required">*</span></label>
							<input name="first-name" type="text" class="input-style-1" value="{{ $user['first_name'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('last name') }}</label>
							<input name="last-name" type="text" class="input-style-1" value="{{ $user['last_name'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('email') }} <span class="required">*</span></label>
							<input name="email" type="email" class="input-style-1" value="{{ $user['email'] }}" autocomplete="off">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('phone') }}</label>
							<input name="phone" type="text" class="input-style-1" value="{{ $user['phone'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('date of birth') }}</label>
							<input name="dob" type="date" class="input-style-1" value="{{ $user['dob'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('slug') }}</label>
							<input name="slug" type="text" class="input-style-1" value="{{ $user['slug'] ?? '' }}">
						</div>
					</div>
				</div>
				<div class="section-footer">
					<button type="submit" data-xhr-name="save-profile-information-button" data-xhr-loading.attr="disabled" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</form>
		</div>
	</div>

	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('update password') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('update-password-sub') }}</p>
		</div>

		<div class="grid">
			<form class="section" onsubmit="handleUpdatePassword()">
				<div class="section-body">
					<div class="grids grids-2 gap-2 grids-1-md">
						<div class="grid">
							<label class="input-style-1-label">{{ __('new password') }} <span class="required">*</span></label>
							<div class="input-wrapper">
								<svg onclick="generatePassword()" title="Generate Password" class="generate-password-icon">
									<use xlink:href="{{ asset('assets/icons.svg#solid-generate') }}" />
								</svg>
								<input name="password" type="text" class="input-style-1" autocomplete="new-password">
							</div>
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('confirm password') }} <span class="required">*</span></label>
							<input name="confirm-password" type="text" class="input-style-1" autocomplete="new-password">
						</div>
					</div>
				</div>
				<div class="section-footer">
					<button data-xhr-name="save-password-button" data-xhr-loading.attr="disabled" type="submit" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</form>
		</div>
	</div>

	@if(User::userRole() === 'admin')
	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('role and status') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('role-and-status-sub') }}</p>
		</div>
		<div class="grid">
			<form class="section" onsubmit="handleRoleAndStatusSave()">
				<div class="section-body">
					<div class="grids grids-2 gap-2 grids-1-md">
						<div class="grid">
							<label class="input-style-1-label">{{ __('role') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="role" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									@foreach(Role::roles() as $role)
									@if($user['role_title'] == $role['title'])
									<option value="{{ $role['title'] }}" selected>{{ ucfirst($role['title']) }}</option>
									@else
									<option value="{{ $role['title'] }}">{{ ucfirst($role['title']) }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('status') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="status" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									<option value="active" @if($user['status']==='active' ) selected @endif>Active</option>
									<option value="inactive" @if($user['status']==='inactive' ) selected @endif>Inactive</option>
									<option value="banned" @if($user['status']==='banned' ) selected @endif>Banned</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="section-footer">
					<button data-xhr-name="save-role-and-status-button" data-xhr-loading.attr="disabled" type="submit" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</form>
		</div>
	</div>
	@endif

	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('about information') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('about-information-sub') }}</p>
		</div>
		<div class="grid">
			<div class="section">
				<div class="section-body">
					<textarea name="about"></textarea>
				</div>
				<div class="section-footer">
					<button onclick="handleUpdateAbout()" data-xhr-name="save-about-button" data-xhr-loading.attr="disabled" type="submit" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</div>
		</div>
	</div>

	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('address') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('address-information-sub') }}</p>
		</div>
		<div class="grid">
			<form class="section" onsubmit="handleUpdateAddress()">
				<div class="section-body">
					<div class="grids grids-2 gap-2 grids-1-md">
						<div class="grid">
							<label class="input-style-1-label">{{ __('address line') }} 1</label>
							<input name="line-1" type="text" class="input-style-1" value="{{ $user['address']['line_1'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('address line') }} 2</label>
							<input name="line-2" type="text" class="input-style-1" value="{{ $user['address']['line_2'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('town/city') }}</label>
							<input name="town-city" type="text" class="input-style-1" value="{{ $user['address']['town_city'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('province/state') }}</label>
							<input name="province-state" type="text" class="input-style-1" value="{{ $user['address']['state_province'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('post code') }}</label>
							<input name="post-code" type="text" class="input-style-1" value="{{ $user['address']['post_code'] ?? '' }}">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('country') }}</label>
							<div class="custom-select-container">
								<select name="country" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									@foreach(Constant::alpha2Countries() as $code=>$name)
									@if(($user['address']['country'] ?? NULL) == $code)
									<option value="{{ $code }}" selected>{{ $name }}</option>
									@else
									<option value="{{ $code }}">{{ $name }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="section-footer">
					<button data-xhr-name="save-address-button" data-xhr-loading.attr="disabled" type="submit" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</form>
		</div>
	</div>

	@if(!empty($additionalFields))
	<div class="form-section-grids">
		<div class="grid">
			<h2 class="heading heading-md">{{ __('other information') }}</h2>
			<p class="text text-sm | clr-neutral-600 margin-top-0-5">{{ __('other-information-sub') }}</p>
		</div>
		<div class="grid">
			<form class="section" onsubmit="handleUpdateAdditionalFields()">
				<div class="section-body">
					<div class="grids grids-1 gap-2 grids-1-md">
						@foreach($additionalFields as $field)
						<div class="grid">
							<label class="input-style-1-label">{{ __(strtolower($field['label'])) }}</label>
							@if($field['type'] === 'string')
							<input data-is="additional-field" name="{{ $field['slug'] }}" type="text" class="input-style-1">
							@elseif($field['type'] === 'text')
							<textarea data-is="additional-field" name="{{ $field['slug'] }}" class="input-style-1"></textarea>
							@endif
						</div>
						@endforeach
					</div>
				</div>
				<div class="section-footer">
					<button data-xhr-name="save-additional-button" data-xhr-loading.attr="disabled" type="submit" class="button  button-primary">{{ __('save') }}</button>
				</div>
			</form>
		</div>
	</div>
	@endif

</div>

@stop

@section('page-script')
{{ loadPluginFile('js/script.js', 'user-manager') }}

<script src="{{ asset('js/ckeditor.js') }}"></script>

<script>
	let aboutEditor = null;

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		initAboutEditor();
		populateAdditionalFields(staticUser());
	}

	function initAboutEditor() {
		ClassicEditor.create(document.querySelector(`textarea[name="about"]`), {
				licenseKey: '',
				initialData: staticUser().about || '',
			})
			.then(editor => {
				aboutEditor = editor;
			})
			.catch(error => {
				console.error(error);
			});
	}

	function staticUser() {
		let user = '{!! addSlashes(json_encode($user)) !!}';
		return JSON.parse(user);
	}

	async function handleProfileInformationSave() {
		event.preventDefault();

		let firstNameEl = document.querySelector('input[name="first-name"]');
		let lastNameEl = document.querySelector('input[name="last-name"]');
		let emailEl = document.querySelector('input[name="email"]');
		let phoneEl = document.querySelector('input[name="phone"]');
		let dobEl = document.querySelector('input[name="dob"]');
		let slugEl = document.querySelector('input[name="slug"]');

		let postData = {
			firstName: firstNameEl.value,
			lastName: lastNameEl.value,
			email: emailEl.value,
			phone: phoneEl.value,
			dob: dobEl.value,
			slug: slugEl.value
		}

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updateProfileInformation(postData, {
			target: 'save-profile-information-button'
		});

		showResponseNotification(n, response);

	}

	async function handleUpdatePassword() {
		event.preventDefault();

		let passwordEl = document.querySelector('input[name="password"]');
		let confirmPasswordEl = document.querySelector('input[name="confirm-password"]');

		let postData = {
			password: passwordEl.value,
			confirmPassword: confirmPasswordEl.value
		}

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updatePassword(postData, {
			target: 'save-password-button'
		});

		showResponseNotification(n, response);

	}

	async function handleRoleAndStatusSave() {
		event.preventDefault();

		let roleEl = document.querySelector('select[name="role"]');
		let statusEl = document.querySelector('select[name="status"]');

		let postData = {
			role: roleEl.value,
			status: statusEl.value
		}

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updateRoleAndStatus(postData, {
			target: 'save-role-and-status-button'
		});

		showResponseNotification(n, response);

	}

	async function handleUpdateAbout() {
		event.preventDefault();

		let postData = {
			about: aboutEditor.getData()
		};

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updateAbout(postData, {
			target: 'save-about-button'
		});

		showResponseNotification(n, response);

	}

	async function handleUpdateAddress() {
		event.preventDefault();

		let addressLine1El = document.querySelector("[name='line-1']");
		let addressLine2El = document.querySelector("[name='line-2']");
		let townCityEl = document.querySelector("[name='town-city']");
		let provinceStateEl = document.querySelector("[name='province-state']");
		let postCodeEl = document.querySelector("[name='post-code']");
		let countryEl = document.querySelector("[name='country']");

		let postData = {
			addressLine1: addressLine1El.value,
			addressLine2: addressLine2El.value,
			townCity: townCityEl.value,
			provinceState: provinceStateEl.value,
			postCode: postCodeEl.value,
			country: countryEl.value
		};

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updateAddress(postData, {
			target: 'save-address-button'
		});

		showResponseNotification(n, response);
	}

	async function handleUpdateAdditionalFields(){

		event.preventDefault();

		let postData = {};
		let fieldEls = document.querySelectorAll("[data-is='additional-field']");

		fieldEls.forEach(el => {
			postData[el.name] = el.value;
		});

		let n = Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});

		let response = await UserManager.updateAdditional(postData, {
			target: 'save-additional-button'
		});

		showResponseNotification(n, response);
	}

	function populateAdditionalFields(user){
		user.details.forEach(row => {
			let el = document.querySelector(`[name="${row.column_name}"]`);
			el.value = row.column_value;
		});
	}

	function chooseProfilePicture() {
		mediaCenter.show({
			useAs: {
				title: '{{ __("set-as-profile-picture") }}',
				max: 1,
				mediaType: 'image',
				onUse: async function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;

					let placeholders = document.querySelectorAll('[data-image="profile-picture"]');
					placeholders.forEach(placeholder => {
						placeholder.setAttribute('src', BASE_URL + '/storage/' + imageURL);
						placeholder.setAttribute('data-src', imageURL);
					});

					let postData = {
						url: imageURL
					};

					let n = Notification.show({
						heading: '{{ __("saving-notification-heading") }}',
						description: '{{ __("saving-notification-description") }}',
						time: 0
					});

					let response = await UserManager.updateProfilePicture(postData);

					showResponseNotification(n, response);

				}
			}
		});
	}
</script>
@endsection