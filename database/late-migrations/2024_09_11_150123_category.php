<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{

		Schema::table('categories', function (Blueprint $table) {
			$table->foreign('image_icon')->references('id')->on('media')->nullOnDelete();
		});

		Schema::table('categories_i18n', function (Blueprint $table) {
			$table->foreign('image_icon')->references('id')->on('media')->nullOnDelete();
			$table->foreign("language_code")->references("code")->on("languages")->onDelete("CASCADE");
		});
	}


	public function down(): void
	{
	}
};
