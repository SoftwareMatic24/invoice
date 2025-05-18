<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	
	public function up(): void
	{
		Schema::create("sessions", function (Blueprint $table) {
			$table->id();
			$table->string("uid");
			$table->text("data")->nullable()->default(NULL);
			$table->string("expiry_datetime", 50)->nullable()->default(NULL);
			$table->string("create_datetime", 50);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists("sessions");
	}
};
