@extends('layouts.portal')
@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('title') }} <span class="required">*</span></label>
								<input name="title" type="text" class="input-style-1" value="{{ $product['title'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('unit price') }} <span class="required">*</span></label>
								<input name="price" type="text" class="input-style-1" value="{{ $product['price'] ?? '' }}">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid | hide">
								<label class="input-style-1-label">{{ __('type') }} <span class="required">*</span></label>
								<select name="type" class="input-style-1">
									<option value="{{ $productType }}">{{ $productType }}</option>
								</select>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('item code') }}</label>
								<input name="code" type="text" class="input-style-1" value="{{ $product['code'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('unit name') }}</label>
								<input name="unit" type="text" class="input-style-1" value="{{ $product['unit'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveProduct()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/product.js', 'quick-invoice') !!}

<script>
	let productId = '{{ $productId ?? "" }}';

	/**
	 * Static data
	 */

	function staticProductId() {
		return '{{ $productId ?? "" }}';
	}

	/**
	 * Save
	 */

	async function saveProduct() {

		let titleEl = document.querySelector('[name="title"]');
		let priceEl = document.querySelector('[name="price"]');
		let codeEl = document.querySelector('[name="code"]');
		let unitEl = document.querySelector('[name="unit"]');
		let typeEl = document.querySelector('[name="type"]');

		let postData = {
			title: titleEl.value,
			price: priceEl.value,
			code: codeEl.value,
			unit: unitEl.value,
			type: typeEl.value
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceProduct.saveProduct(staticProductId(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}
</script>
@stop