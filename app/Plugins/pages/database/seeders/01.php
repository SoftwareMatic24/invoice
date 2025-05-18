<?php

use App\Classes\DateTime;
use App\Http\Controllers\PluginController;
use Illuminate\Support\Facades\DB;

return new class
{

	public function run()
	{
		$pageContent = '[{"title":"Section 1","content":"<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dicta incidunt voluptates molestiae provident veritatis corporis omnis accusamus ratione labore soluta doloremque maxime reiciendis minima accusantium, perspiciatis ex ipsa culpa sint quod blanditiis repellendus mollitia. Ullam eaque quo blanditiis provident culpa! Ullam suscipit accusantium temporibus sint nam possimus minus magni in.<\/p><p>&nbsp;<\/p><h2>Heading<\/h2><p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Distinctio eveniet adipisci neque ducimus qui voluptates voluptatibus a soluta quod id. Ullam eaque quo blanditiis provident culpa! Ullam suscipit accusantium temporibus sint nam possimus minus magni in.<\/p><p>&nbsp;<\/p><h3>Sub-Heading 1<\/h3><p>Distinctio eveniet adipisci neque ducimus qui voluptates voluptatibus a soluta quod id. Ullam eaque quo blanditiis provident culpa! Ullam suscipit accusantium temporibus sint nam possimus minus magni in.<\/p><p>&nbsp;<\/p><h3>Sub-Heading 2<\/h3><p>Distinctio eveniet adipisci neque ducimus qui voluptates voluptatibus a soluta quod id. Ullam eaque quo blanditiis provident culpa! Ullam suscipit accusantium temporibus sint nam possimus minus magni in.<\/p><p>&nbsp;<\/p><h2>Heading<\/h2><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dicta incidunt voluptates molestiae provident veritatis corporis omnis accusamus ratione labore soluta doloremque maxime reiciendis minima accusantium, perspiciatis ex ipsa culpa sint quod blanditiis repellendus mollitia. Ullam eaque quo blanditiis provident culpa! Ullam suscipit accusantium temporibus sint nam possimus minus magni in.<\/p><p>&nbsp;<\/p><ul><li>List point 1<\/li><li>List point 2<\/li><li>List point &nbsp;3<\/li><\/ul>"}]';

		$pages = [
			[
				"title" => "Home",
				"slug" => "home",
				"page_title"=>NULL,
				"description"=>NULL,
				"hard_url" => "",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Home"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Login",
				"slug" => "login",
				"page_title"=>"Sign in Account",
				"description"=>"Log in to access your account.",
				"hard_url" => "portal/login",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Login Account"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Register Account",
				"slug" => "register",
				"page_title"=>"Sign up Your Account",
				"description"=>"Create your account and get started.",
				"hard_url" => "portal/register",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Register Account"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Forgot Password",
				"slug" => "forgot-password",
				"page_title"=>"Forgot Your Password",
				"description"=>"Reset your password to access the account.",
				"hard_url" => "portal/forgot-password",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Forgot Password"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Reset Password",
				"slug" => "reset-password",
				"page_title"=>"Reset Password",
				"description"=>"Reset your password to access the account.",
				"hard_url" => "portal/reset/:uid",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Reset Password"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "2FA Code Verification",
				"slug" => "2fa-verify",
				"page_title"=>"Verification",
				"description"=>"Please verify your code to continue.",
				"hard_url" => "portal/2fa-verify/:uid",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Verify Code"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "404 Not Found",
				"slug" => "404",
				"page_title"=>"404 Not Found",
				"description"=>"The page is not found.",
				"hard_url" => "#",
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "404 Page Not Found"]),
				"content"=>NULL,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Privacy Policy",
				"slug" => "privacy-policy",
				"page_title"=>"Privacy Policy",
				"description"=>NULL,
				"hard_url" => NULL,
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Privacy Policy"]),
				"content"=>$pageContent,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Terms & Conditions",
				"slug" => "terms-and-conditions",
				"page_title"=>"Terms & Conditions",
				"description"=>NULL,
				"hard_url" => NULL,
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Terms & Conditions"]),
				"content"=>$pageContent,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"title" => "Disclaimer",
				"slug" => "disclaimer",
				"page_title"=>"Disclaimer",
				"description"=>NULL,
				"hard_url" => NULL,
				"persistence" => "permanent",
				"status" => "publish",
				"meta" => json_encode(["tabTitle" => "Disclaimer"]),
				"content"=>$pageContent,
				"create_datetime" => DateTime::getDateTime()
			],
		];

		DB::table("pages")->insert($pages);
	}
};
