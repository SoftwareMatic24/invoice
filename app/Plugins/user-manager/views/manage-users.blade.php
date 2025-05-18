@extends('layouts.portal')

@php
$users = User::users();
@endphp

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
				<label for="status-filter" class="input-style-1-label">{{ __("role") }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1" style="min-width: 13rem;">
						<option value="all">{{ __('all') }}</option>
						@foreach(Role::roles() as $role)
						<option value="Role:{{ $role['title'] }}">{{ ucwords($role['title']) }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __("status") }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1" style="min-width: 13rem;">
						<option value="all">{{ __('all') }}</option>
						<option value="Status:active">{{ __('active') }}</option>
						<option value="Status:inactive">{{ __('inactive') }}</option>
						<option value="Status:banned">{{ __('banned') }}</option>
					</select>
				</div>
			</div>
			<a href="{{ url('/portal/user-manager/save') }}" class="button button-primary with-plus-icon">
				{{ ucwords(__("new user")) }}
			</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("email") }}</th>
				<th>{{ __("status") }}</th>
				<th>{{ __("role") }}</th>
				<th>{{ ucwords(__("date registered")) }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="page-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group"></div>
	</div>

</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'user-manager') }}

<script>
	let pageTable = dataTable('page-table');
	let profilePictureSetting = "{{ cache('settings')['profile-picture']['column_value'] }}";

	/**
	 * Static data
	 */

	function staticUsers() {
		let users = '{!! addSlashes(json_encode($users)) !!}';
		return JSON.parse(users);
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let users = await fetchUsers();
		populateUsers(users);
	}

	async function fetchUsers() {
		let response = await UserManager.users();
		return response.data;
	}

	/**
	 * Delete
	 */

	async function deleteUser(userId) {
		let n = showDeletingNotification();
		let response = await UserManager.deleteUser(userId);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateUsers(users) {
		let userData = users.map((user, userIndex) => {
			return [{
					type: 'text',
					value: (userIndex + 1)
				},
				{
					type: 'text',
					value: user.first_name + ' ' + (user.last_name !== null ? user.last_name : '')
				},
				{
					type: 'text',
					value: user.email
				},
				{
					type: 'tag',
					itemClasses: [user.status === 'active' ? 'tag-success' : 'tag-warning'],
					value: capitalize(user.status)
				},
				{
					type: 'text',
					value: user.role_title
				},
				{
					type: 'text',
					value: toLocalDateTime(user.create_datetime)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							event: {
								'click': function() {
									showUserModal(user)
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/user-manager/save/' + user.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteUser(user.id);
											}
										}
									});

								}
							}
						}
					]
				}
			];
		});
		pageTable.init(userData);
	}

	/**
	 * Other
	 */

	async function showUserModal(user) {
		let pageModal = document.querySelector('#page-modal');
		pageModal.querySelector('.modal-title').innerHTML = user.first_name + ' ' + (user.last_name !== null ? user.last_name : '');

		let ip = ''
		if (user.log[0] !== undefined) {
			ip = user.log[0].ip;
			let countryPromise = getCountryByIP(ip);

			countryPromise.then(response => {
				if (response.status === 'fail') return;
				pageModal.querySelector('[data-is="ip-country"]').innerHTML = response.country;
				pageModal.querySelector('[data-is="ip-city"]').innerHTML = response.city;
			});
		}

		let layout = `
			<p><b>{{ __("ip") }}:</b> ${ip}</p>
			<p><b>{{ __("ip country") }}:</b> <span data-is="ip-country"></span></p>
			<p><b>{{ __("ip city") }}:</b> <span data-is="ip-city"></span></p>
			<p><b>{{ __("email") }}:</b> ${user.email}</p>
			<p><b>{{ __("status") }}:</b> ${user.status}</p>
			<p><b>{{ __("role") }}:</b> ${user.role_title}</p>
			<p><b>{{ ucwords(__("date registered")) }}:</b> ${toLocalDateTime(user.create_datetime, true)}</p>
			<p><b>{{ ucwords(__("last update")) }}:</b> ${toLocalDateTime(user.update_datetime, true)}</p>
		`;

		pageModal.querySelector('.modal-text-group').innerHTML = layout;
		showModal('page-modal');
	}

	populateUsers(staticUsers());
</script>
@parent
@stop