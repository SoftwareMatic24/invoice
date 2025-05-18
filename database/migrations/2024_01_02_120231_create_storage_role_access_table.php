<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up(): void
	{
		Schema::create('storage_role_access', function (Blueprint $table) {
			$table->id();
			$table->string("storage");
			$table->string("base_folder");
			$table->string('role_title', 70)->nullable(false);
			$table->foreign('role_title')->references('title')->on('roles')->onUpdate("cascade");
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('storage_role_access');
	}
};
