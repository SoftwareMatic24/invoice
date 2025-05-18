

class ComponentPlugin {

	constructor() {
		if (ComponentPlugin.instance) return ComponentPlugin.instance;
		ComponentPlugin.instance = this;
		return ComponentPlugin.instance;
	}

	/**
	 * Get
	 */

	async componentBySlug(slug) {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/components/one/${slug}`
		});
	}

	async components() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/components/all`
		});
	}

	/**
	 * Save
	 */

	async saveComponent(slug, postData, xhrParams = {}) {
		if (!isEmpty(slug)) return this.updateComponent(slug, postData, xhrParams);
	}

	async updateComponent(slug, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/components/save/${slug}`,
			body: postData,
			...xhrParams
		});
	}


	/**
	 * Delete
	 */

	async deleteComponent(id) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/components/delete/${id}`
		})
	}

}

var Component = new ComponentPlugin();