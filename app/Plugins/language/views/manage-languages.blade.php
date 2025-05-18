@extends('layouts.portal')
@section('main-content')
<div class="data-table-container">
	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __('status') }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Status:active">{{ __('active') }}</option>
						<option value="Status:inactive">{{ __('inactive') }}</option>
					</select>
				</div>
			</div>
			<a href="{{ url('/portal/language') }}/save" class="button button-primary">{{ __('new language') }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('name') }}</th>
				<th>{{ __('code') }}</th>
				<th>{{ __('status') }}</th>
				<th>{{ __('type') }}</th>
				<th>{{ __('date') }}</th>
				<th>{{ __('action') }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

@stop

@section('page-script')

{!! loadPluginFile('js/script.js', 'language') !!}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateLanguages(staticLanguages());
	}

	/**
	 * Static data
	 */

	function staticLanguages() {
		let languages = '{!! addSlashes(json_encode(dbLanguages())) !!}';
		return JSON.parse(languages);
	}

	/**
	 * Fetch
	 */

	async function fetchLanguages() {
		let response = await Language.languages();
		return response.data;
	}

	async function fap() {
		let languages = await fetchLanguages();
		populateLanguages(languages);
	}

	/**
	 * Delete
	 */

	async function deleteLanguage(code) {

		let n = showDeletingNotification();
		let response = await Language.deleteLanguage(code);
		showResponseNotification(n, response);

		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateLanguages(languages) {
		let languageData = languages.map((language, languageIndex) => {

			return [{
					type: 'text',
					value: (languageIndex + 1)
				},
				{
					type: 'text',
					value: language.name
				},
				{
					type: 'text',
					value: language.code
				},
				{
					type: 'tag',
					itemClasses: [language.status === 'active' ? 'tag-success' : 'tag-warning'],
					value: language.status
				},
				{
					type: 'text',
					value: language.type
				},
				{
					type: 'text',
					value: toLocalDateTime(language.create_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/language/save/' + language.code
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteLanguage(language.code);
											}
										}
									});

								}
							}
						}
					]
				}
			];
		});

		pageTable.init(languageData);
	}
</script>
@parent
@stop