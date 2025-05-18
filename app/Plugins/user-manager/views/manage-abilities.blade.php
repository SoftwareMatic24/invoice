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
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("role") }}</th>
				<th>{{ __("privileges") }}</th>
				<th style="width:200px"></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="ability-modal" class="modal" style="max-width: min(60rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">{{ __("assign privilege") }}</p>
		<span onclick="hideModal('ability-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form onsubmit="assignAbility()" action="#">
			<div class="form-group">
				<label class="input-style-1-label">{{ __("role") }}</label>
				<input name="role" type="text" class="input-style-1" disabled>
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __("privilege") }}</label>
				<select name="ability" class="input-style-1">
					@foreach($abilities as $ability)
					<option value="{{ $ability['ability'] }}">{{ $ability["ability"] }}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group">
				<button data-xhr-name="assign-button" data-xhr-loading.attr="disabled" class="button button-sm button-block button-primary">{{ __("assign") }}</button>
			</div>
		</form>

	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/script.js', 'user-manager') !!}

<script>
	var pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateAbilities(staticRoles());
	}

	/**
	 * Static data
	 */

	function staticRoles() {
		let roles = '{!!  addSlashes(json_encode(Role::roles())) !!}';
		return JSON.parse(roles);
	}

	/**
	 * Fetch
	 */

	async function fetchRoles() {
		let response = await UserManager.roles();
		return response.data;
	}

	async function fap(){
		let roles = await fetchRoles();
		populateAbilities(roles);
	}

	/**
	 * Save
	 */

	async function assignAbility() {

		if (event) event.preventDefault();

		let modal = document.querySelector('#ability-modal');

		let role = modal.querySelector('[name="role"]').value;
		let ability = modal.querySelector('[name="ability"]').value;

		let postData = {
			role: role,
			abilities: [ability]
		};

		let n = showSavingNotification();
		let response = await UserManager.assignAbility(postData, {
			target: 'assign-button'
		})
		showResponseNotification(n,response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Delete
	 */

	async function removeAbility(role, ability) {
		let n = showDeletingNotification();
		let response = await UserManager.removeAbility({role, ability});
		showResponseNotification(n, response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateAbilities(roles) {
		let roleData = roles.map((role, roleIndex) => {

			let abilityList = role.abilities.map(ability => {
				return {
					id: role.id + '-' + ability.ability,
					text: ability.ability,
					remove: function() {
						Confirmation.show({
							positiveButton: {
								function: function() {
									removeAbility(role.title, ability.ability);
								}
							}
						});

					}
				}
			});

			return [{
					type: 'text',
					value: (roleIndex + 1)
				},

				{
					type: 'text',
					value: role.title
				},
				{
					type: 'list',
					value: abilityList
				},
				{
					type: 'button-group',
					value: [{
						text: '{{ ucwords(__("assign privilege")) }}',
						classes: ['button', 'button-primary', 'button-primary-border', 'button-text'],
						event: {
							'click': function() {
								showAbilityModal(role.title);
							}
						}
					}]
				}
			];
		});

		pageTable.init(roleData);
	}

	/**
	 * Other
	 */

	function showAbilityModal(role) {

		let modal = document.querySelector('#ability-modal');
		modal.querySelector('.modal-title').innerHTML = '{{ __("assign privilege") }} (' + role + ')';
		modal.querySelector('[name="role"]').value = role;
		showModal('ability-modal');
	}
</script>
@parent
@stop