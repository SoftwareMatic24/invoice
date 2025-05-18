@extends('layouts.portal')
@section('main-content')
<p>{{ __('manage-settings-from-below') }}</p>
<div class="table-1-container affiliate-hive-campaign-table-container | margin-top-3">
	<div class="table-1-header">
		<h2 class="heading">{{ __('settings') }}</h2>
	</div>
	<div class="table-1-body">
		<table>
			<tbody id="settings"></tbody>
		</table>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'setting') }}

<script>
	let generalSettings = [];

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		setGenerateSettings(staticSettings());
		populateSettingsTable();
	}

	/**
	 * Static data
	 */

	function staticSettings() {
		let settings = '{!! addSlashes(json_encode(Setting::settings())) !!}';
		return JSON.parse(settings);
	}

	/**
	 * Save
	 */

	async function updateSettingsStatus(id) {
		let checked = false;
		if (id !== -1) checked = document.querySelector(`#${id}`).checked;
		else checked = document.querySelector(`#all-rss`).checked;

		Setting.updateSetting({name: id, value: checked});

		generalSettings = generalSettings.map(row => {
			if (row.id == id) row.active = checked
			return row;
		})

		populateSettingsTable();
	}

	/**
	 * Populate / Set
	 */

	function setGenerateSettings(settings) {
		let noIndexNoFollow = settings['noindex-nofollow'];
		let userRegistration = settings['user-registration'];
		let forceHTTPS = settings['force-https'];
		noIndexNoFollow = (noIndexNoFollow == undefined || noIndexNoFollow.column_value == '0') ? false : true;
		userRegistration = (userRegistration == undefined || userRegistration.column_value == '0') ? false : true;
		forceHTTPS = (forceHTTPS == undefined || forceHTTPS.column_value == '0') ? false : true;

		generalSettings = [{
				id: 'noindex-nofollow',
				name: 'noindex, nofollow',
				nameSub: '{{ __("meta tag") }}',
				description: '{{ __("description") }}',
				descriptionSub: '{{ __("blocks-search-engines-from-indexing-and-following") }}',
				active: noIndexNoFollow
			},
			{
				id: 'user-registration',
				name: '{{ __("user registration") }}',
				nameSub: '{{ __("account") }}',
				description: '{{ __("description") }}',
				descriptionSub: '{{ __("allow-user-registration-from-website") }}',
				active: userRegistration
			},
			{
				id: 'force-https',
				name: '{{ __("force https") }}',
				nameSub: 'SSL',
				description: '{{ __("description") }}',
				descriptionSub: '{{ __("force-https-for-all-pages") }}',
				active: forceHTTPS
			}
		];

	}

	async function populateSettingsTable() {

		let layouts = generalSettings.map(setting => {

			return `
				<tr>
					<td>
						<span class="value">${setting.name}</span>
						<span class="sub">${setting.nameSub}</span>
					</td>
					<td>
						<span class="value">${setting.description}</span>
						<span class="sub">${setting.descriptionSub}</span>
					</td>
					<td>
						<div class="toggle-button | margin-top-1-5">
							<input ${setting.active === true ? 'checked' : ''} onchange="updateSettingsStatus('${setting.id}')" type="checkbox" id="${setting.id}" />
							<label for="${setting.id}"></label>
						</div>
					</td>
				</tr>
			`
		});

		document.querySelector('#settings').innerHTML = layouts.join('');
	}
</script>

@parent
@stop