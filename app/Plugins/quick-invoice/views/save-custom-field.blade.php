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
								<label class="input-style-1-label">{{ __('name') }} <span class="required">*</span></label>
								<input name="name" type="text" class="input-style-1" value="{{ $field['name'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('document') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="document-type" class="input-style-1">
										@foreach(["invoice"=>"Invoice","proposal"=>"Proposal","delivery-note"=>"Delivery Note"] as $key=>$value)
										@if($key == ($field["document_type"] ?? NULL))
										<option value="{{ $key }}" selected>{{ $value }}</option>
										@else
										<option value="{{ $key }}">{{ $value }}</option>
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
								<label class="input-style-1-label">{{ __('position') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="position" class="input-style-1">
										@foreach(["top"=>"Top","bottom"=>"Bottom"] as $key=>$value)
										@if($key == ($field["position"] ?? NULL))
										<option value="{{ $key }}" selected>{{ $value }}</option>
										@else
										<option value="{{ $key }}">{{ $value }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('business') }} <span class="required">*</span></label>
								<div class="custom-select-container">
									<select name="business" class="input-style-1">
										@foreach(QuickInvoiceBusiness::userBusinesses($userId) as $business)
										@if($business['id'] == ($field["business_id"] ?? NULL))
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
				</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveField()" class="button button-primary button-block">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/document.js', 'quick-invoice') }}

<script>
	
	/**
	 * Static data
	 */

	function staticFieldId(){
		return '{{ $documentFieldId ?? "" }}';
	}

	async function saveField() {

		let nameEl = document.querySelector('[name="name"]');
		let documentTypeEl = document.querySelector('[name="document-type"]');
		let positionEl = document.querySelector('[name="position"]');
		let businessEl = document.querySelector('[name="business"]');

		let postData = {
			name: nameEl.value,
			documentType: documentTypeEl.value,
			position: positionEl.value,
			businessId: businessEl.value
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceDocument.saveUserDocumentCustomField(staticFieldId(), postData, {target: 'save-button'});
		showResponseNotification(n, response);
		
		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	}
</script>
@stop