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
		Schema::create('notifications', function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->text("link")->nullable(true)->default(NULL);
			$table->enum("link_type", ["internal", "external", "none"])->default("none");
			$table->text("meta")->nullable(true)->default(NULL);
			$table->string("create_datetime")->nullable(true)->default(NULL);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('notifications');
	}
};
