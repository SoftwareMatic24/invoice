
<form data-is="page-slug-form" onsubmit="return false" action="#" class="margin-bottom-2">
	<div class="{{ ($page['persistence'] ?? false) === 'permanent' ? 'hide' : '' }}">
		<label class="input-style-1-label">{{ __($heading ?? "page slug") }}</label>
		<input name="slug" type="text" class="input-style-1" value="{{ $pageSlug ?? '' }}">
	</div>
</form>

<div class="button-tiles">
	<div onclick="showOGTagsModal()" class="button button-primary-border button-xs">OG Meta Tags</div>
	<div onclick="showTwitterTagsModal()" class="button button-primary-border button-xs">Twitter Meta Tags</div>
	<div onclick="showFAQSchemaModal()" class="button button-primary-border button-xs">FAQ Schema (JSON-LD)</div>
</div>

<!-- Modals -->
<div id="faq-schema-modal" class="modal" style="max-width: 70%;">
	<div class="modal-header">
		<p class="modal-title">FAQ Schema (JSON-LD)</p>
		<span onclick="hideModal('faq-schema-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="height:60vh;">
		<div class="grids grids-2 | gap-5">
			<div class="grid">
				<div id="faq-schema-questions"></div>

				<div class="form-group | margin-top-2">
					<button onclick="addNewFAQSchemaQuestion()" class="button button-sm button-primary-border">{{ __("add new question") }}</button>
				</div>
			</div>
			<div class="grid">
				<div class="modal-text-group | margin-top-3">
					<p>{{ __("faq-schema-json-ld-question-length-info") }}</p>
					<p>{{ __("faq-schema-json-ld-answer-length-info") }}</p>
					<p>{{ __("faq-schema-json-ld-question-length-info") }}</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="og-tags-modal" class="modal" style="max-width: 70%;">
	<div class="modal-header">
		<p class="modal-title">Open Graph Meta Tags</p>
		<span onclick="hideModal('og-tags-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="height:60vh;">
		<div class="grids grids-2 | gap-5">
			<div class="grid">

				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<label class="input-style-1-label">OG:Title</label>
						<input name="og-title" type="text" class="input-style-1">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">OG:Description</label>
						<textarea name="og-description" class="input-style-1"></textarea>
					</div>
					<div class="form-group">
						<label class="input-style-1-label">OG:Type</label>
						<div class="custom-select-container">
							<select name="og-type" class="input-style-1">
								<option value="website">website</option>
								<option value="article">article</option>
								<option value="blog">blog</option>
								<option value="profile">profile</option>
								<option value="music.song">music.song</option>
								<option value="music.album">music.album</option>
								<option value="music.playlist">music.playlist</option>
								<option value="video.movie">video.movie</option>
								<option value="video.tv_show">video.tv_show</option>
								<option value="video.episode">video.episode</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="grid">
				<div class="modal-text-group | margin-top-3">
					<img onclick="chooseOGImage()" data-is="og-image" class="cursor-pointer" src="{{ asset('assets/default-image-300x158.jpg') }}" alt="og:image" width="300">
				</div>
			</div>
		</div>
	</div>
</div>

<div id="twitter-tags-modal" class="modal" style="max-width: 70%;">
	<div class="modal-header">
		<p class="modal-title">Twitter Meta Tags</p>
		<span onclick="hideModal('twitter-tags-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="height:60vh;">
		<div class="grids grids-2 | gap-5">
			<div class="grid">
				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<label class="input-style-1-label">Twitter:Title</label>
						<input name="twitter-title" type="text" class="input-style-1">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">Twitter:Description</label>
						<textarea name="twitter-description" class="input-style-1"></textarea>
					</div>
					<div class="form-group">
						<label class="input-style-1-label">Twitter:Card</label>
						<div class="custom-select-container">
							<select name="twitter-card" class="input-style-1">
								<option value="summary">summary</option>
								<option value="summary_large_image">summary_large_image</option>
								<option value="app">app</option>
								<option value="player">player</option>
								<option value="gallery">gallery</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="grid">
				<div class="modal-text-group | margin-top-3">
					<img onclick="chooseTwitterImage()" data-is="twitter-image" class="cursor-pointer" src="{{ asset('assets/default-image-300x158.jpg') }}" alt="og:image" width="300">
				</div>
			</div>
		</div>
	</div>
</div>

@section("page-script")
<script>
	let questionText = '{{ __("question") }}';
	let answerText = '{{ __("answer") }}';

	var FAQSchemaJsonLdData = [];

	// FAQ Schema

	function showFAQSchemaModal() {
		showModal('faq-schema-modal');
		let items = [{
			question: '',
			answer: ''
		}];
		if (FAQSchemaJsonLdData === null || FAQSchemaJsonLdData.length <= 0) FAQSchemaJsonLdData = items;
		populateFAQSchemaQuestions(FAQSchemaJsonLdData);
	}

	function populateFAQSchemaQuestions(items) {

		let layouts = items.map((item, itemIndex) => {
			return `
				<div data-id="${itemIndex}" data-is="faq-schema-question-container">
					<div class="form-group ${itemIndex !== 0 ? 'margin-top-2' : ''}">
						<label class="input-style-1-label | d-flex justify-content-space-between align-items-center">
							${questionText} ${itemIndex + 1}
							<span onclick="removeFAQSchemaQuestion(${itemIndex})">
								<svg class="label-remove-icon"><use xlink:href="{{ asset('assets/icons.svg#cross') }}"></use></svg>
							</span>
						</label>
						<input oninput="updateFAQSchemaQuestion('question', ${itemIndex})" type="text" class="input-style-1" value="${item.question}">
					</div>
					<div class="form-group | margin-top-2">
						<label class="input-style-1-label">${answerText}</label>
						<textarea oninput="updateFAQSchemaQuestion('answer', ${itemIndex})" class="input-style-1">${item.answer}</textarea>
					</div>
				</div>
			`;
		});

		document.querySelector('#faq-schema-questions').innerHTML = layouts.join('');
	}

	function addNewFAQSchemaQuestion() {
		FAQSchemaJsonLdData.push({
			question: '',
			answer: ''
		});
		populateFAQSchemaQuestions(FAQSchemaJsonLdData);
	}

	function removeFAQSchemaQuestion(questionIndex) {
		Confirmation.show({
			positiveButton: {
				function: function() {
					FAQSchemaJsonLdData = FAQSchemaJsonLdData.filter((item, itemIndex) => itemIndex != questionIndex);
					populateFAQSchemaQuestions(FAQSchemaJsonLdData);
				}
			}
		});
	}

	function updateFAQSchemaQuestion(type, questionIndex) {
		let container = document.querySelector(`[data-is="faq-schema-question-container"][data-id="${questionIndex}"]`);
		let value = '';
		if (type === 'question') value = container.querySelector('input[type="text"]').value;
		else if (type === 'answer') value = container.querySelector('textarea').value;
		FAQSchemaJsonLdData[questionIndex][type] = value;
	}

	function getFAQSchemaQuestiosData() {

		let data = [];
		let modal = document.querySelector('#faq-schema-modal');
		let questionContainers = modal.querySelectorAll('[data-is="faq-schema-question-container"]');

		questionContainers.forEach(container => {
			let question = container.querySelector('input[type="text"]').value;
			let answer = container.querySelector('textarea').value;

			if (question !== '') {
				data.push({
					question: question,
					answer: answer
				});
			}
		});

		return data;
	}

	// OG Meta Tags

	function showOGTagsModal() {
		showModal('og-tags-modal');
	}

	function chooseOGImage() {
		mediaCenter.show({
			useAs: {
				title: 'Set as OG Image',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector('#og-tags-modal [data-is="og-image"]').setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector('#og-tags-modal [data-is="og-image"]').setAttribute('data-src', imageURL);
				}
			}
		});
	}

	function getOGTagsData() {

		let modal = document.querySelector('#og-tags-modal');
		let ogTitle = modal.querySelector('[name="og-title"]').value;
		let ogDescription = modal.querySelector('[name="og-description"]').value;
		let ogType = modal.querySelector('[name="og-type"]').value;
		let ogImageURL = modal.querySelector('[data-is="og-image"]').getAttribute('data-src');

		if (ogImageURL === undefined || ogImageURL === '') ogImageURL = null;

		return {
			ogTitle,
			ogDescription,
			ogType,
			ogImageURL
		};
	}

	function populateOGTags(data) {

		if (isEmpty(data)) return clearOGTags();

		let modal = document.querySelector('#og-tags-modal');
		let titleEl = modal.querySelector('[name="og-title"]');
		let descriptionEl = modal.querySelector('[name="og-description"]');
		let typeEl = modal.querySelector('[name="og-type"]');

		titleEl.value = data.ogTitle !== undefined ? data.ogTitle : '';
		descriptionEl.value = data.ogDescription !== undefined ? data.ogDescription : '';
		typeEl.value = data.ogType !== undefined ? data.ogType : '';
		
		if (!isEmpty(data.ogImageURL)) {
			modal.querySelector('[data-is="og-image"]').setAttribute('data-src', data.ogImageURL);
			modal.querySelector('[data-is="og-image"]').setAttribute('src', BASE_URL + '/storage/' + data.ogImageURL);
		} else  clearOGImage();
	}

	function clearOGTags(){
		let modal = document.querySelector('#og-tags-modal');
		let titleEl = modal.querySelector('[name="og-title"]');
		let descriptionEl = modal.querySelector('[name="og-description"]');
		let typeEl = modal.querySelector('[name="og-type"]');
		
		titleEl.value = '';
		descriptionEl.value = '';
		typeEl.value = '';
		
		clearOGImage();
	}

	function clearOGImage(){
		let modal = document.querySelector('#og-tags-modal');
		let imageEl = modal.querySelector('[data-is="og-image"]');
		imageEl.removeAttribute('data-src');
		imageEl.setAttribute('src', `${BASE_URL}/assets/default-image-300x158.jpg`);
	}

	// Twitter Meta Tags

	function showTwitterTagsModal() {
		showModal('twitter-tags-modal');
	}

	function chooseTwitterImage() {
		mediaCenter.show({
			useAs: {
				title: 'Set as Twitter Image',
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector('#twitter-tags-modal [data-is="twitter-image"]').setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector('#twitter-tags-modal [data-is="twitter-image"]').setAttribute('data-src', imageURL);
				}
			}
		});
	}

	function getTwitterTagsData() {

		let modal = document.querySelector('#twitter-tags-modal');
		let twitterTitle = modal.querySelector('[name="twitter-title"]').value;
		let twitterDescription = modal.querySelector('[name="twitter-description"]').value;
		let twitterCard = modal.querySelector('[name="twitter-card"]').value;
		let twitterImageURL = modal.querySelector('[data-is="twitter-image"]').getAttribute('data-src');

		if (isEmpty(twitterImageURL)) twitterImageURL = null;

		return {
			twitterTitle,
			twitterDescription,
			twitterCard,
			twitterImageURL
		};
	}

	function populateTwitterTags(data) {

		if (isEmpty(data)) return clearTwitterTags();

		let modal = document.querySelector('#twitter-tags-modal');
		let titleEl = modal.querySelector('[name="twitter-title"]');
		let descriptionEl = modal.querySelector('[name="twitter-description"]');
		let cardEl = modal.querySelector('[name="twitter-card"]');
		let imageEl = modal.querySelector('[data-is="twitter-image"]');

		titleEl.value = data.twitterTitle !== undefined ? data.twitterTitle : '';
		descriptionEl.value = data.twitterDescription !== undefined ? data.twitterDescription : '';
		cardEl.value = data.twitterCard !== undefined ? data.twitterCard : '';

		if (data.twitterImageURL !== null) {
			imageEl.setAttribute('data-src', data.twitterImageURL);
			imageEl.setAttribute('src', BASE_URL + '/storage/' + data.twitterImageURL);
		}
		else clearTwitterImage();
	}

	function clearTwitterTags(){
		let modal = document.querySelector('#twitter-tags-modal');
		let titleEl = modal.querySelector('[name="twitter-title"]');
		let descriptionEl = modal.querySelector('[name="twitter-description"]');
		let cardEl = modal.querySelector('[name="twitter-card"]');

		titleEl.value = '';
		descriptionEl.value = '';
		cardEl.value = '';

		clearTwitterImage();
	}

	function clearTwitterImage(){
		let modal = document.querySelector('#twitter-tags-modal');
		let imageEl = modal.querySelector('[data-is="twitter-image"]');
		imageEl.removeAttribute('data-src');
		imageEl.setAttribute('src', `${BASE_URL}/assets/default-image-300x158.jpg`);
	}

</script>
@parent
@stop