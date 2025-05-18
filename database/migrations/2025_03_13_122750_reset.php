<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	
	public function up(): void
	{
		Schema::create('resets', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->enum('status', ['active', 'inactive']);
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
		});

		Schema::create('reset_tables', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->unsignedBigInteger('reset_id');
			$table->foreign('reset_id')->references('id')->on('resets')->cascadeOnDelete();
		});

		Schema::create('reset_table_conditions', function(Blueprint $table){
			$table->id();
			$table->string('type');
			$table->string('column');
			$table->string('value')->nullable()->default(NULL);
			$table->unsignedBigInteger('reset_table_id');
			$table->foreign('reset_table_id')->references('id')->on('reset_tables')->cascadeOnDelete();
		});

		Schema::create('reset_settings', function(Blueprint $table){
			$table->id();
			$table->boolean('notification_visibility')->default(true);
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
		});
	}


	public function down(): void
	{
		Schema::dropIfExists('reset_settings');
		Schema::dropIfExists('reset_table_conditions');
		Schema::dropIfExists('reset_tables');
		Schema::dropIfExists('resets');
	}
};
