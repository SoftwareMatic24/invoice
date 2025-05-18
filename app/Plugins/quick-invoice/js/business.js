class QuickInvoiceBusinessPlugin {
	constructor() {
		if (QuickInvoiceBusinessPlugin.instance) return QuickInvoiceBusinessPlugin.instance;
		QuickInvoiceBusinessPlugin.instance = this;
		return QuickInvoiceBusinessPlugin.instance;
	}

	/**
	 * Get
	 */

	userBusinesses() {
		return xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/quick-invoice/user/business/all'
		})
	}

	/**
	 * Save
	 */

	saveUserBusiness(businessId, postData, xhrParams = {}) {
		if (isEmpty(businessId)) return this.addUserBusiness(postData, xhrParams);
		return this.updateUserBusiness(businessId, postData, xhrParams);
	}

	addUserBusiness(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/business/save',
			body: postData,
			...xhrParams
		});
	}

	updateUserBusiness(businessId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/user/business/save/' + businessId,
			body: postData,
			...xhrParams
		});
	}


	/**
	 * Delete
	 */

	deleteUserBusiness(businessId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/business/delete/' + businessId
		});
	}

}

var QuickInvoiceBusiness = new QuickInvoiceBusinessPlugin();