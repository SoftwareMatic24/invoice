class AppearancePlugin {

	constructor() {
		if (AppearancePlugin.instance) return AppearancePlugin.instance;
		AppearancePlugin.instance = this;
		return AppearancePlugin.instance;
	}

	/**
	 * Branding: Get
	 */

	async branding() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/appearance/branding/all`
		});
	}

	/**
	 * Branding: Save
	 */

	async saveBranding(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/appearance/branding/update`,
			body: postData,
			...xhrPrams
		});
	}

	async saveAccountBranding(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/appearance/account-branding/update`,
			body: postData,
			...xhrPrams
		})
	}

	/**
	 * Theme: Get
	 */

	async theme(slug) {
		return await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/appearance/themes/' + slug + '/one'
		});
	}

	/**
	 * Theme: Save
	 */

	async saveTheme(slug, postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/appearance/themes/customize/' + slug + '/update',
			body: postData,
			...xhrPrams
		});
	}

	async themeResetColors(slug) {
		return xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/appearance/themes/customize/' + slug + '/reset-colors'
		});
	}

}

var Appearance = new AppearancePlugin();