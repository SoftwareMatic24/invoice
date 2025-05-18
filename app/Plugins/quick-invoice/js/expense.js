class QuickInvoiceExpensePlugin {
	constructor() {
		if (QuickInvoiceExpensePlugin.instance) return QuickInvoiceExpensePlugin.instance;
		QuickInvoiceExpensePlugin.instance = this;
		return QuickInvoiceExpensePlugin.instance;
	}

	/**
	 * Expenses: Get
	 */

	userExpenses() {
		return xhrRequest({
			method: "GET",
			url: BASE_URL + '/api/quick-invoice/user/expense/all'
		});
	}

	/**
	 * Expense: Save
	 */

	saveUserExpense(expenseId, postData, xhrParams = {}) {
		if (isEmpty(expenseId)) return this.addUserExpense(postData, xhrParams);
		return this.updateUserExpense(expenseId, postData, xhrParams);
	}

	addUserExpense(postData, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/expense/save',
			body: postData,
			...xhrParams
		});
	}

	updateUserExpense(expenseId, postData, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/user/expense/save/' + expenseId,
			body: postData,
			...xhrParams
		});
	}

	/**
	 * Expense: Delete
	 */

	deleteUserExpense(expenseId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/expense/delete/' + expenseId
		})
	}


	/**
	 * Category: Get
	 */

	userCategories() {
		return xhrRequest({
			method: "GET",
			url: BASE_URL + '/api/quick-invoice/user/expense/categories/all'
		});
	}

	/**
	 * Category: Save
	 */

	saveUserCategory(categoryId, name, xhrParams = {}) {
		if (isEmpty(categoryId)) return this.addUserCategory(name, xhrParams);
		return this.updateUserCategory(categoryId, name, xhrParams);
	}

	addUserCategory(name, xhrParams = {}) {
		return xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/quick-invoice/user/expense/categories/save',
			body: { name },
			...xhrParams
		});
	}

	updateUserCategory(categoryId, name, xhrParams = {}) {
		return xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/quick-invoice/user/expense/categories/save/' + categoryId,
			body: { name },
			...xhrParams
		});
	}

	/**
	 * Category: Delete
	 */

	deleteUserCategory(categoryId) {
		return xhrRequest({
			method: 'DELETE',
			url: BASE_URL + '/api/quick-invoice/user/expense/categories/delete/' + categoryId
		});
	}


}

var QuickInvoiceExpense = new QuickInvoiceExpensePlugin();