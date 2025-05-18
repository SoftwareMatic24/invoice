let lazyLoadScrollTimeout;

window.addEventListener("scroll", function () {
	clearTimeout(lazyLoadScrollTimeout);
	lazyLoadScrollTimeout = setTimeout(function () {
		handleLazyLoadImages();
	}, 300);
});

function handleLazyLoadImages() {

	let lazyImages = document.querySelectorAll("img.lazy");
	let lazyVideos = document.querySelectorAll("video.lazy");

	if ('IntersectionObserver' in window) {

		let lazyImageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					let lazyImage = entry.target;
					let srcSet = lazyImage.dataset.srcset || '';
					lazyImage.src = lazyImage.dataset.src;
					if (srcSet !== '') lazyImage.srcset = srcSet;
					lazyImage.classList.remove('lazy');
					lazyImageObserver.unobserve(lazyImage);
				}
			});
		});

		let lazyVideoObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					let videoEl = entry.target;
					videoEl.classList.remove("lazy");
					videoEl.removeAttribute("preload");
					if (videoEl.dataset.autoplay !== undefined) videoEl.setAttribute("autoplay", true);
				}
			});
		});

		lazyImages.forEach(lazyImage => {
			lazyImageObserver.observe(lazyImage);
		});

		lazyVideos.forEach(lazyVideo => {
			lazyVideoObserver.observe(lazyVideo);
		});



	} else {
		lazyImages.forEach(lazyImage => {
			let srcSet = lazyImage.dataset.srcset || '';
			lazyImage.src = lazyImage.dataset.src;
			if (srcSet !== '') lazyImage.srcset = srcSet;
			lazyImage.classList.remove('lazy');
		});
	}

}