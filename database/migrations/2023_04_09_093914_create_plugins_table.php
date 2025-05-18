<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('plugins', function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("slug")->unique();
			$table->enum("type", ["parent", "child"])->default("parent");
			$table->string("version", 10)->nullable(true)->default(NULL);
			$table->text("image")->nullable(true)->default(NULL);
			$table->string("author", 100)->nullable(true)->default(NULL);
			$table->string("website")->nullable(true)->default(NULL);
			$table->text("description")->nullable(true)->default(NULL);
			$table->enum("status", ["active", "inactive"])->default("active");
			$table->enum("presistence", ["permanent", "temporary"])->default("temporary");
			$table->enum("visibility", ["sidebar", "page", "all"])->default("page");
			$table->unsignedInteger("order")->nullable(true)->default(NULL);
			$table->text("options")->nullable(true)->default(NULL);
			$table->string("create_datetime");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('plugins');
	}
};
