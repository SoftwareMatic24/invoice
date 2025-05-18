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
								<label class="input-style-1-label">{{ __('name') }} <span class="required">*</span></label>
								<input name="stripe-name" type="text" class="input-style-1" value="{{ $entry['name'] ?? '' }}">
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('email') }}</label>
								<input name="stripe-email" type="text" class="input-style-1" value="{{ $entry['email'] ?? '' }}">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('status') }} <span class="required">*</span></label>
								<div class="select-container chevron">
									<select name="status" class="input-style-1">
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
							<div class="grid">
								<label class="input-style-1-label">{{ __('identifier') }} <span class="required">*</span> </label>
								<div class="select-container chevron">
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
						</div>
					</div>
					<div class="form-group">
						<label class="input-style-1-label">{{ __('public key') }}</label>
						<input name="stripe-public-key" type="text" class="input-style-1" value="{{ $entry['public_key'] ?? '' }}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">{{ __('private key') }}</label>
						<input name="stripe-private-key" type="text" class="input-style-1" value="{{ $entry['private_key'] ?? '' }}">
					</div>
					<div class="form-group">
						<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" class="button button-primary">{{ __('save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
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
		let name = pageForm.querySelector('[name="stripe-name"]').value;
		let email = pageForm.querySelector('[name="stripe-email"]').value;
		let publicKey = pageForm.querySelector('[name="stripe-public-key"]').value;
		let privateKey = pageForm.querySelector('[name="stripe-private-key"]').value;
		let status = pageForm.querySelector('[name="status"]').value;
		let identifier = pageForm.querySelector('[name="identifier"]').value;

		let postData = {
			name: name,
			email: email,
			status: status,
			publicKey: publicKey,
			privateKey: privateKey,
			identifier: identifier,
			paymentMethodSlug: 'stripe'
		};

		let n = showSavingNotification();
		let response = await PaymentMethod.saveEntry(type, entryId, postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = '{{ $backURL }}';
	});
</script>
@parent
@stop