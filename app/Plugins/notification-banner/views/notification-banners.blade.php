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
				<div class="select-container chevron">
					<div class="custom-select-container">
						<select id="status-filter" class="filter-by-search input-style-1" style="min-width: 12rem;">
							<option value="all">{{ __('all') }}</option>
							<option value="Status:active">{{ __('active') }}</option>
							<option value="Status:inactive">{{ __('inactive') }}</option>
						</select>
					</div>
				</div>
			</div>

			<a href="{{ url('/portal/notification-banner/save') }}" class="button button-primary">{{ __('new notification') }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('text') }}</th>
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

{{ loadPluginFile('js/script.js', 'notification-banner') }}

<script>
	let pageTable = dataTable('page-table');

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (!isEmpty(staticNotificationBanners())) populateNotificationBanners(staticNotificationBanners());
	}

	/**
	 * Static data
	 */

	function staticNotificationBanners() {
		let banners = '{!! addSlashes(json_encode(NotificationBanner::notificationBanners())) !!}';
		return JSON.parse(banners);
	}

	/**
	 * Fetch
	 */

	async function fetchNotificationBanners() {
		let response = await NotificationBanner.notificationBanners();
		return response.data;
	}

	async function fap() {
		let notificationBanners = await fetchNotificationBanners();
		populateNotificationBanners(notificationBanners);
	}

	/**
	 * Delete
	 */

	async function deleteNotificationBanner(notificationBannerId) {

		let n = showDeletingNotification();

		let response = await NotificationBanner.deleteNotificationBanner(notificationBannerId);

		showResponseNotification(n, response);

		if(response.data.status === 'success') fap();
	}

	/**
	 * Populate
	 */

	function populateNotificationBanners(notificationBanners) {
		let data = notificationBanners.map((notificationBanner, notificationBannerIndex) => {
			return [{
					type: 'text',
					value: (notificationBannerIndex + 1)
				},
				{
					type: 'excerpt',
					value: notificationBanner.text
				},
				{
					type: 'tag',
					itemClasses: [notificationBanner.status === 'active' ? 'tag-success' : 'tag-danger'],
					value: notificationBanner.status
				},
				{
					type: 'text',
					value: notificationBanner.type
				},
				{
					type: 'text',
					value: toLocalDateTime(notificationBanner.create_datetime, true)
				},

				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/notification-banner/save/' + notificationBanner.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteNotificationBanner(notificationBanner.id)
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

		pageTable.init(data);
	}
</script>
@parent
@stop