
class UserManagerPluginAccount {

	constructor() {
		if (UserManagerPluginAccount.instance) return UserManagerPluginAccount.instance;
		UserManagerPluginAccount.instance = this;
		return UserManagerPluginAccount.instance;
	}

	/**
	 * Account
	 */

	async auth(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/accounts/auth`,
			body: postData,
			...xhrParams
		})
	}

	async register(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/accounts/register`,
			body: postData,
			...xhrParams
		})
	}

	async forgotPassword(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/accounts/forgot-password`,
			body: postData,
			...xhrParams
		});
	}

	async resetPassword(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/accounts/reset-password`,
			body: postData,
			...xhrParams
		});
	}

}


UserManagerAccount = new UserManagerPluginAccount();

