@extends('layouts.portal')

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<label class="input-style-1-label">{{ __('google client id') }}</label>
						<input name="google-client-id" type="text" class="input-style-1">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">{{ __('google client secret') }}</label>
						<input name="google-client-secret" type="text" class="input-style-1">
					</div>
					<div class="form-group">
						<div class="grids grids-2 | gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }}</label>
								<div class="custom-select-container">
									<select name="status" class="input-style-1">
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
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveExternalIntegration()" class="button button-primary button-block">{{ __('update') }}</button>
				</div>
			</div>
		</div>
		<div class="grid-widget | margin-bottom-2">
			<p class="grid-widget-text"><b>{{ __('authorized redirect uri') }}</b></p>
			<p class="grid-widget-text | margin-top-2">{{ __('add-following-url-to-google-cloud-app-redirect-uri') }}</p>
			<input type="text" value="{{ url('/with/google/callback') }}" class="input-style-1 | margin-top-2" readonly>
		</div>
	</div>

	@stop

	@section('page-script')

	{{ loadPluginFile('js/script.js', 'setting') }}

	<script>
		document.addEventListener('DOMContentLoaded', init);

		function init() {
			populateIntegration(staticIntegration());
		}

		/**
		 * Static data
		 */

		function staticIntegration() {
			
		}

		/**
		 * Save
		 */

		async function saveExternalIntegration() {

			let clientId = document.querySelector('[name="google-client-id"]').value;
			let clientSecret = document.querySelector('[name="google-client-secret"]').value;
			let status = document.querySelector('[name="status"]').value;

			let postData = {
				status,
				details: {
					'google-client-id': clientId,
					'google-client-secret': clientSecret
				}
			};

			let n = showSavingNotification();
			let response = await Setting.saveExternalIntegration(postData, 'google-oauth', {
				target: 'save-button'
			});
			showResponseNotification(n, response);
		}

		/**
		 * Populate
		 */

		function populateIntegration(data) {
			let clientIdEl = document.querySelector('[name="google-client-id"]');
			let clientSecretEl = document.querySelector('[name="google-client-secret"]');
			let statusEl = document.querySelector('[name="status"]');

			statusEl.value = response.data.status;

			data.forEach(row => {
				let el = document.querySelector(`[name="${row.column_name}"]`);
				if (el !== null) el.value = row.column_value;
			});
		}
	</script>
	@stop