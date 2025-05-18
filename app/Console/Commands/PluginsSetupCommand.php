<?php

namespace App\Console\Commands;

use App\Http\Controllers\PluginController;
use Illuminate\Console\Command;

class PluginsSetupCommand extends Command
{

	protected $signature = 'setup:Plugins';

	protected $description = 'setup plugins';

	public function handle()
	{
		$pluginController = new(new PluginController());
		$pluginController->setupPlugins();
	}
}
