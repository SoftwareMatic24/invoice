<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){

		Schema::create("social_media_links", function(Blueprint $table){
			$table->id();
			$table->string("icon");
			$table->text("url");
			$table->string("target")->default("_blank");
		});

	}

	public function down(){
		Schema::dropIfExists("social_media_links");
	}

}


?>