@extends('layouts.portal')
@section('main-content')
<div class="grids grids-2">
	<div class="grid">
		<form action="#" onsubmit="handleRoleSave()" class="section no-shadow">
			<div class="section-body">
				<div class="form-group">
					<div class="grids grids-2 gap-3">
						<div class="grid">
							<label class="input-style-1-label">{{ ucwords(__("role title")) }} <span class="required">*</span></label>
							<input name="title" type="text" class="input-style-1">
						</div>
						<div class="grid"></div>
					</div>
				</div>
			</div>
			<div class="section-footer">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" type="submit" class="button button-primary">{{ ucwords(__("save role")) }}</button>
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
		if (!isEmpty(staticRoleId())) populateRole(staticRole());
	}

	function handleRoleSave() {
		event.preventDefault();
		saveRole();
	}

	/**
	 * Static data
	 */

	function staticRoleId() {
		let roleId = '{{ $roleId ?? "" }}';
		return roleId;
	}

	function staticRole(){
		let role = `{!! addSlashes(json_encode(Role::role($roleId))) !!}`;
		return JSON.parse(role);
	}

	/**
	 * Save
	 */

	async function saveRole() {

		let titleEl = document.querySelector('[name="title"]');

		let postData = {
			title: titleEl.value
		};

		let n = await showSavingNotification();
		let response = await UserManager.saveRole(staticRoleId(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = PREFIXED_URL + '/user-manager/roles';
	}

	/**
	 * Populate
	 */

	function populateRole(role) {
		document.querySelector('[name="title"]').value = role.title;
	}

</script>
@stop