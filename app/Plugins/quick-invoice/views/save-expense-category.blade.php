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
								<input name="name" type="text" class="input-style-1" value="{{ $category['name'] ?? '' }}">
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
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveCategory()" class="button button-primary button-block">{{ __('save') }}</button>
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

	function staticCategoryId(){
		return '{{ $categoryId ?? "" }}';
	}

	async function saveCategory() {
		let name = document.querySelector('[name="name"]').value;
		let n = showSavingNotification();		
		let response = await QuickInvoiceExpense.saveUserCategory(staticCategoryId(), name, {target: 'save-button'})
		showResponseNotification(n, response);
		if (response.data.status === 'success') window.location.href = '{{ $backURL }}'
	}
</script>
@stop