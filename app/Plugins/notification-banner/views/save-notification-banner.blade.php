@extends('layouts.portal')
@section('main-content')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<label class="input-style-1-label">{{ __('text') }}</label>
						<input name="text" type="text" class="input-style-1">
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }}</label>
								<div class="custom-select-container">
									<select name="status" class="input-style-1">
										<option value="active">{{ __('active') }}</option>
										<option value="inactive">{{ __('inactive') }}</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('type') }}</label>
								<div class="custom-select-container">
									<select name="type" class="input-style-1">
										<option value="portal">{{ __('portal') }}</option>
										<option value="web">{{ __('website') }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="section-footer">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveNotificationBanner()" class="button button-primary">{{ __('save') }}</button>
			</div>
		</div>

	</div>
	<div class="grid">
		<div class="grid-widget ">

			<form onsubmit="return false;" action="#">
				<div class="form-group">
					<label class="input-style-1-label">{{ __('background color') }}</label>
					<input name="bg-color" type="text" class="input-style-1" placeholder="HEX Code">
				</div>
				<div class="form-group">
					<label class="input-style-1-label">{{ __('text color') }}</label>
					<input name="color" type="text" class="input-style-1" placeholder="HEX Code">
				</div>
			</form>
		</div>
	</div>
</div>


@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'notification-banner') }}

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (!isEmpty(staticNotificationBanner())) populateNotificationBanner(staticNotificationBanner());
	}


	/**
	 * Static data
	 */

	function staticNotificaitonBannerId() {
		return `{!! $notificationBannerId ?? "" !!}`;
	}

	function staticNotificationBanner() {
		let response = '{!! addSlashes(json_encode(NotificationBanner::notificationBanner($notificationBannerId ?? ""))) !!}';
		return JSON.parse(response);
	}


	/**
	 * Save
	 */

	async function saveNotificationBanner() {
		let pageForm = document.querySelector('#page-form');
		let text = pageForm.querySelector('[name="text"]').value;
		let status = pageForm.querySelector('[name="status"]').value;
		let type = pageForm.querySelector('[name="type"]').value;
		let bgColor = document.querySelector('[name="bg-color"]').value;
		let color = document.querySelector('[name="color"]').value;

		let postData = {
			text,
			status,
			type,
			bgColor,
			color
		};

		let n = showSavingNotification();
		let response = await NotificationBanner.saveNotificationBanner(staticNotificaitonBannerId(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = `{{ $backURL }}`;

	}

	/**
	 * Populate
	 */


	function populateNotificationBanner(banner) {
		document.querySelector('[name="text"]').value = banner.text;
		document.querySelector('[name="status"]').value = banner.status;
		document.querySelector('[name="type"]').value = banner.type;

		if (banner.style !== null) {
			let style = JSON.parse(banner.style);
			document.querySelector('[name="bg-color"]').value = toStr(style, 'bgColor');
			document.querySelector('[name="color"]').value = toStr(style, 'color');
		}
	}
</script>

@stop