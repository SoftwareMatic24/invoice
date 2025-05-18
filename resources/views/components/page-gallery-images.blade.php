<p class="grid-widget-text"><b>{{ ucwords(__("gallery images")) }}</b></p>
<div class="post-gallery-media post-gallery-images | margin-top-1">
	<span class="button add-button" onclick="chooseGalleryImages()">+</span>
</div>


<!-- Modal -->
<div id="gallery-image-modal" class="modal" style="max-width: 50%;">
	<div class="modal-header">
		<p class="modal-title">{{ ucwords(__("gallery image")) }}</p>
		<span onclick="hideModal('gallery-image-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="max-height:70vh;">
		<img src="#" alt="gallery image">
	</div>
	<div class="modal-footer">
		<div class="button-group">
			<button class="button button-danger-border" onclick="removeGalleryMedia('image')">Remove Image</button>
		</div>
	</div>
</div>

<script>

	let setGalleryImageText = '{{ __("set gallery image") }}';

	function chooseGalleryImages() {
		mediaCenter.show({
			useAs: {
				title: setGalleryImageText,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let galleryContainer = document.querySelector('.post-gallery-images');
					media.forEach(function(image) {
						galleryContainer.insertAdjacentHTML("beforeend", `<img onclick="showGalleryMedia('image')" data-media-id="${image.id}" src="${BASE_URL}/storage/${image.url}" />`);
					});
				}
			}
		});
	}

	function showGalleryMedia(mediaType) {

		let target = event.target;
		let src = target.getAttribute('src');

		let modal = document.querySelector('#gallery-image-modal');
		let videoModal = document.querySelector('#gallery-video-modal');


		if (modal !== null) {
			let img = modal.querySelector('.modal-body img');
			if (mediaType === 'image') {
				img.setAttribute('src', src);
				showModal('gallery-image-modal');
			}
		}

		if (videoModal !== null) {
			let video = videoModal.querySelector('.modal-body video');
			if (mediaType === 'video') {
				video.setAttribute('src', src);
				showModal('gallery-video-modal');
			}
		}




	}

	function removeGalleryMedia(mediaType) {

		let modal = document.querySelector('#gallery-image-modal');
		let videoModal = document.querySelector('#gallery-video-modal');
		let img = null;
		let video = null;
		

		if(modal !== null) img = modal.querySelector('.modal-body img');
		if(videoModal !== null) video = videoModal.querySelector('.modal-body video');

		Confirmation.show({
			positiveButton: {
				function: function() {

					if (mediaType === 'image' && img !== null) {
						hideModal('gallery-image-modal');
						let src = img.getAttribute('src');
						img.setAttribute('src', '#');

						let images = document.querySelectorAll('.post-gallery-images img');
						images.forEach(i => {
							if (i.getAttribute('src') === src) i.remove();
						});
					} else if (mediaType === 'video' && video !== null) {
						hideModal('gallery-video-modal');
						let src = video.getAttribute('src');
						video.setAttribute('src', '#');

						let videos = document.querySelectorAll('.post-gallery-videos video');
						videos.forEach(v => {
							if (v.getAttribute('src') === src) v.remove();
						});
					}

				}
			}
		});

	}
</script>