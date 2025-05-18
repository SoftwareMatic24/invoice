class SubscriptionPlugin {
	constructor() {
		if (SubscriptionPlugin.instance) return SubscriptionPlugin.instance;
		SubscriptionPlugin.instance = this;
		return SubscriptionPlugin.instance;
	}

	/**
	 * Package
	 */

	packages() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/subscription/packages/all`
		});
	}

	savePackage(id, postData, xhrParams = {}) {
		if (isEmpty(id)) return this.addPackage(postData, xhrParams);
		return this.updatePackage(id, postData, xhrParams);
	}

	addPackage(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/subscription/packages/save`,
			body: postData,
			...xhrParams
		});
	}

	updatePackage(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/subscription/packages/save/${id}`,
			body: postData,
			...xhrParams
		});
	}

	deletePackage(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/subscription/packages/delete/${id}`,
			body: postData,
			...xhrParams
		})
	}

	/**
	 * Classification
	 */

	classifications() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/subscription/classifications/all`
		});
	}

	saveClassification(slug = null, postData, xhrParams = {}) {
		if (isEmpty(slug)) return this.addClassification(postData, xhrParams);
		return this.updateClassification(slug, postData, xhrParams);
	}

	addClassification(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/subscription/classifications/save`,
			body: postData,
			...xhrParams
		});
	}

	updateClassification(slug, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/subscription/classifications/save/${slug}`,
			body: postData,
			...xhrParams
		});
	}

	deleteClassificationBySlug(slug) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/subscription/classifications/delete/slug/${slug}`
		});
	}

	/**
	 * Subscriber
	 */

	subscriber(userId) {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/subscription/subscribers/one/user-id/${userId}`
		});
	}

	saveSubscriber(userId, packageId, transactionId, disable, expiryDateTime = null, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/subscription/packages/subscribers/save`,
			body: {
				userId, packageId, transactionId, disable, expiryDateTime
			},
			...xhrParams
		});
	}


}

var Subscription = new SubscriptionPlugin();