@extends('layouts.portal')
@section('page-style')
<link rel="stylesheet" href="{{ asset('css/nested-sort.css') }}">
@stop

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" onsubmit="return false;">

			<div class="form-group">
				<label class="input-style-1-label">Menu Name</label>
				<input name="menu" type="text" class="input-style-1">
			</div>

			<div class="form-group">
				<label class="input-style-1-label">Display Name</label>
				<input name="display-name" type="text" class="input-style-1">
			</div>

			<div class="form-group | margin-top-3">
				<label data-is="sort-menu-items-heading" class="input-style-1-label | hide">Sort Menu Items</label>
				<div class="accordion-container" id="menu-nested-sort"></div>
			</div>

			<div class="form-group">
				<div data-is="item-links-divider" class="section-divider | hide" style="--width:9rem;">Item Links</div>
			</div>

			<div class="form-group">
				<div class="cards cards-3" id="item-cards"></div>
			</div>

		</form>
	</div>
	<div class="grid | flex-column-reverse-on-md">

		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button onclick="saveMenu()" class="button button-sm button-primary button-block">Save menu</button>
			</div>
		</div>

		<div class="grid-widget | margin-bottom-2">
			<div class="grid-widget-text"><b>Custom Items</b></div>
			<form onsubmit="addItemToMenu()" action="#" class="margin-top-2">
				<div class="form-group">
					<input name="custom-item" type="text" class="input-style-1" placeholder="Enter item title">
				</div>
				<div class="form-group">
					<button class="button button-block button-sm button-primary-border">Add to menu</button>
				</div>
			</form>
		</div>

		<div class="grid-widget | margin-bottom-2">
			<div class="grid-widget-text"><b>Pages</b></div>
			<ul class="list | margin-top-1" id="page-list"></ul>
		</div>

	</div>
</div>


@stop

@section('page-script')

<script src="{{ asset('js/nested-sort.js') }}"></script>
<script>
	let menuId = '{{ $menuId ?? "" }}';
	let sortableMenuList = null;
	let sortableMenuListData = [];
	let pages = [];
	let selectedPages = [];
	let selectedCustomItems = [];
	let flatMenuItems = [];

	function updateNestedList(data = []) {

		if (sortableMenuList !== null) sortableMenuList.destroy();
		if (data.length === 0) document.querySelector('#menu-nested-sort').innerHTML = '';

		sortableMenuList = new NestedSort({
			data: data,
			actions: {
				onDrop(data) {
					sortableMenuListData = data;
				}
			},
			el: '#menu-nested-sort',
			listClassNames: ['nested-sort'],
			nestingLevels: -1
		});
	}

	/**
	 * ===== Custom Links
	 */

	function addItemToMenu() {
		if (event) event.preventDefault();

		let input = document.querySelector('[name="custom-item"]');
		let name = input.value;

		if (validateItem(name, 'custom') === false) return;

		selectedCustomItems.push(name);

		if (sortableMenuList !== null) {
			sortableMenuListData = mapSortableListData(sortableMenuList, sortableMenuListData);
		}

		sortableMenuListData.push({
			id: sortableMenuListData.length,
			text: name,
			parent: undefined,
			type: 'custom'
		});

		input.value = '';

		updateNestedList(sortableMenuListData);
		toggleSortingSectionHeading();
		populateItemCards(sortableMenuListData);
	}

	/**
	 * ===== Pages
	 */

	async function fetchPages() {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/pages/all'
		});

		pages = response.data;
		if (pages === '' || pages === null || pages === undefined) retrun;

		let layouts = pages.map(page => {
			return `<li>
						${page.title}
						<svg onclick="addPageToMenu(${page.id})" class="icon icon-default"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-plus" /></svg>
					</li>`;
		});

		document.querySelector('#page-list').innerHTML = layouts.join('');
	}

	function addPageToMenu(pageId) {
		if (event) event.preventDefault();

		let page = pages.find(page => page.id == pageId);

		let name = page.title;

		if (validateItem(pageId, 'page') === false) return;

		if (sortableMenuList !== null) {
			sortableMenuListData = mapSortableListData(sortableMenuList, sortableMenuListData);
		}

		sortableMenuListData.push({
			id: sortableMenuListData.length,
			text: name,
			parent: undefined,
			type: 'page',
			url: (page.hard_url !== null) ? page.hard_url : page.slug
		});

		selectedPages.push(pageId);

		updateNestedList(sortableMenuListData);
		toggleSortingSectionHeading();
		populateItemCards(sortableMenuListData);
	}

	function findPageId(text) {

		let pageId = null;
		let item = flatMenuItems.find(i => i.text == text);

		pages.forEach(page => {
			let pageURL = (page.hard_url !== null) ? page.hard_url : page.slug;
			if (pageURL === '') pageURL = null;
			if (item.url === pageURL) pageId = page.id;
		});

		return pageId;
	}

	/**
	 * ====== Item Links
	 */

	function populateItemCards(data = null) {

		let layouts = [];

		if (data === null) {
			let customLayouts = selectedCustomItems.map(item => {
				return itemLayout({
					title: item,
					text: '',
					url: '',
					newTab:false,
					type: 'custom'
				})
			});

			let pageLayouts = selectedPages.map(pageId => {

				let page = pages.find(p => p.id == pageId);
				let url = (page.hard_url !== null) ? page.hard_url : page.slug;

				return itemLayout({
					title: page.title,
					text: '',
					url: url,
					newTab: false,
					type: 'page'
				})
			});

			layouts = pageLayouts.concat(customLayouts);
		} else {
			layouts = data.map(row => {
				
				if (row.url == null) row.url = '';

				return itemLayout({
					title: row.text,
					text: row.text,
					url: row.url,
					newTab: row.newTab,
					type: row.type
				})
			});
		}


		document.querySelector('#item-cards').innerHTML = layouts.join('');
	}

	function itemLayout(data) {

		return `
			<div class="card | input-card" data-title="${data.title}">
				<h2 class="input-card-title | margin-bottom-2">${data.title} <svg onclick="removeSelectedItem('${data.type}', '${data.title}')" class="icon icon-danger title-icon"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-trash" /></svg></h2>
				<form action="#">
					<div class="form-group">
						<input name="text" type="text" placeholder="Text" class="input-style-1" value="${data.text}">
					</div>
					<div class="form-group | ${data.type === 'page' ? 'hide' : ''}">
						<input name="url" type="text" placeholder="URL" class="input-style-1" value="${data.url}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">
							Open in new tab &nbsp;
							<input ${(data.newTab !== undefined && data.newTab == true) ? 'checked' : ''} name="target" type="checkbox">
						</label>
						
					</div>
				</form>
			</div>
		`;

	}

	function removeSelectedItem(type, title) {

		Confirmation.show({
			positiveButton: {
				function: function() {

					let error = null;
					let itemToDelete = null;

					sortableMenuList.data.forEach(function(obj) {
						if (obj.text == title) itemToDelete = obj;
					});

					sortableMenuListData.forEach(item => {
						if (itemToDelete.id == item.parent) error = 'Delete the item children first!'
					});


					if (error !== null) {
						Notification.show({
							text: error,
							classes: ['fail']
						});
						return;
					}

					if (type === 'custom') {
						selectedCustomItems = selectedCustomItems.filter(item => item !== title);
					} else if (type === 'page') {
						let page = pages.find(page => page.title == title);

						if (page === undefined) {
							pageId = findPageId(title);
							page = {id: pageId};
						}

						selectedPages = selectedPages.filter(pageId => pageId != page.id);
					}


					sortableMenuListData = sortableMenuListData.filter(item => item.id != itemToDelete.id);
					sortableMenuListData = mapSortableListData(sortableMenuList, sortableMenuListData);

					let map = {};
					sortableMenuListData.forEach((item, itemIndex) => {
						let oldId = sortableMenuListData[itemIndex].id;
						sortableMenuListData[itemIndex].id = itemIndex;
						map[oldId] = itemIndex;
					});

					sortableMenuListData.forEach(item => {
						if (map[item.parent] !== undefined) item.parent = map[item.parent];
					});


					updateNestedList(sortableMenuListData);
					populateItemCards(sortableMenuListData);
					toggleSortingSectionHeading();
				}
			}
		});

	}

	/**
	 * ===== xhr
	 */

	async function saveMenu() {
		let data = menuData();

		if (data.menu.toString().trim() === '') {
			Notification.show({
				text: 'Please fill menu name.',
				classes: ['fail']
			});
			return;
		}


		let n = Notification.show({
			text: 'Saving menu, please wait...',
			time: 0
		});

		let apis = {
			add: {
				url: BASE_URL + '/api/menu/save',
				method: 'POST'
			},
			update: {
				url: BASE_URL + '/api/menu/save/' + menuId,
				method: 'PUT'
			}
		};

		let api = (menuId !== '') ? apis.update : apis.add;

		let response = await xhrRequest({
			method: api.method,
			url: api.url,
			body: data
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		if (response.data.status === 'success') menuId = response.data.menuId;

	}

	async function fetchMenu(menuId) {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/menu/one/' + menuId
		});

		let menu = response.data;

		document.querySelector('[name="menu"]').value = menu.name;
		document.querySelector('[name="display-name"]').value = menu.display_name;

		let items = JSON.parse(menu.items);
		let flatItems = flattenEntities(items);
		flatMenuItems = flatItems;

		flatItems = flatItems.map(item => {
			if (item.text == '' || item.text == null) {
				item.text = item.title;
			}
			return item;
		});

		flatItems.forEach(item => {
			if (item.type === 'custom') selectedCustomItems.push(item.text);
			else if (item.type === 'page') {
				let pageId = findPageId(item.text);
				selectedPages.push(pageId);
			}
		});

		sortableMenuListData = flatItems;
		updateNestedList(flatItems);
		populateItemCards(flatItems);
		toggleSortingSectionHeading();
	}



	/**
	 * ===== Other
	 */

	function validateItem(item, type) {

		if (item.toString().trim() === '') {
			Notification.show({
				text: 'Item title can not be empty.',
				classes: ['fail']
			});
			return false;
		}

		if (type === 'custom') {

			let hasItem = selectedCustomItems.find(i => i == item);


			let hasItem2 = selectedPages.find(pageId => {
				let page = pages.find(page => page.id == pageId);
				if (page.title == item) return true;
			});

			if (hasItem !== undefined || hasItem2 !== undefined) {
				Notification.show({
					text: `This item is already present in menu.`,
					classes: ['fail']
				});
				return false;
			}

		} else {

			let hasItem = selectedPages.find(i => i == item);
			let page = pages.find(page => page.id == item);
			let hasItem2 = selectedCustomItems.find(i => i == page.title);

			if (hasItem !== undefined || hasItem2 !== undefined) {
				Notification.show({
					text: `This item is already present in menu.`,
					classes: ['fail']
				});
				return false;
			}

		}

		return true;
	}

	function menuData() {

		let cards = document.querySelectorAll('#item-cards .card');
		let menu = document.querySelector('[name="menu"]').value;
		let displayName = document.querySelector('[name="display-name"]').value;

		let data1 = [];
		let childParentMap = sortableMenuListData.reduce(function(acc, row) {
			if (row.parent !== undefined) {
				if (acc[row.id] === undefined) acc[row.id] = {
					parent: parseInt(row.parent),
					order: row.order
				};
			} else {
				if (acc[row.id] === undefined) acc[row.id] = {
					parent: undefined,
					order: row.order
				};
			}
			return acc;
		}, acc = {});

		if (sortableMenuList === null) return {
			menu: menu,
			displayName:displayName,
			items: []
		};

		sortableMenuList.data.forEach(row => data1.push({
			id: parseInt(row.id),
			title: row.text
		}));

		cards.forEach(card => {
			let title = card.dataset.title;
			let text = card.querySelector('[name="text"]').value;
			let url = card.querySelector('[name="url"]').value;
			let newTab = card.querySelector('[name="target"]').checked;

			data1.forEach(row => {
				if (row.title == title) {
					row.text = text;
					row.url = url;
					row.newTab = newTab; 
				}
			})
		});

		data1.forEach(row => {
			if (childParentMap[row.id] !== undefined) {
				row.parent = childParentMap[row.id].parent;
				row.order = childParentMap[row.id].order;
			} else row.parent = undefined;

			let isCustom = selectedCustomItems.find(i => i == row.title);
			if (isCustom !== undefined) row.type = 'custom';
			else row.type = 'page';
		})


		let nestedData = nestEntities(data1);

		return {
			menu: menu,
			displayName:displayName,
			items: nestedData
		}

	}

	function nestEntities(entities) {
		const nestedEntities = [];

		const entityMap = new Map();
		entities.forEach(entity => {
			entityMap.set(entity.id, entity);
		});

		entities.sort((a, b) => a.order - b.order);

		entities.forEach(entity => {
			const {
				id,
				text,
				parent
			} = entity;

			if (parent === undefined) {
				nestedEntities.push(entity);
			} else {
				const parentEntity = entityMap.get(parent);
				if (parentEntity) {
					if (!parentEntity.children) {
						parentEntity.children = [];
					}
					parentEntity.children.push(entity);
				}
			}
		});

		return nestedEntities;
	}

	function flattenEntities(entities) {
		const flattenedEntities = [];

		function flatten(entity) {
			flattenedEntities.push(entity);

			if (entity.children) {
				entity.children.sort((a, b) => a.order - b.order);
				entity.children.forEach(child => flatten(child));
				delete entity.children;
			}
		}

		entities.sort((a, b) => a.order - b.order);
		entities.forEach(entity => flatten(entity));

		return flattenedEntities;
	}

	function mapSortableListData(listElement, data) {
		listElement.data.forEach(function(obj) {
			data.forEach(function(objWithParent) {
				if (objWithParent.id == obj.id) objWithParent['text'] = obj.text;
			});
		});

		return data;
	}

	function toggleSortingSectionHeading() {
		let sectionHeading = document.querySelector('[data-is="sort-menu-items-heading"]');
		let itemLinksDivider = document.querySelector('[data-is="item-links-divider"]');

		if (sortableMenuListData.length > 0) {
			sectionHeading.classList.remove('hide');
			itemLinksDivider.classList.remove('hide');
		} else {
			sectionHeading.classList.add('hide');
			itemLinksDivider.classList.add('hide');
		}
	}

	/**
	 * ===== Invoke
	 */

	async function invoke() {
		await fetchPages();
		if (menuId !== '') await fetchMenu(menuId);
	}

	invoke();
</script>

@stop