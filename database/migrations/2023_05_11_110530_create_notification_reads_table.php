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
		Schema::create('notification_reads', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("user_id");
			$table->unsignedBigInteger("notification_id");
			$table->string("create_datetime")->nullable(true)->default(NULL);
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
			$table->foreign("notification_id")->references("id")->on("notifications")->onDelete("CASCADE");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('notification_reads');
	}
};
