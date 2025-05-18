<?php

namespace App\Jobs;

use App\Mail\GenericMail;
use App\Models\ActivityLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	public $timeout = 60;
	
	protected $mailDetails;
	protected $recipientEmail;

	public function __construct($recipientEmail, $mailDetails)
	{
		$this->recipientEmail = $recipientEmail;
		$this->mailDetails = $mailDetails;
	}
	
	public function handle(): void
	{	
		$logCtrl = new ActivityLog();

		try {
			Mail::to($this->recipientEmail)->send(new GenericMail($this->mailDetails));
			$logCtrl->addActivityLog("Email sent", "EMAIL_SENT", NULL, [
				"recipientEmail"=>$this->recipientEmail,
				"subject"=>$this->mailDetails["subject"] ?? NULL
			]);
		}
		catch(Exception $e) {
			$logCtrl->addActivityLog("Email not sent", "EMAIL_NOT_SENT", NULL, [
				"recipientEmail"=>$this->recipientEmail,
				"subject"=>$this->mailDetails["subject"] ?? NULL,
				"error"=>$e->getMessage() ?? NULL
			]);
		}
	}
}
