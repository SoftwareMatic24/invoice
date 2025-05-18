<?php

namespace App\Plugins\QuickInvoice\Controllers;

use App\Classes\Constants;
use App\Classes\DateTime;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ShortCodeController;
use App\Jobs\SendEmailJob;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use App\Plugins\QuickInvoice\Classes\InvoiceConfig;
use App\Plugins\QuickInvoice\Helpers\QuickInvoiceDocuments;
use App\Plugins\QuickInvoice\Models\InvoiceBusiness as ModelsInvoiceBusiness;
use App\Plugins\QuickInvoice\Models\InvoiceDocument as ModelsInvoiceDocument;
use App\Plugins\QuickInvoice\Models\InvoiceDocumentField as ModelsInvoiceDocumentField;
use App\Plugins\QuickInvoice\Models\InvoiceDocumentPayment as ModelsInvoiceDocumentPayment;
use App\Plugins\QuickInvoice\Models\InvoiceDocumentTemplate as ModelsInvoiceDocumentTemplate;
use App\Plugins\QuickInvoice\Models\InvoiceProduct as ModelsInvoiceProduct;
use App\Plugins\QuickInvoice\Models\InvoiceUserDocumentTemplate;
use Constant;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;


class InvoiceDocumentController extends Controller
{

	function saveInvoiceView(Request $request, $documentId = NULL)
	{

		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$document = NULL;

		if ($documentId !== NULL) $document = ModelsInvoiceDocument::getUserDocumentByType($userId, $documentId, "invoice");
		$products = ModelsInvoiceProduct::userProducts($userId)->toArray();

		if ($document !== NULL) {
			$document = $document->toArray();
			$document["meta"] = collect($document["meta"])->pluck("column_value", "column_name")->toArray();
		}

		$fields = $this->getUserDocumentFields($userId, "invoice");

		$pageData = [
			"tabTitle" => empty($documentId) ? __('new invoice') : __('update invoice'),
			"backURL" => Util::prefixedURL($config["slug"] . "/documents/invoice/manage"),
			"pageName" => empty($documentId) ? __('new invoice') : __('update invoice'),
			"pageSlug" => "save-invoice",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentId" => $documentId,
			"documentType" => "invoice",
			"products" => $products,
			"document" => $document,
			"fields" => $fields
		];

		return PluginController::loadView(__DIR__, "save-document.blade.php", $pageData);
	}

	function invoicesView(Request $request, $documentId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$pageData = [
			"tabTitle" => __('manage invoices'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('manage invoices'),
			"pageSlug" => "manage-invoices",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentType" => "invoice",
		];

		return PluginController::loadView(__DIR__, "documents.blade.php", $pageData);
	}

	function saveProposalView(Request $request, $documentId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$document = NULL;

		if ($documentId !== NULL) $document = ModelsInvoiceDocument::getUserDocumentByType($userId, $documentId, "proposal");
		$products = ModelsInvoiceProduct::userProducts($userId)->toArray();
		if ($document !== NULL) {
			$document = $document->toArray();
			$document["meta"] = collect($document["meta"])->pluck("column_value", "column_name")->toArray();
		}

		$fields = $this->getUserDocumentFields($userId, "proposal");

		$pageData = [
			"tabTitle" => empty($documentId) ? __('new proposal') : __('update proposal'),
			"backURL" => Util::prefixedURL($config["slug"] . "/documents/proposal/manage"),
			"pageName" => empty($documentId) ? __('new proposal') : __('update proposal'),
			"pageSlug" => "save-invoice-proposal",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentId" => $documentId,
			"documentType" => "proposal",
			"products" => $products,
			"document" => $document,
			"fields" => $fields
		];

		return PluginController::loadView(__DIR__, "save-document.blade.php", $pageData);
	}

	function proposalsView(Request $request)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$pageData = [
			"tabTitle" => __('manage proposals'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('manage proposals'),
			"pageSlug" => "manage-invoice-proposals",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentType" => "proposal",
		];

		return PluginController::loadView(__DIR__, "documents.blade.php", $pageData);
	}

	function saveDeliveryNoteView(Request $request, $documentId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$document = NULL;

		if ($documentId !== NULL) $document = ModelsInvoiceDocument::getUserDocumentByType($userId, $documentId, "delivery-note");
		$products = ModelsInvoiceProduct::userProducts($userId)->toArray();
		if ($document !== NULL) {
			$document = $document->toArray();
			$document["meta"] = collect($document["meta"])->pluck("column_value", "column_name")->toArray();
		}

		$fields = $this->getUserDocumentFields($userId, "delivery-note");

		$pageData = [
			"tabTitle" => empty($documentId) ? __('new delivery note') : __('update delivery note'),
			"backURL" => Util::prefixedURL($config["slug"] . "/documents/delivery-note/manage"),
			"pageName" => empty($documentId) ? __('new delivery note') : __('update delivery note'),
			"pageSlug" => "save-invoice-delivery-note",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentId" => $documentId,
			"documentType" => "delivery-note",
			"products" => $products,
			"document" => $document,
			"fields" => $fields
		];

		return PluginController::loadView(__DIR__, "save-document.blade.php", $pageData);
	}

	function deliveryNotesView(Request $request)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$pageData = [
			"tabTitle" => __('manage delivery notes'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('manage delivery notes'),
			"pageSlug" => "manage-invoice-delivery-notes",
			"pluginConfig" => $config,
			"userId" => $userId,
			"documentType" => "delivery-note",
		];

		return PluginController::loadView(__DIR__, "documents.blade.php", $pageData);
	}

	function documentTemplatesView(Request $request)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$templates = QuickInvoiceDocuments::templatesWithUserTemplates($userId);

		$pageData = [
			"tabTitle" => __('document templates'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('document templates'),
			"pageSlug" => "quick-invoice-templates",
			"pluginConfig" => $config,
			"userId" => $userId,
			"templates" => $templates,
			"viewOnly"=>false
		];

		return PluginController::loadView(__DIR__, "document-templates.blade.php", $pageData);
	}

	function viewOnlyDocumentTemplatesView(){
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$templates = QuickInvoiceDocuments::templatesWithUserTemplates($userId);
	
		$pageData = [
			"tabTitle" => __('document templates'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('document templates'),
			"pageSlug" => "document-templates",
			"pluginConfig" => $config,
			"userId" => $userId,
			"templates" => $templates,
			"viewOnly"=>true
		];

		return PluginController::loadView(__DIR__, "document-templates.blade.php", $pageData);
	}


	function customFieldsView(Request $request)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$fields = $this->getUserDocumentFields($userId);

		$pageData = [
			"tabTitle" => __('custom fields'),
			"backURL" => Util::prefixedURL("dashboard/"),
			"pageName" => __('custom fields'),
			"pageSlug" => "quick-invoice-custom-fields",
			"pluginConfig" => $config,
			"userId" => $userId,
			"fields" => $fields
		];

		return PluginController::loadView(__DIR__, "custom-fields.blade.php", $pageData);
	}

	function saveCustomFieldView(Request $request, $fieldId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$userId = $request["loggedInUser"]["id"] ?? NULL;
		$field = $this->getUserDocumentField($userId, $fieldId);

		$pageData = [
			"tabTitle" => __('save custom field'),
			"backURL" => Util::prefixedURL("setting/quick-invoice/custom-fields"),
			"pageName" => __('save custom field'),
			"pageSlug" => "quick-invoice-custom-fields",
			"pluginConfig" => $config,
			"userId" => $userId,
			"field" => $field,
			"documentFieldId" => $field["id"] ?? NULL
		];

		return PluginController::loadView(__DIR__, "save-custom-field.blade.php", $pageData);
	}

	function onlineDocumentView(Request $request, $uid, $documentNumber)
	{
		//1.Plugin
		$config = PluginController::getPluginConfig(__DIR__);
		$pluginSlug = $config["slug"];

		//2.Document
		$document = ModelsInvoiceDocument::getDocumentByUIDAndDocumentNumber($uid, $documentNumber);
		if ($document === NULL) die("Invalid request");
		$document = $document->toArray();
		$templateData = $this->formatDocumentTemplateData($document);

		//3. Template
		$default_template = QuickInvoiceDocuments::templates()[0];
		$user_template = QuickInvoiceDocuments::userActiveTemplate($document['added_by']);

		$template_slug = $default_template['slug'];
		$primaryColor = $default_template['primary_color'];
		$secondaryColor = $default_template['secondary_color'];
		
		if(!empty($user_template)){
			$template_slug = $user_template['document_template_slug'];
		}

		if(!empty($user_template['primary_color'])){
			$primaryColor = $user_template['primary_color'];
		}

		if(!empty($user_template['secondary_color'])){
			$secondaryColor = $user_template['secondary_color'];
		}

		//3. Document Template
		$viewFile = app_path("Plugins/$pluginSlug/invoice-templates/$template_slug.blade.php");
		$templateView = View::file($viewFile, $templateData)->render();

		//4. Page
		$pageData = [
			"tabTitle" => $templateData["documentTypeText"] . "# " . $templateData["documentNumber"],
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Online document",
			"pageSlug" => "online-document",
			"pluginConfig" => $config,
			"document" => $document,
			"templateView" => $templateView,
			"primaryColor" => $primaryColor,
			"secondaryColor" => $secondaryColor,
			'templateSlug'=>$template_slug
		];

		return PluginController::loadView(__DIR__, "document.blade.php", $pageData);
	}

	/**
	 * User documents
	 */

	function getUserDocuments($userId, $documentType)
	{
		return ModelsInvoiceDocument::getUserDocumentsByType($userId, $documentType);
	}

	function saveUserDocument($documentId = NULL, $documentType, $userId, $data)
	{

		$validator = Validator::make($data, [
			"client" => "required|numeric",
			"business" => "required|numeric",
			"issueDate" => "required|max:255|date_format:Y-m-d",
			"dueDate" => "nullable|max:255|date_format:Y-m-d",
			"documentNumber" => "nullable|max:255",
			"referenceNumber" => "nullable|max:255",
			"orderNumber" => "nullable|max:255",
			"paymentMethod" => "nullable|max:50",
			"deliveryType" => "nullable|max:50",
			"currency" => "nullable|max:3",
			"items" => "required|array",
			"items.*.item-name" => "required|max:255",
			"items.*.item-quantity" => "required|numeric",
			"items.*.item-unit-price" => "nullable|numeric",
			"items.*.item-vat" => "nullable|numeric",
		], [
			"items.required" => __('document-item-required'),
			"items.*.item-name.required" => __('document-item-name-required'),
			"items.*.item-quantity.required" => __('document-item-qty-required'),
			"items.*.item-quantity.numeric" => __('document-item-qty-numeric'),
			"items.*.item-unit-price.numeric" => __('document-item-unit-price-numeric'),
			"items.*.item-vat.numeric" => __('document-item-vat-numeric'),
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$clientId = $data["client"] ?? NULL;
		$businessId = $data["business"] ?? NULL;

		if (!$this->isAuthorizedToSaveDocument($userId, $clientId, $businessId)) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}

		if ($data["documentNumber"] === NULL) {
			$data["documentNumber"] = "DN" . DateTime::getCurrentTimestampInMilliseconds() . strtoupper(Str::random(1, 2));
		}

		$documentId = ModelsInvoiceDocument::saveUserDocument($documentId, $documentType, $userId, $data);
		if (empty($documentId)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	
	}

	function deleteUserDocument($userId, $documentId)
	{
		$isDeleted = ModelsInvoiceDocument::deleteUserDocument($userId, $documentId);
		if ($isDeleted == 0) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function emailUserDocumentViaEmail($userId, $documentId, $recipient, $subject, $message)
	{

		if ($documentId === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('document-id-required'));
		}
		else if ($recipient === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('recipient-required'));
		}
		else if ($subject === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('subject-required'));
		}
		else if ($message === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('message-required'));
		}

		$document = ModelsInvoiceDocument::getUserDocument($userId, $documentId);
		if ($document === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('document-not-available'));
		}
		$document = $document->toArray();

		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();

		$message = $shortCodeController->parseShortCode($message, [
			"textShortCodes" => [
				"[business-name]" => $document["business"]["name"] ?? "",
			]
		]);

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("document");
		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => [
				"[client-name]" => $document["client"]["name"] ?? "",
				"[business-name]" => $document["business"]["name"] ?? "",
				"[document-name]" => Str::slug($document["document_type"], " "),
				"[document-number]" => $document["document_number"],
				"[document-link]" => url("/quick-invoice/documents/online/" . $document["uid"] . "/" . $document["document_number"]),
				"[message]" => nl2br($message)
			]
		]);
		$emailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";

		$mailDetails = [
			"template" => $emailTemplate,
			"subject" => $subject ?? $mailTemplate["subject"],
			"data" => $mailTemplate["content"],
			"signature" => $mailTemplate["signature"] ?? NULL
		];

		dispatch(new SendEmailJob($recipient, $mailDetails))->onQueue("email");

		return HTTP::inBoolArray(true, __('email-sent-notification-heading'), __('email-sent-notification-description'));
	}

	// Request

	function emailUserDocumentViaEmailRequest(Request $request)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->emailUserDocumentViaEmail($userId, $data["documentId"] ?? NULL, $data["recipient"] ?? NULL, $data["subject"] ?? NULL, $data["message"] ?? NULL);
		return HTTP::inStringResponse(($response));
	}

	// Util

	function isAuthorizedToSaveDocument($userId, $clientId, $businessId){
		$clientCtrl = (new InvoiceClientController());
		$businessCtrl = (new InvoiceBusinessController());

		if(!empty($clientId) && !$clientCtrl->clientBelongsToUser($clientId, $userId)) return false;
		if (!empty($businessId) && !$businessCtrl->businessBelongsToUser($businessId, $userId)) return false;

		return true;
	}

	/**
	 * User invoice
	 */

	// Request

	function userInvoicesRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->getUserDocuments($userId, "invoice");
	}

	function saveUserInvoiceRequest(Request $request,  $documentId = NULL)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserDocument($documentId, "invoice", $userId, $data);
		return HTTP::inStringResponse($response);
	}

	function deleteUserInvoiceRequest(Request $request,  $documentId)
	{
		$userId = $request->user()->id;
		$response = $this->deleteUserDocument($userId, $documentId);
		return HTTP::inStringResponse($response);
	}

	// User Proposals

	function userProposalsRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->getUserDocuments($userId, "proposal");
	}

	function saveUserProposalRequest(Request $request, $documentId = NULL)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserDocument($documentId, "proposal", $userId, $data);
		return HTTP::inStringResponse($response);
	}

	function deleteUserProposalRequest(Request $request, $documentId)
	{
		$userId = $request->user()->id;
		$response = $this->deleteUserDocument($userId, $documentId);
		return HTTP::inStringResponse($response);
	}

	// User Delivery Note

	function userDeliveryNotesRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->getUserDocuments($userId, "delivery-note");
	}

	function saveUserDeliveryNoteRequest(Request $request, $documentId = NULL)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserDocument($documentId, "delivery-note", $userId, $data);
		return HTTP::inStringResponse($response);
	}

	function deleteUserDeliveryNoteRequest(Request $request, $documentId)
	{
		$userId = $request->user()->id;
		$response = $this->deleteUserDocument($userId, $documentId);
		return HTTP::inStringResponse($response);
	}

	// User Copy Document

	function copyUserDocument($userId, $documentId, $newDocumentType)
	{
		if ($documentId === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('document-id-required'));
		}
		else if ($newDocumentType === NULL) {
			return HTTP::inBoolArray(false, __('action-required'), __('document-type-required'));
		}

		if (!$this->isDocumentCopyPossible($documentId, $newDocumentType)) {
			return HTTP::inBoolArray(false, __('action-required'), __('can-not-convert-document'));
		}

		$isCopied = ModelsInvoiceDocument::copyDocumentAs($userId, $documentId, $newDocumentType);
		
		if ($isCopied === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('document-generated-notification-heading'), __('document-generated-notification-description'));
	}

	function copyUserDocumentRequest(Request $request)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		return $this->copyUserDocument($userId, $data["documentId"] ?? NULL, $data["documentType"] ?? NULL);
	}

	function isDocumentCopyPossible($documentId, $newDocumentType)
	{
		$documentRow = ModelsInvoiceDocument::getRow($documentId);
		if ($documentRow === NULL) return false;

		if ($documentRow["document_type"] === "invoice" && $newDocumentType === "delivery-note") return true;
		else if ($documentRow["document_type"] === "proposal" && $newDocumentType === "invoice") return true;
		return false;
	}

	// User Document Payment

	function saveUserDocumentPayment($userId, $data)
	{

		$validator = Validator::make($data, [
			"referenceNumber" => "nullable|max:255",
			"amount" => "required|numeric",
			"date" => "nullable|max:255|date_format:Y-m-d",
			"note" => "nullable",
			"documentId" => "required|numeric"
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$documentRow = ModelsInvoiceDocument::getUserRow($data["documentId"], $userId);

		if ($documentRow === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		if ($documentRow["document_type"] !== "invoice") {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		$paymentId = ModelsInvoiceDocumentPayment::savePayment($data);

		if ($paymentId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('payment-added-notification-heading'), __('payment-added-notification-description'));
	}

	function saveUserDocumentPaymentRequest(Request $request)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserDocumentPayment($userId, $data);
		return HTTP::inStringResponse($response);
	}

	function userDocumentPayments($userId, $documentId)
	{
		$document = ModelsInvoiceDocument::getUserDocument($userId, $documentId);
		if ($document === NULL) return [];
		return $document->payments;
	}

	function userDocumentPaymentsRequest(Request $request, $documentId)
	{
		$userId = $request->user()->id;
		return $this->userDocumentPayments($userId, $documentId);
	}

	function deleteUserDocumentPayment($userId, $paymentId)
	{
		$payment = ModelsInvoiceDocumentPayment::getPayment($paymentId);

		if ($payment === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		$documentId = $payment["document_id"];
		$document = ModelsInvoiceDocument::getRow($documentId);

		if ($document["added_by"] != $userId) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}

		ModelsInvoiceDocumentPayment::deletePayemnt($paymentId);

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserDocumentPaymentRequest(Request $request, $paymentId)
	{
		$userId = $request->user()->id;
		$response = $this->deleteUserDocumentPayment($userId, $paymentId);
		return HTTP::inStringResponse($response);
	}

	// User Document Stats

	function userInvoiceStats($userId)
	{
		$documents = ModelsInvoiceDocument::getUserDocumentsByType($userId, "invoice");
		$documents = $documents->toArray();
		return $this->documentsStats($documents);
	}

	function documentsStats($documents)
	{
		$formattedDocuments = [];
		$clients = [];
		$currencies = [];
		$years = [];

		foreach ($documents as $document) {
			$issueDate = $document["issue_date"];

			list($createYear, $createMonth, $createDay) = explode("-", $issueDate);
			if (!isset($formattedDocuments[$createYear])) $formattedDocuments[$createYear] = [];
			if (!isset($formattedDocuments[$createYear][$createMonth])) $formattedDocuments[$createYear][$createMonth] = [];

			$formattedDocuments[$createYear][$createMonth][] = [
				"id" => $document["id"],
				"document_number" => $document["document_number"],
				"currency" => $document["currency"],
				"discount" => $document["discount"],
				"discount_type" => $document["discount_type"],
				"client" => $document["client"]["name"],
				"day" => $createDay,
				"items" => $document["items"],
				"payments" => $document["payments"],
			];

			if (!in_array($document["client"]["name"], $clients)) $clients[] = $document["client"]["name"];
			if (!in_array($document["currency"], $currencies)) $currencies[] = $document["currency"];
			if (!in_array($createYear, $years)) $years[] = $createYear;
		}
		rsort($years);

		return [
			"currencies" => $currencies,
			"clients" => $clients,
			"years" => $years,
			"documents" => $formattedDocuments
		];
	}

	// User Document Fields

	function getUserDocumentField($userId, $fieldId)
	{
		$fields = $this->getUserDocumentFields($userId);
		$field = array_reduce($fields, function ($acc, $field) use ($fieldId) {
			if ($field["id"] == $fieldId) $acc = $field;
			return $acc;
		}, NULL);
		return $field;
	}

	function getUserDocumentFields($userId, $documentType = NULL)
	{
		$businesses = ModelsInvoiceBusiness::userBusinesses($userId)->toArray();
		$businessIDs = array_map(function ($row) {
			return $row["id"];
		}, $businesses);

		$fields = ModelsInvoiceDocumentField::getFieldsByBusinessIDs($businessIDs)->toArray();

		if ($documentType !== NULL) {
			$fields = array_filter($fields, function ($field) use ($documentType) {
				if ($field["document_type"] === $documentType) return true;
			});
		}

		return $fields;
	}

	function userDocumentFieldsRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->getUserDocumentFields($userId);
	}

	function saveUserDocumentField($userId, $fieldId = NULL, $data)
	{

		$validator = Validator::make($data, [
			"name" => "required|max:255",
			"documentType" => "required|max:255|in:invoice,proposal,delivery-note,expense",
			"position" => "required|max:255|in:top,bottom",
			"businessId" => "required|numeric"
		]);
		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$userBusiness = ModelsInvoiceBusiness::userBusiness($userId, $data["businessId"]);

		if (InvoiceConfig::$isMultiUser === true && $userBusiness === NULL) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}

		$data["slug"] = Str::slug($data["name"]);

		$responseFiledId = ModelsInvoiceDocumentField::saveField($fieldId, $data);
		if ($responseFiledId === NULL && $fieldId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}
		else if ($responseFiledId === NULL && $fieldId !== NULL) {
			return HTTP::inBoolArray(false, __('request-failed'), __('no-change-made'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function saveUserDocumentFieldRequest(Request $request, $fieldId = NULL)
	{
		$userId = $request->user()->id;
		$data = $request->post();
		$response = $this->saveUserDocumentField($userId, $fieldId, $data);
		return HTTP::inStringResponse($response);
	}

	function deleteUserDocumentField($userId, $fieldId)
	{
		$data = ["userId" => $userId, "fieldId" => $fieldId];
		$validator  = Validator::make($data, [
			"userId" => "required:max:255|numeric",
			"fieldId" => "required|max:255|numeric"
		]);
		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$field = $this->getUserDocumentField($userId, $fieldId);
		if ($field === NULL) {
			return HTTP::inBoolArray(false, __('unauthorized'), __('unauthorized-notification-description'));
		}

		$isDeleted = ModelsInvoiceDocumentField::deleteField($fieldId);
		if ($isDeleted == 0) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserDocumentFieldRequest(Request $request, $fieldId)
	{
		$userId = $request->user()->id;
		$response = $this->deleteUserDocumentField($userId, $fieldId);
		return HTTP::inStringResponse($response);
	}


	// Document Template

	function saveUserDocumentTemplate($userId, $requestData){
		InvoiceUserDocumentTemplate::saveUserTemplate($userId, $requestData);
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function saveUserDocumentTemplateRequest(Request $request){
		$requestData  = $request->post();
		$response = $this->saveUserDocumentTemplate(Auth::user()->id, $requestData);
		return HTTP::inStringResponse($response);
	}

	function activateUserDocumentTemplate($userId, $requestData){
		$template = ModelsInvoiceDocumentTemplate::getTemplateBySlug($requestData['templateSlug']);

		if(empty($template)) {
			return HTTP::inBoolArray(false, __('not-found-notification-heading'), __('not-found-notificaiton-description'));
		}

		$template = $template->toArray();

		$user_template = QuickInvoiceDocuments::userTemplate($userId, $template['slug']);

		if(!empty($user_template)){
			$data = [
				'primaryColor'=>$user_template['primary_color'],
				'secondaryColor'=>$user_template['secondary_color'],
				'templateSlug'=>$template['slug']
			];
		}
		else {
			$data = [
				'primaryColor'=>$template['primary_color'],
				'secondaryColor'=>$template['secondary_color'],
				'templateSlug'=>$template['slug']
			];
		}

		InvoiceUserDocumentTemplate::saveUserTemplate($userId, $data);
		InvoiceUserDocumentTemplate::activateUserTemplate($userId, $template['slug']);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function activateUserDocumentTemplateRequest(Request $request){
		$requestData  = $request->post();
		$response = $this->activateUserDocumentTemplate(Auth::user()->id, $requestData);
		return HTTP::inStringResponse($response);
	}
	
	function formatDocumentTemplateData($document)
	{
		$documentTypeText = $document["document_type"];
		$documentTypeText = ucwords(Str::slug($documentTypeText, " "));
		$issueDate = NULL;
		$dueDate = NULL;
		if (!empty($document["issue_date"])) $issueDate = DateTime::formatISO8601DateTodmy($document["issue_date"]);
		if (!empty($document["due_date"])) $dueDate = DateTime::formatISO8601DateTodmy($document["due_date"]);
		$customFields = $this->formatDocumentTemplateCustomFieldsData($document["business"]["id"], $document["document_type"], $document["meta"]);

		return [
			"client" => $this->formatDocumentTemplateClientData($document["client"]),
			"business" => $this->formatDocumentTemplateBusinessData($document["business"]),
			"documentType" => $document["document_type"],
			"documentTypeText" => $documentTypeText,
			"documentNumber" => $document["document_number"] ?? NULL,
			"referenceNumber" => $document["reference_number"] ?? NULL,
			"orderNumber" => $document["order_number"] ?? NULL,
			"issueDate" => $issueDate,
			"dueDate" => $dueDate,
			"salutation" => $document["salutation"] ?? NULL,
			"note" => $document["note"] ?? NULL,
			"items" => $document["items"] ?? [],
			"paymentMethod" => $document["payment_method"] ?? NULL,
			"deliveryType" => $document["delivery_type"] ?? NULL,
			"customFields" => $customFields
		];
	}

	function formatDocumentTemplateClientData($client)
	{
		$addressLine1 = [];
		$addressLine2 = [];

		if (!empty($client["city"])) $addressLine1[] = $client["city"];
		if (!empty($client["country"])) $addressLine1[] = Constants::$countries[$client["country"]];
		if (sizeof($addressLine1) === 0) $addressLine1 = NULL;
		else $addressLine1 = implode(", ", $addressLine1);

		if (!empty($client["street"])) $addressLine2[] = $client["street"];
		if (!empty($client["postcode"])) $addressLine2[] = $client["postcode"];
		if (!empty($client["province_state"])) $addressLine2[] = $client["province_state"];
		if (sizeof($addressLine2) === 0) $addressLine2 = NULL;
		else $addressLine2 = implode(", ", $addressLine2);

		return [
			"name" => $client["name"],
			"addressLine1" => $addressLine1,
			"addressLine2" => $addressLine2
		];
	}

	function formatDocumentTemplateBusinessData($business)
	{
		return [
			"name" => $business["name"],
			"logoURL" => $business["logo"]["url"] ?? NULL,
			"street"=>$business["street"] ?? NULL,
			"street_2"=>$business["street_2"] ?? NULL,
			"city"=>$business["city"] ?? NULL,
			"country"=> !empty($business["country"]) ? Constant::alpha2Countries()[$business["country"]] : NULL,
			"province_state"=>$business["province_state"] ?? NULL,
			"postcode"=>$business["postcode"] ?? NULL,
		];
	}

	function formatDocumentTemplateCustomFieldsData($businessId, $documentType, $meta)
	{
		$fields = ModelsInvoiceDocumentField::getFieldsByBusiness($businessId)->toArray();

		return array_reduce($fields, function ($acc, $field) use ($meta, $businessId, $documentType) {
			$match = NULL;
			foreach ($meta as $row) {
				if ($row["column_name"] == $field["slug"] && $field["business_id"] == $businessId && $documentType == $field["document_type"]) $match = $row;
			}

			if ($match !== NULL) {
				$position = $field["position"];
				if (!isset($acc[$position])) $acc[$position] = [];
				$acc[$position][] = [
					"label" => $field["name"],
					"slug" => $match["column_name"],
					"value" => $match["column_value"],
				];
			}

			return $acc;
		}, []);
	}
}
