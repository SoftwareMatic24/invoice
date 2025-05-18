class QuickInvoiceDocumentPlugin {
	constructor() {
		if (QuickInvoiceDocumentPlugin.instance) return QuickInvoiceDocumentPlugin.instance;
		QuickInvoiceDocumentPlugin.instance = this;
		return QuickInvoiceDocumentPlugin.instance;
	}

	/**
	 * Document: Get
	 */

	userDocumentsByType(documentType) {
		return xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/quick-invoice/user/documents/' + documentType + '/all'
		});
	}

	/**
	 * Docuemnt: Save
	 */

	save(id, type, postData, xhrParams = {}) {
		if (isEmpty(id)) return this.addDocument(type, postData, xhrParams);
		return this.updateDocument(id, type, postData, xhrParams);
	}

	addDocument(type, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/quick-invoice/user/documents/${type}/save`,
			body: postData,
			...xhrParams
		});
	}

	updateDocument(id, type, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/quick-invoice/user/documents/${type}/save/${id}`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Docuemnt: Delete
	 */

	deleteUserDocument(documentId, documentType) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/documents/' + documentType + '/delete/' + documentId
		});
	}

	/**
	 * Document: Copy
	 */

	copyUserDocument(documentId, newDocumentType) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/copy',
			body: { documentId, documentType: newDocumentType }
		});
	}

	/**
	 * Document: Payments
	 */

	userDocumentPayments(documentId) {
		return xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/quick-invoice/user/documents/' + documentId + '/payments/all'
		});
	}

	saveUserDocumentPayment(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/payments/save',
			body: postData,
			...xhrParams
		});
	}

	deleteUserDocumentPayment(paymentId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/documents/payments/delete/' + paymentId
		})
	}

	/**
	 * Custom Fields: Get
	 */

	userDocumentCustomFields() {
		return xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/quick-invoice/user/documents/fields/all'
		});
	}

	/**
	 * Custom Fields: Save
	 */

	saveUserDocumentCustomField(fieldId, postData, xhrParams = {}) {
		if (isEmpty(fieldId)) return this.addUserDocumentCustomField(postData, xhrParams);
		return this.updateUserDocumentCustomField(fieldId, postData, xhrParams);
	}

	addUserDocumentCustomField(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/fields/save',
			body: postData,
			...xhrParams
		})
	}

	updateUserDocumentCustomField(fieldId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/user/documents/fields/save/' + fieldId,
			body: postData,
			...xhrParams
		})
	}

	/**
	 * Custom Fields: Delete
	 */

	deleteUserDocumentCustomField(fieldId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/documents/fields/delete/' + fieldId
		});
	}


	/**
	 * Templates: Save
	 */

	saveUserDocumentTemplate(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/templates/save',
			body: postData,
			...xhrParams
		});
	}

	activateUserDocumentTemplate(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/templates/activate',
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Docuemnt: Other
	 */

	sendViaEmail(documentId, recipient, subject, message, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/documents/send-via-email',
			body: { documentId, recipient, subject, message },
			...xhrParams
		});
	}


}

var QuickInvoiceDocument = new QuickInvoiceDocumentPlugin();