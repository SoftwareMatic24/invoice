<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up()
	{
		Schema::create("pages", function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("page_title")->nullable(true)->default(NULL);
			$table->string("slug")->unique();
			$table->text("description")->nullable()->default(NULL);
			$table->string("hard_url")->nullable(true)->default(NULL);
			$table->enum("persistence", ["permanent", "temporary"])->default("temporary");
			$table->enum("status", ["publish", "drafts"])->default("drafts");
			$table->unsignedBigInteger("featured_image")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("featured_video")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("featured_video_thumbnail")->nullable(true)->default(NULL);
			$table->text("content")->nullable(true)->default(NULL);
			$table->text("meta")->nullable(true)->default(NULL);
			$table->string("create_datetime");
			$table->foreign("featured_image")->references("id")->on("media")->onDelete("set null");
			$table->foreign("featured_video")->references("id")->on("media")->onDelete("set null");
			$table->foreign("featured_video_thumbnail")->references("id")->on("media")->onDelete("set null");
		});
	}

	public function down()
	{
		Schema::dropIfExists("pages_i18n");
		Schema::dropIfExists("pages");
	}


	public function bridge()
	{

		$pages_i18n = function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("page_title")->nullable(true)->default(NULL);
			$table->text("description")->nullable()->default(NULL);
			$table->text("content")->nullable(true)->default(NULL);
			$table->text("meta")->nullable(true)->default(NULL);
			$table->unsignedBigInteger("page_id");
			$table->string("language_code", 10);
			$table->string("create_datetime");
			$table->foreign("page_id")->references("id")->on("pages")->onDelete("CASCADE");
			$table->foreign("language_code")->references("code")->on("languages")->onDelete("CASCADE");
		};

		return [
			[
				"dirs" => ["Plugins/language/database/migrations"],
				"blueprints" => [
					"pages_i18n" => $pages_i18n
				]
			]
		];
	}
};
