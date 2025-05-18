class CurrencyPlugin {
	constructor() {
		if (CurrencyPlugin.instance) return CurrencyPlugin.instance;
		CurrencyPlugin.instance = this;
		return CurrencyPlugin.instance;
	}

	/**
	 * Get
	 */

	async currencies() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/currency/all`
		})
	}

	/**
	 * Save
	 */

	async saveCurrency(id, postData, xhrParams = {}) {
		if (isEmpty(id)) return this.addCurrency(postData, xhrParams);
		return this.updateCurrency(id, postData, xhrParams);
	}

	async addCurrency(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/currency/save`,
			body: postData,
			...xhrParams
		});
	}

	async updateCurrency(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/currency/save/${id}`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Delete
	 */

	async deleteCurrency(id) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/currency/delete/${id}`
		})
	}

}

var Currency = new CurrencyPlugin();