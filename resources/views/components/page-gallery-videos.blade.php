<p class="grid-widget-text"><b>{{ __("gallery videos") }}</b></p>
<div class="post-gallery-media post-gallery-videos | margin-top-1">
	<span class="button add-button" onclick="chooseGalleryVideos()">+</span>
</div>

<!-- Modal -->
<div id="gallery-video-modal" class="modal" style="max-width: 50%;">
	<div class="modal-header">
		<p class="modal-title">{{ __("gallery video") }}</p>
		<span onclick="hideModal('gallery-video-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="max-height:70vh;">
		<video controls src="#"></video>
	</div>
	<div class="modal-footer">
		<div class="button-group">
			<button class="button button-danger-border" onclick="removeGalleryMedia('video')">{{ __("remove video") }}</button>
		</div>
	</div>
</div>

<script>

	let setGalleryVideoText = '{{ __("set gallery video") }}';

	function chooseGalleryVideos() {
		mediaCenter.show({
			useAs: {
				title: setGalleryVideoText,
				mediaType: 'video',
				onUse: function(params = []) {
					let media = params.media;
					let galleryContainer = document.querySelector('.post-gallery-videos');

					media.forEach(function(video) {
						galleryContainer.insertAdjacentHTML("beforeend", `<video onclick="showGalleryMedia('video')" data-media-id="${video.id}" src="${BASE_URL}/storage/${video.url}" />`);
					});
				}
			}
		});
	}
</script>