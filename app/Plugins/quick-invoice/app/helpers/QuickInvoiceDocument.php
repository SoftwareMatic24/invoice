<?php

namespace App\Plugins\QuickInvoice\Helpers;

use App\Plugins\QuickInvoice\Models\InvoiceDocument;
use App\Plugins\QuickInvoice\Models\InvoiceDocumentTemplate;
use App\Plugins\QuickInvoice\Models\InvoiceUserDocumentTemplate;

class QuickInvoiceDocuments {

	/**
	 * Documents: Get
	 */

	static function userDocumentsByType($userId, $type){
		return InvoiceDocument::getUserDocumentsByType($userId, $type);
	}


	/**
	 * Templates: Get
	 */

	static function templates(){
		return InvoiceDocumentTemplate::getTemplates()->toArray();
	}

	static function templatesWithUserTemplates($userId){
		$templates = self::templates();
		$userTemplates = self::userTemplates($userId);

		return array_map(function($template) use($userTemplates) {

			$slug = $template['slug'];
			$userTemplate = $userTemplates[$slug] ?? NULL;
			
			if(isset($userTemplate)){
				$template['status'] = $userTemplate['status'];
				$template['primary_color'] = $userTemplate['primary_color'];
				$template['secondary_color'] = $userTemplate['secondary_color'];
			}
			else {
				$template['status'] = 'inactive';
			}

			return $template;

		}, $templates);
	}

	static function userTemplate($userId, $slug){
		$template = InvoiceUserDocumentTemplate::userTemplateBySlug($userId, $slug);
		return !empty($template) ? $template->toArray() : NULL;
	}

	static function userActiveTemplate($userId){
		$template = InvoiceUserDocumentTemplate::userActiveTemplate($userId);
		return !empty($template) ? $template->toArray() : NULL;
	}

	static function userTemplates($userId){
		return InvoiceUserDocumentTemplate::userTemplatesKeyBySlug($userId)->toArray();
	}



}
