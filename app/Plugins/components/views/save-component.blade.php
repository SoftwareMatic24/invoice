@extends('layouts.portal')

@section('page-style')
{{ loadPluginFile('css/style.css', 'components') }}
@stop

@php
$component = Component::component($componentSlug);
@endphp

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">

		<div class="section | no-shadow">
			<form class="section-body | padding-0" onsubmit="return false;">
				<div class="form-group">
					<div class="grids grids-2 gap-2">
						<div class="grid">
							<label class="input-style-1-label">{{ __("title") }} <span class="required">*</span></label>
							<input name="title" type="text" class="input-style-1">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __("visibility") }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="visibility" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									<option value="visible">{{ __('visible') }}</option>
									<option value="hidden">{{ __('hidden') }}</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

		<div class="section | margin-top-4 no-shadow">
			<div class="section-body | padding-0">
				<div id="sections" class="component-sections"></div>
				<button onclick="handleAddSection()" class="button button-primary-border | margin-top-4">{{ __('add new section') }}</button>
			</div>
		</div>

	</div>

	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button onclick="saveComponent()" class="button button-primary button-block">{{ ucwords(__("save component")) }}</button>
			</div>
		</div>
		<div class="grid-widget | margin-bottom-2">
			@include("components/page-language-select")
		</div>
		<div id="instructions-grid-widget"></div>
	</div>
</div>
@stop

@section('page-script')
@parent

<script src="{{ asset('js/ckeditor.js') }}"></script>

{{ loadPluginFile('js/script.js', 'components') }}

<script>
	let sections = [];
	let schema = null;
	let componentSlug = '{{ $componentSlug }}';

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (pageLanguage === null) pageLanguage = primaryLanguage();
		populateComponent(staticComponent());
	}

	initChooseLanguageComponent(languages(), async function(selectedLanguage) {
		let component = await fetchComponent(componentSlug);
		populateComponent(component, pageLanguage.code);
	});

	// Fetch

	async function fetchComponent(componentSlug) {
		let response = await Component.componentBySlug(componentSlug);
		return response.data;
	}

	// Save

	async function saveComponent() {

		let titleEl = document.querySelector('input[name="title"]');
		let visibilityEl = document.querySelector('select[name="visibility"]');

		let postData = {
			title: titleEl.value,
			visibility: visibilityEl.value,
			sections: sections,
			languageCode: pageLanguage.code
		};

		let n = showSavingNotification();
		let response = await Component.saveComponent(componentSlug, postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if(response.data.status === 'success') window.location.href = '{!! $backURL; !!}';

	}

	// Get

	function staticComponent() {
		let component = `{!! addSlashes(json_encode($component)) !!}`;
		return JSON.parse(component);
	}

	function languages() {
		let languages = '{!! addSlashes(json_encode(languages())) !!}';
		return JSON.parse(languages);
	}

	function primaryLanguage() {
		return languages().find(lang => lang.type === 'primary');
	}


	// Handlers

	function handleAddSection() {
		let section = formatSectionGroups(staticComponent().groups);
		section = section.map(s => [s]);
		addSection(section);
	}

	function handleRemoveSection(sectionIndex) {
		sections.splice(sectionIndex, 1);
		renderSections(sections);
	}

	function handleAddGroupItem(groupId, sectionIndex) {
		let s = schema.find(s => s.groupId == groupId);
		let item = JSON.parse(JSON.stringify(s.fields));

		addGroupItem(groupId, sectionIndex, item);
	}

	function handleRemoveSubGroup(groupId, sectionIndex, subGroupIndex) {
		removeSubGroup(groupId, sectionIndex, subGroupIndex);
	}

	function handleInput(sectionIndex, groupIndex, subGroupIndex, fieldIndex) {
		let targetEl = event.target;
		let value = targetEl.value;
		sections[sectionIndex][groupIndex][subGroupIndex][fieldIndex].value = value;
	}

	// Methods

	function addSection(section) {

		if (!canAddSection()) {
			return Notification.show({
				text: '{!! __("limit-reached-notification-heading") !!}',
				description: '{!! __("limit-reached-notification-description") !!}',
				classes: ['fail']
			});
		}

		sections.push(section);
		renderSections(sections);
	}

	function addGroupItem(groupId, sectionIndex, item) {

		if (!canAddGroupItem(groupId, sectionIndex)) {
			return Notification.show({
				text: '{!! __("limit-reached-notification-heading") !!}',
				description: '{!! __("limit-reached-notification-description") !!}',
				classes: ['fail']
			});
		}

		let groupIndex = findIndexForGroup(groupId, sectionIndex);
		sections[sectionIndex][groupIndex].push(item);
		renderSections(sections);
	}

	function removeSubGroup(groupId, sectionIndex, subGroupIndex) {
		let groupIndex = findIndexForGroup(groupId, sectionIndex);

		sections[sectionIndex][groupIndex].splice(subGroupIndex, 1);
		renderSections(sections);
	}

	function chooseImage(sectionIndex, groupIndex, subGroupIndex, fieldIndex) {
		mediaCenter.show({
			useAs: {
				title: '{!! __("set image") !!}',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;

					sections[sectionIndex][groupIndex][subGroupIndex][fieldIndex].value = media[0];
					renderSections(sections);
				}
			}
		});
	}

	function chooseVideo(sectionIndex, groupIndex, subGroupIndex, fieldIndex) {
		mediaCenter.show({
			useAs: {
				title: '{!! __("set image") !!}',
				max: 1,
				mediaType: 'video',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;

					sections[sectionIndex][groupIndex][subGroupIndex][fieldIndex].value = media[0];
					renderSections(sections);
				}
			}
		});
	}

	function populateComponent(component, languageCode) {

		let titleEl = document.querySelector('input[name="title"]');
		let visibilityEl = document.querySelector('select[name="visibility"]');

		titleEl.value = component.title;
		visibilityEl.value = component.visibility;

		schema = formatSchema(component);
		sections = formatSections(component, languageCode);

		if (sections.length === 0) handleAddSection();
		renderSections(sections);

	}

	// Formatters

	function formatSchema(component) {

		return component.groups.map((group) => {
			return {
				groupId: group.id,
				name: group.name,
				maxEntries: group.max_entries,
				fields: group.schema.map(row => {
					return {
						label: row.label,
						type: row.type,
						value: null,
						groupId: group.id
					}
				})
			}
		});
	}

	function formatSections(component, languageCode) {

		let primaryLang = primaryLanguage();
		let sections = component.f_data_sections;

		if (languageCode !== undefined && primaryLang.code != languageCode) sections = component.f_data_sections_i18n[languageCode];

		return sections.map(fSection => {
			return Object.values(fSection).map(groups => {
				return formatSectionGroups(groups);
			});
		});
	}

	function formatSectionGroups(groups) {
		return groups.map(field => {

			let schema = [];
			if (field._schema !== undefined) schema = field._schema.group.schema;
			else schema = field.schema;

			return schema.map(s => {
				return {
					label: s.label,
					value: field[s.label] == undefined ? null : field[s.label],
					type: s.type,
					groupId: s.component_group_id
				}
			});

		});
	}

	// Render

	function renderSections(sections) {
		let sectionEl = document.querySelector('#sections');

		let sectionsHTML = sections.map((section, sectionIndex) => {
			return sectionHTML(section, sectionIndex);
		}).join('');

		sectionEl.innerHTML = sectionsHTML;
	}

	function sectionHTML(section, sectionIndex) {
		let html = section.map(group => {
			return `<div class="component-sub-section">${groupHTML(group, sectionIndex)}</div>`;
		}).join('');

		return `<div class="component-section">
					<div class="component-section-header">
						&nbsp;
						<span onclick="handleRemoveSection('${sectionIndex}')">
							<svg class="label-remove-icon"><use xlink:href="{{ asset('assets/icons.svg#cross') }}"></use></svg>
						</span>
					</div>
					${html}
				</div>`;
	}

	function groupHTML(group, sectionIndex) {

		let html = group.map((fields, subGroupIndex) => {

			return `
					<form action="#" onsubmit="return false;">
						${fieldsHTML(fields, group[0][0].groupId, sectionIndex, subGroupIndex)}
						<div class="${subGroupIndex == 0 ? 'hide' : ''}"><button onclick="handleRemoveSubGroup('${group[0][0].groupId}', '${sectionIndex}', '${subGroupIndex}')" class="button button-danger-ghost no-padding">Remove</button></div>
					</form>`;
		}).join('');


		if (isMultipleGroupsPossible(group.length > 0 && group[0][0].groupId)) {
			html = `${html} <div><button onclick="handleAddGroupItem('${group[0][0].groupId}', '${sectionIndex}')" class="button button-primary-ghost underline no-padding">Add New ${groupName(group[0][0].groupId)}</button></div>`;
		}

		return html;
	}

	function fieldsHTML(fields, groupId, sectionIndex, subGroupIndex) {
		return fields.map((field, fieldIndex) => {
			return `<div class="form-group">${fieldHTML(field, groupId, sectionIndex, subGroupIndex, fieldIndex)}</div>`;
		}).join('');
	}

	function fieldHTML(field, groupId, sectionIndex, subGroupIndex, fieldIndex) {

		let groupIndex = findIndexForGroup(groupId, sectionIndex);

		let labelHTML = `<label class="input-style-1-label">${field.label}</label>`;
		let inputHTML = ``;

		if (field.type === 'string') {
			inputHTML = `<input oninput="handleInput('${sectionIndex}', '${groupIndex}', '${subGroupIndex}', '${fieldIndex}')" class="input-style-1" type="text" value="${toStr(field, 'value')}" />`;
		} else if (field.type === 'text') {
			inputHTML = `<textarea oninput="handleInput('${sectionIndex}', '${groupIndex}', '${subGroupIndex}', '${fieldIndex}')" class="input-style-1">${toStr(field, 'value')}</textarea>`;
		} else if (field.type === 'image') {
			let imageURL = `${BASE_URL}/assets/default-image-300x158.jpg`;

			if (!isEmpty(field.value) && !isEmpty(field.value.url)) imageURL = `${BASE_URL}/storage/${field.value.url}`;

			inputHTML = `
				<img onclick="chooseImage('${sectionIndex}', '${groupIndex}', '${subGroupIndex}', '${fieldIndex}')" class="cursor-pointer" style="width:10rem;height:10rem;object-fit:cover;border:0.1rem solid var(--clr-neutral-500);border-radius:0.4rem;" src="${imageURL}" />
			`;
		} else if (field.type === 'video') {

			let imageURL = `${BASE_URL}/assets/default-video-image-300x158.jpg`;
			let videoURL = null;

			if (!isEmpty(field.value) && !isEmpty(field.value.url)) videoURL = `${BASE_URL}/storage/${field.value.url}`;

			inputHTML = `
				<img onclick="chooseVideo('${sectionIndex}', '${groupIndex}', '${subGroupIndex}', '${fieldIndex}')" class="cursor-pointer" style="width:10rem;height:10rem;object-fit:cover;border:0.1rem solid var(--clr-neutral-500);border-radius:0.4rem;" src="${imageURL}" />
			`;

			if (!isEmpty(videoURL)) inputHTML = `<video class="cursor-pointer" onclick="chooseVideo('${sectionIndex}', '${groupIndex}', '${subGroupIndex}', '${fieldIndex}')" style="width:10rem;height:10rem;object-fit:cover;border:0.1rem solid var(--clr-neutral-500);border-radius:0.4rem;"><source src="${videoURL}" ></video>`;
		}

		return `${labelHTML}${inputHTML}`;
	}



	// Utils

	function groupName(groupId) {
		let group = schema.find(s => s.groupId == groupId);
		if (group === undefined) return null;
		return group.name;
	}

	function findIndexForGroup(groupId, sectionIndex) {

		let groups = sections[sectionIndex];
		let index = null;

		groups.forEach((group, groupIndex) => {
			group.forEach((subGroup, subGroupIndex) => {
				subGroup.forEach((field, fieldIndex) => {
					if (groupId == field.groupId && index == null) {
						index = groupIndex;
					}
				});
			});
		});

		return index;
	}

	// Checks

	function isMultipleGroupsPossible(groupId) {
		let group = schema.find(s => s.groupId == groupId);
		if (group === undefined) return false;

		if (group.maxEntries == 1) return false;
		return true;
	}

	function canAddGroupItem(groupId, sectionIndex) {

		let maxEntries = 1;

		let schemaObj = schema.find(s => s.groupId == groupId);
		if (schemaObj === undefined) maxEntries = 0;

		maxEntries = schemaObj.maxEntries;
		let groupIndex = findIndexForGroup(groupId, sectionIndex);

		if (maxEntries != null && sections[sectionIndex][groupIndex].length >= maxEntries) return false;

		return true;
	}

	function canAddSection() {
		let component = staticComponent();
		if (component.max_entries != null && sections.length >= component.max_entries) return false;
		return true;
	}
</script>
@stop