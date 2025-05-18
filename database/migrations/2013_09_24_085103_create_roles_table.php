<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up()
	{
		Schema::create('roles', function (Blueprint $table) {
			$table->id();
			$table->string('title',70)->nullable(false)->unique();
			$table->timestamp("created_at")->useCurrent();
			$table->timestamp("updated_at")->useCurrent();
		});

		Schema::create('role_additional_fields', function (Blueprint $table) {
			$table->id();
			$table->string('label');
			$table->string('slug');
			$table->enum('type', ['string', 'text'])->default('string');
			$table->string('role_title',70);
			$table->string("create_datetime", 50);
			$table->string("update_datetime", 50)->nullable()->default(NULL);
			$table->foreign('role_title')->references('title')->on('roles')->cascadeOnDelete();
		});
	}

	public function down()
	{
		Schema::dropIfExists('role_additional_fields');
		Schema::dropIfExists('roles');
	}
};
