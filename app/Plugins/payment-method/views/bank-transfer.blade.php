@extends('layouts.portal')
@section('main-content')
<div class="grids grids-2">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form id="page-form" action="#">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('bank name') }}</label>
								<input name="local-bank-name" type="text" class="input-style-1" value="{{ $entry['name'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('branch code') }}</label>
								<input name="local-bank-branch-code" type="text" class="input-style-1" value="{{ $entry['other']['branch-code'] ?? '' }}">
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('account number') }}</label>
								<input name="local-bank-iban-number" type="text" class="input-style-1" value="{{ $entry['other']['iban-number'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('sort code') }}</label>
								<input name="local-bank-sort-code" type="text" class="input-style-1" value="{{ $entry['other']['sort-code'] ?? '' }}">
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="grids grids-2 gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('account name') }}</label>
								<input name="local-bank-account-title" type="text" class="input-style-1" value="{{ $entry['other']['account-title'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }}</label>
								<div class="custom-select-container">
									<select name="local-bank-status" class="input-style-1">
										@foreach(["active","inactive"] as $status)
										@if($status === ($entry['status'] ?? NULL))
										<option value="{{ $status }}" selected>{{ ucfirst($status) }}</option>
										@else
										<option value="{{ $status }}">{{ ucfirst($status) }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="grids grids-2 gap-3">
							<div class="grid">
								<label class="input-style-1-label">{{ __('identifier') }} <span class="required">*</span> </label>
								<div class="custom-select-container">
									<select name="identifier" class="input-style-1">
										@foreach($identifiers as $identifier)
										@if($identifier->slug == ($entry['payment_method_identifier'] ?? NULL))
										<option value="{{ $identifier->slug }}" selected>{{ Str::slug($identifier->slug, " ") }}</option>
										@else
										<option value="{{ $identifier->slug }}">{{ Str::slug($identifier->slug, " ") }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>

					<div class="form-group">
						<label class="input-style-1-label">{{ __('note') }}</label>
						<textarea name="local-bank-note" class="input-style-1">{{ $entry['note'] ?? "" }}</textarea>
					</div>

					<div class="form-group">
						<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" class="button button-primary">{{ __('save') }}</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	<div class="grid"></div>
</div>
@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'payment-method') }}

<script>
	let type = '{!! $type !!}';
	let entryId = '{{ $entryId ?? "" }}';
	let pageForm = document.querySelector('#page-form');

	pageForm.addEventListener('submit', async function() {
		event.preventDefault();

		let name = pageForm.querySelector('[name="local-bank-name"]').value;
		let branchCode = pageForm.querySelector('[name="local-bank-branch-code"]').value;
		let sortCode = pageForm.querySelector('[name="local-bank-sort-code"]').value;
		let iban = pageForm.querySelector('[name="local-bank-iban-number"]').value;
		let accountTitle = pageForm.querySelector('[name="local-bank-account-title"]').value;
		let identifier = pageForm.querySelector('[name="identifier"]').value;

		let status = pageForm.querySelector('[name="local-bank-status"]').value;
		let note = pageForm.querySelector('[name="local-bank-note"]').value;

		let postData = {
			name: name,
			status: status,
			paymentMethodSlug: 'bank-transfer',
			note: note,
			identifier: identifier,
			other: {
				'branch-code': branchCode,
				'sort-code': sortCode,
				'iban-number': iban,
				'account-title': accountTitle
			}
		};

		let n = showSavingNotification();
		let response = await PaymentMethod.saveEntry(type, entryId, postData, {target: 'save-button'});
		showResponseNotification(n, response);

		if(response.data.status === 'success') window.location.href = '{{ $backURL }}';
		
	});
</script>

@parent
@stop