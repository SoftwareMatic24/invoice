<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	
	public function up(): void
	{
		Schema::create('user_addresses', function(Blueprint $table){
			$table->id();
			$table->string('line_1')->nullable()->default(NULL);
			$table->string('line_2')->nullable()->default(NULL);
			$table->string('town_city')->nullable()->default(NULL);
			$table->string('state_province')->nullable()->default(NULL);
			$table->string('post_code')->nullable()->default(NULL);
			$table->string('country', 2)->nullable()->default(NULL);
			$table->unsignedBigInteger('user_id');
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
			$table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
		});
	}

	
	public function down(): void
	{
		Schema::dropIfExists('user_addresses');
	}
};
