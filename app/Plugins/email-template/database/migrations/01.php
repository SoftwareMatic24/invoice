<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

	public function up(){
		Schema::create("email_signatures", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->string("slug")->unique();
			$table->text("content")->nullable(true)->default(NULL);
		});
		Schema::create("email_templates", function(Blueprint $table){
			$table->id();
			$table->string("title");
			$table->string("slug")->unique();
			$table->string("subject")->nullable(true)->default(NULL);
			$table->text("instructions")->nullable(true)->default(NULL);
			$table->text("content")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("email_signature_id")->nullable(true)->default(NULL);
			$table->foreign("email_signature_id")->on("email_signatures")->references("id")->onDelete("SET NULL");
		});
	}

	public function down(){
		Schema::dropIfExists("email_templates");
		Schema::dropIfExists("email_signatures");
	}

};