<p class="grid-widget-text">{{ __('you are adding content for') }} <span data-is="primary-language" class="underline"></span> {{ strtolower(__('language')) }}.</p>
<p class="grid-widget-text | margin-top-1"><span onclick="showChooseLanguageModal()" class="underline cursor-pointer">{{ __('click here') }}</span> {{ __('to change language') }}.</p>

<!-- Modal -->
<div id="choose-language-modal" class="modal" style="max-width: min(50rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">{{ __('choose language') }}</p>
		<span onclick="hideModal('choose-language-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="max-height:70vh;">
		<form action="#" onsubmit="return false;">
			<div class="form-group">
				<div class="custom-select-container">
					<select name="component-lang" class="input-style-1">
						@foreach(cache('languages') as $language)
						<option value="{{ $language['code'] }}">{{ $language["name"] }}</option>
						@endforeach
					</select>
				</div>
			</div>
			
		</form>
	</div>
	<div class="modal-footer | d-flex justify-content-end">
		<button onclick="chooseLanguage()" class="button button-primary">{{ __('select') }}</button>
	</div>
</div>

<script>
	var pageLanguage = null;
	var pageLanguages = null;
	let chooseLanguageCallback = null;

	function initChooseLanguageComponent(languages, callback){
		let modalEl = document.querySelector('#choose-language-modal');
		let primarylanguageEl = document.querySelector('[data-is="primary-language"]');
		let langEl = modalEl.querySelector('[name="component-lang"]');
		let primaryLanguage = languages.find(lang => lang.type === 'primary');

		primarylanguageEl.innerHTML = primaryLanguage.name;
		langEl.value = primaryLanguage.code;
		
		pageLanguages = languages;
		chooseLanguageCallback = callback;
	}

	function chooseLanguage(){
		let modalEl = document.querySelector('#choose-language-modal');
		let primarylanguageEl = document.querySelector('[data-is="primary-language"]');
		let langEl = modalEl.querySelector('[name="component-lang"]');
		
		pageLanguage = pageLanguages.find(lang => lang.code === langEl.value);
		primarylanguageEl.innerHTML = pageLanguage.name;

		chooseLanguageCallback(pageLanguage);
		hideModal('choose-language-modal');
	}

	function showChooseLanguageModal(){
		
		let primaryLanguage = pageLanguages.find(lang => lang.type === 'primary');
		
		if(pageLanguage === null) {
			Notification.show({
				heading: '{{ __("action-required") }}',
				description: `{{ __('please add content for') }} ${primaryLanguage.name} {{ __('language before choosing other language') }}.`,
				classes: ['fail']
			});
			return;
		};

		showModal('choose-language-modal');
	}

</script>