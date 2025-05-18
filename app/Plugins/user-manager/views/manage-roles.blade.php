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
			<a href="{{ url('/portal/user-manager/roles/save') }}" class="button button-primary with-plus-icon">
				{{ ucwords(__("new role")) }}
			</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("role") }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/script.js', 'user-manager') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateRoles(staticRoles());
	}

	/**
	 * Static data
	 */

	function staticRoles() {
		let roles = '{!! addSlashes(json_encode(Role::roles())) !!}';
		return JSON.parse(roles);
	}

	/**
	 * Fetch
	 */

	async function fetchRoles() {
		let response = await UserManager.roles();
		return response.data;
	}

	async function fap() {
		let roles = await fetchRoles();
		populateRoles(roles);
	}

	/**
	 * Delete
	 */

	async function deleteRole(roleId) {
		let n = showDeletingNotification();
		let response = await UserManager.deleteRole(roleId);
		showResponseNotification(n, response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateRoles(roles) {
		let roleData = roles.map((role, roleIndex) => {
			return [{
					type: 'text',
					value: (roleIndex + 1)
				},

				{
					type: 'text',
					value: role.title
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/user-manager/roles/save/' + role.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteRole(role.id);
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

		pageTable.init(roleData);
	}
</script>
@parent
@stop