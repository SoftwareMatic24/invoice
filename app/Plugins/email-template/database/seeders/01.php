<?php

use Illuminate\Support\Facades\DB;

return new class
{
	public function run()
	{
		$signatures = [
			[
				"title"=>"Default Signature",
				"slug"=>"default-signature",
				"content"=>"
					<div style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;'>
						<div
							style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
							<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
								<h2 style='box-sizing: border-box;padding: 0;margin: 0;font-size: 24px;'>Do you have any question?</h2>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>If
									you have any questions or concerns,
									please don't hesitate to contact us. We' re always happy to help.</p><a href='[website-link]/contact'
									style='box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>Contact
									Us</a>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
									Thank you again for choosing us. We look forward to serving you.</p>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 30px;font-size: 17px;line-height: 1.4;'>
									Best regards,
								</p>
								<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 5px;font-size: 17px;line-height: 1.4;'>
									[app-name]</p>
							</div>
						</div>
					</div>
				"
			]
		];

		$emailTemplates = [
			[
				"title" => "Registration",
				"slug" => "registration",
				"subject" => "Confirm Your Account",
				"email_signature_id"=>1,
				"instructions" => json_encode([
					"Verification link: [verification-link]",
					"User name: [user-name]",
				]),
				"content" => "
					<div
						style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
						<div
							style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
							<h2
								style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
								Account Confirmation Required</h2>
						</div>
						<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
							<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'><b
									style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [user-name],</b></p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>Thank
								you for registering on our website. We're excited to have you join our community of users!</p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>To
								complete your registration, we need to verify your email address. Please click the button below or copy and
								paste it into your browser to confirm your account: </p>
							<a href='[verification-link]'
								style='padding:0;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>Verify
								Account</a>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								Verification Link: <a href='[verification-link]'
									style='border:none;box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;'>[verification-link]</a>
							</p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>Once
								your account has been confirmed, you'll be able to access all the features of our website, and start using
								it immediately. </p>
						</div>
					</div>
				"
			],
			[
				"title" => "Account Registration With Details",
				"slug" => "registration-with-details",
				"subject" => "Welcome! Your Account Details Inside",
				"email_signature_id"=>1,
				"instructions" => json_encode([
					"User name: [user-name]",
					"User email: [user-email]",
					"User password: [user-password]",
				]),
				"content" => "
					<div
						style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
						<div
							style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
							<h2
								style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
								Welcome! Your Account Details Inside
							</h2>
						</div>
						<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
							<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'><b
									style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [user-name],</b></p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>Thank
								you for registering on our website. We're excited to have you join our community of users!</p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								Please find your login credentials below for accessing your account:
							</p>
					
							<table
								style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;text-align: left;width: 100%;border-collapse: collapse;border: 2px solid lightgray;border-radius: 3px;'>
								<tbody style='box-sizing: border-box;padding: 0;margin: 0;'>
									<tr style='box-sizing: border-box;padding: 0;margin: 0;'>
										<td
											style='box-sizing: border-box;padding: 15px 15px;margin: 0;border-right: 2px solid lightgray;border-bottom: 2px solid lightgray;'>
											<b style='box-sizing: border-box;padding: 0;margin: 0;'>Email</b>
										</td>
										<td
											style='box-sizing: border-box;padding: 15px 15px;margin: 0;border-right: 2px solid lightgray;border-bottom: 2px solid lightgray;'>
											[user-email]
										</td>
									</tr>
									<tr style='box-sizing: border-box;padding: 0;margin: 0;'>
										<td
											style='box-sizing: border-box;padding: 15px 15px;margin: 0;border-right: 2px solid lightgray;border-bottom: 2px solid lightgray;'>
											<b style='box-sizing: border-box;padding: 0;margin: 0;'>Temporary Password</b>
										</td>
										<td
											style='box-sizing: border-box;padding: 15px 15px;margin: 0;border-right: 2px solid lightgray;border-bottom: 2px solid lightgray;'>
											[user-password]
										</td>
									</tr>
								</tbody>
							</table>
					
							<a href='[website-link]/portal/login'
								style='padding:0;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>
								Login Account
							</a>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								Login Link: <a href='[website-link]'
									style='border:none;box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;'>[website-link]</a>
							</p>
						</div>
					</div>
				"
			],
			[
				"title" => "Forgot Password",
				"slug" => "forgot-password",
				"subject" => "Reset Your Password",
				"email_signature_id"=>1,
				"instructions" => json_encode([
					"User name: [user-name]",
					"Reset link: [reset-link]"
				]),
				"content" => "
					<div
						style='box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;'>
						<div
							style='box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-position: center;min-height: 120px;'>
							<h2
								style='box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 4%;'>
								Reset Your Password
							</h2>
						</div>
						<div style='box-sizing: border-box;padding: 40px 40px;margin: 0;'>
							<p style='box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;'><b
									style='box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;'>Dear [user-name],
								</b></p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								We have received a request to reset your password of your account.
							</p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								To proceed with the password reset process, please click the button or follow the link below:
							</p>
							<a href='[reset-link]'
								style='padding:0;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;'>
								Reset Account
							</a>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								Password Reset Link: <a href='[reset-link]'
									style='border:none;box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;'>[reset-link]</a>
							</p>
							<p style='box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;'>
								If you did not request a password reset, please disregard this email. If you suspect that your account has
								been compromised, please contact our support team immediately.
							</p>
						</div>
					</div>
				"
			]
		];

		DB::table("email_signatures")->insert($signatures);
		DB::table("email_templates")->insert($emailTemplates);
	}
};
