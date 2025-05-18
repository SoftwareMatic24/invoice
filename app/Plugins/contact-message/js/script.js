class ContactMessagePlugin {

	constructor() {
		if (ContactMessagePlugin.instance) return ContactMessagePlugin.instance;
		ContactMessagePlugin.instance = this;
		return ContactMessagePlugin.instance;
	}

	/**
	 * Contact Message: Get
	 */

	async messages() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/contact-message/all`
		});
	}

	/**
	 * Contact Message: Save
	 */

	async contact(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/contact-message/send`,
			body: postData,
			...xhrParams
		});
	}

	async reply(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/contact-message/reply`,
			body: postData,
			...xhrParams
		});
	}

	async markAsRead(id) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/contact-message/mark-as-read/${id}`
		});
	}

	/**
	 * Contact Message: Delete
	 */

	async deleteMessage(id) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/contact-message/delete/${id}`
		});
	}

}

ContactMessage = new ContactMessagePlugin();

