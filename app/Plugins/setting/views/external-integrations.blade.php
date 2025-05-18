@extends('layouts.portal')
@section('main-content')
<div class="table-1-container affiliate-hive-campaign-table-container | margin-top-3">
	<div class="table-1-header">
		<h2 class="heading">{{ __('available options') }}</h2>
	</div>
	<div class="table-1-body">
		<table>
			<tbody id="settings"></tbody>
		</table>
	</div>
</div>
@stop

@section('page-script')

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateExternalIntegrations(staticExternalIntegrations());
	}

	/**
	 * Static data
	 */

	function staticExternalIntegrations() {
		let data = '{!! addSlashes(json_encode(Setting::externalIntegrations())) !!}';
		return JSON.parse(data);
	}

	/**
	 * Populate
	 */

	async function populateExternalIntegrations(data) {

		let generalSettings = data.map(row => {
			return {
				id: row.id,
				name: row.title,
				nameSub: '',
				description: 'Description',
				descriptionSub: row.description,
				status: row.status,
				slug: row.slug
			};
		});

		let layouts = generalSettings.map(setting => {

			return `
				<tr>
					<td>
						<span class="value">${setting.name}</span>
						<span class="sub | clr-green-400"></span>
					</td>
					<td>
						<span class="value">{{ __("description") }}</span>
						<span class="sub">${setting.descriptionSub}</span>
					</td>
					<td>
						<span class="tag ${setting.status === 'active' ? 'tag-success' : 'tag-warning'}">${setting.status}</span>
					</td>
					<td>
						<a href="${PREFIXED_URL}/setting/external-integrations/${setting.slug}"><svg class="icon"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-gear" /></svg></a>
					</td>
				</tr>
				
			`
		});

		document.querySelector('#settings').innerHTML = layouts.join('');
	}
</script>

@parent
@stop