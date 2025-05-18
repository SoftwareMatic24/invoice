<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up(): void
	{
		Schema::create('cron_jobs', function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("slug");
			$table->enum("status", ["active","pause", "inactive"]);
			$table->integer("run_every_seconds")->nullable()->default(NULL);
			$table->string("last_run_date_time", 50)->nullable()->default(NULL);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('cron_jobs');
	}
};
