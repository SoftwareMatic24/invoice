<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class {
	public function run(){
		DB::table("pages")->where("slug", "home")->update([
			"page_title"=>"Professional Invoice Maker for every businesses",
			"meta"=>json_encode([
				"tabTitle"=>"Invoice Maker Software Demo | Ready Made Software"
			])
		]);
	}
}

?>