class LanguagePlugin {
	constructor() {
		if (LanguagePlugin.instance) return LanguagePlugin.instance;
		LanguagePlugin.instance = this;
		return LanguagePlugin.instance;
	}

	/**
	 *Language: Get
	 */

	async languages() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/language/all`
		});
	}

	async languagePluginSlugs() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/language/plugin-slugs`
		})
	}

	/**
	 * Language: Save
	 */

	async saveLanguage(code, postData, xhrPrams = {}) {
		if (isEmpty(code)) return this.addLanguage(postData, xhrPrams);
		return this.updateLanguage(code, postData, xhrPrams);
	}

	async addLanguage(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/language/save`,
			body: postData,
			...xhrPrams
		});
	}

	async updateLanguage(code, postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/language/save/${code}`,
			body: postData,
			...xhrPrams
		});
	}

	/**
	* Language: Delete
	*/

	async deleteLanguage(code) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/language/delete/${code}`
		});
	}


	/**
	 * Language Setting: Save
	 */

	async saveLanguageSetting(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/language/settings`,
			body: postData,
			...xhrPrams
		})
	}


	/**
	 * Translations: Get
	 */

	async translations(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/language/translations`,
			body: postData,
			...xhrPrams
		});
	}

	/**
	 * Translations: Save
	 */

	async saveTranslations(postData, xhrPrams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/language/translations/save`,
			body: postData,
			...xhrPrams
		})
	}

}

var Language = new LanguagePlugin();