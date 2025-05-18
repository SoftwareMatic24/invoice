<?php

namespace App\Plugins\ContactMessage\App\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ShortCodeController;
use App\Jobs\SendEmailJob;
use App\Plugins\ContactMessage\App\Models\ContactMessage;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller {

	// Views

	function manageContactMessagesView(Request $request){

		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle"=>__('contact messages'),
			"backURL"=>Util::prefixedURL("dashboard"),
			"pageName"=>__('contact messages'),
			"pageSlug"=>"contact-messages",
			"pluginConfig"=>$config
		];

		return PluginController::loadView(__DIR__, "contact-messages.blade.php", $pageData);
	}

	// Methods

	 function markAsRead($messageId) {
		ContactMessage::markAsRead($messageId);
		return true;
	 }

	 function replyContact(array $requestData){

		$shortCodeController = new ShortCodeController();
		$settings = Cache::get("settings");

		$validator = Validator::make($requestData, [
			"name"=>"required|string|max:255",
			"email"=>"required|email",
			"reply"=>"required|string"
		], [
			'name.required'=>__('name-field-required'),
			'email.required'=>__('email-field-required'),
			'reply.required'=>__('reply-field-required')
		]);

		if($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("contact-reply");

		$emailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => [
				"[user-name]" => $requestData["name"],
				"[reply]" => $requestData["reply"],
			]
		]);

		$mailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";

		$mailDetails = [
			"template" => $mailTemplate,
			"subject" => $emailTemplate["subject"],
			"title" => $emailTemplate["subject"],
			"data" => $emailTemplate["content"],
			"signature" => $emailTemplate["signature"] ?? NULL
		];

		dispatch(new SendEmailJob($requestData["email"], $mailDetails))->onQueue("email");

		return HTTP::inBoolArray(true, __('message-sent-notification-heading'), __('message-sent-notification-description'));
	 }

	// Requests

	 function contactRequest(Request $request){
		$data = $request->post();
		ContactMessage::addContactMessage($data);
		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('message-sent-notification-heading'), __('message-sent-notification-description')));
	 }

	 function replyContactRequest(Request $request){
		$requestData = $request->post();
		$response = $this->replyContact($requestData);
		return HTTP::inStringResponse($response);
	 }

	 function contactMessagesRequest(){
		return ContactMessage::getContactMessages();
	 }

	 function deleteContactMessagesRequest($contactMessageId){
		ContactMessage::deleteContactMessage($contactMessageId);
		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description')));
		
	 }
}

?>