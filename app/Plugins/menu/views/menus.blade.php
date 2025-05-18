@extends('layouts.portal')

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')
@inject('util','App\Classes\Util')

<div class="data-table-container">

	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1">
			<svg class="icon search-icon"><use xlink:href="{{ asset('assets/icons.svg#search') }}" /></svg>
			<svg class="icon cross-icon"><use xlink:href="{{ asset('assets/icons.svg#cross') }}" /></svg>
		</div>
		<div class="data-table-toolbar-section right">

			<a href="{{ $util->prefixedURL($pluginConfig['slug']) }}/save" class="button button-primary">New Menu</a>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>Name</th>
				<th>Display Name</th>
				<th>Type</th>
				<th>Status</th>
				<th>Date Created</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

</div>

<div id="page-modal" class="modal">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close"><use xlink:href="{{ asset('assets/icons.svg#cross') }}" /></svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group"></div>
	</div>
</div>

@stop

@section('page-script')

<script>
	var pageTable = dataTable('page-table');
	pageTable.over

	async function fetchMenus() {
		let response = await xhrRequest({
			url: BASE_URL + '/api/menu/all',
			method: 'GET'
		});

		let menus = response.data;

		let menuData = menus.map((menu, menuIndex) => {

			return [{
					type: 'text',
					value: (menuIndex + 1)
				},
				{
					type: 'text',
					value: menu.name
				},
				{
					type: 'text',
					value: menu.display_name
				},
				{
					type: 'text',
					value: menu.presistence === 'permanent' ? 'Default' : 'Custom',
				},
				{
					type: 'tag',
					itemClasses: ['tag-success'],
					value: 'Active'
				},
				{
					type: 'text',
					value: toLocalDateTime(menu.create_datetime)
				},
				{
					type: 'button-group-icon',
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/menu/save/' + menu.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteMenu(menu.id);
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

		pageTable.init(menuData);
	}

	async function deleteMenu(menuId) {

		let n = Notification.show({
			text: 'Deleting menu, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			url: BASE_URL + '/api/menu/delete/' + menuId,
			method: 'DELETE'
		});

		Notification.hideAndShowDelayed(n.data.id, {
			classes: [response.data.status],
			text: response.data.msg
		});

		if (response.data.status === 'success') fetchMenus();

	}

	async function showPageModal(faq) {

		let pageModal = document.querySelector('#page-modal');
		pageModal.querySelector('.modal-title').innerHTML = faq.title;


		let layout = `
			<p><b>Date:</b> ${toLocalDateTime(faq.create_datetime)}</p>
			<p><b>Answer:</b></p>
			<p>${faq.description}</p>
		`;

		pageModal.querySelector('.modal-text-group').innerHTML = layout;
		showModal('page-modal');
	}

	fetchMenus();
</script>

@stop