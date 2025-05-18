<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){
		Schema::create("settings", function(Blueprint $table){
			$table->id();
			$table->string("column_name");
			$table->text("column_value")->nullable(true)->default(NULL);
		});

		Schema::create("sitemap", function(Blueprint $table){
			$table->id();
			$table->enum("status", ["active", "inactive"])->default("active");
			$table->text("excluded_urls")->nullable(true)->default(NULL);
		});

		Schema::create("external_integrations", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->string("slug")->unique(true);
			$table->text("description")->nullable(true)->default(NULL);
			$table->enum("status", ["active", "inactive"])->default("inactive");
		});

		Schema::create("external_integration_details", function(Blueprint $table){
			$table->id();
			$table->string("column_name");
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->string("external_integration_slug");
			$table->foreign("external_integration_slug")->references("slug")->on("external_integrations")->onDelete("CASCADE");
		});


		Schema::create("two_factor_auths", function(Blueprint $table){
			$table->id();
			$table->enum("status", ["active", "inactive"])->default("inactive");
			$table->string("code")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("user_id");
			$table->string("code_create_datetime", 50)->nullable(true)->default(NULL);;
			$table->string("create_datetime", 50);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});

	}

	public function down(){
		Schema::dropIfExists("settings");
		Schema::dropIfExists("sitemap");
		Schema::dropIfExists("external_integration_details");
		Schema::dropIfExists("external_integrations");
		Schema::dropIfExists("two_factor_auths");
	}

}

?>