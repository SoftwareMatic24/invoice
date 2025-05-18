<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

	public function up()
	{
		Schema::create("components", function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("slug")->unique(true)->nullable(false);
			$table->integer("max_entries")->nullable()->default(NULL);
			$table->enum("persistence", ["permanent", "temporary"])->default("temporary");
			$table->enum("visibility", ["visible", "hidden"])->default("visible");
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
		});

		Schema::create("component_groups", function (Blueprint $table) {
			$table->id();
			$table->string("name");
			$table->integer("max_entries")->nullable()->default(NULL);
			$table->unsignedBigInteger("component_id");
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
			$table->foreign("component_id")->references("id")->on("components")->cascadeOnDelete();
		});

		Schema::create("component_group_schemas", function (Blueprint $table) {
			$table->id();
			$table->string("label");
			$table->enum("type", ["string", "text", "image", "video"])->default("string");
			$table->unsignedBigInteger("component_group_id");
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
			$table->foreign("component_group_id")->references("id")->on("component_groups")->cascadeOnDelete();
		});

		Schema::create("component_data_sections", function (Blueprint $table) {
			$table->id();
			$table->string('component_slug');
			$table->string('component_number');
			$table->foreign("component_slug")->references("slug")->on("components")->cascadeOnDelete();
		});

		Schema::create("component_data_section_data", function (Blueprint $table) {
			$table->id();
			$table->string("label");
			$table->text("value")->nullable()->default(NULL);
			$table->unsignedBigInteger("component_group_id");
			$table->unsignedBigInteger("component_data_section_id");
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
			$table->foreign("component_group_id")->references("id")->on("component_groups")->cascadeOnDelete();
			$table->foreign("component_data_section_id")->references("id")->on("component_data_sections")->cascadeOnDelete();
		});
	}

	public function down()
	{
		Schema::dropIfExists("component_data_section_data_i18n");
		Schema::dropIfExists("component_data_section_data");
		Schema::dropIfExists("component_data_sections");
		Schema::dropIfExists("component_group_schemas");
		Schema::dropIfExists("component_groups");
		Schema::dropIfExists("components");
	}

	public function bridge()
	{

		$componentSectioni18n = function (Blueprint $table) {
			$table->id();
			$table->string('component_slug');
			$table->string('component_number');
			$table->string("language_code", 10);
			$table->foreign("component_slug")->references("slug")->on("components")->cascadeOnDelete();
			$table->foreign("language_code")->references("code")->on("languages")->onDelete("CASCADE");
		};

		$componentSectionDatai18n = function (Blueprint $table) {
			$table->id();
			$table->string("label");
			$table->text("value")->nullable()->default(NULL);
			$table->unsignedBigInteger("component_group_id");
			$table->unsignedBigInteger("component_data_section_id");
			$table->string("create_datetime");
			$table->string("update_datetime")->nullable(true)->default(NULL);
			$table->foreign("component_group_id", "comp_g_id_fk")->references("id")->on("component_groups")->cascadeOnDelete();
			$table->foreign("component_data_section_id", "comp_d_s_id_fk")->references("id")->on("component_data_section_i18n")->cascadeOnDelete();
		};

		return [
			[
				"dirs" => ["Plugins/language/database/migrations"],
				"blueprints" => [
					"component_data_section_i18n" => $componentSectioni18n,
					"component_data_section_data_i18n" => $componentSectionDatai18n,
				]
			]
		];
	}
};
