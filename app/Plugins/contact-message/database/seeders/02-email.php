<?php

use Illuminate\Support\Facades\DB;

return new class
{

	public function run()
	{

		$contactReplyContent = <<<HTML

			<div style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
				<div style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
				<h2 style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
					Contact Message Reply
				</h2>
				</div>
				<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
					<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'>
						<b style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [user-name],</b>
					</p>
					<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
						[reply]
					</p>
				</div>
			</div>

			HTML;

		$emails = [
			[
				"title" => "Contact Reply",
				"slug" => "contact-reply",
				"subject" => "Contact Reply",
				"instructions" => NULL,
				"content" => $contactReplyContent,
				"email_signature_id" => 1
			]
		];

		DB::table("email_templates")->insert($emails);
	}
};
