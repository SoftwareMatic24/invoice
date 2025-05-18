<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){
		$documentEmailContent = "
			<div
				style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
				<div
					style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
					<h2
						style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
						[document-name]# [document-number]
					</h2>
				</div>
				<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
					<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'><b
							style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [client-name],
						</b></p>
					<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
						[message]
					</p>
					
					<a href='[document-link]'
						style='padding:0;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>
						Download [document-name]
					</a>
					<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
						Download link: <a href='[document-link]'
							style='border:none;box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;'>[document-link]</a>
					</p>
				</div>
			</div>
		";
		$emailTemplates = [
			[
				"title" => "Document",
				"subject" => "Document",
				"slug" => "document",
				"content" => $documentEmailContent,
				"instructions" => json_encode([
					"Client Name: [client-name]",
					"Business Name: [business-name]",
					"Document Name: [document-name]",
					"Document Number: [document-number]"
				]),
				"email_signature_id" => 1
			]
		];
		DB::table("email_templates")->insert($emailTemplates);
	}
}

?>