@extends('layouts.portal')
@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('language') }}</label>
								<div class="custom-select-container">
									<select name="language" class="input-style-1">
										@foreach($languages as $code=>$language)
										<option value="{{ $code }}">{{ $language }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }}</label>
								<div class="custom-select-container">
									<select name="status" class="input-style-1">
										<option value="active">{{ __('active') }}</option>
										<option value="inactive">{{ __('inactive') }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('type') }}</label>
								<div class="custom-select-container">
									<select name="type" class="input-style-1">
										<option value="secondary">{{ __('secondary') }}</option>
										<option value="primary">{{ __('primary') }}</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('direction') }}</label>
								<div class="custom-select-container">
									<select name="direction" class="input-style-1">
										<option value="ltr">LTR</option>
										<option value="rtl">RTL</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveLanguage()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'language') }}

<script>

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		if(!isEmpty(staticLanguage())) populateLanguage(staticLanguage());
	}

	/**
	 * Static data
	 */

	function staticLanguageCode(){
		return '{{ $languageCode ?? "" }}';
	}

	function staticLanguage(){
		let language = '{!! addSlashes(json_encode(dbLanguage($languageCode ?? ""))) !!}';
		return JSON.parse(language);
	}

	/**
	 * Save
	 */

	async function saveLanguage() {
		let languageEl = document.querySelector('select[name="language"]');
		let statusEl = document.querySelector('select[name="status"]');
		let typeEl = document.querySelector('select[name="type"]');
		let directionEl = document.querySelector('select[name="direction"]');

		let languageName = languageEl.options[languageEl.selectedIndex].innerHTML;
		let languageCode = languageEl.value;


		let postData = {
			name: languageName,
			code: languageCode,
			type: typeEl.value,
			status: statusEl.value,
			direction: directionEl.value
		};


		let n = showSavingNotification();
		let response = await Language.saveLanguage(staticLanguageCode(), postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}

	/**
	 * Populate
	 */

	function populateLanguage(language) {
		let languageEl = document.querySelector('select[name="language"]');
		let typeEl = document.querySelector('select[name="type"]');
		let statusEl = document.querySelector('select[name="status"]');
		let directionEl = document.querySelector('select[name="direction"]');
		
		languageEl.value = language.code;
		typeEl.value = language.type;
		statusEl.value = language.status;
		directionEl.value = language.direction;
	}

</script>
@stop