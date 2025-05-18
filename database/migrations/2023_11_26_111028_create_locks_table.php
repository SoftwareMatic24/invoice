<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('locks', function (Blueprint $table) {
			$table->id();
			$table->string("slug",255)->unique();
			$table->string("expiry_datetime",50)->nullable(true)->default(NULL);
			$table->string("create_datetime",50);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('locks');
	}
};
