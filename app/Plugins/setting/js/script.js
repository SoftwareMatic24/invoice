class SettingPlugin {
	constructor() {
		if (SettingPlugin.instance) return SettingPlugin.instance;
		SettingPlugin.instance = this;
		return SettingPlugin.instance;
	}

	/**
	 * Setting: Save
	 */

	async updateSetting(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/update`,
			body: postData,
			...xhrParams
		});
	}

	async updateGlobalScript(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/global-scripts/update`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Sitemap: Save
	 */

	async updateSitemapStatus(postData) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/sitemap/save`,
			body: postData
		});
	}

	async updateSitemapExcludeURLs(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/sitemap/update/excluded-urls`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * SMTP: Save
	 */

	async updateSMTP(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/smtp/update`,
			body: postData,
			...xhrParams
		});
	}

	async smtpTest(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/setting/smtp/test`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * 2FA: Save
	 */

	async save2FA(postData, xhrResponse = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/setting/2fa/save`,
			body: postData,
			...xhrResponse
		})
	}

	/**
	 * External integrations: Save
	 */

	async saveExternalIntegration(postData, slug, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/setting/external-integrations/save/${slug}`,
			body: postData,
			...xhrParams
		})
	}


}

var Setting = new SettingPlugin();