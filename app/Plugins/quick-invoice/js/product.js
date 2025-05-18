class QuickInvoiceProductPlugin {

	constructor() {
		if (QuickInvoiceProductPlugin.instance) return QuickInvoiceProductPlugin.instance;
		QuickInvoiceProductPlugin.instance = this;
		return QuickInvoiceProductPlugin.instance;
	}

	/**
	 * Products: Get
	 */

	userProducts() {
		return xhrRequest({
			method: "GET",
			url: BASE_URL + '/api/quick-invoice/user/products/all'
		});
	}

	/**
	 * Products: Save
	 */

	saveProduct(productId, postData, xhrParams = {}) {
		if (isEmpty(productId)) return this.addProduct(postData, xhrParams);
		return this.updateProduct(productId, postData, xhrParams);
	}

	addProduct(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/products/save',
			body: postData,
			...xhrParams
		});
	}

	updateProduct(productId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/user/products/save/' + productId,
			body: postData,
			...xhrParams
		});
	}


	/**
	 * Products: Delete
	 */

	deleteUserProduct(productId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/products/delete/' + productId
		})
	}

}

var QuickInvoiceProduct = new QuickInvoiceProductPlugin();