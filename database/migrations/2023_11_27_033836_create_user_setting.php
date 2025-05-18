<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up(): void
	{
		Schema::create('user_settings', function (Blueprint $table) {
			$table->id();
			$table->string("column_name");
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("user_id");
			$table->foreign("user_id")->references("id")->on("users")->onDelete("CASCADE");
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_settings');
	}
};
