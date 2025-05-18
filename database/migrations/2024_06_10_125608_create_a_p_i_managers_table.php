<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create("api_keys", function (Blueprint $table) {
			$table->id();
			$table->string("key")->unique();
			$table->string("public_key")->nullable()->default(NULL);
			$table->string("private_key")->nullable()->default(NULL);
			$table->string("create_datetime", 50);
		});

		Schema::create("api_key_ip_access", function (Blueprint $table) {
			$table->id();
			$table->string("ip", 40);
			$table->enum("access", ["grant", "deny"]);
			$table->unsignedBigInteger("api_key_id");
			$table->string("create_datetime", 50);
			$table->foreign("api_key_id")->references("id")->on("api_keys")->onDelete("CASCADE");
		});
	}

	public function down(): void
	{
		Schema::dropIfExists("api_key_ip_access");
		Schema::dropIfExists("api_keys");
	}
};
