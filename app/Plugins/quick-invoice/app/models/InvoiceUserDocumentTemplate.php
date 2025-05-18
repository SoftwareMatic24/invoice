<?php

namespace App\Plugins\QuickInvoice\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceUserDocumentTemplate extends Model {

	public $timestamps = false;

	protected $fillable = [
		'primary_color',
		'secondary_color',
		"user_id",
		"document_template_slug"
	];

	protected $table = "user_document_templates";

	function documentTemplate(){
		return $this->belongsTo(InvoiceDocumentTemplate::class, 'document_template_slug', 'slug');
	}

	static function basicRelation(){
		return self::with('documentTemplate');
	}

	// Query: Get

	static function userTemplatesKeyBySlug($userId){
		return self::basicRelation()->where('user_id', $userId)->get()->keyBy('document_template_slug');
	}

	static function userActiveTemplate($userId){
		return self::basicRelation()->where('status', 'active')->where('user_id', $userId)->first();
	}

	static function userTemplateBySlug($userId, $templteSlug){
		return self::basicRelation()->where('user_id', $userId)->where('document_template_slug', $templteSlug)->first();
	}
	

	// Query: Save

	static function saveUserTemplate($userId, $data){
		$record = self::userTemplateBySlug($userId, $data['templateSlug']);
		if(empty($record)) return self::autoAddUserTemplate($data, $userId);
		return self::updateUserTemplate($userId, $data);
	}

	static function autoAddUserTemplate($data, $userId){

		self::create([
			'primary_color'=>$data['primaryColor'],
			'secondary_color'=>$data['secondaryColor'],
			'document_template_slug'=>$data['templateSlug'],
			'user_id'=>$userId
		]);

	}

	static function updateUserTemplate($userId, $data){
		return self::where('user_id', $userId)->where('document_template_slug', $data['templateSlug'])->update([
			'primary_color'=>$data['primaryColor'],
			'secondary_color'=>$data['secondaryColor'] ?? NULL
		]);
	}

	static function activateUserTemplate($userId, $templteSlug){

		self::where('user_id', $userId)->update([
			'status'=>'inactive'
		]);

		self::where('user_id', $userId)->where('document_template_slug', $templteSlug)->update([
			'status'=>'active'
		]);

	}


}

?>