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
		Schema::create('activity_logs', function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("slug")->nullable(false);
			$table->string("ip")->nullable(true);
			$table->unsignedBigInteger("user_id")->nullable(true)->default(NULL);
			$table->string("create_datetime");
			$table->foreign("user_id")->references("id")->on("users")->onDelete("SET NULL");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('activity_logs');
	}
};
