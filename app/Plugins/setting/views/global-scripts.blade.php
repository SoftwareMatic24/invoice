@extends('layouts.portal')
@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" onsubmit="return false;">
			<div class="form-group">
				<label class="input-style-1-label">{{ __('head area') }}</label>
				<textarea name="global-script-head" class="input-style-1" rows="9">{{ urldecode($settings["global-scripts-head"]["column_value"] ?? "")}}</textarea>
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __('foot area') }}</label>
				<textarea name="global-script-foot" class="input-style-1" rows="9">{{ urldecode($settings["global-scripts-foot"]["column_value"] ?? "")}}</textarea>
			</div>
		</form>
	</div>
	<div class="grid">
		<div class="grid | flex-column-reverse-on-md">
			<div class="grid-widget | margin-bottom-2">
				<div class="button-group">
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveScripts()" class="button button-primary button-block">{{ __('update') }}</button>
				</div>
			</div>
			<div class="grid-widget | margin-bottom-2">
				<p class="grid-widget-text"><b>{{ __('note') }}:</b> {{ __('invalid-script-break-website') }}</p>
			</div>
		</div>
	</div>
</div>
@stop
@section('page-script')

{{ loadPluginFile('js/script.js', 'setting') }}

<script>
	async function saveScripts() {
		let head = document.querySelector('[name="global-script-head"]').value;
		let foot = document.querySelector('[name="global-script-foot"]').value;

		head = encodeURIComponent(head);
		foot = encodeURIComponent(foot);

		let n = showSavingNotification();
		let response = await Setting.updateGlobalScript({head, foot}, {target: 'save-button'});
		showResponseNotification(n, response);
	}
</script>

@stop