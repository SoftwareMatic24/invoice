<p class="grid-widget-text"><b>{{ ucwords(__($heading ?? "featured video")) }}</b></p>
<div class="video-upload-container featured-video-container">
	<video poster="{{ asset('assets/default-video-image-300x158.jpg') }}" onclick="chooseFeaturedVideo()" src="" data-is="featured-video" class="width-100 margin-top-2 cursor-pointer"></video>
	<span onclick="chooseFeaturedVideoThumbnail()" data-is="add-video-thumbnail" class="tag tag-light">{{ ucwords(__("upload thumbnail")) }}</span>
	<span onclick="removeFeaturedVideoThumbnail()" data-is="remove-video-thumbnail" class="tag tag-danger">{{ ucwords(__("remove thumbnail")) }}</span>
</div>

<script>

	let setAsFeaturedVideoText = '{{ __("set as featured video") }}';
	let setAsFeaturedVideoThumbnailText = '{{ __("set as featured video thumbnail") }}';

	function chooseFeaturedVideo() {
		mediaCenter.show({
			useAs: {
				title: setAsFeaturedVideoText,
				max: 1,
				mediaType: 'video',
				onUse: function(params = []) {
					let media = params.media;
					let url = media[0].url;
					let mediaId = media[0].id;
					document.querySelector('.featured-video-container').classList.remove('has-thumbnail');
					document.querySelector('.featured-video-container').classList.add('has-video', 'no-thumbnail');
					document.querySelector('[data-is="featured-video"]').setAttribute('src', BASE_URL + '/storage/' + url);
					document.querySelector('[data-is="featured-video"]').setAttribute('data-src', mediaId);
					document.querySelector('[data-is="featured-video"]').removeAttribute('poster');
				}
			}
		});
	}

	function chooseFeaturedVideoThumbnail() {
		mediaCenter.show({
			useAs: {
				title: setAsFeaturedVideoThumbnailText,
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					let mediaId = media[0].id;

					document.querySelector('[data-is="featured-video"]').setAttribute('poster', BASE_URL + '/storage/' + imageURL);
					document.querySelector('[data-is="featured-video"]').setAttribute('data-poster', mediaId);
					document.querySelector('.featured-video-container').classList.remove('no-thumbnail');
					document.querySelector('.featured-video-container').classList.add('has-thumbnail');
				}
			}
		});
	}

	function removeFeaturedVideoThumbnail() {
		if (event) event.preventDefault();
		document.querySelector('[data-is="featured-video"]').removeAttribute('data-poster');
		document.querySelector('[data-is="featured-video"]').removeAttribute('poster');
		document.querySelector('.featured-video-container').classList.remove('has-thumbnail');
		document.querySelector('.featured-video-container').classList.add('no-thumbnail');
	}
</script>