<?php

namespace App\Plugins\QuickInvoice\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\QuickInvoice\Helpers\QuickInvoiceBusiness;
use App\Plugins\QuickInvoice\Models\InvoiceBusiness;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use User;

class InvoiceBusinessController extends Controller {

	function businessesView(){
		$config = PluginController::getPluginConfig(__DIR__);
		$user = User::user();

		$pageData = [
			"tabTitle" => __('manage businesses'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName"=> __('manage businesses'),
			"pageSlug" => "manage-user-invoice-business",
			"pluginConfig" => $config,
			"userId"=>$user['id']
		];

		return PluginController::loadView(__DIR__, "manage-business.blade.php", $pageData);
	}

	function saveBusinessView(Request $request, $businessId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);
		$business = NULL;
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		if($businessId !== NULL) $business = InvoiceBusiness::userBusiness($userId, $businessId);

		$pageData = [
			"tabTitle" => empty($businessId) ? __('new business') : __('update business'),
			"backURL" => Util::prefixedURL("/quick-invoice/business/manage"),
			"pageName"=> empty($businessId) ? __('new business') : __('update business'),
			"pageSlug" => "save-user-invoice-business",
			"pluginConfig" => $config,
			"businessId"=>$businessId,
			"business"=>$business
		];

		return PluginController::loadView(__DIR__, "save-business.blade.php", $pageData);
	}

	/**
	 * User business
	 */

	function userBusinesses($userId){
		return InvoiceBusiness::userBusinesses($userId);
	}

	// Util

	function businessBelongsToUser($businessId, $userId){
		$business = QuickInvoiceBusiness::userBusiness($businessId, $userId);
		return !empty($business);
	}

	// Request

	function userBusinessesRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userBusinesses($userId);
	}

	// Save User Business

	function saveUserBusiness($businessId, $userId, $data){
		$validator = Validator::make($data, [
			"name"=>"required|max:255",
			"email"=>"nullable|max:255|email",
			"country"=>"required|max:2",
			"city"=>"nullable|max:255",
			"province"=>"nullable|max:255",
			"street"=>"nullable|max:255",
			"street2"=>"nullable|max:255",
			"postcode"=>"nullable|max:50",
			"telephone"=>"nullable|max:50",
			"phone"=>"nullable|max:50",
			"fax"=>"nullable|max:50",
			"website"=>"nullable|max:255",
			"businessId"=>"nullable|max:100",
			"taxId"=>"nullable|max:100",
			"tradeRegister"=>"nullable|max:100",
			"logoMediaId"=>"nullable|numeric",
			"signatureMediaId"=>"nullable|numeric"
		]);

		if($validator->fails()){
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$businessId = InvoiceBusiness::saveUserBusiness($businessId, $userId, $data);
		if($businessId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));

	}

	function saveUserBusinessRequest(Request $request, $businessId = NULL){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserBusiness($businessId, $userId, $data);
		return HTTP::inStringResponse($response);
	}

	// Delete User Business

	function deleteUserBusiness($businessId, $userId){
		$isDeleted = InvoiceBusiness::deleteUserBusiness($businessId, $userId);
		
		if($isDeleted === 0) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(false, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserBusinessRequest(Request $request, $businessId){
		$userId = $request->user()->id;
		$response = $this->deleteUserBusiness($businessId, $userId);
		return HTTP::inStringResponse($response);
	}
}

?>