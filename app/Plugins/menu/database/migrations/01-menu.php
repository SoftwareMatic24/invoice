<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){
		Schema::create("menus", function(Blueprint $table){
			$table->id();
			$table->string("name")->unique();
			$table->string("display_name")->nullable()->default(NULL);
			$table->text("items")->nullable(true)->default(NULL);
			$table->boolean('lock_name')->default(false);
			$table->enum('presistence', ['temporary', 'permanent'])->default('permanent');
			$table->string("create_datetime")->nullable(true)->default(NULL);
			$table->string("update_datetime")->nullable(true)->default(NULL);
		});
	}

	public function down(){
		Schema::dropIfExists("menus");
	}

}


?>