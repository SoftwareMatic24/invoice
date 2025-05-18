@extends('layouts.portal')
@section('main-content')
<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="section no-shadow">
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group">
						<label class="input-style-1-label">{{ __('brand name') }}</label>
						<input name="name" type="text" class="input-style-1" value="{{ $branding['brand-name'] ?? '' }}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">{{ __('about brand') }}</label>
						<textarea id="about" class="input-style-1">{{ $branding['brand-about'] ?? '' }}</textarea>
					</div>
				</form>
			</div>
		</div>
		<div class="section | no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('sidebar') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('sidebar header color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="sidebarHeaderColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('sidebar color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="sidebarColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('sidebar dropdown color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="sidebarDropdownColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('sidebar text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="sidebarTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('sidebar text active color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="sidebarTextActiveColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('page') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('page backgroud color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="pageBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('header background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="pageHeaderBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('header text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="pageHeaderTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('header icon color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="pageHeaderIconColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('header icon hover color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="pageHeaderIconHoverColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('button') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group" style="margin-top: 0;">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('button border radius') }}</label>
								<div class="range-input">
									<input name="buttonBorderRadius" type="range" min="0" max="100" value="0" class="input-style-1" oninput="this.nextElementSibling.value = this.value + 'px'">
									<output>0px</output>
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('primary button color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="primaryButtonColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('primary button text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="primaryButtonTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('primary button hover color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="primaryButtonHoverColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('primary button hover text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="primaryButtonHoverTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger button color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="dangerButtonColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger button text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="dangerButtonTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger button hover color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="dangerButtonHoverColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger button hover text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="dangerButtonHoverTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('tags') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('success background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagSuccessBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('success text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagSuccessTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagDangerBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('danger text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagDangerTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('warning background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagWarningBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('warning text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tagWarningTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('table') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group" style="margin-top: 0;">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('search and filter radius') }}</label>
								<div class="range-input">
									<input name="tableFiltersRadius" type="range" min="0" max="100" value="0" class="input-style-1" oninput="this.nextElementSibling.value = this.value + 'px'">
									<output>0px</output>
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('filter-bar background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tableFilterbarBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('filter-bar text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tableFilterbarTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('header background color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tableHeaderBackgroundColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid">
								<label class="input-style-1-label">{{ __('header text color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="tableHeaderTextColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="section no-shadow margin-top-2">
			<div class="section-header | margin-bottom-2">
				<h2 class="section-title">{{ __('cards') }}</h2>
			</div>
			<div class="section-body">
				<form onsubmit="return false">
					<div class="form-group">
						<div class="grids grids-2 gap-2">
							<div class="grid">
								<label class="input-style-1-label">{{ __('color') }}</label>
								<div class="color-input">
									<input type="text" class="input-style-1" placeholder="">
									<input name="statsCardColor" type="color" class="input-style-1" style="max-width: 100px;">
								</div>
							</div>
							<div class="grid"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid | flex-column-reverse-on-md">
			<div class="grid-widget | margin-bottom-2">
				<div class="button-group">
					<button data-xhr-name="save-button" data-xhr-loading.attr="disabled" onclick="saveBrand()" class="button button-primary button-block">{{ __('update') }}</button>
				</div>
			</div>
			<div class="grid-widget | margin-bottom-2">
				<div class="grids grids-2 gap-1">
					<div class="grid">
						<p class="grid-widget-text"><b>{{ __('logo') }}</b></p>
						<div class="logo-box" onclick="chooseBrandingImage('logo')">
							@if($branding["brand-logo"] !== NULL)
							<img data-is="logo-image" class="width-100 cursor-pointer" src="{{ url('/storage') }}/{{$branding['brand-logo']}}" data-src="{{$branding['brand-logo']}}" alt="{{ __('logo') }}">
							@else
							<img data-is="logo-image" class="width-100 cursor-pointer" src="#" alt="">
							@endif
						</div>
						<div>
							<div class="range-input">
								<input name="brand-logo-size" type="range" min="0" max="500" value="0" class="input-style-1" oninput="this.nextElementSibling.value = this.value + 'px'">
								<output>0px</output>
							</div>
						</div>
					</div>
					<div class="grid">
						<p class="grid-widget-text"><b>{{ __('light logo') }}</b></p>
						<div class="logo-box" onclick="chooseBrandingImage('logo-light')">
							@if($branding["brand-logo-light"] !== NULL)
							<img data-is="logo-light-image" class="width-100 cursor-pointer" src="{{ url('/storage') }}/{{$branding['brand-logo-light']}}" data-src="{{$branding['brand-logo-light']}}" alt="logo">
							@else
							<img data-is="logo-light-image" class="width-100 cursor-pointer" src="#" alt="">
							@endif
						</div>
						<div>
							<div class="range-input">
								<input name="brand-logo-light-size" type="range" min="0" max="500" value="0" class="input-style-1" oninput="this.nextElementSibling.value = this.value + 'px'">
								<output>0px</output>
							</div>
						</div>
					</div>

				</div>
				<div class="grids grids-2 gap-1 | margin-top-2">
					<div class="grid">
						<p class="grid-widget-text"><b>{{ __('fav icon') }}</b></p>
						<div class="logo-box" onclick="chooseBrandingImage('fav-icon')">
							@if($branding["brand-fav-icon"] !== NULL)
							<img data-is="fav-icon-image" class="width-100 cursor-pointer" src="{{ url('/storage') }}/{{$branding['brand-fav-icon']}}" data-src="{{$branding['brand-fav-icon']}}" alt="fav icon">
							@else
							<img data-is="fav-icon-image" class="width-100 cursor-pointer" src="#" alt="">
							@endif
						</div>
					</div>
					<div class="grid">
						<p class="grid-widget-text"><b>{{ __('portal logo') }}</b></p>
						<div class="logo-box" onclick="chooseBrandingImage('portal-logo')">
							@if($branding["brand-portal-logo"] !== NULL)
							<img data-is="portal-logo-image" class="width-100 cursor-pointer" src="{{ url('/storage') }}/{{$branding['brand-portal-logo']}}" data-src="{{$branding['brand-portal-logo']}}" alt="logo">
							@else
							<img data-is="portal-logo-image" class="width-100 cursor-pointer" src="#" alt="">
							@endif
						</div>
					</div>
				</div>

			</div>
			<div class="grid-widget | margin-top-2">
				<a href="#" onclick="resetColors()">{{ __('reset colors') }}</a>
			</div>
		</div>
	</div>
</div>
@stop

@section('page-script')

<script src="{{ asset('js/ckeditor.js') }}"></script>
{!! loadPluginFile('js/script.js', 'appearance') !!}

<script>
	let aboutEditor = null;

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		initAboutCKEditor();
		populateBranding(staticBranding());
	}

	function initAboutCKEditor() {
		ClassicEditor.create(document.querySelector(`#about`), {
				licenseKey: ''
			})
			.then(editor => {
				aboutEditor = editor;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: htu0gx4ou56h-hrgzwh179cfe');
				console.error(error);
			});
	}


	/**
	 * Static data
	 */

	function staticDefaultBranding() {
		let response = '{!! addSlashes(json_encode(Appearance::defaultBranding())) !!}';
		return JSON.parse(response);
	}

	function staticBranding() {
		let response = '{!! addSlashes(json_encode(Appearance::branding())) !!}';
		return JSON.parse(response);
	}


	/**
	 * Save
	 */

	async function saveBrand() {

		let colorChange = false;

		let name = document.querySelector('[name="name"]').value;

		let colorInputEls = document.querySelectorAll('input[type="color"]');

		let buttonBorderRadius = document.querySelector('[name="buttonBorderRadius"]').value;
		let tableFiltersRadius = document.querySelector('[name="tableFiltersRadius"]').value;

		let logoURL = document.querySelector('[data-is="logo-image"]').dataset.src;
		let logoLightURL = document.querySelector('[data-is="logo-light-image"]').dataset.src;
		let favIconURL = document.querySelector('[data-is="fav-icon-image"]').dataset.src;
		let portalLogoURL = document.querySelector('[data-is="portal-logo-image"]').dataset.src;

		let logoSize = document.querySelector('[name="brand-logo-size"]').value;
		let logoLightSize = document.querySelector('[name="brand-logo-light-size"]').value;

		let about = aboutEditor.getData();

		let postData = {
			name,
			logoURL,
			logoLightURL,
			favIconURL,
			portalLogoURL,
			logoSize,
			logoLightSize,
			about,
			buttonBorderRadius,
			tableFiltersRadius
		};

		colorInputEls.forEach(inputEl => {
			let name = inputEl.getAttribute('name');
			let value = inputEl.value;
			postData[name] = value;
		});

		let n = showSavingNotification();
		let response = await Appearance.saveBranding(postData, {
			target: 'save-button'
		});
		showResponseNotification(n, response);

		if (response.data.status === 'success') {
			let brandElements1 = document.querySelectorAll('.sidebar-brand');
			let brandElements2 = document.querySelectorAll('.navigation-brand');

			brandElements1.forEach(element => element.innerHTML = name);
			brandElements2.forEach(element => element.innerHTML = name);

			location.reload();
		}

	}

	function resetColors() {

		for (key in staticDefaultBranding()) {
			let el = document.querySelector(`[name="${key}"]`);
			if (el !== null) {
				let customEvent = new Event('change', {
					bubbles: true,
					cancelable: true
				});
				let value = staticDefaultBranding()[key];
				el.value = value;
				el.dispatchEvent(customEvent);
			}
		}

		saveBrand();
	}

	/**
	 * Populate
	 */

	function populateBranding(branding) {
		
		for (key in staticDefaultBranding()) {
			let el = document.querySelector(`[name="${key}"]`);

			if (el !== null) {
				let customEventInput = new Event('input', {
					bubbles: true,
					cancelable: true
				});
				let customEventChange = new Event('change', {
					bubbles: true,
					cancelable: true
				});

				let value = staticDefaultBranding()[key];
				if (branding[key] !== undefined) value = branding[key];

				el.value = value;
				el.dispatchEvent(customEventInput);
				el.dispatchEvent(customEventChange);
			}
		}
	}

	/**
	 * Other
	 */

	function chooseBrandingImage(type) {
		mediaCenter.show({
			useAs: {
				title: `Set as ${slugToText(type)}`,
				max: 1,
				mediaType: 'image',
				onUse: function(params = []) {
					let media = params.media;
					let imageURL = media[0].url;
					document.querySelector(`[data-is="${type}-image"]`).setAttribute('src', BASE_URL + '/storage/' + imageURL);
					document.querySelector(`[data-is="${type}-image"]`).setAttribute('data-src', imageURL);
				}
			}
		});
	}

	function hexToHSL(hex) {
		hex = hex.replace("#", "");

		var r = parseInt(hex.substring(0, 2), 16) / 255;
		var g = parseInt(hex.substring(2, 4), 16) / 255;
		var b = parseInt(hex.substring(4, 6), 16) / 255;

		var min = Math.min(r, g, b);
		var max = Math.max(r, g, b);
		var lightness = (min + max) / 2;

		var saturation = 0;
		if (min !== max) {
			saturation = lightness > 0.5 ? (max - min) / (2 - max - min) : (max - min) / (max + min);
		}

		var hue = 0;
		if (max === min) {
			hue = 0;
		} else {
			var delta = max - min;
			if (max === r) {
				hue = (g - b) / delta + (g < b ? 6 : 0);
			} else if (max === g) {
				hue = (b - r) / delta + 2;
			} else if (max === b) {
				hue = (r - g) / delta + 4;
			}
			hue /= 6;
		}

		hue = Math.round(hue * 360);
		saturation = Math.round(saturation * 100);
		lightness = Math.round(lightness * 100);

		return "hsl(" + hue + ", " + saturation + "%, " + lightness + "%)";
	}

	function HSLToHex(hslColor) {
		var hslRegex = /^hsl\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*\)$/i;
		var match = hslColor.match(hslRegex);

		if (match) {
			var hue = parseInt(match[1]);
			var saturation = parseInt(match[2]);
			var lightness = parseInt(match[3]);

			var h = hue / 360;
			var s = saturation / 100;
			var l = lightness / 100;

			var r, g, b;

			if (s === 0) {
				r = g = b = l; // achromatic (gray)
			} else {
				var hueToRGB = function hueToRGB(p, q, t) {
					if (t < 0) t += 1;
					if (t > 1) t -= 1;
					if (t < 1 / 6) return p + (q - p) * 6 * t;
					if (t < 1 / 2) return q;
					if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
					return p;
				};

				var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
				var p = 2 * l - q;

				r = hueToRGB(p, q, h + 1 / 3);
				g = hueToRGB(p, q, h);
				b = hueToRGB(p, q, h - 1 / 3);
			}

			var toHex = function toHex(c) {
				var hex = Math.round(c * 255).toString(16);
				return hex.length === 1 ? "0" + hex : hex;
			};

			var redHex = toHex(r);
			var greenHex = toHex(g);
			var blueHex = toHex(b);

			var hexColor = "#" + redHex + greenHex + blueHex;

			return hexColor;
		} else {
			return null;
		}
	}

	function updateHSLPercentage(hslColor, percentage) {

		var hslRegex = /^hsl\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*\)$/i;
		var match = hslColor.match(hslRegex);

		if (match) {
			var hue = parseInt(match[1]);
			var saturation = parseInt(match[2]);
			var lightness = parseInt(match[3]);

			lightness = percentage;

			var updatedHSLColor = "hsl(" + hue + ", " + saturation + "%, " + lightness + "%)";

			return updatedHSLColor;
		} else {
			return null;
		}
	}
</script>

@stop