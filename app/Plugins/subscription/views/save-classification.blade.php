@extends('layouts.portal')

@php
	$classification = Subscription::classificationBySlug($slug);
@endphp

@section('main-content')
<div class="grids grids-2">
	<div class="grid">
		<div class="section | no-shadow">
			<form id="page-form" class="section-body" onsubmit="return false;">
				<div class="grids grids-1">
					<div class="grid">
						<label class="input-style-1-label">{{ __('name') }} <span class="required">*</span></label>
						<input name="name" type="text" class="input-style-1" value="{{ $page['title'] ?? '' }}">
					</div>
				</div>
			</form>
			<div class="section-footer">
				<button onclick="saveClassification()" data-xhr-name="save-button" data-xhr-loading.attr="disabled" class="button button-primary">{{ __('save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

{{ loadPluginFile('js/script.js', 'subscription') }}

@section('page-script')
@parent
<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if(!isEmpty(staticClassificationSlug())) populateClassification(staticClassification());
	}


	/**
	 * Static data
	 */

	function staticClassificationSlug() {
		return '{{ $slug ?? "" }}'
	}

	function staticClassification(){
		let classification = '{!! addSlashes(json_encode($classification)) !!}';
		return JSON.parse(classification);
	}

	/**
	 * Save
	 */

	async function saveClassification() {

		let nameEl = document.querySelector('input[name="name"]');

		let postData=  {
			name: nameEl.value
		};

		let n = showSavingNotification();

		let response = await Subscription.saveClassification(staticClassificationSlug(), postData, {
			target: `save-button`
		});

		showResponseNotification(n, response);

		if (response.data.status === 'success') window.location.href = `{!! $backURL ?? '' !!}`;
	}

	/**
	 * Populate
	 */

	function populateClassification(classification) {
		let nameEl = document.querySelector('input[name="name"]');
		nameEl.value = classification.name;
	}
</script>

@stop