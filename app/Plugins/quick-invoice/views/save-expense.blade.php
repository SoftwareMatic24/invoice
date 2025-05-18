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
								<input name="title" type="text" class="input-style-1" value="{{ $expense['title'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('category') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="category" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach(QuickInvoiceExpense::userCategories($userId) as $category)
										@if($category['id'] == ($expense["expense_category"] ?? NULL))
										<option value="{{ $category['id'] }}" selected>{{ $category['name'] }}</option>
										@else
										<option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
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
								<label class="input-style-1-label">{{ __('reference number') }}</label>
								<input name="reference-number" type="text" class="input-style-1" value="{{ $expense['reference_number'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('expense date') }}</label>
								<input name="expense-date" type="date" class="input-style-1" value="{{ $expense['expense_date'] ?? '' }}">
							</div>

						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('amount') }}</h2>
			</div>
			<div class="section-body margin-top-2">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('currency') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="currency" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach(Constant::currencies() as $currencyCode=>$currency)
										@if($currencyCode == ($expense["currency"] ?? NULL))
										<option value="{{ $currencyCode }}" selected>{{ $currency }}</option>
										@else
										<option value="{{ $currencyCode }}">{{ $currency }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('price/cost') }} <span class="required">*</span></label>
								<input name="price" type="text" class="input-style-1" value="{{ $expense['price'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">
									Tax
									<select name="tax-type" style="margin-left: 0.6rem;">
										@foreach(["percentage"=>"%", "amount"=>"amount"] as $key=>$value)
										@if($key == ($expense["tax_type"] ?? NULL))
										<option value="{{ $key }}" selected>{{ $value }}</option>
										@else
										<option value="{{ $key }}">{{ $value }}</option>
										@endif
										@endforeach
									</select>
								</label>
								<input name="tax" type="text" class="input-style-1" value="{{ $expense['tax'] ?? '' }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="section no-shadow | margin-top-3">
			<div class="section-header">
				<h2 class="heading heading-sm">{{ __('other') }}</h2>
			</div>
			<div class="section-body | mrgin-top-3">
				<form action="#" autocomplete="off">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('client') }}</label>
								<div class="custom-select-container">
									<select name="client" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach(QuickInvoiceClient::userClients($userId) as $client)
										@if($client['id'] == ($expense["client"]["id"] ?? NULL))
										<option value="{{ $client['id'] }}" selected>{{ $client['name'] }}</option>
										@else
										<option value="{{ $client['id'] }}">{{ $client['name'] }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="grid">
								<label class="input-style-1-label">{{ __('business') }}</label>
								<div class="custom-select-container">
									<select name="business" class="input-style-1">
										<option value="">{{ __('select') }}</option>
										@foreach(QuickInvoiceBusiness::userBusinesses($userId) as $business)
										@if($business['id'] == ($expense["business"]["id"] ?? NULL))
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
						<label class="input-style-1-label">{{ __('note') }}</label>
						<textarea name="note" class="input-style-1" rows="4" style="resize: none;">{{ $expense["note"] ?? "" }}</textarea>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveExpense()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/expense.js', 'quick-invoice') !!}

<script>
	/**
	 * Static data
	 */

	function staticExpenseId() {
		return '{{ $expenseId ?? "" }}';
	}

	/**
	 * Save
	 */

	async function saveExpense() {

		let titleEl = document.querySelector('[name="title"]');
		let categoryEl = document.querySelector('[name="category"]');
		let referenceNumberEl = document.querySelector('[name="reference-number"]');
		let expenseDateEl = document.querySelector('[name="expense-date"]');
		let currencyEl = document.querySelector('[name="currency"]');
		let priceEl = document.querySelector('[name="price"]');
		let taxEl = document.querySelector('[name="tax"]');
		let taxTypeEl = document.querySelector('[name="tax-type"]');
		let clientEl = document.querySelector('[name="client"]');
		let businessEl = document.querySelector('[name="business"]');
		let noteEl = document.querySelector('[name="note"]');

		let postData = {
			title: titleEl.value,
			category: categoryEl.value,
			referenceNumber: referenceNumberEl.value,
			expenseDate: expenseDateEl.value,
			currency: currencyEl.value,
			price: priceEl.value,
			tax: taxEl.value,
			taxType: taxTypeEl.value,
			client: clientEl.value,
			business: businessEl.value,
			note: noteEl.value
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceExpense.saveUserExpense(staticExpenseId(), postData, {target: 'save-button'});
		showResponseNotification(n, response);
	
		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}
</script>
@stop