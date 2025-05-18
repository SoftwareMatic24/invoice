@extends('layouts.portal')

@section('main-content')


<div class="grids main-sidebar-grids">
	<div class="grid">
		<h2 class="heading-2">{{ __('colors') }}</h2>
		<form id="colors-form" class="margin-top-2" action="#" onsubmit="return false"></form>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveTheme()" class="button button-primary button-block">{{ __('update') }}</button>
			</div>
		</div>
		<div class="grid-widget">
			<div>
				<a href="javascript:void(0)" onclick="resetColors()">{{ __('reset colors') }}</a>
			</div>
		</div>

	</div>
</div>

@stop

@section('page-script')

{!! loadPluginFile('js/script.js', 'appearance') !!}

<script>

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		fap();
	}

	/**
	 * Static data
	 */

	function staticThemeSlug() {
		return '{!! $themeSlug ?? "" !!}';
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let theme = await fetchTheme();
		populateTheme(theme);
	}

	async function fetchTheme() {
		let response = await Appearance.theme(staticThemeSlug());
		return response.data;
	}

	/**
	 * Save
	 */

	async function saveTheme() {
		let colors = getColors();

		let postData = {
			colors
		};

		let n = showSavingNotification();
		let response = await Appearance.saveTheme(staticThemeSlug(), postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

	}

	async function resetColors() {
		let n = showProcessingNotification();
		let response = await Appearance.themeResetColors(staticThemeSlug());
		showResponseNotification(n ,response);
		fap();
	}

	/**
	 * Populate
	 */

	function populateTheme(theme) {
		if (theme === '') return;
		let options = theme.options;
		if (options !== null) options = JSON.parse(options);
		populateColors(options.colors !== undefined ? options.colors : options.defaultColors);
	}

	function populateColors(colors) {
		let formEl = document.querySelector('#colors-form');

		let gridsView = ``;
		for (let name in colors) {
			let label = name.replaceAll('--', '').replaceAll('-', ' ').replaceAll('clr', 'color');
			label = capitalizeAll(label);
			let value = colors[name];

			gridsView += `
				<div class="grid" style="width:calc(50% - 1rem)">
					<label class="input-style-1-label">${label}</label>
					<div class="color-input">
						<input type="text" class="input-style-1" value="${value}">
						<input name="${name}" type="color" class="input-style-1" value="${value}" style="max-width: 100px;">
					</div>
				</div>
			`;
		}

		let view = `
			<div class="form-group">
				<div class="grids grids-2 gap-2 | flex-wrap">${gridsView}</div>
			</div>
		`;

		formEl.innerHTML = view;

		initColorInput();
	}

	// Other

	function getColors() {
		let formEl = document.querySelector('#colors-form');
		let colorInputEls = formEl.querySelectorAll('.color-input');

		let colors = Array.from(colorInputEls).reduce((acc, el) => {
			let colorInputEl = el.querySelector('input[type="color"]');
			let name = colorInputEl.name;
			let value = colorInputEl.value;
			acc[name] = value;
			return acc;
		}, {});

		return colors;
	}
</script>
@stop