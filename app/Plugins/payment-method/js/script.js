class PaymentMethodPlugin {
	constructor() {
		if (PaymentMethodPlugin.instance) return PaymentMethodPlugin.instance;
		PaymentMethodPlugin.instance = this;
		return PaymentMethodPlugin.instance;
	}

	/**
	 * Payment Method: Get
	 */

	async entries(type, paymentMethodSlug) {
		return await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/payment-method/' + type + '/all/' + paymentMethodSlug
		});
	}

	/**
	 * Payment Method: Save
	 */

	async saveEntry(type, entryId, postData, xhrParam = {}) {
		if (isEmpty(entryId)) return this.addEntry(type, postData, xhrParam);
		return this.updateEntry(type, entryId, postData, xhrParam);
	}

	async addEntry(type, postData, xhrParam = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/payment-method/${type}/save`,
			body: postData,
			...xhrParam
		});
	}

	async updateEntry(type, entryId, postData, xhrParam = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/payment-method/${type}/save/${entryId}`,
			body: postData,
			...xhrParam
		})
	}


	/**
	 * Payment Method: Delete
	 */

	async deleteEntry(type, entryId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/payment-method/' + type + '/delete/' + entryId
		});
	}

}

var PaymentMethod = new PaymentMethodPlugin();