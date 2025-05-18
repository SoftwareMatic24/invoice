@extends('layouts.portal')

@php
	$package = Subscription::package($subscriptionPackageId ?? NULL);
@endphp

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" class="section no-shadow" onsubmit="return false;">
			<div class="section-body">
				<div class="form-group">
					<div class="grids grids-2 gap-2">
						<div class="grid">
							<label class="input-style-1-label">{{ __('title') }} <span class="required">*</span></label>
							<input name="title" type="text" class="input-style-1">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('classification') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="classification" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									@foreach(Subscription::classifications() as $classification)
									<option value="{{ $classification['id'] }}">{{ $classification['name'] }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('price') }} <span class="required">*</span></label>
							<input name="price" type="text" class="input-style-1">
						</div>
						<div class="grid">
							<label class="input-style-1-label">{{ __('status') }} <span class="required">*</span></label>
							<div class="custom-select-container">
								<select name="status" class="input-style-1">
									<option value="">{{ __('select') }}</option>
									<option value="active">{{ __('active') }}</option>
									<option value="inactive">{{ __('inactive') }}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="grids grids-1 gap-2 margin-top-2">
						<div class="grid">
							<div class="form-group">
								<label class="input-style-1-label">{{ __('description') }}</label>
								<textarea name="description" class="input-style-1"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<form action="#" onsubmit="return false" class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('items') }}</h2>
			</div>
			<div class="section-body">
				<div class="form-group" style="margin-top: 0;">
					<div id="package-items"></div>
				</div>
				<div class="form-group">
					<button onclick="addNewItem()" class="button button-primary-border">{{ __('add new item') }}</button>
				</div>
			</div>
		</form>
		@if(count($limits) > 0)
		<form action="#" onsubmit="return false" class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('package limits') }}</h2>
			</div>
			<div class="section-body">
				<div id="limits">
					@foreach($limits as $limitIndex=>$limit)
					<div class="form-group" style="{{ $limitIndex == 0 ? 'margin-top:0px;' : '' }}">
						<label class="input-style-1-label">{{ ucfirst(__(strtolower($limit["label"]))) }}</label>
						<input name="{{ $limit['slug'] }}" type="text" class="input-style-1">
					</div>
					@endforeach
				</div>
			</div>
		</form>
		@endif
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="savePackage()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'subscription') }}

<script>

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		if(!isEmpty(staticPackageId())) populatePackage(staticPackage());
		else addNewItem();
	}

	/**
	 * Static data
	 */

	function staticPackageId() {
		return '{{ $subscriptionPackageId ?? "" }}';
	}

	function staticPackage(){
		let package = '{!! addSlashes(json_encode($package)) !!}';
		return JSON.parse(package);
	}

	/**
	 * Save
	 */

	async function savePackage() {

		let titleEl = document.querySelector('[name="title"]');
		let priceEl = document.querySelector('[name="price"]');
		let statusEl = document.querySelector('[name="status"]');
		let classificationEl = document.querySelector('[name="classification"]');
		let descriptionEl = document.querySelector('textarea[name="description"]');

		let items = getPackageItems();
		let limits = getLimits();

		let postData = {
			title: titleEl.value,
			description: descriptionEl.value,
			price: priceEl.value,
			status: statusEl.value,
			classificationId: classificationEl.value,
			items: items,
			limits: limits
		};

		let n = showSavingNotification();
		let response = await Subscription.savePackage(staticPackageId(), postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = `${PREFIXED_URL}/subscription/manage`;
	}

	/**
	 * Populate
	 */

	function populatePackage(data) {
		let titleEl = document.querySelector('[name="title"]');
		let priceEl = document.querySelector('[name="price"]');
		let statusEl = document.querySelector('[name="status"]');
		let classificationEl = document.querySelector('select[name="classification"]');
		let descriptionEl = document.querySelector('textarea[name="description"]');

		titleEl.value = data.title;
		priceEl.value = data.price;
		statusEl.value = data.status;
		descriptionEl.value = data.description;
		classificationEl.value = data.classification_id;

		data.details.forEach(item => {
			addNewItem(item);
		});

		data.limits.forEach(limit => {
			let slug = limit.slug;
			let el = document.querySelector(`input[name="${slug}"]`);
			if (el !== null) el.value = limit.limit;
		});

	}

	/**
	 * Other
	 */

	function addNewItem(data = null) {
		let view = `
			<div class="grids grids-2 gap-2 margin-top-5	 position-relative">
				<div class="grid">
					<input name="item-name" type="text" class="input-style-1" placeholder="{{ __('item') }} {{ strtolower(__('name')) }}" value="${data === null ? '' : data.name}">
				</div>
				<div class="grid">
					<div class="custom-select-container">
						<select name="item-included" class="input-style-1">
							<option ${(data !== null && data.included == '1') ? 'selected' : ''} value="true">{{ __('included') }}</option>
							<option  ${(data !== null && data.included == '0') ? 'selected' : ''} value="false">{{ __('not included') }}</option>
						</select>
					</div>
				</div>
				<svg style="position:absolute;right:0;top:-3rem;" onclick="confirmRemoveItem()" class="dynamic-content-close" ><use xlink:href="${BASE_URL}/assets/icons.svg#cross" /></svg>
			</div>
		`;


		document.querySelector('#package-items').insertAdjacentHTML('beforeend', view);

	}

	function getPackageItems() {
		let items = [];

		let packageItemsEl = document.querySelector('#package-items');
		let grids = packageItemsEl.querySelectorAll('.grids');

		grids.forEach(grid => {
			let itemNameEl = grid.querySelector('[name="item-name"]');
			let itemIncludedEl = grid.querySelector('[name="item-included"]');

			if (itemNameEl.value !== '') {
				items.push({
					name: itemNameEl.value,
					included: itemIncludedEl.value == 'true' ? 1 : 0
				});
			}

		});

		return items;
	}

	function confirmRemoveItem() {

		let target = event.target;
		let container = target.closest('.grids');

		Confirmation.show({
			positiveButton: {
				function: function() {
					container.remove();
				}
			}
		});

	}

	function getLimits() {

		let limits = [];

		let limitEl = document.querySelector('#limits');
		if(!limitEl) return limits;

		let inputEls = limitEl.querySelectorAll('input[name]');

		inputEls.forEach(inputEl => {
			limits.push({
				name: inputEl.name,
				value: inputEl.value
			});
		});

		return limits;
	}
</script>
@stop