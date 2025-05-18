<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


	public function up()
	{
		Schema::create("currencies", function (Blueprint $table) {
			$table->id();
			$table->string("currency")->unique();
			$table->string("symbol", 20);
			$table->enum("type", ["primary", "secondary"])->default("secondary");
			$table->decimal("rate",8,2);
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
		});
	}

	public function down()
	{
		Schema::dropIfExists("currencies");
	}
};
