<?php

namespace App\Plugins\QuickInvoice\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\QuickInvoice\Models\InvoiceBusiness as ModelsInvoiceBusiness;
use App\Plugins\QuickInvoice\Models\InvoiceClient as ModelsInvoiceClient;
use App\Plugins\QuickInvoice\Models\InvoiceExpense as ModelsInvoiceExpense;
use App\Plugins\QuickInvoice\Models\InvoiceExpenseCategory as ModelsInvoiceExpenseCategory;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use User;

class InvoiceExpenseController extends Controller {

	function saveExpenseView(Request $request, $expenseId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$userExpense = NULL;
		if($expenseId !== NULL) $userExpense = ModelsInvoiceExpense::userExpense($userId, $expenseId);
		if($userExpense !== NULL) $userExpense = $userExpense->toArray();

		$pageData = [
			"tabTitle" => empty($expenseId) ? __('new expense') : __('update expense'),
			"backURL" => Util::prefixedURL("/quick-invoice/expense/manage"),
			"pageName"=> empty($expenseId) ? __('new expense') : __('update expense'),
			"pageSlug" => "manage-invoice-expense",
			"pluginConfig" => $config,
			"userId"=>$userId,
			"expenseId"=>$expenseId,
			"expense"=>$userExpense
		];

		return PluginController::loadView(__DIR__, "save-expense.blade.php", $pageData);
	}

	function expensesView(){
		$config = PluginController::getPluginConfig(__DIR__);
		$user = User::user();

		$pageData = [
			"tabTitle" => __('manage expense'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName"=> __('manage expense'),
			"pageSlug" => "manage-invoice-expense",
			"pluginConfig" => $config,
			"userId"=>$user['id']
		];
		return PluginController::loadView(__DIR__, "manage-expense.blade.php", $pageData);
	}

	function expenseCategoriesView(){
		$config = PluginController::getPluginConfig(__DIR__);
		$user = User::user();

		$pageData = [
			"tabTitle" => __('manage expense categories'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName"=> __('manage expense categories'),
			"pageSlug" => "manage-invoice-expense-categories",
			"pluginConfig" => $config,
			"userId"=>$user['id']
		];
		return PluginController::loadView(__DIR__, "manage-expense-categories.blade.php", $pageData);
	}

	function saveCategoryView(Request $request, $categoryId = NULL){
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$config = PluginController::getPluginConfig(__DIR__);
		$category = ModelsInvoiceExpenseCategory::userCategory($userId, $categoryId);

		$pageData = [
			"tabTitle" => empty($categoryId) ? __('new category') : __('update category'),
			"backURL" => Util::prefixedURL("/quick-invoice/expense/categories"),
			"pageName"=> empty($categoryId) ? __('new category') : __('update category'),
			"pageSlug" => "manage-invoice-expense-categories",
			"pluginConfig" => $config,
			"categoryId"=>$categoryId,
			"category"=>$category
		];
		return PluginController::loadView(__DIR__, "save-expense-category.blade.php", $pageData);
	}


	// Get User Category

	function userCategories($userId){
		return ModelsInvoiceExpenseCategory::userCategories($userId);
	}

	function userCategoriesRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userCategories($userId);
	}


	// Save User Category

	function saveUserCategory($categoryId, $addedBy, $data){

		$validator = Validator::make($data, [
			"name"=>"required|max:255"
		],[
			'name.required'=>__('name-field-required')
		]);

		if($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$categoryId = ModelsInvoiceExpenseCategory::saveUserCategory($categoryId, $addedBy, $data);
		if($categoryId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function saveUserCategoryRequest(Request $request, $categoryId = NULL){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserCategory($categoryId, $userId, $data);
		return HTTP::inStringResponse($response);
	}


	// Delete User Category

	function deleteUserCategory($userId, $categoryId){
		$isDeleted = ModelsInvoiceExpenseCategory::deleteUserCategory($categoryId, $userId);
		
		if($isDeleted === false) {
			return HTTP::inBoolArray(false, __('error-notifiaction-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	// Request
	function deleteUserCategoryRequest(Request $request, $categoryId){
		$userId = $request->user()->id;
		$response = $this->deleteUserCategory($userId, $categoryId);
		return HTTP::inStringResponse($response);
	}

	// Get User Expense

	function userExpenses($userId){
		return ModelsInvoiceExpense::userExpenses($userId);
	}

	function userExpensesRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userExpenses($userId);
	}

	// Save User Expense

	function saveUserExpense($userId, $expenseId = NULL, $data){
		$validator = Validator::make($data, [
			"title"=>"required|max:255",
			"category"=>"required|numeric",
			"referenceNumber"=>"nullable|max:255",
			"expenseDate"=>"nullable|max:255|date_format:Y-m-d",
			"currency"=>"required|max:3",
			"price"=>"required|numeric",
			"tax"=>"nullable|numeric",
			"taxType"=>"nullable|in:amount,percentage",
			"client"=>"nullable|numeric",
			"business"=>"nullable|numeric",
			"note"=>"nullable|string"
		]);
		if($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$categoryId = $data["category"] ?? NULL;
		$clientId = $data["client"] ?? NULL;
		$businessId = $data["business"] ?? NULL;

		if($categoryId !== NULL && ModelsInvoiceExpenseCategory::userCategory($userId, $categoryId) === NULL) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}
		if($clientId !== NULL && ModelsInvoiceClient::userClient($userId, $clientId) === NULL) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}
		if($businessId !== NULL && ModelsInvoiceBusiness::userBusiness($userId, $businessId) === NULL) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}

		$expenseId = ModelsInvoiceExpense::saveUserExpense($userId, $expenseId, $data);

		if($expenseId === NULL){
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notifcaiton-heading'), __('save-notification-description'));
	}

	function saveUserExpenseRequest(Request $request, $expenseId = NULL){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserExpense($userId, $expenseId, $data);
		return HTTP::inStringResponse($response);
	}

	// Delete User Expense

	function deleteUserExpense($userId, $expenseId) {

		$isDeleted = ModelsInvoiceExpense::deleteUserExpense($userId, $expenseId);
		if($isDeleted === 0) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserExpenseRequest(Request $request, $expenseId){
		$userId = $request->user()->id;
		$response = $this->deleteUserExpense($userId, $expenseId);
		return HTTP::inStringResponse($response);
	}

}

?>