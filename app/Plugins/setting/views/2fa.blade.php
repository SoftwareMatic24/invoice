@extends('layouts.portal')

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">

					<div class="form-group">
						<div class="grids grids-2 | gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="status" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										<option value="active">{{ __('active') }}</option>
										<option value="inactive">{{ __('inactive') }}</option>
									</select>
								</div>
							</div>
							<div class="grid"></div>
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
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveSettings()" class="button button-primary button-block">{{ __('update') }}</button>
				</div>
			</div>
		</div>
		<div class="grid-widget | margin-bottom-2">
			<p class="grid-widget-text"><b>{{ __('important') }}</b></p>
			<p class="grid-widget-text | margin-top-2">{{ __('2fa-login-attempt-code-1') }} {{ request()["loggedInUser"]["email"] }}. {{ __('2fa-login-attempt-code-2') }}</p>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'setting') }}

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populate2FA(static2FA());
	}

	/**
	 * Static data
	 */

	function static2FA() {
		let data = '{!! addSlashes(json_encode(Setting::get2FA(request()["loggedInUser"]["id"]))) !!}';
		return JSON.parse(data);
	}

	/**
	 * Save
	 */

	async function saveSettings() {
		let status = document.querySelector('[name="status"]').value;
		let postData = {
			status
		};

		let n = showSavingNotification();
		let response = await Setting.save2FA(postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);
	}

	/**
	 * Populate
	 */

	function populate2FA(data) {
		let statusEl = document.querySelector('[name="status"]');
		if (isEmpty(data)) return;
		let status = data.status;
		statusEl.value = status;
	}
</script>
@stop