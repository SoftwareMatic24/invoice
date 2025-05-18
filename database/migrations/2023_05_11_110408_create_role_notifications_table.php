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
		Schema::create('role_notifications', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("notification_id");
			$table->string('role',70)->nullable(false);
			$table->foreign("notification_id")->references("id")->on("notifications")->onDelete("CASCADE");
			$table->foreign('role')->references('title')->on('roles')->onUpdate("CASCADE")->onDelete("CASCADE");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('role_notifications');
	}
};
