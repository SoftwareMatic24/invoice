<?php

namespace App\Plugins\QuickInvoice\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\QuickInvoice\Models\InvoiceProduct as ModelsInvoiceProduct;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use User;

class InvoiceProductController extends Controller {

	function productsView($productType){
		$config = PluginController::getPluginConfig(__DIR__);
		$productType = substr($productType ,0, strlen($productType) - 1);
		$pageSlug = "manage-invoice-".$productType;
		$user = User::user();

		$pageData = [
			"tabTitle" => __('manage')." ".__($productType),
			"backURL" => Util::prefixedURL($config["slug"] . ""),
			"pageName"=> __('manage')." ".__($productType),
			"pageSlug" => $pageSlug,
			"pluginConfig" => $config,
			"productType"=>$productType,
			"userId"=>$user['id']
		];

		return PluginController::loadView(__DIR__, "products.blade.php", $pageData);
	}

	function saveProductView(Request $request, $productType, $productId = NULL){

		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$config = PluginController::getPluginConfig(__DIR__);
		$product = ModelsInvoiceProduct::userProduct($userId, $productId);
		$user = User::user();

		$pageData = [
			"tabTitle" => empty($productId) ? __('new '. $productType) : __('update '.$productType),
			"backURL" => Util::prefixedURL("/quick-invoice/products/manage/".$productType."s"),
			"pageName"=> empty($productId) ? __('new '. $productType) : __('update '.$productType),
			"pageSlug" => "manage-invoice-".$productType,
			"pluginConfig" => $config,
			"productId"=>$productId,
			"productType"=>$productType,
			"product"=>$product,
			"userId"=>$user['id']
		];

		return PluginController::loadView(__DIR__, "save-product.blade.php", $pageData);
	}

	// Product

	function userProducts($userId){
		return ModelsInvoiceProduct::userProducts($userId);
	}

	function userProductsRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userProducts($userId);
	}

	// Save Product

	function saveProduct($productId = NULL, $userId, $data){
		
		$validator = Validator::make($data, [
			"title"=>"required|max:255",
			"price"=>"required|numeric|min:0",
			"code"=>"nullable|max:150",
			"unit"=>"nullable|max:50",
			"type"=>"required"
		]);

		if($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$productId = ModelsInvoiceProduct::saveUserProduct($productId, $userId, $data);
		if($productId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function addProductRequest(Request $request){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveProduct(NULL, $userId, $data);
		return HTTP::inStringResponse($response);
	}

	function updateProductRequest(Request $request, $productId){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveProduct($productId, $userId, $data);
		return HTTP::inStringResponse($response);
	}

	// Delete Product

	function deleteUserProduct($userId, $productId){
		$isDeleted = ModelsInvoiceProduct::deleteUserProduct($userId, $productId);
		
		if($isDeleted === false) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}
		
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserProductRequest(Request $request, $productId){
		$userId = $request->user()->id;
		$response = $this->deleteUserProduct($userId, $productId);
		return HTTP::inStringResponse($response);
	}


}

?>