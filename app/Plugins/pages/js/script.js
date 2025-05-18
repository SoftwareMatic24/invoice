class PagePlugin {

	constructor() {
		if (PagePlugin.instance) return PagePlugin.instance;
		PagePlugin.instance = this;
		return PagePlugin.instance;
	}

	/**
	 * Fetch
	 */

	async page(pageId) {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/pages/one/${pageId}`
		});
	}

	async pages() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/pages/all`
		});
	}

	/**
	 * Save
	 */

	async savePage(pageId, data, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/pages/save`,
			body: data,
			...xhrParams
		});
	}

	/**
	 * Delete
	 */

	async deletePage(pageId) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/pages/delete/${pageId}`
		});
	}

}

var Page = new PagePlugin();