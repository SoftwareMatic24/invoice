<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PluginTestCommand extends Command
{
	protected $signature = 'test:Plugins {--slug=default}';
	protected $description = 'Test plugins';
	public function handle()
	{
		$pluginSlug = $this->option("slug");
		$dir = app_path("/Plugins/$pluginSlug/tests/Unit");
		if(!is_dir($dir)) die("/tests/Unit directory not found for plugin: ".$pluginSlug);

		$command = "vendor\\bin\\phpunit ".$dir;
		$this->info(shell_exec($command));
	}
}
