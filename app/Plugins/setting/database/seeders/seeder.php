<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

return new class extends Seeder
{

	public function run()
	{

		$settings = [
			
			[
				"column_name" => "user-registration",
				"column_value" => 1
			],
			[
				"column_name" => "user-payment-methods",
				"column_value" => true
			],
			[
				"column_name" => "website-preview-option",
				"column_value" => true
			],
			[
				"column_name" => "account-home-button",
				"column_value" => false
			],
			[
				"column_name" => "portal-sidebar-separators",
				"column_value" => true
			],
			[
				"column_name" => "profile-picture",
				"column_value" => true,
			],
			[
				"column_name" => "portal-nav-copyright",
				"column_value" => NULL,
			],
			[
				"column_name" => "on-user-manager-update-user-redirect",
				"column_value" => false,
			],
			[
				"column_name" => "email-template",
				"column_value" => "design-0"
			]
		];

		DB::table("settings")->insert($settings);
		DB::table("sitemap")->insert([
			"status" => "inactive"
		]);


		// External Integrations

		$externalIntegrations = [
			[
				"title" => "Google OAuth",
				"slug" => "google-oauth",
				"description" => "Setup Google OAuth to be able to login & register with gmail.",
				"status" => "inactive"
			]
		];

		DB::table("external_integrations")->insert($externalIntegrations);

		// SMTP

		$smtpData = [
			[
				"column_name" => "smtp-host",
				"column_value" => env("MAIL_HOST"),
			],
			[
				"column_name" => "smtp-port",
				"column_value" => env("MAIL_PORT"),
			],
			[
				"column_name" => "smtp-encryption",
				"column_value" => env("MAIL_ENCRYPTION"),
			],
			[
				"column_name" => "smtp-email",
				"column_value" => env("MAIL_USERNAME"),
			],
			[
				"column_name" => "smtp-password",
				"column_value" => env("MAIL_PASSWORD"),
			],
			[
				"column_name" => "smtp-from-name",
				"column_value" => env("MAIL_FROM_NAME"),
			],
			[
				"column_name" => "smtp-domain",
				"column_value" => url("/"),
			],
		];

		DB::table("settings")->insert($smtpData);
	}

	public function bridge()
	{
		$emailTemplatesFunc = function () {
			$emailTemplates = [
				[
					"title" => "2FA Account Confirmation",
					"subject" => "2FA Verification Code",
					"slug" => "2fa-confirmation",
					"email_signature_id" => 1,
					"content" => "
						<div
							style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
							<div
								style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
								<h2
									style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
									2FA Verification Code
								</h2>
							</div>
							<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
								<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'><b
										style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [user-name],
									</b></p>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
									To login your account, please use the following One-Time Verification Code.
								</p>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
									Code: <b>[verification-code]</b>
								</p>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
									Please click the button or copy and paste verification link into your browser to verify code.
								</p>
								<a href='[verification-link]'
									style='padding:0;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>
									Verify Code
								</a>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
									Verification Link: <a href='[verification-link]'
										style='border:none;box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;'>[verification-link]</a>
								</p>
							</div>
						</div>
					",
					"instructions" => json_encode([
						"User name: [user-name]",
						"2FA code: [verification-code]",
						"Verification link: [verification-link]",
					])
				],
				[
					"title" => "Testing Mail",
					"subject" => "Testing Mail",
					"slug" => "test",
					"email_signature_id" => 1,
					"content" => "",
					"instructions" => NULL
				]
			];

			DB::table("email_templates")->insert($emailTemplates);
		};

		return [
			[
				"dirs" => ["Plugins/email-template/database/seeders"],
				"seeds" => [$emailTemplatesFunc]
			]
		];
	}
};
