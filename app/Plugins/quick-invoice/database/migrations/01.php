<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){
		Schema::create("clients", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->string("email")->nullable(true)->default(NULL);
			$table->string("country",2);
			$table->string("city")->nullable(true)->default(NULL);
			$table->string("province_state")->nullable(true)->default(NULL);
			$table->string("street")->nullable(true)->default(NULL);
			$table->string("street_2")->nullable(true)->default(NULL);
			$table->string("postcode",50)->nullable(true)->default(NULL);
			$table->string("telephone",50)->nullable(true)->default(NULL);
			$table->string("phone",50)->nullable(true)->default(NULL);
			$table->string("fax",50)->nullable(true)->default(NULL);
			$table->string("website")->nullable(true)->default(NULL);
			$table->string("registration_number",100)->nullable(true)->default(NULL);
			$table->string("registration_number_2",100)->nullable(true)->default(NULL);
			$table->string("tax_number",100)->nullable(true)->default(NULL);
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
		});

		Schema::create("client_defaults", function(Blueprint $table){
			$table->id();
			$table->decimal("discount", 8)->default(0);
			$table->enum("discount_type", ["percentage", "amount"])->default("percentage");
			$table->string("payment_method", 50)->nullable(true)->default(NULL);
			$table->string("currency_code", 3)->nullable(true)->default(NULL);
			$table->text("salutation")->nullable(true)->default(NULL);
			$table->text("note")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("client_id");
			$table->foreign("client_id")->references("id")->on("clients")->onDelete("cascade");
		});

		Schema::create("products", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->decimal("price", 8)->default(0);
			$table->string("unit", 50)->nullable(true)->default(NULL);
			$table->string("code", 150)->nullable(true)->default(NULL);
			$table->enum("type", ["product", "service"])->default("product");
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
		});

		Schema::create("businesses", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->string("email")->nullable(true)->default(NULL);
			$table->string("country", 2);
			$table->string("city")->nullable(true)->default(NULL);
			$table->string("province_state")->nullable(true)->default(NULL);
			$table->string("street")->nullable(true)->default(NULL);
			$table->string("street_2")->nullable(true)->default(NULL);
			$table->string("postcode",50)->nullable(true)->default(NULL);
			$table->string("telephone",50)->nullable(true)->default(NULL);
			$table->string("phone",50)->nullable(true)->default(NULL);
			$table->string("fax",50)->nullable(true)->default(NULL);
			$table->string("website")->nullable(true)->default(NULL);
			$table->string("business_id",100)->nullable(true)->default(NULL);
			$table->string("tax_id",100)->nullable(true)->default(NULL);
			$table->string("trade_register",100)->nullable(true)->default(NULL);
			$table->unsignedBigInteger("logo_id")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("signature_id")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
			$table->foreign("logo_id")->references("id")->on("media")->onDelete("set null");
			$table->foreign("signature_id")->references("id")->on("media")->onDelete("set null");
		});

		Schema::create("expense_categories", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
		});

		Schema::create("expenses", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->string("reference_number")->nullable(true)->default(NULL);
			$table->string("currency", 3)->nullable(true)->default(NULL);
			$table->string("expense_date", 50)->nullable(true)->default(NULL);
			$table->decimal("price", 8)->default(0);
			$table->decimal("tax", 8)->default(0);
			$table->enum("tax_type", ["percentage", "amount"])->default("percentage");
			$table->text("note")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("expense_category")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("client")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("business")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("client")->references("id")->on("clients")->onDelete("set null");
			$table->foreign("business")->references("id")->on("businesses")->onDelete("set null");
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
			$table->foreign("expense_category")->references("id")->on("expense_categories")->onDelete("set null");
		});

		Schema::create("documents", function(Blueprint $table){
			$table->id();
			$table->string("uid")->unique();
			$table->enum("document_type", ["invoice", "proposal", "delivery-note"])->default("invoice");
			$table->string("document_number");
			$table->string("reference_number")->nullable(true)->default(NULL);
			$table->string("order_number")->nullable(true)->default(NULL);
			$table->string("issue_date", 50)->nullable(true)->default(NULL);
			$table->string("due_date", 50)->nullable(true)->default(NULL);
			$table->string("currency", 3)->nullable(true)->default(NULL);
			$table->string("payment_method", 50)->nullable(true)->default(NULL);
			$table->string("delivery_type", 50)->nullable(true)->default(NULL);
			$table->decimal("discount", 8)->default(0);
			$table->enum("discount_type", ["percentage", "amount"])->default("percentage");
			$table->text("salutation")->nullable(true)->default(NULL);
			$table->text("note")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("client")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("business")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("added_by")->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("client")->references("id")->on("clients")->onDelete("set null");
			$table->foreign("business")->references("id")->on("businesses")->onDelete("set null");
			$table->foreign("added_by")->references("id")->on("users")->onDelete("set null");
		});

		Schema::create("document_items", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->decimal("quantity", 8, 2)->default(1);
			$table->decimal("unit_price", 8, 2)->default(0);
			$table->string("unit", 50)->nullable(true)->default(NULL);
			$table->string("code", 150)->nullable(true)->default(NULL);
			$table->decimal("vat", 8, 2)->default(0);
			$table->unsignedBigInteger("document_id");
			$table->foreign("document_id")->references("id")->on("documents")->onDelete("CASCADE");
		});

		Schema::create("document_meta", function(Blueprint $table){
			$table->id();
			$table->string("column_name");
			$table->text("column_value");
			$table->unsignedBigInteger("document_id");
			$table->foreign("document_id")->references("id")->on("documents")->onDelete("CASCADE");
		});

		Schema::create("document_payments", function(Blueprint $table){
			$table->id();
			$table->decimal("amount",8,2)->default(0);
			$table->string("reference_number")->nullable(true)->default(NULL);
			$table->text("note")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("document_id");
			$table->string("payment_datetime", 50)->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable(true)->default(NULL);
			$table->foreign("document_id")->references("id")->on("documents")->onDelete("CASCADE");
		});

		Schema::create("document_templates", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->string("slug")->unique();
			$table->string("primary_color",18)->nullable(true)->default(NULL);
			$table->string("secondary_color",18)->nullable(true)->default(NULL);
			$table->string("create_datetime", 50);
		});

		Schema::create("user_document_templates", function(Blueprint $table){
			$table->id();
			$table->string('primary_color', 18);
			$table->string('secondary_color', 18)->nullable()->default(NULL);
			$table->enum('status', ['active', 'inactive'])->default('inactive');
			$table->unsignedBigInteger("user_id");
			$table->string("document_template_slug");
			$table->foreign("document_template_slug")->references("slug")->on("document_templates")->onDelete("CASCADE");
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});

		Schema::create("document_fields", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->string("slug");
			$table->enum("document_type", ["invoice", "proposal", "delivery-note"]);
			$table->unsignedBigInteger("business_id");
			$table->enum("position", ["top", "bottom"])->default("top");
			$table->foreign("business_id")->references("id")->on("businesses")->onDelete("CASCADE");
			$table->unique(["slug", "document_type","business_id"]);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable();
		});
	}

	public function down(){
		Schema::dropIfExists("products");
		Schema::dropIfExists("client_defaults");
		Schema::dropIfExists("clients");
		Schema::dropIfExists("expense_categories");
		Schema::dropIfExists("expenses");
		Schema::dropIfExists("businesses");
		Schema::dropIfExists("document_payments");
		Schema::dropIfExists("document_meta");
		Schema::dropIfExists("document_items");
		Schema::dropIfExists("documents");
		Schema::dropIfExists("document_templates");
		Schema::dropIfExists("user_document_templates");
		Schema::dropIfExists("document_fields");
	}

}


?>