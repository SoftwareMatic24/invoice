<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(){

		Schema::create("payment_methods", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->string("slug")->unique();
			$table->string("image")->nullable(true)->default(NULL);
			$table->string("create_datetime",50);
			$table->string("update_datetime",50)->nullable(true)->default(NULL);
		});

		Schema::create("payment_method_identifiers", function(Blueprint $table){
			$table->id();
			$table->string("slug")->unique();
			$table->string("description")->nullable(true)->default(NULL);
		});

		Schema::create("user_payment_methods", function(Blueprint $table){
			$table->id();
			$table->string("name")->nullable(true)->default(NULL);
			$table->string("email")->nullable(true)->default(NULL);
			$table->string("payment_method_identifier");
			$table->enum("status", ["active", "inactive"])->default("inactive");
			$table->text("public_key")->nullable(true)->default(NULL);
			$table->text("private_key")->nullable(true)->default(NULL);
			$table->text("other")->nullable(true)->default(NULL);
			$table->string("payment_method_slug");
			$table->text("note")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("user_id");
			$table->string("create_datetime",50);
			$table->string("update_datetime",50)->nullable(true)->default(NULL);
			$table->foreign("payment_method_slug")->references("slug")->on("payment_methods")->onDelete("CASCADE")->opUpdate("CASCADE");
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
			$table->foreign("payment_method_identifier")->references("slug")->on("payment_method_identifiers")->onDelete("CASCADE");
		});

		Schema::create("system_payment_methods", function(Blueprint $table){
			$table->id();
			$table->string("name")->nullable(true)->default(NULL);
			$table->string("email")->nullable(true)->default(NULL);
			$table->string("payment_method_identifier");
			$table->enum("status", ["active", "inactive"])->default("inactive");
			$table->text("public_key")->nullable(true)->default(NULL);
			$table->text("private_key")->nullable(true)->default(NULL);
			$table->text("other")->nullable(true)->default(NULL);
			$table->string("payment_method_slug");
			$table->text("note")->nullable(true)->default(NULL);
			$table->string("create_datetime",50);
			$table->string("update_datetime",50)->nullable(true)->default(NULL);
			$table->foreign("payment_method_slug")->references("slug")->on("payment_methods")->onDelete("CASCADE")->opUpdate("CASCADE");
			$table->foreign("payment_method_identifier")->references("slug")->on("payment_method_identifiers")->onDelete("CASCADE");
		});

		Schema::create("transactions", function(Blueprint $table){
			$table->id();
			$table->text("uid");
			$table->string("customer_name")->nullable(true)->default(NULL);
			$table->string("customer_email")->nullable(true)->default(NULL);
			$table->string("product_name")->nullable(true)->default(NULL);
			$table->string("product_amount")->nullable(true)->default(NULL);
			$table->decimal("product_quantity", 10, 2)->nullable(true)->default(NULL);
			$table->string("currency")->nullable(true)->default(NULL);
			$table->string("payment_method")->nullable(true)->default(NULL);
			$table->string("type");
			$table->enum("status", ["pending", "complete", "cancel", "return"]);
			$table->text("other")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("user_id")->nullable(true)->default(NULL);
			$table->string("create_datetime", 100);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});
	}

	public function down(){
		Schema::dropIfExists("user_payment_methods");
		Schema::dropIfExists("system_payment_methods");
		Schema::dropIfExists("transactions");
		Schema::dropIfExists("payment_methods");
	}
}

?>