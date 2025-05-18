@extends('layouts.portal')
@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form action="#" id="page-form" onsubmit="return false;">
					<div class="form-group">
						<label class="input-style-1-label">{{ __('description') }}</label>
						<textarea name="account-description" class="input-style-1">{{ $branding["account-page-description"] ?? "" }}</textarea>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid | flex-column-reverse-on-md">
			<div class="grid-widget | margin-bottom-2">
				<div class="button-group">
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="save()" class="button button-primary button-block">{{ __('update') }}</button>
				</div>
			</div>
			<div class="grid-widget | margin-bottom-2">
				<p class="grid-widget-text"><b>{{ __('background image') }}</b></p>
				@if($branding["account-page-image"] !== NULL)
				<img onclick="chooseBackgroundImage()" data-is="bg-image" class="width-100 margin-top-2 cursor-pointer" data-src="{{ $branding['account-page-image'] }}" src="{{ url('/').'/storage/'.$branding['account-page-image'] }}" alt="bg image">
				@else
				<img onclick="chooseBackgroundImage()" data-is="bg-image" class="width-100 margin-top-2 cursor-pointer" src="{{ asset('assets/account-bg.jpg') }}" alt="bg image">
				@endif
				<form action="#" onsubmit="return false;" class="margin-top-2">
					<div class="form-group">
						<label class="input-style-1-label">{{ __('overlay color') }}</label>
						<div class="color-input">
							<input type="text" class="input-style-1" value="{{ $branding['account-page-image-overlay'] ?? '#22262a' }}">
							<input name="account-page-image-overlay" type="color" class="input-style-1" style="max-width: 100px;" value="{{ $branding['account-page-image-overlay'] ?? '#22262a' }}">
						</div>
					</div>
					<div class="form-group">
						<label class="input-style-1-label">{{ __('opacity') }}</label>
						<div class="range-input">
							<input name="account-page-image-opacity" type="range" min="0" max="100" value="{{ $branding['account-page-image-opacity'] ?? '90' }}" class="input-style-1" oninput="this.nextElementSibling.value = this.value + '%'">
							<output>{{ $branding['account-page-image-opacity'] ?? '90' }}%</output>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'appearance') }}

<script>
	async function save() {

		let description = document.querySelector('[name="account-description"]').value;
		let overlay = document.querySelector('[name="account-page-image-overlay"]').value;
		let opacity = document.querySelector('[name="account-page-image-opacity"]').value;
		let bgImageURL = document.querySelector('[data-is="bg-image"]').dataset.src;

		let postData = {
			description,
			bgImageURL,
			overlay,
			opacity
		};

		let n = showSavingNotification();

		let response = await Appearance.saveAccountBranding(postData, {
			target: 'save-button'
		});

		showResponseNotification(n, response);
	}

	function chooseBackgroundImage() {
		mediaCenter.show({
			useAs: {
				title: '{{ __("set as background") }}',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector(`[data-is="bg-image"]`).setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector(`[data-is="bg-image"]`).setAttribute('data-src', imageURL);
				}
			}
		});
	}
</script>

@stop