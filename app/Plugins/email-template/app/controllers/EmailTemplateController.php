<?php

namespace App\Plugins\EmailTemplate\Controller;

require_once __DIR__."/../models/EmailTemplate.php";
require_once __DIR__."/../models/EmailSignature.php";

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\EmailTemplate\Model\EmailSignature;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller {


	// views 

	function emailTemplatesView(){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => ucwords(__("email templates")),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName" => ucwords(__("email templates")),
			"pageSlug" => "email-template",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "email-templates.blade.php", $pageData);
	}

	function saveEmailTemplateView($emailTemplateId){
		$config = PluginController::getPluginConfig(__DIR__);

		$signatures = EmailSignature::getRows();

		$pageData = [
			"tabTitle" => ucwords(__("edit email templates")),
			"backURL" => Util::prefixedURL($config["slug"]),
			"pageName" => ucwords(__("edit email templates")),
			"pageSlug" => "email-template",
			"pluginConfig" => $config,
			"emailTemplateId"=>$emailTemplateId,
			"signatures"=>$signatures
		];

		return PluginController::loadView(__DIR__, "save-email-template.blade.php", $pageData);
	}

	function emailSignatureTemplates(){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Email Signature Templates",
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName" => "Email Signature Templates",
			"pageSlug" => "email-template",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "email-signature-templates.blade.php", $pageData);
	}

	function saveSignatureTemplateView($signatureId = NULL){
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $signatureId === NULL ? "Add Signature Template" : "Edit Signature Template",
			"backURL" => Util::prefixedURL("email-template/signatures"),
			"pageName" => $signatureId === NULL ? "New Signature Template" : "Update Signature Template",
			"pageSlug" => "email-template",
			"pluginConfig" => $config,
			"signatureId"=>$signatureId
		];

		return PluginController::loadView(__DIR__, "save-signature-template.blade.php", $pageData);
	}


	// Email Templates

	function emailTemplates(){
		return EmailTemplate::getEmailTemplates();
	}

	function emailTemplate($emailTemplateId){
		return EmailTemplate::getEmailTemplate($emailTemplateId);
	}

	function saveEmailTemplate($emailTemplateId, $data){
		EmailTemplate::updateEmailTemplate($emailTemplateId, $data);
		return ["status"=>"success", "msg"=> __("email-template-save-notification")];
	}

	function saveEmailTemplateRequest(Request $request, $emailTemplateId){
		$data = $request->post();
		return $this->saveEmailTemplate($emailTemplateId, $data);
	}

	// Email Signatures

	function emailSignatures(){
		return EmailSignature::getSignatures();
	}

	function emailSignature($signatureId){
		return EmailSignature::getSignature($signatureId);
	}

	function saveSignature($signatureId = NULL, $data){

		$validator = Validator::make($data, [
			"title"=>"required|max:255"
		]);

		if ($validator->fails()) return ["status" => "fail", "msg" => $validator->errors()->all()[0]];

		$slug = Str::slug($data["title"]);
		$signature = EmailSignature::getRowBySlug($slug);


		if($signatureId === NULL && $signature !== NULL) return ["status"=>"fail", "msg"=>"This Email Signature Template already exists."];
		else if($signatureId !== NULL && $signature !== NULL && $signature["id"] != $signatureId) return ["status"=>"fail", "msg"=>"This Email Signature Template already exists."];

		$data["slug"] = $slug;

		if($signatureId === NULL) {
			$signature = EmailSignature::addSignature($data);
			$signatureId = $signature["id"];
		}
		else EmailSignature::updateSignature($signatureId, $data);

		return ["status"=>"success", "msg"=>"Email signature is saved.", "signatureId"=>$signatureId];
	}

	function deleteSignature($signatureId){
		EmailSignature::deleteRow($signatureId);
		return ["status"=>"success", "msg"=>"Email Signature Template is deleted."];
	}

	function saveSignatureRequest(Request $request, $signatureId = NULL){
		$data = $request->post();
		return $this->saveSignature($signatureId, $data);
	}

	// Email Design

	function getEmailDesignConfig($designName = NULL){
		$settings = Cache::get("settings");
		if($designName === NULL) $designName = $settings["email-template"]["column_value"];

		$file = resource_path("views/mails/$designName/config.json");
		if(!file_exists($file)) return NULL;
		return json_decode(file_get_contents($file), true);
	}

	// HTML Elements

	function htmlButton($text, $link, $type = "primary", $options = []){
		$color = $options["color"] ?? NULL;
		$backgroundColor = $options["backgroundColor"] ?? NULL;
		$designConfig = $this->getEmailDesignConfig();
		$styles = ["border:none;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;"];

		if($type === "primary") $styles[] = "background-color: ".$designConfig["primaryColor"];

		$stylesStr = implode(";", $styles);

		return "<a href='$link' style='$stylesStr'>$text</a>";
	}

}

?>