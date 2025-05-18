<?php

namespace App\Console\Commands;

use App\Http\Controllers\PluginController;
use App\Models\Plugin;
use Illuminate\Console\Command;

class PluginsLangSetupCommand extends Command
{
	
	protected $signature = 'setup:PluginsLang';
	protected $description = 'Setup plugin languages';


	public function handle()
	{
		$pluginController = new PluginController();
		$activePlugins = Plugin::getActivePluginSlugs();
		$pluginController->seedLanguageFiles($activePlugins);
	}
}
