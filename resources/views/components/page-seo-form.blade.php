@php
if(isset($meta) && is_string($meta)) $meta = json_decode($meta, true);
if(isset($meta) && !empty($meta)){
$tabTitle = $meta['tabTitle'] ?? '';
$metaAuthor = $meta['metaAuthor'] ?? '';
$metaDescription = $meta['metaDescription'] ?? '';
}

@endphp

<div class="form-group">
	<div class="grids grids-2 gap-2">
		<div class="grid">
			<label class="input-style-1-label">{{ __("tab title") }}</label>
			<input name="tab-title" type="text" class="input-style-1" value="{{ $tabTitle ?? '' }}">
		</div>

		<div class="grid">
			<label class="input-style-1-label">{{ __("author") }}</label>
			<input name="meta-author" type="text" class="input-style-1" value="{{ $metaAuthor ?? '' }}">
		</div>
	</div>
</div>

<div class="form-group">
	<label class="input-style-1-label">{{ __("meta description") }}</label>
	<input name="meta-description" type="text" class="input-style-1" value="{{ $metaDescription ?? '' }}">
</div>

@section('page-script')

<script>
	function getSEOFormData() {

		let title = document.querySelector('[name="tab-title"]').value;
		let author = document.querySelector('[name="meta-author"]').value;
		let description = document.querySelector('[name="meta-description"]').value;

		return {
			tabTitle: title,
			metaAuthor: author,
			metaDescription: description
		};
	}

	function setSEOFormData(data) {
		document.querySelector('[name="tab-title"]').value = data.tabTitle !== undefined ? data.tabTitle : '';
		document.querySelector('[name="meta-author"]').value = data.metaAuthor !== undefined ? data.metaAuthor : '';
		document.querySelector('[name="meta-description"]').value = data.metaDescription !== undefined ? data.metaDescription : '';
	}

	function resetSEOFormData() {

		setSEOFormData({
			tabTitle: '',
			metaAuthor: '',
			metaDescription: ''
		});
	}
</script>

@parent
@stop