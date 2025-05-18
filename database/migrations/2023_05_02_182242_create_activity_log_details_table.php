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
		Schema::create('activity_log_details', function (Blueprint $table) {
			$table->id();
			$table->string("column_name");
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("activity_log_id");
			$table->foreign("activity_log_id")->references("id")->on("activity_logs")->onDelete("CASCADE");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('activity_log_details');
	}
};
