<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishSchedulePostsCommand extends Command
{
	protected $signature = 'publish:ScheduledPosts';
	protected $description = 'Publish the scheduled posts if the time has arrived.';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$postControllerClass = "App\Plugins\Posts\Controller\PostController";
		if (class_exists($postControllerClass)) {
			$postController = new $postControllerClass;
			$postController->publishScheduledPosts();
		}
	}
}
