<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->id();
			$table->string('first_name');
			$table->string('last_name')->nullable(true)->default(NULL);
			$table->string('email')->unique();
			$table->string('slug')->nullable()->default(NULL)->unique();
			$table->string('password');
			$table->string('phone')->nullable(true)->default(NULL);
			$table->text('about')->nullable(true)->default(NULL);
			$table->text('image')->nullable(true)->default(NULL);
			$table->enum('status', ['active', 'inactive', 'banned'])->default('active');
			$table->enum('visibility', ['public', 'private'])->default('public');
			$table->date('dob')->nullable(true)->default(NULL);
			$table->string('role_title', 70)->nullable(false);
			$table->integer('max_attempts')->default(5);
			$table->rememberToken();
			$table->string('create_datetime', 80)->nullable(true)->default(NULL);
			$table->string('update_datetime', 80)->nullable(true)->default(NULL);
			$table->foreign('role_title')->references('title')->on('roles')->onUpdate("cascade");
		});
	}

	public function down()
	{
		Schema::dropIfExists('users');
	}

	


};
