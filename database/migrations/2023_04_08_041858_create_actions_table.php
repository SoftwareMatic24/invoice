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
		Schema::create('actions', function (Blueprint $table) {
			$table->id();
			$table->string("slug");
			$table->string("uid");
			$table->enum("status", ["pending", "complete", "pre-complete"]);
			$table->text("data")->nullable(true)->default(NULL);
			$table->string("create_datetime");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('actions');
	}
};
