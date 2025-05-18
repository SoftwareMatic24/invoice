
class UserManagerPlugin {

	constructor() {
		if (UserManagerPlugin.instance) return UserManagerPlugin.instance;
		UserManagerPlugin.instance = this;
		return UserManagerPlugin.instance;
	}


	/**
	 * Users: Get
	 */

	async user(userId) {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/user-manager/users/one/${userId}`,
		});
	}

	async users() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/user-manager/users/all`,
		});
	}

	/**
	 * Users: Save
	 */

	async saveUser(userId, postData, xhrParams = {}) {
		return isEmpty(userId)
			? this.addUser(postData, xhrParams)
			: this.updateUser(userId, postData, xhrParams);
	}

	async addUser(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/users/save`,
			body: postData,
			...xhrParams
		});
	}

	async updateUser(userId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/users/save/${userId}`,
			body: postData,
			...xhrParams
		});
	}

	async updateProfileInformation(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/profile-information`,
			body: postData,
			...xhrParams
		});
	}

	async updatePassword(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/password`,
			body: postData,
			...xhrParams
		});
	}

	async updateRoleAndStatus(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/role-and-status`,
			body: postData,
			...xhrParams
		});
	}

	async updateAbout(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/about`,
			body: postData,
			...xhrParams
		})
	}

	async updateAddress(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/address`,
			body: postData,
			...xhrParams
		})
	}

	async updateAdditional(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/additional`,
			body: postData,
			...xhrParams
		})
	}

	async updateProfilePicture(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/user/users/update/profile-picture`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Users: Delete
	 */

	async deleteUser(userId) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/user-manager/users/delete/${userId}`
		})
	}


	/**
	 * Roles: Get
	 */

	async roles() {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/user-manager/roles/all`
		});
	}

	async role(roleId) {
		return xhrRequest({
			method: 'GET',
			url: `${BASE_URL}/api/user-manager/roles/one/${roleId}`
		});
	}

	/**
	 * Roles: Save
	 */

	async saveRole(roleId, postData, xhrParams) {
		return isEmpty(roleId)
			? this.addRole(postData, xhrParams)
			: this.updateRole(roleId, postData, xhrParams);
	}

	async addRole(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/roles/add`,
			body: postData,
			...xhrParams
		});
	}

	async updateRole(roleId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: `${BASE_URL}/api/user-manager/roles/update/${roleId}`,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Roles: Delete
	 */

	async deleteRole(roleId) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/user-manager/roles/delete/${roleId}`
		});
	}


	/**
	 * Abilities: Save
	 */


	async assignAbility(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: `${BASE_URL}/api/user-manager/abilities/assign`,
			body: postData,
			...xhrParams
		})
	}

	/**
	 * Abilities: Delete
	 */

	async removeAbility(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'DELETE',
			url: `${BASE_URL}/api/user-manager/abilities/remove`,
			body: postData,
			...xhrParams
		})
	}


}


UserManager = new UserManagerPlugin();

