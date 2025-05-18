<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
			$table->string('ability',255)->nullable(false);
			$table->string('role_title',70)->nullable(false);
			$table->foreign('role_title')->references('title')->on('roles')->onDelete("cascade")->onUpdate("cascade");;
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('abilities');
	}
};
