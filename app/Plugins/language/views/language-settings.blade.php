@extends('layouts.portal')
@section('main-content')
<div class="grids grids-2">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<div class="grids grids-2 gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('portal language') }}</label>
								<div class="custom-select-container">
									<select name="language" class="input-style-1">
										@foreach($languages as $language)
										@if($language["code"] == $portalLang)
										<option value="{{ $language['code'] }}" selected>{{ $language["name"] }}</option>
										@else
										<option value="{{ $language['code'] }}">{{ $language["name"] }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
				</form>
			</div>
			<div class="section-footer">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveLanguage()" class="button button-primary">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'language') }}

<script>
	async function saveLanguage() {
		let languageEl = document.querySelector('select[name="language"]');

		let postData = {
			language: languageEl.value
		};

		let n = showSavingNotification();
		let response = await Language.saveLanguageSetting(postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if (response.data.status == 'success') window.location.reload();
	}
</script>

@stop