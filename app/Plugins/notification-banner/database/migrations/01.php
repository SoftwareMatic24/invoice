<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){

		Schema::create("notification_banners", function(Blueprint $table){
			$table->id();
			$table->text("text");
			$table->enum("status", ["active", "inactive"])->default("active");
			$table->enum("type", ["web", "portal"])->default("web");
			$table->text("style")->nullable(true)->default(NULL);
			$table->string("create_datetime")->nullable(true)->default(NULL);
		});

	}

	public function down(){
		Schema::dropIfExists("notification_banners");
	}

}

?>