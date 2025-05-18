<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){

		Schema::create("newsletters", function(Blueprint $table){
			$table->id();
			$table->string("uid");
			$table->string("name")->nullable(true)->default(NULL);
			$table->string("email");
			$table->enum("status", ["subscribed", "unsubscribed"])->default("subscribed");
			$table->string("create_datetime")->nullable(true)->default(NULL);
			$table->string("unsubscribe_datetime")->nullable(true)->default(NULL);
		});

	}

	public function down(){
		Schema::dropIfExists("newsletters");
	}

}

?>