@extends('layouts.portal')
@section('main-content')
<div id="sitemap-table" class="table-1-container | margin-top-3">
	<div class="table-1-header">
		<h2 class="heading">{{ __('sitemap') }}</h2>
		<div class="toggle-button">
			<input onchange="updateSitemapStatus()" type="checkbox" id="sitemap">
			<label for="sitemap"></label>
		</div>
	</div>
	<div class="table-1-body | hide">
		<table>
			<tbody>
				<tr>
					<td>
						<span class="value">{{ __('url') }}: <a target="_blank" class="clr-link-400" href="{{ url('/sitemap')}}">{{ url('/sitemap')}}</a></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<h2 class="heading heading-2 | margin-top-5">{{ __('exclude urls') }}</h2>
<p class="margin-top-1">{{ __('sitemap-url-in-each-line') }}</p>
<textarea name="excluded-urls" class="input-style-1 | margin-top-2" rows="10"></textarea>
<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveExcludedURLs()" class="button button-primary | margin-top-2">{{ __('update') }}</button>

@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'setting') }}

<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateSitemap(staticSitemap());
	}

	/**
	 * Static data
	 */

	function staticSitemap() {
		let sitemap = '{!! addSlashes(json_encode(Setting::sitemap())) !!}';
		return JSON.parse(sitemap);
	}

	/**
	 * Save
	 */

	async function saveExcludedURLs() {
		let urls = document.querySelector('[name="excluded-urls"]').value;

		let n = showSavingNotification();
		let response = await Setting.updateSitemapExcludeURLs({
			urls
		}, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

	}

	async function updateSitemapStatus() {
		let status = document.querySelector('#sitemap').checked;
		if (status === true) status = 'active';
		else status = 'inactive';

		let response = await Setting.updateSitemapStatus({
			status
		});
		toggleSitemapStatus(status);
	}

	/**
	 * Populate
	 */

	function populateSitemap(sitemap) {
		document.querySelector('[name="excluded-urls"]').value = sitemap.excluded_urls;
		toggleSitemapStatus(sitemap.status);
	}

	/**
	 * Other
	 */

	function toggleSitemapStatus(status) {
		if (status === 'active') {
			document.querySelector('#sitemap').checked = true;
			document.querySelector('#sitemap-table .table-1-body').classList.remove('hide');
		} else {
			document.querySelector('#sitemap').checked = false;
			document.querySelector('#sitemap-table .table-1-body').classList.add('hide');
		}
	}
</script>

@parent
@stop