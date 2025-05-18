<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){

		Schema::create("tax_classes", function($table){
			$table->id();
			$table->string("title");
			$table->unsignedBigInteger("user_id")->nullable(true)->default(null);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});

		Schema::create("tax_class_rates", function($table){
			$table->id();
			$table->string("country")->nullable(true)->default(NULL);
			$table->string("state")->nullable(true)->default(NULL);
			$table->string("city")->nullable(true)->default(NULL);
			$table->string("postcode", 50)->nullable(true)->default(NULL);
			$table->decimal("rate", 8, 2)->default(0);
			$table->string("tax_name")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("tax_class_id");
			$table->foreign("tax_class_id")->references("id")->on("tax_classes")->onDelete("CASCADE");
		});

		Schema::create("shipping_classes", function($table){
			$table->id();
			$table->string("title");
			$table->unsignedBigInteger("user_id")->nullable(true)->default(null);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});

		Schema::create("shipping_zones", function($table){
			$table->id();
			$table->string("country")->nullable(true)->default(NULL);
			$table->string("state")->nullable(true)->default(NULL);
			$table->string("city")->nullable(true)->default(NULL);
			$table->string("postcode", 50)->nullable(true)->default(NULL);
			$table->unsignedBigInteger("shipping_class_id");
			$table->foreign("shipping_class_id")->references("id")->on("shipping_classes")->onDelete("CASCADE");
		});

		Schema::create("shipping_zone_conditions", function($table){
			$table->id();
			$table->decimal("from", 8, 2)->default(0);
			$table->decimal("to", 8, 2)->nullable(true)->default(NULL);
			$table->decimal("cost", 8, 2)->default(0);
			$table->unsignedBigInteger("shipping_zone_id");
			$table->foreign("shipping_zone_id")->references("id")->on("shipping_zones")->onDelete("CASCADE");
		});

		Schema::create("tax_settings", function($table){
			$table->id();
			$table->string("column_name", 100);
			$table->text("column_value")->nullable(true)->default(null);
			$table->unsignedBigInteger("user_id")->nullable(true)->default(null);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});
	}

	public function down(){
		Schema::dropIfExists("tax_classes");
		Schema::dropIfExists("tax_settings");
	}

}

?>