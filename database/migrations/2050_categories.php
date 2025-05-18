<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('category_classifications', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->string('slug')->unique();
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
		});
		
		Schema::create('categories', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->string('slug');
			$table->string('description')->nullable()->default(NULL);
			$table->string('svg_icon')->nullable()->default(NULL);
			$table->unsignedBigInteger('image_icon')->nullable()->default(NULL);
			$table->boolean('featured')->default(false);
			$table->boolean('featured_2')->default(false);
			$table->enum('accessibility', ['share', 'role-share', 'none'])->default('none');
			$table->unsignedBigInteger('user_id')->nullable()->default(NULL);
			$table->string('category_classification_slug')->nullable()->default(NULL);
			$table->unsignedBigInteger('parent_category_id')->nullable()->default(NULL);
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
			$table->unique(['slug', 'category_classification_slug']);
			$table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
			$table->foreign('category_classification_slug')->references('slug')->on('category_classifications')->cascadeOnDelete();
			$table->foreign('parent_category_id')->references('id')->on('categories')->cascadeOnDelete();
			//$table->foreign('image_icon')->references('id')->on('media')->nullOnDelete();
		});
		
		Schema::create('categories_i18n', function(Blueprint $table){
			$table->id();
			$table->string('name');
			$table->string('description')->nullable()->default(NULL);
			$table->string('svg_icon')->nullable()->default(NULL);
			$table->unsignedBigInteger('image_icon')->nullable()->default(NULL);
			$table->string("language_code", 10);
			$table->unsignedBigInteger('category_id');
			$table->string('create_datetime', 50);
			$table->string('update_datetime', 50)->nullable()->default(NULL);
			$table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
			//$table->foreign("language_code")->references("code")->on("languages")->onDelete("CASCADE");
			//$table->foreign('image_icon')->references('id')->on('media')->nullOnDelete();
		});
	}

	
	public function down(): void
	{
		Schema::dropIfExists('category_classifications');
		Schema::dropIfExists('categories');
	}
};
