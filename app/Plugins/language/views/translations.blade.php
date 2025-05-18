@extends('layouts.portal')

@section('page-style')
<style>
	.accordion.active .accordion-body.e-loader {
		padding: 4rem 2rem;
	}
</style>
@stop

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="margin-top-3">
			<div data-type="theme" data-name="none" class="accordion | margin-top-2">
				<div onclick="handleAccordionClick()" class="accordion-header">
					<h2 class="accordion-title">{{ ucwords(__('theme translation')) }}</h2>
					<svg class="icon plus">
						<use xlink:href="{{ asset('/assets/icons.svg#solid-plus') }}" />
					</svg>
					<svg class="icon minus">
						<use xlink:href="{{ asset('/assets/icons.svg#solid-minus') }}" />
					</svg>
				</div>
				<div class="accordion-body">
				</div>
			</div>
		</div>
		<div id="dynamic-accordion-container"></div>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-is="publish-button" onclick="saveTranslation()" class="button button-primary button-block">{{ __('update') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'language') }}

<script>
	let languages = '{!! $languages ?? [] !!}';
	let activeLanguages = '{!! $activeLanguages ?? [] !!}';
	let eloader = new eLoader();
	let translations = [];

	languages = JSON.parse(languages);
	activeLanguages = JSON.parse(activeLanguages);


	/**
	 * Fetch
	 */

	async function fetchLanguageFiles(type, name) {
		let postData = {type, name};
		let response = await Language.translations(postData);
		return response.data;
	}

	async function fetchMainLanguage() {
		let t = await fetchLanguageFiles('main', 'none');
		if (isEmpty(t)) return;

		let lang = {};

		for (key in t.main) {
			lang[key] = JSON.parse(t.main[key]);
		}

		translations.push({
			type: 'main',
			name: 'none',
			data: lang
		})
	}

	async function fetchPluginSlugsWithLanguage() {
		let response = await Language.languagePluginSlugs();

		response.data.forEach((pluginSlug) => {
			generateAccordion('plugin', pluginSlug);
		});

		initAccordions();
	}

	async function saveTranslation(){

		let postData = accorionsData();

		let n = showSavingNotification();
		let response = await Language.saveTranslations(postData, {target: 'save-button'});
		showResponseNotification(n, response);

		
		if(response.data.status === 'success') window.location.reload();
	}
	


	// Accordion

	async function handleAccordionClick() {
		let target = event.target;
		let accordionEl = target.closest('.accordion');
		let accordionBodyEl = accordionEl.querySelector('.accordion-body');

		let mainTranslation = translations.find(row => row.type === 'main');

		if (mainTranslation === undefined) {
			Notification.show({
				text: notificationTexts.processing
			})
			return;
		}

		let type = accordionEl.dataset.type;
		let name = accordionEl.dataset.name;
		
		if (!accordionEl.classList.contains('active')) {
			// open
			eloader.show(accordionBodyEl);

			let translationData = translations.find(row => {
				if (row.type === type && row.name === name) return true;
			});

			if (translationData === undefined) {
				let t = await fetchLanguageFiles(type, name);

				let lang = {};

				for (let key in t[type]) {
					lang[key] = JSON.parse(t[type][key]);
				}

				for (let key in t[name]) {
					lang[key] = JSON.parse(t[name][key]);
				}

				translations.push({
					name: name,
					type: type,
					data: lang
				});

				translationData = lang;
			} else translationData = translationData.data;


			let translationDataLanguageCodes = Object.keys(translationData);
			
			activeLanguages.forEach(activeLanguage=>{
				if(!translationDataLanguageCodes.includes(activeLanguage.code) && translationDataLanguageCodes.length > 0){
					let languageOne = translationData[translationDataLanguageCodes[0]];
					let languageOneKeys = Object.keys(languageOne);
					languageOneKeys.forEach(key=>{
						if(translationData[activeLanguage.code] === undefined) translationData[activeLanguage.code] = {};
						translationData[activeLanguage.code][key] = '';
					});
				}
			});



			populateTranslationAccordion(type, name, translationData);

			eloader.hide(accordionBodyEl);
		} else {
			eloader.hide(accordionBodyEl);
		}
	}

	function populateTranslationAccordion(type, name, data) {

		let themeAccordionEl = document.querySelector('[data-type="theme"][data-name="none"]');
		let themeAccordionBodyEl = themeAccordionEl.querySelector('.accordion-body');


		let mainTranslation = translations.find(row => row.type === 'main');
		if (mainTranslation === undefined) return;
		mainTranslation = mainTranslation.data;

		
		let view = ``;
		let languageCounter = 0;
		for (let key in data) {

			let languageCode = key.replace('-original','');

			view += `<div class="${languageCounter > 0 ? 'margin-top-2' : ''}" style="background-color:var(--clr-neutral-200); padding:1rem;border-radius:0.4rem">
						<b class="input-style-1-label underline" style="margin:0;">${(languageCounter + 1)}. ${languages[languageCode]}</b>
				</div>`;

			view += '<div class="grids" style="flex-wrap:wrap;gap:2rem;">';
			for (label in data[key]) {

				if (label.includes('=====')) continue;

				let value = '';
				if (mainTranslation[languageCode] !== undefined && mainTranslation[languageCode][label] !== undefined) value = mainTranslation[languageCode][label];
				
				view += `
					<div class="margin-top-2" style="width:calc(33.33% - 1.33rem)">
						<label class="input-style-1-label">${slugToText(label)}</label>
						<input class="input-style-1" type="text" data-language-code="${languageCode}" data-name="${label}" value="${value}" />
					</div>
				`;
			}

			view += '</div>';

			languageCounter++;
		}


		view = `<form>${view}</form>`;

		if (type === 'theme') themeAccordionBodyEl.innerHTML = view;
		else if(type === 'plugin' || type === 'system') {
			
			let accordionEl = document.querySelector(`[data-type="${type}"][data-name="${name}"]`);
			let accordionBodyEl = accordionEl.querySelector('.accordion-body');

			accordionBodyEl.innerHTML = view;
		}
		else if(type === 'system'){

		}
	}

	function generateAccordion(type, name) {

		let accordionContainerEl = document.querySelector('#dynamic-accordion-container');

		let view = `
			<div data-type="${type}" data-name="${name}" class="accordion | margin-top-2">
				<div onclick="handleAccordionClick()" class="accordion-header">
					<h2 class="accordion-title">${slugToText(name)} ${slugToText(type)} Translation</h2>
					<svg class="icon plus">
						<use xlink:href="{{ asset('/assets/icons.svg#solid-plus') }}" />
					</svg>
					<svg class="icon minus">
						<use xlink:href="{{ asset('/assets/icons.svg#solid-minus') }}" />
					</svg>
				</div>
				<div class="accordion-body">

				</div>
			</div>
		`;

		accordionContainerEl.insertAdjacentHTML('beforeend', view);
	}

	function accorionsData(){
		let data = {};
		let accordionEls = document.querySelectorAll('.accordion[data-type][data-name]');
	
		accordionEls.forEach(accordionEl => {
			let inputEls = accordionEl.querySelectorAll('input[type="text"][data-name][data-language-code]');
			inputEls.forEach(inputEl => {
				let label = inputEl.dataset.name;
				let languageCode = inputEl.dataset.languageCode;
				let value = inputEl.value;
				if(data[languageCode] === undefined) data[languageCode] = [];
				data[languageCode].push({ label, value});
			});
		});

	
		return data;
	}

	fetchMainLanguage();
	generateAccordion('system', 'global');
	fetchPluginSlugsWithLanguage();
</script>

@stop