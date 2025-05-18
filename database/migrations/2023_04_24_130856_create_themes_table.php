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
		Schema::create('themes', function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("slug");
			$table->enum("status", ["active", "inactive"])->default("inactive");
			$table->string("author", 100)->nullable(true)->default(null);
			$table->string("website")->nullable(true)->default(null);
			$table->text("image")->nullable(true)->default(null);
			$table->text("options")->nullable(true)->default(NULL);
			$table->string("create_datetime");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('themes');
	}
};
