@extends('layouts.portal')

@php
$clients = QuickInvoiceClient::userClients($userId);
$businesses = QuickInvoiceBusiness::userBusinesses($userId);
@endphp

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">

		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('client') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select onchange="handleClientChange()" name="client" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach($clients as $client)
										@if($client['id'] == ($document["client"]["id"] ?? NULL))
										<option value="{{ $client['id'] }}" selected>{{ $client['name'] }}</option>
										@else
										<option value="{{ $client['id'] }}">{{ $client['name'] }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="grid">
								<label class="input-style-1-label">{{ __('business') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select onchange="handleBusinessChange()" name="business" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach($businesses as $business)
										@if($business['id'] == ($document["business"]["id"] ?? NULL))
										<option value="{{ $business['id'] }}" selected>{{ $business['name'] }}</option>
										@else
										<option value="{{ $business['id'] }}">{{ $business['name'] }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('date of issue') }} <span class="required">*</span></label>
								<input name="issue-date" type="date" class="input-style-1" value="{{ $document['issue_date'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">
									@if($documentType === "invoice")
									{{ __('due date') }}
									@else
									{{ __('delivery date') }}
									@endif
								</label>
								<input name="due-date" type="date" class="input-style-1" value="{{ $document['due_date'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('document') }}</h2>
			</div>
			<div class="section-body | margin-top-2">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('document number') }}</label>
								<input name="document-number" type="text" class="input-style-1" value="{{ $document['document_number'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('reference number') }}</label>
								<input name="reference-number" type="text" class="input-style-1" value="{{ $document['reference_number'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('order number') }}</label>
								<input name="order-number" type="text" class="input-style-1" value="{{ $document['order_number'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('payment') }}</h2>
			</div>
			<div class="section-body margin-top-2">
				<form onsubmit="return false">

					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<div class="input-wrapper">
									<label class="input-style-1-label">{{ __('payment method') }}</label>
									<input name="payment-method" type="text" class="input-style-1 input-style-1-suggestion" value="{{ $document['payment_method'] ?? '' }}">
									<ul class="input-style-1-suggestions">
										@foreach(PaymentMethod::paymentMethods() as $row)
										<li>{{ $row['title'] }}</li>
										@endforeach
									</ul>
								</div>

							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('currency') }}</label>
								<div class="custom-select-container">
									<select name="currency" class="input-style-1" onchange="handleCurrencyChange()">
										<option value="">Select...</option>
										@foreach(Constant::currencies	() as $currencyCode=>$currency)
										@if(($document['currency'] ?? NULL) === $currencyCode)
										<option value="{{ $currencyCode }}" selected>{{ $currency }}</option>
										@else
										<option value="{{ $currencyCode }}">{{ $currency }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('delivery method') }}</label>
								<input name="delivery-type" type="text" class="input-style-1" value="{{ $document['delivery_type'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		
		<div class="section no-shadow | margin-top-3 hide" data-is="custom-fields-section">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('additional details') }}</h2>
			</div>
			<div class="section-body margin-top-2">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2" data-is="fields-container"></div>
					</div>
				</form>
			</div>
		</div>
	

		<div class="section no-shadow margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('items') }}</h2>
			</div>
			<div class="section-body">
				<div class="margin-top-2">
					<div class="document-item-list"></div>
					<div class="margin-top-2">
						<button onclick="addNewItem()" class="button button-sm button-primary-border">
							<svg class="icon" style="transform: translateX(-0.3rem);">
								<use xlink:href="{{ asset('assets/icons.svg#solid-plus-circle') }}" />
							</svg>
							{{ __('new item') }}
						</button>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveDocument()" class="button button-primary button-block">
					{{ __('save') }}
				</button>
			</div>
		</div>
		<div class="grid-widget | margin-bottom-2">
			<div>
				<label class="input-style-1-label">{{ __('introduction/salutation') }}</label>
				<textarea name="salutation" class="input-style-1">{{ $document['salutation'] ?? "" }}</textarea>
			</div>
			<div class="margin-top-2">
				<label class="input-style-1-label">{{ __('note') }}</label>
				<textarea name="note" class="input-style-1">{{ $document['note'] ?? "" }}</textarea>
			</div>
		</div>
		<div class="grid-widget sticky | margin-bottom-2" style="position: sticky;top:0">
			<label class="input-style-1-label">
				{{ __('discount') }}
				<select onchange="handleDiscountTypeChange()" name="discount-type" style="margin-left: 0.6rem;">
					@foreach(["percentage"=>"%", "amount"=>"amount"] as $key=>$value)
					@if(($document['discount_type'] ?? NULL) === $key)
					<option value="{{ $key }}" selected>{{ $value }}</option>
					@else
					<option value="{{ $key }}">{{ $value }}</option>
					@endif
					@endforeach
				</select>
			</label>
			<div class="input-wrapper">
				<svg class="info-icon">
					<use xlink:href="{{ asset('assets/icons.svg#solid-info') }}"></use>
				</svg>
				<span data-info="true">{{ __('discount-applient-after-vat') }}</span>
				<input oninput="handleDiscountChange()" name="discount" type="text" class="input-style-1" value="{{ $document['discount'] ?? '' }}">
			</div>
			<p class="grid-widget-text | margin-top-2"><b>{{ __('total') }}</b></p>
			<p class="grid-widget-total"><span data-is="items-total">0</span> <span data-is="items-currency"></span></p>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/document.js', 'quick-invoice') !!}

<script>
	let items = [];

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		let businessEl = document.querySelector('[name="business"]');
		businessEl.dispatchEvent(new Event('change'));
		populateCustomFields(businessEl.value);
	}

	/**
	 * Static data
	 */

	function staticClients() {
		let clients = '{!! addSlashes(json_encode($clients)) !!}';
		return JSON.parse(clients);
	}

	function staticDocumentId() {
		return '{!! $documentId ?? ""  !!}';
	}

	function staticDocumentType() {
		return '{!! $documentType ?? "" !!}';
	}

	function staticDocument() {
		let doc = '{!! addSlashes(json_encode($document)) !!}';
		return JSON.parse(doc);
	}

	function staticProducts() {
		let products = '{!! addSlashes(json_encode($products));  !!}';
		return JSON.parse(products);
	}

	function staticCustomFields(){
		let fields = '{!! addSlashes(json_encode($fields)) !!}';
		return JSON.parse(fields);
	}


	/**
	 * Save
	 */

	async function saveDocument() {
		if (event) event.preventDefault();
		
		let clientEl = document.querySelector('[name="client"]');
		let businessEl = document.querySelector('[name="business"]');
		let issueDateEl = document.querySelector('[name="issue-date"]');
		let dueDateEl = document.querySelector('[name="due-date"]');
		let documentNumberEl = document.querySelector('[name="document-number"]');
		let referenceNumberEl = document.querySelector('[name="reference-number"]');
		let orderNumberEl = document.querySelector('[name="order-number"]');
		let paymentMethodEl = document.querySelector('[name="payment-method"]');
		let deliveryTypeEl = document.querySelector('[name="delivery-type"]');
		let currencyEl = document.querySelector('[name="currency"]');
		let noteEl = document.querySelector('[name="note"]');
		let salutationEl = document.querySelector('[name="salutation"]');
		let discountEl = document.querySelector('[name="discount"]');
		let discountTypeEl = document.querySelector('[name="discount-type"]');
		let meta = {};

		meta = {
			...meta,
			...getAdditionalData()
		};

		let postData = {
			client: clientEl.value,
			business: businessEl.value,
			issueDate: issueDateEl.value,
			dueDate: dueDateEl.value,
			documentNumber: documentNumberEl.value,
			referenceNumber: referenceNumberEl.value,
			orderNumber: orderNumberEl.value,
			paymentMethod: paymentMethodEl.value,
			deliveryType: deliveryTypeEl.value,
			currency: currencyEl.value,
			note: noteEl.value,
			salutation: salutationEl.value,
			discount: discountEl.value,
			discountType: discountTypeEl.value,
			items: items,
			meta: meta
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceDocument.save(staticDocumentId(), staticDocumentType(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') {
			window.location.href = `{{ $backURL ?? '' }}`;
		}
	}

	/**
	 * Other
	 */

	function getAdditionalData() {
		let els = document.querySelectorAll('[name^="additional-field-"]');
		return Array.from(els).reduce((acc, el) => {
			let key = el.name.replaceAll('additional-field-', '');
			acc[key] = el.value;
			return acc;
		}, {});
	}

	/**
	 * Item
	 */

	function addNewItem(itemP = null) {
		if (event) event.preventDefault();

		let item = {
			'item-vat': 0,
			'item-quantity': 1
		};
		if (itemP !== null) {
			item['item-name'] = itemP.title;
			item['item-quantity'] = itemP.quantity;
			item['item-unit-price'] = itemP.unit_price;
			item['item-unit'] = itemP.unit;
			item['item-code'] = itemP.code;
			item['item-vat'] = itemP.vat;
		}

		items.push(item);
		renderItems(items);
	}

	function deleteItem(itemIndex) {
		if (event) event.preventDefault();

		Confirmation.show({
			positiveButton: {
				function: function() {
					items.splice(itemIndex, 1);
					renderItems(items);
				}
			}
		});
	}

	function renderItems(items) {
		let el = document.querySelector('.document-item-list');
		let _suggestionListHTML = suggestionListHTML(staticProducts());

		//1. Items
		let itemsHTML = items.map((item, itemIndex) => {
			return `
				<div class="d-flex gap-1 margin-top-2" data-item-index="${itemIndex}">
					<div style="min-width: 18rem;">
						<label class="input-style-1-label">Item Name</label>
						<div class="input-wrapper">
							<input oninput="handleItemChange(${itemIndex})" name="item-name" type="text" class="input-style-1 input-style-1-suggestion" value="${toStr(item, 'item-name')}">
							<ul class="input-style-1-suggestions | thin-scroll-bar">
								${_suggestionListHTML}
							</ul>
						</div>
					</div>
					<div >
						<label class="input-style-1-label">Item Code</label>
						<input oninput="handleItemChange(${itemIndex})" name="item-code" type="text" class="input-style-1" style="max-width: 14rem;" value="${toStr(item, 'item-code')}">
					</div>
					<div style="width:13rem">
						<label class="input-style-1-label">Unit Name</label>
						<input oninput="handleItemChange(${itemIndex})" name="item-unit" type="text" class="input-style-1" style="max-width: 14rem;" value="${toStr(item, 'item-unit')}">
					</div>
					<div style="width:13rem">
						<label class="input-style-1-label">Qty.</label>
						<input oninput="handleItemChange(${itemIndex})" name="item-quantity" type="text" class="input-style-1" style="max-width: 14rem;" value="${toStr(item, 'item-quantity')}">
					</div>
					<div style="width:13rem">
						<label class="input-style-1-label">Unit Price</label>
						<input oninput="handleItemChange(${itemIndex})" name="item-unit-price" type="text" class="input-style-1" style="max-width: 14rem;" value="${toStr(item, 'item-unit-price')}">
					</div>
					<div style="width:13rem">
						<label class="input-style-1-label">VAT %</label>
						<input oninput="handleItemChange(${itemIndex})" name="item-vat" type="text" class="input-style-1" style="max-width: 14rem;" value="${toStr(item, 'item-vat')}">
					</div>
					<div>
						<label class="input-style-1-label">&nbsp;</label>
						<button class="button button-icon button-icon-danger action-button" onclick="deleteItem(${itemIndex})">
							<svg class="icon">
								<use xlink:href="{{ asset('assets/icons.svg#solid-trash') }}" />
							</svg>
						</button>
					</div>
				</div>
			`;
		}).join('');

		el.innerHTML = itemsHTML;
		initInputStyle1Suggestions();
		renderItemsTotal(items);
	}

	function handleItemChange(itemIndex) {
		let targetEl = event.currentTarget;
		let value = targetEl.value;
		let name = targetEl.name;
		items[itemIndex][name] = value;
		renderItemsTotal(items);
	}

	function renderItemsTotal(items) {

		let discountEl = document.querySelector('[name="discount"]');
		let discountTypeEl = document.querySelector('[name="discount-type"]');

		let totalEl = document.querySelector('[data-is="items-total"]');
		let itemCurrencyEl = document.querySelector('[data-is="items-currency"]');
		let currencyEl = document.querySelector('[name="currency"]');

		let discount = parseFloat(discountEl.value);
		let discountType = discountTypeEl.value;
		if (isNaN(discount)) discount = 0;


		let pricing = calculateItemsTotal(items, discount, discountTypeEl.value);

		totalEl.innerHTML = formatNumber(pricing.total);
		if (currencyEl.value !== '') itemCurrencyEl.innerHTML = currencyEl.value;
	}

	/**
	 * Suggestion
	 */

	function suggestionListHTML(products) {
		return products.map(product => {
			return `<li onclick="selectSuggestion(${product.id})">${product.title}</li>`;
		}).join('');
	}

	function selectSuggestion(productId) {
		let targetEl = event.target;
		let itemEl = targetEl.closest('[data-item-index]');
		let itemIndex = itemEl.getAttribute('data-item-index');

		let product = staticProducts().find(p => p.id == productId);
		
		items[itemIndex]['item-name'] = toStr(product, 'title');
		items[itemIndex]['item-quantity'] = 1;
		items[itemIndex]['item-unit'] = toStr(product, 'unit');
		items[itemIndex]['item-code'] = toStr(product, 'code');
		items[itemIndex]['item-unit-price'] = toStr(product, 'price');
		items[itemIndex]['item-vat'] = 0;

		renderItems(items);
	}

	/**
	 * Currency
	 */

	function handleCurrencyChange() {
		renderItemsTotal(items);
	}

	/**
	 * Client
	 */

	function populateClientDefaults(client) {
		let discountEl = document.querySelector('[name="discount"]');
		let discountTypeEl = document.querySelector('[name="discount-type"]');
		let currencyEl = document.querySelector('[name="currency"]');
		let noteEl = document.querySelector('[name="note"]');
		let salutationEl = document.querySelector('[name="salutation"]');
		let paymentMethodEl = document.querySelector('[name="payment-method"]');

		currencyEl.value = toStr(client, 'default', 'currency_code');
		noteEl.value = toStr(client, 'default', 'note');
		salutationEl.value = toStr(client, 'default', 'salutation');
		paymentMethodEl.value = toStr(client, 'default', 'payment_method');
		discountEl.value = toStr(client, 'default', 'discount');
		discountTypeEl.value = toStr(client, 'default', 'discount_type');

		if (toStr(client, 'default', 'currency_code') !== '') currencyEl.dispatchEvent(new Event('change'));
	}

	function handleClientChange() {
		let targetEl = event.currentTarget;
		let clientId = targetEl.value;
		let client = staticClients().find(c => c.id == clientId);
		if (client === undefined) return;
		populateClientDefaults(client);
	}

	/**
	 * Business
	 */

	function handleBusinessChange(){
		let businessId = event.target.value;
		let fieldsContainerEl = document.querySelector('[data-is="fields-container"]');

		let fields = businessFields(businessId);
		let layout = customFieldsLayout(fields);

		if(fields.length > 0){
			showCustomFieldsSection();
			fieldsContainerEl.innerHTML = layout;
		}
		else {
			hideCustomFieldsSection();
		}

	}

	/**
	 * Custom Fields
	 */

	function showCustomFieldsSection(){
		let el = document.querySelector('[data-is="custom-fields-section"]');
		el.classList.remove('hide');
	}

	function hideCustomFieldsSection(){
		let el = document.querySelector('[data-is="custom-fields-section"]');
		el.classList.add('hide');

		clearCustomFieldsSection();
	}

	function clearCustomFieldsSection(){

	}

	function customFieldsLayout(fields){
		return fields.map(field => {
			return `
				<div class="grid">
					<label class="input-style-1-label">${field.name}</label>
					<input name="additional-field-${field.slug}" type="text" class="input-style-1">
				</div>
			`;
		}).join('');
	}

	function businessFields(businessId){
		return Object.values(staticCustomFields()).filter(field => field.business_id == businessId);
	}

	function populateCustomFields(businessId){
		let fields = businessFields(businessId);
		
		fields.forEach(field => {
			let inputEl = document.querySelector(`[name="additional-field-${field.slug}"]`);
			let value = staticDocument().meta[field.slug] || null;
			if(inputEl) inputEl.value = value;
		});
	}

	/**
	 * Discount
	 */

	function handleDiscountTypeChange() {
		renderItems(items);
	}

	function handleDiscountChange() {
		renderItems(items);
	}

	if (isEmpty(staticDocument())) addNewItem();
	else staticDocument().items.forEach(item => addNewItem(item));
</script>
@stop