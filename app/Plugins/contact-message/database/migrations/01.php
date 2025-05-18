<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


	public function up(){
		Schema::create("contact_messages", function(Blueprint $table){
			$table->id();
			$table->boolean("read")->default(0);
			$table->string("create_datetime");
		});

		Schema::create("contact_message_details", function(Blueprint $table){
			$table->id();
			$table->string("column_name");
			$table->text("column_value")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("contact_message_id");
			$table->foreign("contact_message_id")->references("id")->on("contact_messages")->onDelete("CASCADE");
		});

	}

	public function down(){
		Schema::dropIfExists("contact_message_details");
		Schema::dropIfExists("contact_messages");
	}

}

?>