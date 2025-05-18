@extends('layouts.portal')
@section('main-content')
<div class="data-table-container">
	<div class="data-table-toolbar">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __("status") }}:</label>
				<div class="select-container chevron">
					<div class="custom-select-container">
						<select id="status-filter" class="filter-by-search input-style-1" style="min-width: 10rem;">
							<option value="all">{{ __('all') }}</option>
							<option value="Status:drafts">{{ __('drafts') }}</option>
							<option value="Status:publish">{{ __('publish') }}</option>
						</select>
					</div>
				</div>
			</div>
			<a href="{{ url('/portal/pages/save') }}" class="button button-primary with-plus-icon">{{ __("add page") }}</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("name") }}</th>
				<th>{{ __("type") }}</th>
				<th>{{ __("status") }}</th>
				<th>{{ ucwords(__("date created")) }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="page-modal" class="modal modal-sm">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group"></div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'pages') }}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populatePages(staticPages());
	}

	/**
	 * Static data
	 */

	function staticPages() {
		let pages = '{!! addSlashes(json_encode(Page::allPages())) !!}';
		return JSON.parse(pages);
	}

	/**
	 * Fetch
	 */

	async function fetchPages() {
		let response = await Page.pages();
		return response.data;
	}

	async function fap() {
		let pages = await fetchPages();
		populatePages(pages);
	}

	/**
	 * Delete
	 */

	async function deletePage(pageId) {
		let n = showDeletingNotification();
		let response = await Page.deletePage(pageId);
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populatePages(pages) {
		let pagesData = pages.map((page, pageIndex) => {

			return [{
					type: 'text',
					value: (pageIndex + 1)
				},
				{
					type: 'text',
					value: page.title
				},
				{
					type: 'text',
					value: page.persistence === 'permanent' ? 'Default' : 'Custom'
				},
				{
					type: 'tag',
					itemClasses: [page.status === 'publish' ? 'tag-success' : 'tag-warning'],
					value: capitalize(page.status)
				},
				{
					type: 'text',
					value: toLocalDateTime(page.create_datetime)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							event: {
								'click': function() {
									showPageModal(page);
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/pages/save/' + page.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deletePage(page.id);
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

		pageTable.init(pagesData);
	}

	/**
	 * Other
	 */

	async function showPageModal(page) {

		let pageModal = document.querySelector('#page-modal');
		pageModal.querySelector('.modal-title').innerHTML = page.title;

		let meta = JSON.parse(page.meta);

		let hardURL = {
			home: '',
			login: PORTAL_PREFIX + '/login',
			register: PORTAL_PREFIX + '/register',
			'forgot-password': PORTAL_PREFIX + '/forgot-password',
			'reset-password': null,
		};

		let url = BASE_URL + '/' + page.slug;
		if (page.hard_url !== null) url = BASE_URL + '/' + page.hard_url;


		let layout = `
			<p><b>{{ __('url') }}:</b> <a href="${url}" target="_blank">${url}</a></p>
			<p><b>{{ __('status') }}:</b> ${capitalize(page.status)}</p>
			<p class="margin-top-2 margin-bottom-2"><b class="underline">{{ __('page seo') }}</b></p>
			<p><b>{{ ucwords(__('tab title')) }}:</b> ${(meta !== null && meta.tabTitle !== undefined && meta.tabTitle !== null) ? meta.tabTitle : ''}</p>
			<p><b>{{ ucwords(__('meta description')) }}:</b> ${(meta !== null && meta.metaDescription !== undefined && meta.metaDescription !== null) ? meta.metaDescription : ''}</p>
			<p><b>{{ __('author') }}:</b> ${(meta !== null && meta.metaAuthor !== undefined && meta.metaAuthor !== null) ? meta.metaAuthor : ''}</p>
			<p class="margin-top-2 margin-bottom-2"><b class="underline">{{ ucwords(__('featured image')) }}</b></p>
			<p><img src="${BASE_URL + '/storage/' + page.featured_image}" width="100" alt="" /></p>
		`;

		pageModal.querySelector('.modal-text-group').innerHTML = layout;
		showModal('page-modal');
	}
</script>

@stop