<?php

namespace App\Plugins\Tax\Controllers\Tax;

require_once __DIR__."/../models/TaxSetting.php";
require_once __DIR__."/../models/ShippingClass.php";

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Tax\Models\ShippingClass;
use App\Plugins\Tax\Models\TaxClass;
use App\Plugins\Tax\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxController extends Controller {

	// views

	function taxesView(){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Manage Tax",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Manage Tax",
			"pageSlug" => "manage-tax",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-tax.blade.php", $pageData);
	}

	function saveTaxView($taxClassId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $taxClassId === NULL ? "Add New Tax Class" : "Edit Tax Class",
			"backURL" => Util::prefixedURL($config["slug"]."/tax"),
			"pageName" => $taxClassId === NULL ? "New Tax Class" : "Update Tax Class",
			"pageSlug" => "manage-tax",
			"pluginConfig" => $config,
			"taxClassId"=>$taxClassId
		];

		return PluginController::loadView(__DIR__, "save-tax-class.blade.php", $pageData);
	}

	function shippingsView(){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Manage Shipping",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Manage Shipping",
			"pageSlug" => "manage-shipping",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-shipping.blade.php", $pageData);
	}

	function saveShippingClassView($shippingClassId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $shippingClassId === NULL ? "Add New Shipping Class" : "Edit Shipping Class",
			"backURL" => Util::prefixedURL($config["slug"]."/shipping"),
			"pageName" => $shippingClassId === NULL ? "New Shipping Class" : "Update Shipping Class",
			"pageSlug" => "manage-shipping",
			"pluginConfig" => $config,
			"shippingClassId"=>$shippingClassId
		];

		return PluginController::loadView(__DIR__, "save-shipping-class.blade.php", $pageData);
	}

	function settingsView(){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Tax Settings",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Tax Settings",
			"pageSlug" => "tax-setting",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "setting.blade.php", $pageData);
	}

	// methods

	function userTaxClasses($userId){
		return TaxClass::getTaxClassesByUserId($userId);
	}

	function userTaxClass($userId, $taxClassId){
		return TaxClass::getTaxClassByIdUserId($taxClassId, $userId);
	}

	function saveTaxClass($userId, $taxClassId = NULL, $data){
		$errors = [];

		if(empty($data["taxClassName"])) $errors[] = "Tax Class Name is required";

		foreach($data["rates"] as $row){
			if(empty($row["rate"])) $errors[] = "Rate % is required";
			else if(!is_numeric($row["rate"])) $errors[] = "Rate % must be a number";
			if(empty($row["tax"])) $errors[] = "Tax name is required";
		}
		
		if(sizeof($errors) > 0) return ["status"=>"fail", "msg"=>$errors[0]];

		if($taxClassId === NULL) {
			$taxClass = TaxClass::saveTaxClass($userId, $taxClassId, $data);
			$taxClassId = $taxClass->id;
		}
		else TaxClass::saveTaxClass($userId, $taxClassId, $data);
		

		return ["status"=>"success", "msg"=>"Tax Class is saved", "taxClassId"=>$taxClassId];
	}

	function deleteUserTaxClass($userId, $taxClassId){
		TaxClass::deleteUserTaxClass($userId, $taxClassId);
		return ["status"=>"success", "msg"=>"Tax Class deleted"];
	}

	function userShippingClasses($userId){
		return ShippingClass::getUserShippingClasses($userId);
	}

	function userShippingClass($userId, $shippingClassId){
		return ShippingClass::getUserShippingClass($userId, $shippingClassId);
	}

	function saveUserShippingClass($userId, $shippingClassId = NULL, $data){
		$errors = [];
		$zones = $data["zones"] ?? [];
		
	
		foreach($zones as $zone){
			
			foreach($zone["conditions"] ?? [] as $condition){

		
				if(!is_numeric($condition["from"])) $errors[] = "From price must be a number.";
				else if ($condition["to"] !== '' && $condition["to"] !== NULL && !is_numeric($condition["to"])) $errors[] = "To price can only be a number or empty";
				else if (!is_numeric($condition["cost"])) $errors[] = "Cost must be a number";
				else if (is_numeric($condition["from"]) && is_numeric($condition["to"]) && floatval($condition["from"]) >= floatval($condition["to"])) $errors[] = "From price must be greater than to price";
			}
		}

		if(empty($data["shippingClassName"])) $errors[] = "Shipping Class Name is required";
		if(sizeof($zones) === 0) $errors[] = "Please add at least one shipping zone";
		if(sizeof($errors) > 0) return ["status"=>"fail", "msg"=>$errors[0]];

		if($shippingClassId === NULL){
			$shippingClass = ShippingClass::addUserShippingClass($userId, $data);
			$shippingClassId = $shippingClass["id"];
		}
		else ShippingClass::updateUserShippingClass($userId, $shippingClassId, $data);

		return ["status"=>"success", "msg"=>"Shipping class is saved", "shippingClassId"=>$shippingClassId];
	}

	function deleteUserShippingClass($userId, $shippingClassId){
		ShippingClass::deleteUserShippingClass($userId, $shippingClassId);
		return ["status"=>"success", "msg"=>"Shipping class is deleted"];
	}
	
	function userSettings($userId){
		return TaxSetting::getSettingsByUserId($userId);
	}

	function updateUserSettings($userId, $data){
		foreach($data as $columnName=>$columnValue){
			TaxSetting::saveSetting($userId, $columnName, $columnValue);
		}
		return ["status"=>"success", "msg"=>"Settings updated"];
	}

	// requests

	function userTaxClassesRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userTaxClasses($userId);
	}

	function userTaxClassRequest(Request $request, $taxClassId){
		$userId = $request->user()->id;
		return $this->userTaxClass($userId, $taxClassId);
	}

	function saveTaxClassRequest(Request $request, $taxClassId = NULL){
		$data = $request->post();
		$userId = $request->user()->id;
		return $this->saveTaxClass($userId, $taxClassId, $data);
	}
	
	function deleteUserTaxClassRequest(Request $request, $taxClassId){
		$userId = $request->user()->id;
		return $this->deleteUserTaxClass($userId, $taxClassId);
	}

	function userShippingClassesRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userShippingClasses($userId);
	}

	function userShippingClassRequest(Request $request, $shippingClassId){
		$userId = $request->user()->id;
		return $this->userShippingClass($userId, $shippingClassId);
	}

	function saveUserShippingClassRequest(Request $request, $shippingClassId = NULL){
		$userId = $request->user()->id;
		$data = $request->post();
		return $this->saveUserShippingClass($userId, $shippingClassId, $data);
	}

	function deleteUserShippingClassRequest(Request $request, $shippingClassId = NULL){
		$userId = $request->user()->id;
		return $this->deleteUserShippingClass($userId, $shippingClassId);
	}

	function userSettingsRequest(Request $request){
		$userId = $request->user()->id;
		return $this->userSettings($userId);
	}

	function updateUserSettingsRequest(Request $request){
		$data = $request->post();
		$userId = $request->user()->id;
		return $this->updateUserSettings($userId, $data);
	}

}

?>