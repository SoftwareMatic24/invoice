@extends('layouts.portal')

@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section | no-shadow">
			<form id="page-form" class="section-body" onsubmit="return false;">
				<div class="grids grids-2 gap-2 grids-1-md">
					<div class="grid">
						<label class="input-style-1-label">{{ __("page name") }} <span class="required">*</span></label>
						<input name="title" type="text" class="input-style-1" value="{{ $page['title'] ?? '' }}">
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ __("page title") }} <span class="required">*</span></label>
						<input name="page-title" type="text" class="input-style-1" value="{{ $page['page_title'] ?? '' }}">
					</div>
				</div>
				<div class="grids grids-1 | margin-top-2">
					<div class="grid">
						<label class="input-style-1-label">{{ __("page description") }}</label>
						<input name="description" type="text" class="input-style-1" value="{{ $page['description'] ?? '' }}">
					</div>
				</div>
			</form>
		</div>

		<div class="section | margin-top-4 no-shadow">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('page seo') }}</h2>
			</div>
			<form class="section-body" onsubmit="return false;">
				<x-page-seo-form :meta="$page['meta'] ?? NULL" />
			</form>
		</div>

		<div class="section | margin-top-4 no-shadow">
			<div class="section-header">
				<h2 class="section-title">{{ __('page content') }}</h2>
			</div>
			<div class="section-body">
				<x-page-content />
			</div>
		</div>

	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="publish-button"
					data-xhr-loading.attr="disabled"
					onclick="savePage('publish')"
					class="button button-primary button-block">
					{{ __("publish page") }}
				</button>
				@if(($page["persistence"] ?? false) !== "permanent")
				<button data-xhr-name="drafts-button"
					data-xhr-loading.attr="disabled"
					onclick="savePage('drafts')"
					class="button button-primary-border button-block">
					{{ __("save to drafts") }}
				</button>
				@endif
			</div>
			<p class="grid-widget-text status | margin-top-3 hide"><b>{{ __('status') }}</b>: <span></span></p>
			<p class="grid-widget-text url | margin-top-1 hide"><b>URL</b>: <a href="#" target="_blank">{{ __('view page') }}</a></p>
		</div>
		<div class="grid-widget | margin-bottom-2">
			<x-page-language-select />
		</div>

		<div data-is="featured-image-widget" class="grid-widget | margin-bottom-2">
			<x-page-featured-image />
		</div>

		<div data-is="featured-video-widget" class="grid-widget | margin-bottom-2">
			<x-page-featured-video />
		</div>

		<div class="grid-widget | margin-bottom-2">
			<p class="grid-widget-text | margin-bottom-2"><b>{{ __("advance seo") }}</b></p>
			<x-page-advance-seo :page="$page ?? NULL" :pageSlug="$page['slug'] ?? ''" />
		</div>
	</div>
</div>
@stop

{{ loadPluginFile('js/script.js', 'pages') }}

@section('page-script')
@parent
<script>
	document.addEventListener('DOMContentLoaded', init);

	function init() {
		if (!isEmpty(staticPageId())) populatePage(staticPage());
		initLanguageChoice();
	}

	function initLanguageChoice() {
		initChooseLanguageComponent(staticLanguages(), function(selectedLanguage) {
			let featuredImageWidgetEl = document.querySelector('[data-is="featured-image-widget"]');
			let featuredVideoWidgetEl = document.querySelector('[data-is="featured-video-widget"]');
			let pageSlugFormEl = document.querySelector('[data-is="page-slug-form"]');

			let primaryLanguage = staticLanguages().find(lang => lang.type === 'primary');

			if (primaryLanguage.code === selectedLanguage.code) {
				featuredImageWidgetEl.classList.remove('hide');
				featuredVideoWidgetEl.classList.remove('hide');
				pageSlugFormEl.classList.remove('hide');
			} else {
				featuredImageWidgetEl.classList.add('hide');
				featuredVideoWidgetEl.classList.add('hide');
				pageSlugFormEl.classList.add('hide');
			}

			if (!isEmpty(staticPageId())) populatePage(staticPage(), selectedLanguage.code);
		});
	}

	/**
	 * Static data
	 */

	function staticPageId() {
		return '{{ $pageId ?? "" }}'
	}

	function staticPage() {
		let page = '{!! addSlashes(json_encode($page)) !!}';
		return JSON.parse(page);
	}

	function staticLanguages() {
		let languages = '{!! addSlashes(json_encode( languages() )) !!}';
		return JSON.parse(languages);
	}

	/**
	 * Save
	 */

	async function savePage(status) {
		let postData = pageData();

		if(!isEmpty(staticPageId())) postData.pageId = staticPageId();
		postData.status = status;

		let n = showSavingNotification();
		
		let response = await Page.savePage(staticPageId(), postData, { target: `${status}-button`});
		
		showResponseNotification(n, response);

		if(response.data.status === 'success') window.location.href = `{!! $backURL ?? '' !!}`;
	}

	/**
	 * Populate
	 */

	function populatePage(page, langCode = null) {

		clearContentList();

		let pageObject = buildPageObject(page, langCode);

		if (pageLanguage === null) pageLanguage = staticLanguages().find(lang => lang.type === 'primary');

		populateBasicPage(pageObject);
		populatePageContent(pageObject);
		populatePageStatusAndURL(page);
		populateFeaturedImage(page);
		populateFeaturedVideo(page);
		setSEOFormData(pageObject.meta);
		populateFAQSchema(pageObject.meta);
		populateOGTags(pageObject.meta.og);
		populateTwitterTags(pageObject.meta.twitter);

	}

	function populateBasicPage(pageObject){
		let titleEl = document.querySelector('input[name="title"]');
		let pageTitleEl = document.querySelector('input[name="page-title"]');
		let descriptionEl = document.querySelector('input[name="description"]');

		titleEl.value = pageObject.title;
		pageTitleEl.value = pageObject.page_title;
		descriptionEl.value = pageObject.description;
	}

	function populatePageContent(pageObject){
		if (!isEmpty(pageObject.content)) {
			let content = JSON.parse(pageObject.content);
			content.forEach(c => {
				createContentSection(c);
			});
		}
	}

	function populateFeaturedImage(page){
		let featuredImageEl = document.querySelector('[data-is="featured-image"]');
		let url = !isEmpty(page.featured_image) ? `${BASE_URL}/storage/${page.featured_image.url}` : null;
		if(!isEmpty(url)) {
			featuredImageEl.setAttribute('src', url);
			featuredImageEl.setAttribute('data-src', page.featured_image.id);
		}
	}

	function populateFeaturedVideo(page){
		let featuredVideoContainerEl = document.querySelector('.featured-video-container');
		let featuredVideoEl = document.querySelector('[data-is="featured-video"]');

		let videoURL = !isEmpty(page.featured_video) ? `${BASE_URL}/storage/${page.featured_video.url}` : null;
		let thumbnailURL = !isEmpty(page.featured_video_thumbnail) ? `${BASE_URL}/storage/${page.featured_video_thumbnail.url}` : null;

		if(!isEmpty(videoURL)){
			featuredVideoEl.setAttribute('src', videoURL);
			featuredVideoEl.setAttribute('data-src', page.featured_video.id);
			featuredVideoEl.removeAttribute('poster');
			featuredVideoContainerEl.classList.add('has-video');
			featuredVideoContainerEl.classList.add('no-thumbnail');
		}

		if(!isEmpty(thumbnailURL)){
			featuredVideoEl.setAttribute('poster', thumbnailURL);
			featuredVideoEl.setAttribute('data-poster', page.featured_video_thumbnail.id);
			featuredVideoEl.classList.remove('no-thumbnail');
			featuredVideoContainerEl.classList.add('has-thumbnail');
		}

	}

	function populatePageStatusAndURL(page){
		let url =  isEmpty(page.hard_url) ? `${BASE_URL}/${page.slug}` : `${BASE_URL}/${page.hard_url}`;
		document.querySelector('.grid-widget-text.status').classList.remove('hide');
		document.querySelector('.grid-widget-text.url').classList.remove('hide');
		document.querySelector('.grid-widget-text.status span').innerHTML = capitalize(page.status);
		document.querySelector('.grid-widget-text.url a').setAttribute('href', url);
	}

	function populateFAQSchema(meta){
		FAQSchemaJsonLdData = !isEmpty(meta.faqSchemaJsonLd) ? meta.faqSchemaJsonLd : [];
	}


	/**
	 * Other
	 */

	function pageData() {

		let title = document.querySelector('input[name="title"]').value;
		let pageTitle = document.querySelector('input[name="page-title"]').value;
		let description = document.querySelector('input[name="description"]').value;
		let slug = document.querySelector('input[name="slug"]').value;
		let featuredImageURL = document.querySelector('[data-is="featured-image"]').getAttribute("data-src");
		let featuredVideoURL = document.querySelector('[data-is="featured-video"]').getAttribute("data-src");
		let featuredVideoThumbnailURL = document.querySelector('[data-is="featured-video"]').getAttribute("data-poster");
		let languageCode = !isEmpty(pageLanguage) ? pageLanguage.code : null;

		let meta = getSEOFormData();
		let sections = getContentSectionsData();
		meta.faqSchemaJsonLd = getFAQSchemaQuestiosData();
		meta.og = getOGTagsData();
		meta.twitter = getTwitterTagsData();

		return {
			title,
			pageTitle,
			description,
			slug,
			featuredImageURL,
			featuredVideoURL,
			featuredVideoThumbnailURL,
			meta,
			sections,
			languageCode
		}

	}

	function clearContentList() {
		document.querySelector('#content-list').innerHTML = '';
	}

	function buildPageObject(page, langCode = null) {

		let i18n = page.pagei18n;
		let selectedi18n = isEmpty(i18n) ? null : i18n.find(row => row.language_code === langCode);
		let primaryLanguage = staticLanguages().find(row => row.type === 'primary');

		let obj = {
			page_title: '',
			title: '',
			description: '',
			meta: {},
			content: ''
		};

		if ((isEmpty(langCode) && isEmpty(selectedi18n)) || langCode === primaryLanguage.code) {
			obj.page_title = page.page_title;
			obj.title = page.title;
			obj.description = page.description;
			obj.meta = !isEmpty(page.meta) ? JSON.parse(page.meta) : null;
			obj.content = page.content;
		} else if (!isEmpty(i18n) && !isEmpty(selectedi18n)) {
			obj.page_title = selectedi18n.page_title;
			obj.title = selectedi18n.title;
			obj.description = selectedi18n.description;
			obj.meta = !isEmpty(selectedi18n.meta) ? JSON.parse(selectedi18n.meta) : null;
			obj.content = selectedi18n.content;
		}

		return obj;
	}
</script>

@stop