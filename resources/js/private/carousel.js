function carouselSlider(id, frames) {
	this.id = id;
	this.frames = frames;
	this.currentSlide = 1;
	this.direction = null;
	this.inTransition = false;
	this.circular = true;

	this.next = function (index) {

		if (this.inTransition === true) return
		if (this.circular === false && this.currentSlide >= this.frames.length) return this.currentSlide = this.frames.length;

		let carousel = document.querySelector(`#${this.id} .carousel`);
		let carouselSlider = document.querySelector(`#${this.id} .carousel-slider`);

		if (this.direction === 'previous' && this.circular === true) {
			carouselSlider.prepend(carouselSlider.lastElementChild);
		}

		this.direction = 'next';
		this.inTransition = true;

		carousel.style.justifyContent = 'flex-start';

		if (this.circular === true) carouselSlider.setAttribute(`style`, `width:${frames.length * 100}%; transform:translateX(${-(100 / this.frames.length)}%);`);
		else {
			this.currentSlide++;
			carouselSlider.setAttribute(`style`, `width:${frames.length * 100}%; transform:translateX(${this.currentSlide * -(100 / this.frames.length)}%);`);
		}

	}

	this.previous = function () {
		if (this.inTransition === true) return;
		if (this.circular === false && this.currentSlide <= 1) return this.currentSlide = 1;
		this.inTransition = true;
		let carousel = document.querySelector(`#${this.id} .carousel`);
		let carouselSlider = document.querySelector(`#${this.id} .carousel-slider`);

		if (this.direction === 'next' && this.circular === true) {
			carouselSlider.appendChild(carouselSlider.firstElementChild);
			this.direction = 'previous'
		}

		if (this.direction === null && this.circular === true) {
			carouselSlider.appendChild(carouselSlider.firstElementChild);
		}

		if (this.circular === true) {
			carousel.style.justifyContent = 'flex-end';
			carouselSlider.setAttribute(`style`, `width:${frames.length * 100}%; transform:translateX(${(100 / this.frames.length)}%);`);
		} else {
			this.currentSlide--;
			carouselSlider.setAttribute(`style`, `width:${frames.length * 100}%; transform:translateX(${this.currentSlide * - (100 / this.frames.length)}%);`);
		}

	}

}

function carouselClass() {
	this.pool = [];
}

carouselClass.prototype.init = function (id, frames) {

	let container = document.querySelector('#' + id);

	if (frames === null) {
		let slideEls = container.querySelectorAll('.carousel-slide');
		frames = Array.from(slideEls).map(el => el.outerHTML);
	}

	let slider = new carouselSlider(id, frames);
	this.pool.push(slider);

	container.innerHTML = `
			<div class="carousel">
				<div class="carousel-slider" style="width:${frames.length * 100}%">${frames.join('')}</div>
			</div>
	`;

	let carouselSliderElement = document.querySelector(`#${id} .carousel-slider`);
	carouselSliderElement.addEventListener('transitionend', function () {

		if (event.target !== undefined && event.target !== null && !event.target.classList.contains('carousel-slider')) return;

		if (slider.direction === 'next') carouselSliderElement.appendChild(carouselSliderElement.firstElementChild);
		else if (slider.direction === 'previous') carouselSliderElement.prepend(carouselSliderElement.lastElementChild);
		else if (slider.direction === null) {
			carouselSliderElement.prepend(carouselSliderElement.lastElementChild);
			slider.direction = 'previous';
		}

		carouselSliderElement.style.transition = 'none';
		carouselSliderElement.style.transform = 'translate(0)';

		setTimeout(() => {
			carouselSliderElement.style.transition = 'all 200ms';
			slider.inTransition = false;
		})
	});

	return slider;
}

carouselClass.prototype.find = function (id) {
	return this.pool.find((slider) => slider.id == id);
}

const carousel = new carouselClass();