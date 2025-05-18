<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(){
		Schema::create("languages", function(Blueprint $table){
			$table->id();
			$table->string("name");
			$table->string("code", 10)->unique();
			$table->enum("status", ["active", "inactive"])->default("active");
			$table->enum("type", ["primary", "secondary"])->default("secondary");
			$table->enum("direction", ["ltr", "rtl"])->default("ltr");
			$table->string("create_datetime");
		});
	}

	public function down(){
		Schema::dropIfExists("languages");
	}
}

?>