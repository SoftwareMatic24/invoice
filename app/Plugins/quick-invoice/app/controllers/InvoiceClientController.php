<?php

namespace App\Plugins\QuickInvoice\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\QuickInvoice\Helpers\QuickInvoiceClient;
use App\Plugins\QuickInvoice\Models\InvoiceClient;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use User;

class InvoiceClientController extends Controller {
	
	// View

	function saveClientView($clientId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);
		$client = NULL;

		if($clientId !== NULL) {
			$userId = request()["loggedInUser"]["id"] ?? -1;
			$client = InvoiceClient::userClient($userId, $clientId);
			if($client !== NULL) $client = $client->toArray();
		}

		$pageData = [
			"tabTitle" => empty($clientId) ? __('new client') : __('update client'),
			"backURL" => Util::prefixedURL($config["slug"] . "/clients/manage"),
			"pageName"=> empty($clientId) ? __('new client') : __('update client'),
			"pageSlug" => "save-invoice-client",
			"pluginConfig" => $config,
			"clientId"=>$clientId,
			"client"=>$client
		];

		return PluginController::loadView(__DIR__, "save-client.blade.php", $pageData);
	}

	function manageClientsView(){
		$config = PluginController::getPluginConfig(__DIR__);
		$user = User::user();

		$pageData = [
			"tabTitle" => __('manage clients'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName"=> __('manage clients'),
			"pageSlug" => "manage-invoice-clients",
			"pluginConfig" => $config,
			"userId"=>$user['id']
		];
		return PluginController::loadView(__DIR__, "manage-clients.blade.php", $pageData);
	}

	/**
	 * User client
	 */

	function userClients($userId){
		return InvoiceClient::userClients($userId);
	}

	// Util

	function clientBelongsToUser($clientId, $userId){
		$client = QuickInvoiceClient::userClient($clientId, $userId);
		return !empty($client);
	}

	// Request

	function userClientsRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userClients($userId);
	}

	// Save Client

	function saveClient($clientId = NULL, $userId, $data){
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
			"registrationNumber"=>"nullable|max:100",
			"registrationNumber2"=>"nullable|max:100",
			"taxNumber"=>"nullable|max:100",
			"default.discount"=>"nullable|numeric",
			"default.currency"=>"nullable|max:3"
		]);

		if($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}
		
		if(($data["default"]["discount"] ?? NULL) !== NULL && ($data["default"]["discountType"] ?? NULL) === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('select-discount-type'));
		}

		$clientId = InvoiceClient::saveClient($clientId, $userId, $data);
		if($clientId === false) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function addClientRequest(Request $request){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveClient(NULL, $userId, $data);
		return HTTP::inStringResponse($response);
	}

	function updateClientRequest(Request $request, $clientId){
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveClient($clientId, $userId, $data);
		return HTTP::inStringResponse($response);
	}


	// Delete Client

	function deleteUserClient($userId, $clientId){
		$bool = InvoiceClient::deleteUserClient($userId, $clientId);

		if(!$bool) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}
		
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserClientRequest(Request $request, $clientId){
		$userId = $request->user()->id;
		$response = $this->deleteUserClient($userId, $clientId);
		return HTTP::inStringResponse($response);
	}

}

?>