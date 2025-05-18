<?php

namespace App\Plugins\EmailTemplate\Model;

require_once __DIR__ . "/EmailSignature.php";

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"title",
		"slug",
		"subject",
		"instructions",
		"content",
		"email_signature_id"
	];

	// Relation

	function signature()
	{
		return $this->belongsTo(EmailSignature::class, "email_signature_id", "id");
	}

	// Build

	static function basicRelation()
	{
		return self::with("signature");
	}


	// Query

	static function getEmailTemplates()
	{
		$relation = self::basicRelation();
		return $relation->get();
	}

	static function getEmailTemplate($emailTemplateId)
	{
		$relation = self::basicRelation();
		return $relation->where("id", $emailTemplateId)->first();
	}

	static function getEmailTemplateBySlug($slug, $parseShortCode = true)
	{
		$relation = self::basicRelation();
		return $relation->where("slug", $slug)->first();
	}

	static function updateEmailTemplate($emailTemplateId, $data)
	{
		return self::where("id", $emailTemplateId)->update([
			"subject" => $data["subject"] ?? "",
			"email_signature_id"=>$data["signature"] ?? NULL,
			"content" => $data["data"]
		]);
	}
}
