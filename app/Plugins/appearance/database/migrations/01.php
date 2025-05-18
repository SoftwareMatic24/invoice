<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


	public function up()
	{
		Schema::create("default_branding", function (Blueprint $table) {
			$table->id();
			$table->string("column_name")->unique(true);
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->string("create_datetime")->nullable(true)->default(NULL);
		});

		Schema::create("branding", function (Blueprint $table) {
			$table->id();
			$table->string("column_name")->unique(true);
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->string("create_datetime")->nullable(true)->default(NULL);
		});
	}

	public function down()
	{
		Schema::dropIfExists("branding_default");
		Schema::dropIfExists("branding");
	}
};
