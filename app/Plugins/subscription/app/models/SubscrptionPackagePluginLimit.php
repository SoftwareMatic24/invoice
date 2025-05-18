<?php

namespace App\Plugins\Subscription\Model;

use App\Models\Plugin;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackagePluginLimit extends Model {
	public $timestamps = false;
	protected $fillable = [
		"plugin_slug",
		"label",
		"limit_slug"
	];

	// Relation

	function plugin(){
		return $this->belongsTo(Plugin::class, "plugin_slug", "slug");
	}

	static function basicRelation(){
		return self::with("plugin");
	}

	// Query: Get

	static function getActivePluginLimits(){
		return self::whereHas("plugin", function($plugin){
			$plugin->where("status", "active");
		})->with("plugin")->get();
	}

}

?>