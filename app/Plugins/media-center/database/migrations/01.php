<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


	public function up(){
		Schema::create("media_center_folders", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->unsignedBigInteger("user_id")->nullable(true)->default(NULL);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("SET NULL");
		});

		Schema::create("media_center_folder_accessibilities", function(Blueprint $table){
			$table->id();
			$table->string("role_title", 70);
			$table->unsignedBigInteger("media_center_folder_id");
			$table->string("create_datetime", 50);
			$table->foreign("role_title")->references("title")->on("roles")->onDelete("CASCADE");
			$table->foreign("media_center_folder_id", "fk_media_center_folder_accessibilities")->references("id")->on("media_center_folders")->onDelete("CASCADE");
		});

		Schema::create("media", function(Blueprint $table){
			$table->id();
			$table->text("url");
			$table->text("thumbnail")->nullable(true)->default(NULL);
			$table->string("type");
			$table->text("options")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("user_id")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("folder_id");
			$table->boolean("private")->default(false);
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("SET NULL");
			$table->foreign("folder_id")->references("id")->on("media_center_folders")->onDelete("CASCADE");
		});
	}

	public function down(){
		Schema::dropIfExists("media_center_folder_accessibilities");
		Schema::dropIfExists("media");
		Schema::dropIfExists("media_center_folders");
	}

}

?>