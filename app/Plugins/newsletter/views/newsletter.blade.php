@extends('layouts.portal')

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')
@inject('util','App\Classes\Util')

<div class="data-table-container">

	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
			<svg class="icon cross-icon">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>

		</div>
		<div class="data-table-toolbar-section right">

			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1 separator">
				<label for="status-filter" class="input-style-1-label">Status:</label>
				<select id="status-filter" class="filter-by-search input-style-1">
					<option value="all">All</option>
					<option value="Status:Subscribed">Subscribed</option>
					<option value="Status:Unsubscribed">Unsubscribed</option>
				</select>
			</div>

			<a href="{{ $util->prefixedURL($pluginConfig['slug']) }}/save" class="button button-primary">New Record</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>Name</th>
				<th>Email</th>
				<th>Status</th>
				<th>Subscription Date</th>
				<th>Unsubscribe Date</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

</div>


@stop

@section('page-script')

<script>

	let pageTable = dataTable('page-table');

	async function fetchNewsletter() {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/newsletter/all'
		});

		let newsletterData = response.data.map((newsletter, newsletterIndex) => {

			return [
				{
					type: 'text',
					value: (newsletterIndex + 1)
				},
				{
					type: 'text',
					value: newsletter.name
				},
				{
					type: 'text',
					value: newsletter.email
				},
				{
					type: 'tag',
					itemClasses: [newsletter.status === 'subscribed' ? 'tag-success' : 'tag-danger'],
					value: capitalize(newsletter.status)
				},
				{
					type: 'text',
					value: toLocalDateTime(newsletter.create_datetime, true)
				},
				{
					type: 'text',
					value: toLocalDateTime(newsletter.unsubscribe_datetime, true)
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/newsletter/save/' + newsletter.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteNewsletter(newsletter.id)
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

		pageTable.init(newsletterData);
	}

	async function deleteNewsletter(newsletterId){

		let n = Notification.show({
			text: 'Deleting, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			url: BASE_URL + '/api/newsletter/delete/' + newsletterId,
			method: 'DELETE'
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		fetchNewsletter();
	}

	fetchNewsletter();
</script>

@parent
@stop