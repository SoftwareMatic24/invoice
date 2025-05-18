class QuickInvoiceClientPlugin {

	constructor() {
		if (QuickInvoiceClientPlugin.instance) return QuickInvoiceClientPlugin.instance;
		QuickInvoiceClientPlugin.instance = this;
		return QuickInvoiceClientPlugin.instance;
	}

	/**
	 * Client: Get
	 */

	userClients() {
		return xhrRequest({
			method: "GET",
			url: BASE_URL + '/api/quick-invoice/user/clients/all'
		});
	}

	/**
	 * Client: Save
	 */

	saveUserClient(id, postData, xhrParams = {}) {
		if (isEmpty(id)) return this.addUserClient(postData, xhrParams);
		return this.updateUserClient(id, postData, xhrParams);
	}

	addUserClient(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/clients/save',
			body: postData,
			...xhrParams
		});
	}

	updateUserClient(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/clients/save/' + id,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Client: Delete
	 */

	deleteUserClient(clientId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/clients/delete/' + clientId
		});
	}


}

var QuickInvoiceClient = new QuickInvoiceClientPlugin();