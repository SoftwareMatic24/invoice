class NotificationBannerPlugin {
	constructor() {
		if (NotificationBannerPlugin.instance) return NotificationBannerPlugin.instance;
		NotificationBannerPlugin.instance = this;
		return NotificationBannerPlugin.instance;
	}

	/**
	 * Get
	 */

	notificationBanners() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/notification-banner/all`
		});
	}

	/**
	 * Save
	 */

	saveNotificationBanner(id, postData, xhrParams = {}) {
		if (isEmpty(id)) return this.addNotificationBanner(id, postData, xhrParams);
		return this.updateNotificationBanner(id, postData, xhrParams);
	}

	addNotificationBanner(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/notification-banner/save`,
			body: postData,
			...xhrParams
		});
	}

	updateNotificationBanner(id, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/notification-banner/save/${id}`,
			body: postData,
			...xhrParams
		})
	}

	/**
	 * Delete
	 */

	deleteNotificationBanner(notificationBannerId) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/notification-banner/delete/${notificationBannerId}`
		});
	}

}


var NotificationBanner = new NotificationBannerPlugin();