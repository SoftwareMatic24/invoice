<?php

namespace App\Plugins\QuickInvoice\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDocumentTemplate extends Model {
	public $timestamps = false;
	protected $fillable = [
		"name",
		"slugs",
		"primary_color",
		"secondary_color",
		"create_datetime"
	];
	protected $table = "document_templates";


	/**
	 * Query: Get
	 */

	static function getTemplates(){
		return self::get();
	}

	static function getTemplateBySlug($slug){
		return self::where('slug', $slug)->first();
	}

}

?>