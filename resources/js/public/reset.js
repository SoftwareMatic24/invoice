class _Reset {

	constructor() {
		if (_Reset.instance) return _Reset.instance;
		_Reset.instance = this;
		return _Reset.instance;
	}

	/**
	 * Reset: Get
	 */

	activeResets() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/resets/active/all`
		});
	}

	/**
	 * Reset: Save
	 */

	doReset(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/resets/reset`,
			body: postData,
			...xhrParams
		});
	}

	doResetAll(xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/resets/reset/all`,
			body: {},
			...xhrParams
		});
	}

	/**
	 * Reset Setting: Save
	 */

	updateSetting(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/resets/settings/update/one`,
			body: postData,
			...xhrParams
		});
	}

}

var Reset = (new _Reset());