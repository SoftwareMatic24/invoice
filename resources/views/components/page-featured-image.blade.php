<p class="grid-widget-text"><b>{{ ucwords(__($heading ?? "featured image")) }}</b></p>
<img onclick="chooseFeaturedImage()" data-is="featured-image" class="width-100 margin-top-2 cursor-pointer" src="{{ asset('assets/default-image-300x158.jpg') }}" alt="featured image">

<script>
	let setAsFeaturedImageText = '{{ __("set as featured image") }}';

	function chooseFeaturedImage() {
		mediaCenter.show({
			useAs: {
				title: setAsFeaturedImageText,
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector('[data-is="featured-image"]').setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector('[data-is="featured-image"]').setAttribute('data-src', media[0].id);
				}
			}
		});
	}
</script>