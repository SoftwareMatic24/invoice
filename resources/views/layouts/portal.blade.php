@extends('layouts.master')
@inject('util', 'App\Classes\Util')
@inject("projectController", "App\Http\Controllers\ProjectController")
@inject('pluginController', 'App\Http\Controllers\PluginController')
@inject('appearanceController', 'App\Plugins\Appearance\Controller\AppearanceController')

@section('style')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@yield('page-style')

{{ $pluginController->loadLayout(sidebarPlugins(), 'head') }}
{!! $appearanceController->generatePortalStyle() !!}

@if (Setting::setting('portal-sidebar-separators') == 0)
<style>
	.sidebar-list>li.separator {
		display: none;
	}
</style>
@endif
@stop

<?php

$specialStylingPlugin = function(){
	return env("PORTAL_STYLE_PLUGIN") ?? NULL;
}

?>


@section('content')

<div class="navigation">
	<div class="container">
		<div class="flex-1">
			<x-icon.icon data-is="toggle-sidebar" class="navigation-menu-icon" name="hamburger" />
			<a href="{{ url('/portal/dashboard') }}" class="navigation-brand">{{ Appearance::brandName() }}</a>
			<span class="navigation-profile">&nbsp;</span>
		</div>
	</div>
</div>

<div class="main-container">
	<x-portal.sidebar pageSlug="{{ $pageSlug }}" />

	<div class="main">

		<div class="main-header">

			@if ($backURL ?? false)
			<a href="{{ $backURL }}">
				<x-icon.icon class="icon back" name="solid-arrow-left" />
			</a>
			@endif

			<h1 class="main-header-title">{{ $pageName ?? '' }}</h1>
			
			<div class="d-flex flex-direction-column gap-1 width-100 padding-top-1 padding-bottom-1">
				@if (($pageSlug ?? null) === 'dashboard' && request()['loggedInUser']['role_title'] !== 'admin')
					{!! NotificationBanner::notificationBannerPortalView() !!}
				@endif

				@if (($pageSlug ?? null) === 'dashboard' && request()['loggedInUser']['role_title'] == 'admin' && Reset::hasActiveResets() && Reset::isNotificationVisible())
				{!! Reset::resetNotificationView() !!}
				@endif
			</div>

			<div class="main-shortcuts">

				<span class="main-shortcuts-entity-container bell-icon-container">
					<x-icon.icon data-is="toggle-notification-panel" class="main-shortcuts-entity icon bell-icon" name="outline-bell" />
				</span>

				<div class="floating-panel notification-panel">
					<div class="floating-panel-header">
						<h2 class="floating-panel-heading">{{ __('notifications') }}</h2>
						<span class="tag" data-is="new-notifications">{{ __('new') }} 0</span>
					</div>

					<div class="floating-panel-body">
						<ul class="floating-panel-list" role="list"></ul>
					</div>

					<div class="floating-panel-footer">
						<a href="{{ $util->prefixedURL('/notifications') }}" class="floating-panel-footer-link">{{ __('all notifications') }}</a>
						<svg class="icon notification-settings | hide">
							<use xlink:href="{{ asset('assets/icons.svg#solid-gear') }}" />
						</svg>
					</div>
				</div>

				@if (request()['loggedInUser']['image'] === null)
				@if (cache('settings')['profile-picture']['column_value'] == '0')
				<span class="main-shortcuts-entity-container | hide-on-lg"><img data-image="profile-picture" src="{{ url('storage/' . cache('settings')['brand-fav-icon']['column_value']) }}" class="main-shortcuts-entity user-avatar" width="23" height="23" data-is="toggle-profile-panel" /></span>
				@else
				<span class="main-shortcuts-entity-container | hide-on-lg"><img data-image="profile-picture" src="{{ asset('assets/avatar.png') }}" class="main-shortcuts-entity user-avatar" width="23" height="23" data-is="toggle-profile-panel" /></span>
				@endif
				@else
				<span class="main-shortcuts-entity-container | hide-on-lg"><img data-image="profile-picture" src="{{ asset('storage/' . request()['loggedInUser']['image']) }}" class="main-shortcuts-entity user-avatar" width="23" height="23" data-is="toggle-profile-panel" /></span>
				@endif

				@if(empty($specialStylingPlugin()))
				<div class="floating-panel profile-panel">
					<div class="floating-panel-header">
						<h2 data-is="username" class="floating-panel-heading">
							{{ request()['loggedInUser']['first_name'] }}
						</h2>
					</div>
					<div class="floating-panel-body">
						<ul class="floating-panel-list" role="list">
							<li>
								<a href="#" data-navigation="profile">
									<svg class="icon icon-default">
										<use xlink:href="{{ asset('assets/icons.svg#solid-user') }}" />
									</svg>
									<div class="info">
										<p class="floating-panel-list-title">{{ __('profile') }}</p>
										<span class="floating-panel-list-description">{{ __('profile-description') }}</span>
									</div>
								</a>
							</li>
							<li>
								<a href="#" data-navigation="activity-log">
									<svg class="icon icon-default">
										<use xlink:href="{{ asset('assets/icons.svg#solid-book') }}" />
									</svg>
									<div class="info">
										<p class="floating-panel-list-title">{{ __('activity log') }}</p>
										<span class="floating-panel-list-description">{{ __('activity-log-description') }}</span>
									</div>
								</a>
							</li>
							<li>
								<a href="#" data-navigation="logout">
									<svg class="icon icon-default">
										<use xlink:href="{{ asset('assets/icons.svg#solid-logout') }}" />
									</svg>
									<div class="info">
										<p class="floating-panel-list-title">{{ __('logout') }}</p>
										<span class="floating-panel-list-description">{{ __('logout-description') }}</span>
									</div>
								</a>
							</li>
						</ul>
					</div>
				</div>
				@else
				{{ $pluginController::loadWidget($specialStylingPlugin(), "profile-panel") }}
				@endif
			</div>

		</div>

		<div class="main-body">
			@yield('main-content')
		</div>

	</div>

</div>
@stop

@section('content-bottom')
{{ $pluginController->loadLayout(sidebarPlugins(), 'foot') }}
@stop

@section('script')
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/moment-timezone.js') }}"></script>
<script src="{{ asset('js/chart.min.js') }}"></script>


{!!  loadFile('/resources/js/private/popover.js') !!}
{!!  loadFile('/resources/js/public/reset.js') !!}

<script>
	
	let geneticData = null;

	var navigationLink = {
		profile: `${PREFIXED_URL}/profile`,
		help: "{{ $util->prefixedURL('/help') }}",
		'activity-log': "{{ $util->prefixedURL('/activity') }}",
		logout: "{{ $util->prefixedURL('/logout') }}",
	};

	var notificationTexts = {
		saving: "{{ __('saving-notification') }}",
		updating: "{{ __('updating-notification') }}",
		processing: "{{ __('processing-notification') }}",
		deleting: "{{ __('deleting-notification') }}",
		removing: "{{ __('removing-notification') }}",
	};

	// Navigation

	function setNavigationLinks() {
		for (key in navigationLink) {
			let navElements = document.querySelectorAll(`[data-navigation="${key}"]`);
			navElements.forEach(function(navElement) {
				navElement.setAttribute('href', navigationLink[key]);
			});
		}
	}

	setNavigationLinks();

	// Global

	window.addEventListener('resize', function() {
		setPortalMainAreaHeight();
		setPortalSidebarBodyHight();
	});

	window.addEventListener('click', function() {
		let targetEl = event.target;
		if (!targetEl.closest('.input-wrapper')) hideInputStyle1Suggestions();
		if (!targetEl.classList.contains('.floating-dropdown-toggler') && !targetEl.closest('.floating-dropdown-toggler')) FloatingDropdown.hide();
	});

	// Sidebar

	let sidebar = document.querySelector('.sidebar');
	let toggleSidebarElements = document.querySelectorAll('[data-is="toggle-sidebar"]');
	let sidebarListOptions = document.querySelectorAll('.sidebar-list li a');

	sidebarListOptions.forEach(function(sidebarListOption) {

		sidebarListOption.addEventListener('click', function(e) {

			let target = e.target;

			let li = target.closest('li');

			toggleClass(li, 'active');

		});

	});

	toggleSidebarElements.forEach(function(toggleSidebarElement) {
		toggleSidebarElement.addEventListener('click', function() {
			let hasClass = toggleClass(sidebar, 'active', true);
			if (hasClass == false) showSidebar();
			else hideSidebar();
		});
	});

	function showSidebar() {
		sidebar.classList.add('active');
		showOverlay();

		Trigger.OVERLAY_CLICK = function() {
			hideSidebar();
			Trigger.clear();
		}

	}

	function hideSidebar() {
		sidebar.classList.remove('active');
		hideOverlay();
	}

	// Form

	function initInputStyle1Suggestions() {
		let els = document.querySelectorAll('.input-style-1-suggestion');
		els.forEach(el => {
			if (el.getAttribute('data-init') == true) return;
			el.setAttribute('data-init', true);
			el.addEventListener('input', handleSuggestionInput);
		});

		function handleSuggestionInput() {
			let targetEl = event.currentTarget;
			let value = targetEl.value;
			let inputWrapperEl = targetEl.closest('.input-wrapper');
			let suggestionListEl = inputWrapperEl.querySelector('.input-style-1-suggestions');
			let suggestionListItemEls = suggestionListEl.querySelectorAll('li');

			let partialMatch = false;
			Array.from(suggestionListItemEls).forEach(liEl => {
				let match = liEl.innerHTML.toString().toLowerCase().includes(value.toLowerCase());
				if (match) liEl.classList.remove('hide');
				else liEl.classList.add('hide');
				if (match) partialMatch = true;

				if (liEl.getAttribute('default-click') == true) return;

				liEl.addEventListener('click', handleInputStyle1SuggestionSelect);
				liEl.setAttribute('default-click', true);
			});

			if (value === '') partialMatch = false;
			if (partialMatch) suggestionListEl.classList.add('active');
			else suggestionListEl.classList.remove('active');
		}
	}

	function hideInputStyle1Suggestions() {
		let els = document.querySelectorAll('.input-style-1-suggestions');
		els.forEach(el => el.classList.remove('active'));
	}

	function handleInputStyle1SuggestionSelect() {
		let targetEl = event.currentTarget;
		let inputWrapperEl = targetEl.closest('.input-wrapper');
		let inputSuggestionEl = inputWrapperEl.querySelector('.input-style-1-suggestion');
		let value = targetEl.innerHTML;
		inputSuggestionEl.value = value;
		hideInputStyle1Suggestions();
	}

	function handleSelectListDim() {
		let els = document.querySelectorAll('select');
		els.forEach(el => {
			el.addEventListener("change", function() {
				let targetEl = event.target;
				if (targetEl.value === "") targetEl.classList.add("dim");
				else targetEl.classList.remove("dim");
			});
			if (el.value !== '') el.classList.remove('dim');
		});
	}

	initInputStyle1Suggestions();
	handleSelectListDim();

	// Portal Functions
	function selectFile(id) {
		let fileInput = document.querySelector(`#${id}`);
		fileInput.click();
	}

	function portalMainAreaHeight() {

		let windowInnerHeight = window.innerHeight;

		let mainBody = document.querySelector('.main-body');
		let mainHeaderHeight = document.querySelector('.main-header').clientHeight;

		let mainBodyPaddingTop = parseFloat(window.getComputedStyle(mainBody).getPropertyValue('padding-top'));

		let height = windowInnerHeight - mainHeaderHeight - mainBodyPaddingTop;

		return height;
	}

	async function fetchPortalGenericData() {

		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/portal/generic'
		});

		genericData = response.data;
		if (genericData == '' || genericData == null || genericData == false || genericData.length === 0) return;

		for (let key in genericData) {

			let value = genericData[key];
			let elements = document.querySelectorAll(`[data-plugin-generic="${key}"]`);

			let chunks = key.split('--');
			if (chunks.length === 2 && chunks[1] === 'count' && value != 0) {
				elements.forEach(element => element.setAttribute('data-count', value))
			} else if (chunks.length === 2 && chunks[1] === 'count' && value == 0) {
				elements.forEach(element => element.removeAttribute('data-count'))
			} else {
				elements.forEach(element => element.innerHTML = value);
			}
		}

		populateNotifications(genericData.notifications);
	}

	fetchPortalGenericData();

	// Notifications

	function notificationsLayout(notifications) {

		let newNotificationsCount = 0;
		let layouts = notifications.map(notification => {
			let except = false;
			let meta = notification.meta;
			if (meta !== null) meta = JSON.parse(meta);

			let appearance = meta.appearance !== undefined ? meta.appearance : {};
			let exceptions = meta.exceptions !== undefined ? meta.exceptions : [];

			exceptions.forEach(exceptionUserId => {
				if (exceptionUserId == SAFE_USER.id) except = true;
			})
			if (except === true) return false;

			let onClick = `openNotification(${notification.id})`;
			if (notification.read.length <= 0) newNotificationsCount++;



			return `
				<li class="${notification.read.length <= 0 ? 'new' : ''}">
					<a href="#" onclick="${onClick}">
						<svg class="icon notification-icon" style="--color:${appearance.color};--bgColor:${appearance.bgColor}">
							<use xlink:href="${BASE_URL}/assets/icons.svg#${appearance.icon !== undefined ? appearance.icon : ''}"></use>
						</svg>
						<div class="info">
							<p class="floating-panel-list-title">${notification.title}</p>
							<span class="floating-panel-list-description">${toLocalDateTime(notification.create_datetime, true)}</span>
						</div>
					</a>
				</li>
			`;

		}).filter(view => view != false);

		return {
			layouts: layouts,
			newCount: newNotificationsCount
		};
	}

	function populateNotifications(notifications) {

		let bellIconContainer = document.querySelector('.main-shortcuts-entity-container.bell-icon-container');
		let notificationPanel = document.querySelector('.floating-panel.notification-panel');
		let panelList = notificationPanel.querySelector('.floating-panel-body .floating-panel-list');
		let newNotification = notificationPanel.querySelector('[data-is="new-notifications"]');

		let allNotifications = notifications.userNotifications.concat(notifications.roleNotifications);


		let response = notificationsLayout(allNotifications);
		let layouts = response.layouts;
		newNotificationsCount = response.newCount;

		if (newNotificationsCount > 0) {
			newNotification.classList.add('tag-warning');
			bellIconContainer.classList.add('active');
		} else {
			newNotification.classList.remove('tag-warning');
			bellIconContainer.classList.remove('active');
		}

		newNotification.innerHTML = `New ${newNotificationsCount}`;
		panelList.innerHTML = layouts.join('');
	}

	function openNotification(notificationId) {
		if (event) event.preventDefault();
		window.location.href = PREFIXED_URL + '/notifications/open/' + notificationId;
		hideAllFloatingPanel();

		setTimeout(() => {
			fetchPortalGenericData();
		}, 1000);
	}

	// Notifications


	function showProcessingNotification(){
		return Notification.show({
			heading: '{{ __("processing-notification-heading") }}',
			description: '{{ __("processing-notification-description") }}',
			time: 0
		});
	}

	function showSavingNotification() {
		return Notification.show({
			heading: '{{ __("saving-notification-heading") }}',
			description: '{{ __("saving-notification-description") }}',
			time: 0
		});
	}

	function showDeletingNotification() {
		return Notification.show({
			heading: '{{ __("deleting-notification-heading") }}',
			description: '{{ __("deleting-notification-description") }}',
			time: 0
		});
	}

	function showResponseNotification(n, response) {

		let heading = response.data.heading || null;
		let description = response.data.description || response.data.msg;

		Notification.hideAndShowDelayed(n.data.id, {
			classes: [response.data.status],
			heading: heading,
			description: description,
		});
	}

	// Reset

	function hideResetNotification(){
		
		let targetEl = event.target;
		let containerEl = targetEl.closest('.inline-notification-container');
		containerEl.remove();
		

		xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/resets/settings/update/one`,
			body: {
				type: 'notification_visibility',
				value: false
			}
		});

	}


	// Accordion

	function initAccordions() {
		let accordions = document.querySelectorAll('.accordion');

		accordions.forEach(accordion => {
			let header = accordion.querySelector('.accordion-header');
			if (header === null) return;
			let clickAlreadyAdded = header.getAttribute('data-click-event');
			header.setAttribute('data-click-event', true);

			if (clickAlreadyAdded != 'true') {

				header.addEventListener('click', function() {
					toggleClass(accordion, 'active');
				});
			}

		});
	}

	initAccordions();


	// Floating Dropdown

	let FloatingDropdown = function() {
		let PUBLIC = {};

		let show = function(id = null, items = [], position = null) {
			if (id === null) return console.err('ID is required');
			let el = createFloatingDropdown(id, items, position);
			document.body.append(el);
		}

		let hide = function(id = null) {
			if (id === null) return document.querySelectorAll('.floating-dropdown-container').forEach(el => el.remove());
			let el = document.querySelector('#' + id);
			if (el !== null) el.remove();
		}

		let createFloatingDropdown = function(id = null, items = [], position = null) {
			let positionStr = createPositionStr(position);

			let dropDownContainerEl = document.createElement('div');
			dropDownContainerEl.classList.add('floating-dropdown-container');
			dropDownContainerEl.setAttribute('style', positionStr);
			dropDownContainerEl.id = id;

			let ulEl = document.createElement('ul');
			ulEl.classList.add('floating-dropdown');
			dropDownContainerEl.appendChild(ulEl);

			let liEls = createItemsElements(items);
			liEls.forEach(liEl => {
				ulEl.appendChild(liEl);
			});

			return dropDownContainerEl;
		}

		let createItemsElements = function(items) {
			let els = items.map(item => createItemElement(item));
			return els;
		}

		let createItemElement = function(item) {
			let iconHTMl = '';
			if (item.icon !== undefined) iconHTMl = `<svg class="icon"><use xlink:href="${item.icon.url}" /></svg>`;

			let liEl = document.createElement('li');
			let aEl = document.createElement('a');

			if (item.link === undefined) aEl.href = 'javascript:void(0)';
			if (item.link !== undefined && item.link.url !== undefined) aEl.href = item.link.url;
			if (item.link !== undefined && item.link.target !== undefined) aEl.target = item.link.target;
			if (item.event !== undefined && item.event.click !== undefined) aEl.addEventListener('click', item.event.click);

			if (item.type !== 'separator') aEl.innerHTML = `${iconHTMl}${toStr(item,'text')}`;
			else liEl.classList.add('separator');

			liEl.appendChild(aEl);
			return liEl;
		}

		let createPositionStr = function(position = null) {
			if (position === null) return '';

			if (position.element !== undefined && position.element !== null) {
				let cords = position.element.getBoundingClientRect();
				position.top = cords.top;
				position.left = cords.left;
				delete position.element;
			}

			if (position.topOffset !== undefined && position.top !== undefined) position.top += position.topOffset;
			if (position.bottomOffset !== undefined && position.bottom !== undefined) position.bottom += position.bottomOffset;
			if (position.leftOffset !== undefined && position.left !== undefined) position.left += position.leftOffset;
			if (position.rightOffset !== undefined && position.right !== undefined) position.right += position.rightOffset;

			let positionArr = [];
			for (let key in position) {
				positionArr.push(`${key}:${position[key]}px`);
			}
			return positionArr.join(';');
		}

		PUBLIC.show = show;
		PUBLIC.hide = hide;

		return PUBLIC;
	}();


	// Sheet Table

	function addSheetTableRow(id, htmlContent) {
		let targetEl = document.querySelector('#' + id);
		let sheetTableWrapperEl = targetEl.closest('.sheet-table-wrapper');
		let sheetTableEl = sheetTableWrapperEl.querySelector('table[class="sheet"]');
		let sheetTableBodyEl = sheetTableEl.querySelector('tbody');
		sheetTableBodyEl.insertAdjacentHTML('beforeend', htmlContent);
	}

	function updateSheetTable(id, htmlContent) {
		let targetEl = document.querySelector('#' + id);
		let sheetTableWrapperEl = targetEl.closest('.sheet-table-wrapper');
		let sheetTableEl = sheetTableWrapperEl.querySelector('table[class="sheet"]');
		let sheetTableBodyEl = sheetTableEl.querySelector('tbody');
		sheetTableBodyEl.innerHTML = htmlContent;
	}

	function removeSheetTableRow() {
		let targetEl = event.target;
		let rowEl = targetEl.closest('tr');

		Confirmation.show({
			positiveButton: {
				function: function() {
					rowEl.remove();
				}
			}
		});
	}


	// Dynamic Sizes

	function setPortalMainAreaHeight(heightParam = null) {

		let height = null;
		let heightMargin = 0;

		if (heightParam !== null) height = heightParam;
		else height = portalMainAreaHeight();

		if (window.innerWidth < 992) heightMargin = 50;

		document.querySelector('.main-body').style.height = (height - heightMargin) + 'px';
	}

	function setPortalSidebarBodyHight(heightParam = null) {

		let height = null;

		if (heightParam !== null) height = heightParam;
		else height = portalMainAreaHeight();

		document.querySelector('.sidebar-body').style.height = (height) + 'px';

	}

	setPortalMainAreaHeight();
	setPortalSidebarBodyHight();

	// Modal

	function showModal(id, cantClose = false) {

		showOverlay();
		document.querySelector(`#${id}`).classList.add('active');

		if (cantClose !== true) {
			Trigger.OVERLAY_CLICK = function() {
				hideModal(id);
			}
		}
	}

	function hideModal(id) {
		let el = document.querySelector(`#${id}`);
		if (el) el.classList.remove('active');
		else {
			let els = document.querySelectorAll(".modal");
			els.forEach(el => el.classList.remove("active"));
		}
		hideOverlay();
	}

	// Aside

	function showAside(id) {
		let asideEl = document.querySelector('#' + id);
		if (asideEl === null) return;

		Trigger.OVERLAY_CLICK = () => {
			hideAside(id);
		};

		asideEl.classList.add('active');
		showOverlay();

	}

	function hideAside(id = null) {
		if (id === null) {
			let asideEls = document.querySelectorAll('.aside');
			asideEls.forEach(asideEl => asideEl.classList.remove('active'));
			hideOverlay();
			return;
		}
		let asideEl = document.querySelector('#' + id);
		asideEl.classList.remove('active')
		hideOverlay();
	}

	// Tabs

	let tabContainers = document.querySelectorAll('.tabs-container');

	tabContainers.forEach(container => {

		let lis = container.querySelectorAll('.tabs-header li');

		lis.forEach(li => {
			li.addEventListener('click', switchTab);
		});
	});


	function switchTab() {
		let target = event.target;
		if (target.nodeName !== 'LI') target = target.closest('li');

		let container = target.closest('.tabs-container');
		let lis = container.querySelectorAll(':scope > .tabs-header ul li');

		let selectedIndex = 0;

		lis.forEach((li, liIndex) => {
			if (li === target) {
				li.classList.add('active');
				selectedIndex = liIndex;
			} else li.classList.remove('active');
		});

		let divs = container.querySelectorAll(':scope > .tabs-body > div');

		divs.forEach((div, divIndex) => {
			if (divIndex === selectedIndex) div.classList.add('active');
			else div.classList.remove('active');
		});

	}

	// Color Input

	function initColorInput() {
		let colorInputEls = document.querySelectorAll('.color-input');
		colorInputEls.forEach(el => {

			if (el.getAttribute('data-init') == 'true') return;
			el.setAttribute('data-init', true);

			let inputTextEl = el.querySelector('input[type="text"]');
			let inputColorEl = el.querySelector('input[type="color"]');

			inputTextEl.addEventListener('input', function() {
				let target = event.target;
				let hexColor = target.value;
				let isValidHex = validateHexColor(hexColor);

				if (isValidHex === false) {
					inputTextEl.style.borderColor = 'red';
					inputColorEl.style.borderColor = 'red';
				} else {
					inputTextEl.style.borderColor = 'var(--clr-neutral-400)';
					inputColorEl.style.borderColor = 'var(--clr-neutral-400)';
					inputColorEl.value = hexColor;
				}

			});

			inputColorEl.addEventListener('change', function() {
				let target = event.target;
				let hexColor = target.value;
				let isValidHex = validateHexColor(hexColor);

				if (isValidHex === false) {
					inputTextEl.style.borderColor = 'red';
					inputColorEl.style.borderColor = 'red';
				} else {
					inputTextEl.style.borderColor = 'var(--clr-neutral-400)';
					inputColorEl.style.borderColor = 'var(--clr-neutral-400)';
					inputTextEl.value = inputColorEl.value;
				}
			});
		});
	}

	initColorInput();

	// validators

	function validateHexColor(hexColor) {
		const hexColorRegex = /^#?([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/;
		return hexColorRegex.test(hexColor);
	}


	// Helpers

	function camelCaseToNormalText(input) {
		if (typeof input !== 'string' || input.length === 0) {
			return '';
		}

		const words = [];
		let currentWord = '';

		for (let i = 0; i < input.length; i++) {
			const char = input[i];

			if (char === char.toUpperCase()) {

				if (currentWord.length > 0) {
					words.push(currentWord);
				}
				currentWord = char.toLowerCase();
			} else {
				currentWord += char;
			}
		}

		if (currentWord.length > 0) {
			words.push(currentWord);
		}

		return words.join(' ');
	}

	async function getCountryByIP(ip) {

		let response = await xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/portal/ip-detail',
			body: {
				ip: ip
			}
		});

		return response.data;
	}

	function runDynamicFunction(name = null) {
		return new Promise((resolve, reject) => {
			let error = false;
			let promisesResponse = [];
			let promises = [];
			let elements = document.querySelectorAll(`[data-dynamic-function]`);

			elements.forEach(ele => {
				if (ele.dataset.dynamicFunction == name && name !== null) promises.push(eval(ele.dataset
					.dynamicFunction)());
				else promises.push(eval(ele.dataset.dynamicFunction)());
			});

			let promiseInterval = setInterval(() => {

				promises.forEach(promise => {
					promise.then((data) => {
						promisesResponse.push(data);
					}).catch((err) => {
						error = true;
					});
				});

				if (promisesResponse.length >= promises.length) {
					clearInterval(promiseInterval);
					resolve(promisesResponse);
				} else if (error === true) {
					clearInterval(promiseInterval);
					reject(true);
				}

				debugLog("=== Dynamic Fields Interval ===");
			}, 500);

		});
	}

	function copyToClipboard(text) {
		const textarea = document.createElement('textarea');
		textarea.value = text;
		textarea.setAttribute('readonly', '');
		textarea.style.position = 'absolute';
		textarea.style.left = '-9999px';
		document.body.appendChild(textarea);
		textarea.select();
		document.execCommand('copy');
		document.body.removeChild(textarea);
	}

	function convertHTMLToSymbols(text) {
		const element = document.createElement('textarea');
		element.innerHTML = text;
		return element.value;
	}

	function groupUntilColumn(arr) {

		if (arr.length <= 0) return [];
		let delimiter = arr[0].column_name;

		let group = arr.reduce((acc, obj) => {

			if (obj.column_name === delimiter) acc.push({});

			if (obj.media !== undefined && obj.media !== null && obj.media !== undefined) {
				acc[acc.length - 1][obj.column_name] = obj.media;
			} else acc[acc.length - 1][obj.column_name] = obj.column_value;


			return acc;

		}, []);

		return group;
	}

	function getElementValue(el) {

		if (el == null || el == undefined) return '';

		if (el.nodeName.toLowerCase() === 'input' || el.nodeName.toLowerCase() === 'select' || el.nodeName
			.toLowerCase() === 'textarea') return el.value;
		else if (el.nodeName.toLowerCase() === 'img') return el.getAttribute('data-src');
		else if (el.nodeName.toLowerCase() === 'video') return el.getAttribute('data-src');
		else return el.innerHTML;
	}

	function hexToRgb(hex) {
		hex = hex.replace(/^#/, '');
		const r = parseInt(hex.substring(0, 2), 16);
		const g = parseInt(hex.substring(2, 4), 16);
		const b = parseInt(hex.substring(4, 6), 16);
		return [r, g, b];
	}
</script>
@yield('page-script')
@stop