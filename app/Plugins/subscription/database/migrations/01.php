<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up()
	{	

		Schema::create('subscription_package_classifications', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->string('slug')->unique();
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
		});

		Schema::create("subscription_packages", function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->text("description")->nullable(true)->default(NULL);
			$table->decimal("price",8,2);
			$table->enum("status", ["active", "inactive"])->default("active");
			$table->unsignedBigInteger("user_id")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("classification_id");
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
			$table->foreign("classification_id")->references("id")->on("subscription_package_classifications")->restrictOnDelete();
			$table->foreign("user_id")->references("id")->on("users")->onDelete("SET NULL");
		});

		Schema::create("subscription_package_details", function (Blueprint $table) {
			$table->id();
			$table->string("name");
			$table->boolean("included")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("subscription_package_id");
			$table->foreign("subscription_package_id")->references("id")->on("subscription_packages")->onDelete("CASCADE");
		});

		Schema::create("subscription_package_limits", function (Blueprint $table) {
			$table->id();
			$table->string("slug");
			$table->string("limit")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("subscription_package_id");
			$table->foreign("subscription_package_id")->references("id")->on("subscription_packages")->onDelete("CASCADE");
		});

		Schema::create("subscription_package_plugin_limits", function (Blueprint $table) {
			$table->id();
			$table->string("plugin_slug");
			$table->string("label");
			$table->string("limit_slug");
			$table->foreign("plugin_slug")->references("slug")->on("plugins")->onDelete("CASCADE");
		});

		Schema::create("subscription_subscribable_roles", function (Blueprint $table) {
			$table->id();
			$table->string("role_title");
			$table->foreign("role_title")->references('title')->on('roles')->cascadeOnDelete();
		});

	}

	public function down()
	{
		Schema::dropIfExists("subscription_subscribers");
		Schema::dropIfExists("subscription_package_limits");
		Schema::dropIfExists("subscription_package_details");
		Schema::dropIfExists("subscription_packages");
		Schema::dropIfExists("subscription_package_plugin_limits");
		Schema::dropIfExists("subscription_package_classifications");
	}

	public function bridge(){
		$blueprints = [
			"subscription_subscribers"=> function(Blueprint $table){
				$table->id();
				$table->unsignedBigInteger("user_id");
				$table->unsignedBigInteger("subscription_package_id");
				$table->unsignedBigInteger("transaction_id")->nullable(true);
				$table->boolean('disable')->default(false);
				$table->string("expiry_datetime", 50)->nullable(true)->default(NULL);
				$table->string("create_datetime", 50);
				$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
				$table->foreign("subscription_package_id")->references("id")->on("subscription_packages")->onDelete("restrict");
				$table->foreign("transaction_id")->references("id")->on("transactions")->onDelete("SET NULL");
			}
		];

		return [
			[
				"dirs"=>["Plugins/payment-method/database/migrations"],
				"blueprints"=>$blueprints
			]
		];
	}

};
