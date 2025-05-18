<?php

namespace App\Console;

use App\Http\Controllers\PluginController;
use App\Http\Controllers\QueueController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{

	protected function schedule(Schedule $schedule): void
	{

		$backgroundCommands = PluginController::getActivePluginsScheduleCommands(["backgroundCommands"=>true]);
		$tasks = PluginController::getActivePluginScheduleTasks();	

		foreach($backgroundCommands as $command){
			$schedule->command($command)->everyMinute()->runInBackground();
		}

		$schedule->command("queue:work --queue=email --max-time=57")->everyMinute()->runInBackground(); 
		$schedule->command("publish:ScheduledPosts")->everyMinute();

		foreach($tasks as $task){
			$schedule->call($task);
		}
		
	}

	
	protected function commands(): void
	{
		$this->load(__DIR__ . '/Commands');
		require base_path('routes/console.php');
	}
}
